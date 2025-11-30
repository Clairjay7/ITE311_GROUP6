<?php

namespace App\Controllers\ITStaff;

use App\Controllers\BaseController;
use App\Models\SystemBackupModel;

class BackupController extends BaseController
{
    protected $backupModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->backupModel = new SystemBackupModel();
    }

    public function index()
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $backups = $this->backupModel->getBackupsWithUser();

        $data = [
            'title' => 'Create Backup',
            'backups' => $backups,
        ];

        return view('itstaff/backup/index', $data);
    }

    public function create()
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $validation = $this->validate([
            'backup_name' => 'required|max_length[255]',
            'backup_type' => 'required|in_list[database,files,full]',
            'notes' => 'permit_empty|max_length[1000]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $backupType = $this->request->getPost('backup_type');
        $backupName = $this->request->getPost('backup_name') ?: 'backup_' . date('Y-m-d_H-i-s');
        $notes = $this->request->getPost('notes');

        // Create backup directory if it doesn't exist
        $backupDir = WRITEPATH . 'backups/';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        try {
            $fileName = $backupName . '_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupDir . $fileName;

            // Create database backup
            if ($backupType === 'database' || $backupType === 'full') {
                $this->createDatabaseBackup($filePath);
            }

            // Create files backup (if needed)
            if ($backupType === 'files' || $backupType === 'full') {
                // Check if ZipArchive is available before attempting file backup
                if (!class_exists('ZipArchive')) {
                    // If only files backup is requested and zip is not available, throw error
                    if ($backupType === 'files') {
                        throw new \Exception('PHP Zip extension is not enabled. Please enable php_zip extension in your PHP configuration to create file backups. You can create database backups instead.');
                    }
                    // If full backup is requested, skip files and only do database
                    // Continue with database backup only
                } else {
                    // For files backup, create a zip file
                    $zipFileName = str_replace('.sql', '.zip', $fileName);
                    $zipPath = $backupDir . $zipFileName;
                    $this->createFilesBackup($zipPath);
                    $filePath = $zipPath;
                }
            }

            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

            // Save backup record to database
            $backupData = [
                'backup_name' => $backupName,
                'backup_type' => $backupType,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'status' => 'completed',
                'created_by' => session()->get('user_id'),
                'notes' => $notes,
            ];

            if ($this->backupModel->insert($backupData)) {
                // Log the backup creation
                $this->logBackupAction('Backup created', $this->backupModel->getInsertID());
                
                return redirect()->to('/it/backup')->with('success', 'Backup created successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to save backup record.');
            }
        } catch (\Exception $e) {
            // Log error
            $this->logBackupAction('Backup failed: ' . $e->getMessage(), null, 'error');
            
            return redirect()->back()->withInput()->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $backup = $this->backupModel->find($id);
        
        if (!$backup || !file_exists($backup['file_path'])) {
            return redirect()->to('/it/backup')->with('error', 'Backup file not found.');
        }

        return $this->response->download($backup['file_path'], null);
    }

    public function delete($id)
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $backup = $this->backupModel->find($id);
        
        if (!$backup) {
            return redirect()->to('/it/backup')->with('error', 'Backup not found.');
        }

        // Delete file if exists
        if (file_exists($backup['file_path'])) {
            unlink($backup['file_path']);
        }

        if ($this->backupModel->delete($id)) {
            // Log the backup deletion
            $this->logBackupAction('Backup deleted', $id);
            
            return redirect()->to('/it/backup')->with('success', 'Backup deleted successfully.');
        } else {
            return redirect()->to('/it/backup')->with('error', 'Failed to delete backup.');
        }
    }

    /**
     * Create database backup
     */
    private function createDatabaseBackup($filePath)
    {
        $db = \Config\Database::connect();
        $dbName = $db->database;
        $dbUser = $db->username;
        $dbPass = $db->password;
        $dbHost = $db->hostname;

        // Use mysqldump if available
        $command = "mysqldump -h {$dbHost} -u {$dbUser} -p{$dbPass} {$dbName} > {$filePath} 2>&1";
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            // Fallback: Create SQL manually
            $this->createDatabaseBackupManual($filePath);
        }
    }

    /**
     * Create database backup manually (fallback)
     */
    private function createDatabaseBackupManual($filePath)
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        
        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            $sql .= "\n-- Table: {$table}\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            $createTable = $db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            if ($createTable) {
                $sql .= $createTable['Create Table'] . ";\n\n";
            }
            
            $rows = $db->table($table)->get()->getResultArray();
            if (!empty($rows)) {
                $sql .= "INSERT INTO `{$table}` VALUES\n";
                $values = [];
                foreach ($rows as $row) {
                    $rowValues = array_map(function($value) use ($db) {
                        return $value === null ? 'NULL' : $db->escape($value);
                    }, array_values($row));
                    $values[] = '(' . implode(',', $rowValues) . ')';
                }
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        file_put_contents($filePath, $sql);
    }

    /**
     * Create files backup
     */
    private function createFilesBackup($zipPath)
    {
        // Check if ZipArchive is available
        if (!class_exists('ZipArchive')) {
            throw new \Exception('PHP Zip extension is not enabled. Please enable php_zip extension in your PHP configuration to create file backups.');
        }
        
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            // Add application files (excluding sensitive directories)
            $excludeDirs = ['vendor', 'node_modules', 'writable/logs', 'writable/cache', 'writable/session', 'writable/debugbar', 'writable/backups'];
            $this->addDirectoryToZip(ROOTPATH, $zip, $excludeDirs);
            
            $zip->close();
        } else {
            throw new \Exception('Failed to create zip file.');
        }
    }

    /**
     * Add directory to zip recursively
     */
    private function addDirectoryToZip($dir, $zip, $excludeDirs = [], $basePath = '')
    {
        $files = scandir($dir);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            $relativePath = $basePath . $file;
            
            // Skip excluded directories
            $shouldExclude = false;
            foreach ($excludeDirs as $excludeDir) {
                if (strpos($relativePath, $excludeDir) === 0) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if ($shouldExclude) continue;
            
            if (is_dir($filePath)) {
                $zip->addEmptyDir($relativePath);
                $this->addDirectoryToZip($filePath, $zip, $excludeDirs, $relativePath . '/');
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Log backup action to system logs
     */
    private function logBackupAction($action, $backupId = null, $level = 'info')
    {
        try {
            $logModel = new \App\Models\SystemLogModel();
            $userAgent = $this->request->getUserAgent();
            $userAgentString = $userAgent ? (method_exists($userAgent, 'getAgentString') ? $userAgent->getAgentString() : (string)$userAgent) : 'Unknown';
            
            // Truncate user_agent if too long
            if (strlen($userAgentString) > 255) {
                $userAgentString = substr($userAgentString, 0, 252) . '...';
            }
            
            // Truncate action if too long
            $actionString = strtolower(str_replace(' ', '_', $action));
            if (strlen($actionString) > 100) {
                $actionString = substr($actionString, 0, 97) . '...';
            }
            
            $logModel->insert([
                'level' => $level,
                'message' => substr($action . ' by IT Staff: ' . (session()->get('name') ?? 'Unknown') . ($backupId ? ' (Backup ID: ' . $backupId . ')' : ''), 0, 65535), // TEXT field limit
                'user_id' => session()->get('user_id'),
                'ip_address' => $this->request->getIPAddress() ?: '0.0.0.0',
                'user_agent' => $userAgentString,
                'module' => 'backup_restore',
                'action' => $actionString,
            ]);
        } catch (\Exception $e) {
            // Silently fail logging to prevent infinite loop
            log_message('error', 'Failed to log backup action: ' . $e->getMessage());
        }
    }
}

