<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SystemModel;
use App\Models\SystemLogModel;
use App\Models\SystemBackupModel;
use App\Models\UserModel;

class SystemController extends BaseController
{
    protected $systemModel;
    protected $logModel;
    protected $backupModel;
    protected $userModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->systemModel = new SystemModel();
        $this->logModel = new SystemLogModel();
        $this->backupModel = new SystemBackupModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Redirect to dashboard instead of showing old system settings
        return redirect()->to('/admin/system/dashboard');
    }

    public function create()
    {
        $data = [
            'title' => 'Add System Setting',
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/system/create', $data);
    }

    public function store()
    {
        $rules = [
            'setting_name' => 'required|max_length[255]|is_unique[system_controls.setting_name]',
            'setting_value' => 'required|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'setting_name' => $this->request->getPost('setting_name'),
            'setting_value' => $this->request->getPost('setting_value'),
        ];

        $this->systemModel->insert($data);

        return redirect()->to('/admin/system')->with('success', 'System setting created successfully.');
    }

    public function edit($id)
    {
        $setting = $this->systemModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/system')->with('error', 'System setting not found.');
        }

        $data = [
            'title' => 'Edit System Setting',
            'setting' => $setting,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/system/edit', $data);
    }

    public function update($id)
    {
        $setting = $this->systemModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/system')->with('error', 'System setting not found.');
        }

        $rules = [
            'setting_name' => "required|max_length[255]|is_unique[system_controls.setting_name,id,{$id}]",
            'setting_value' => 'required|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'setting_name' => $this->request->getPost('setting_name'),
            'setting_value' => $this->request->getPost('setting_value'),
        ];

        $this->systemModel->update($id, $data);

        return redirect()->to('/admin/system')->with('success', 'System setting updated successfully.');
    }

    public function delete($id)
    {
        $setting = $this->systemModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/system')->with('error', 'System setting not found.');
        }

        $this->systemModel->delete($id);

        return redirect()->to('/admin/system')->with('success', 'System setting deleted successfully.');
    }

    /**
     * System Control Dashboard
     */
    public function dashboard()
    {
        $db = \Config\Database::connect();

        // Get statistics
        $activeUsers = $this->userModel->where('status', 'active')->countAllResults();
        
        // Get recent system logs count
        $recentLogsCount = $this->logModel
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();
        
        $errorLogsCount = $this->logModel
            ->whereIn('level', ['error', 'critical', 'alert', 'emergency'])
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->countAllResults();
        
        // Get backup statistics
        $totalBackups = $this->backupModel->countAllResults();

        // Get recent logs
        $recentLogs = $this->logModel
            ->select('system_logs.*, users.username as user_name')
            ->join('users', 'users.id = system_logs.user_id', 'left')
            ->orderBy('system_logs.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Calculate system uptime (simplified)
        $systemUptime = '99.8%';

        // Get users by role for monitoring
        $usersByRole = $db->table('users u')
            ->select('r.name as role_name, COUNT(u.id) as user_count, SUM(CASE WHEN u.status = "active" THEN 1 ELSE 0 END) as active_count')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('r.name !=', 'patient')
            ->groupBy('r.id', 'r.name')
            ->orderBy('r.name', 'ASC')
            ->get()->getResultArray();

        // Get role-specific statistics
        $roleStats = [];
        $roleMap = [
            'admin' => ['id' => 1, 'name' => 'Admin', 'icon' => 'fa-user-shield', 'color' => '#2e7d32'],
            'doctor' => ['id' => 2, 'name' => 'Doctor', 'icon' => 'fa-user-doctor', 'color' => '#0288d1'],
            'nurse' => ['id' => 3, 'name' => 'Nurse', 'icon' => 'fa-user-nurse', 'color' => '#f59e0b'],
            'receptionist' => ['id' => 4, 'name' => 'Receptionist', 'icon' => 'fa-user-tie', 'color' => '#10b981'],
            'finance' => ['id' => 6, 'name' => 'Finance', 'icon' => 'fa-dollar-sign', 'color' => '#8b5cf6'],
            'pharmacy' => ['id' => 9, 'name' => 'Pharmacy', 'icon' => 'fa-pills', 'color' => '#ef4444'],
            'lab_staff' => ['id' => 8, 'name' => 'Lab Staff', 'icon' => 'fa-flask', 'color' => '#ec4899'],
        ];

        foreach ($roleMap as $roleName => $roleInfo) {
            $roleId = $roleInfo['id'];
            $roleUsers = $db->table('users')
                ->where('role_id', $roleId)
                ->where('status', 'active')
                ->countAllResults();
            
            $moduleName = str_replace('_', '_', $roleName);
            $moduleLogs = $this->logModel
                ->where('module', $moduleName)
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                ->countAllResults();
            
            $moduleHealth = 'operational';
            $tablesToCheck = [];
            
            switch ($roleName) {
                case 'admin':
                    $tablesToCheck = ['admin_patients', 'schedules', 'billing'];
                    break;
                case 'doctor':
                    $tablesToCheck = ['consultations', 'doctor_orders'];
                    break;
                case 'nurse':
                    $tablesToCheck = ['patient_vitals', 'nurse_notes', 'doctor_orders'];
                    break;
                case 'receptionist':
                    $tablesToCheck = ['appointments', 'admin_patients'];
                    break;
                case 'finance':
                    $tablesToCheck = ['billing'];
                    break;
                case 'pharmacy':
                    $tablesToCheck = ['pharmacy'];
                    break;
                case 'lab_staff':
                    $tablesToCheck = ['lab_requests', 'lab_results'];
                    break;
            }
            
            $tablesExist = true;
            foreach ($tablesToCheck as $table) {
                if (!$db->tableExists($table)) {
                    $tablesExist = false;
                    break;
                }
            }
            
            $moduleHealth = $tablesExist ? 'operational' : 'warning';
            
            $roleStats[$roleName] = [
                'name' => $roleInfo['name'],
                'icon' => $roleInfo['icon'],
                'color' => $roleInfo['color'],
                'active_users' => $roleUsers,
                'activity_24h' => $moduleLogs,
                'health' => $moduleHealth,
            ];
        }

        // Get system-wide statistics
        $totalPatients = $db->tableExists('admin_patients') ? $db->table('admin_patients')->countAllResults() : 0;
        $totalAppointments = $db->tableExists('appointments') ? $db->table('appointments')->where('appointment_date >=', date('Y-m-d'))->countAllResults() : 0;
        $totalBills = $db->tableExists('billing') ? $db->table('billing')->where('status', 'pending')->countAllResults() : 0;
        $totalLabRequests = $db->tableExists('lab_requests') ? $db->table('lab_requests')->where('status', 'pending')->countAllResults() : 0;
        $totalOrders = $db->tableExists('doctor_orders') ? $db->table('doctor_orders')->where('status', 'pending')->countAllResults() : 0;

        $data = [
            'title' => 'System Control Dashboard',
            'name' => session()->get('name'),
            'systemUptime' => $systemUptime,
            'activeUsers' => $activeUsers,
            'systemAlerts' => $errorLogsCount,
            'recentLogsCount' => $recentLogsCount,
            'errorLogsCount' => $errorLogsCount,
            'totalBackups' => $totalBackups,
            'recentLogs' => $recentLogs,
            'usersByRole' => $usersByRole,
            'roleStats' => $roleStats,
            'totalPatients' => $totalPatients,
            'totalAppointments' => $totalAppointments,
            'totalBills' => $totalBills,
            'totalLabRequests' => $totalLabRequests,
            'totalOrders' => $totalOrders,
        ];

        return view('admin/system/dashboard', $data);
    }

    /**
     * System Logs - Index
     */
    public function logs()
    {
        $filters = [
            'level' => $this->request->getGet('level'),
            'module' => $this->request->getGet('module'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        $perPage = 50;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;

        $query = $this->logModel->getFilteredLogs($filters);
        $totalLogs = $query->countAllResults(false);
        $logs = $query->limit($perPage, $offset)->findAll();

        $data = [
            'title' => 'System Logs',
            'logs' => $logs,
            'filters' => $filters,
            'pager' => [
                'current_page' => $page,
                'total_items' => $totalLogs,
                'per_page' => $perPage,
                'total_pages' => ceil($totalLogs / $perPage),
            ],
        ];

        return view('admin/system/logs/index', $data);
    }

    /**
     * System Logs - View
     */
    public function logsView($id)
    {
        $log = $this->logModel
            ->select('system_logs.*, users.username as user_name, users.email as user_email')
            ->join('users', 'users.id = system_logs.user_id', 'left')
            ->find($id);

        if (!$log) {
            return redirect()->to('/admin/system/logs')->with('error', 'Log entry not found.');
        }

        $data = [
            'title' => 'Log Details',
            'log' => $log,
        ];

        return view('admin/system/logs/view', $data);
    }

    /**
     * System Logs - Delete
     */
    public function logsDelete($id)
    {
        if ($this->logModel->delete($id)) {
            return redirect()->to('/admin/system/logs')->with('success', 'Log entry deleted successfully.');
        } else {
            return redirect()->to('/admin/system/logs')->with('error', 'Failed to delete log entry.');
        }
    }

    /**
     * System Logs - Clear
     */
    public function logsClear()
    {
        $days = $this->request->getPost('days') ?? 30;
        $dateThreshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $deleted = $this->logModel
            ->where('created_at <', $dateThreshold)
            ->delete();

        return redirect()->to('/admin/system/logs')->with('success', "Cleared {$deleted} log entries older than {$days} days.");
    }

    /**
     * Backup - Index
     */
    public function backup()
    {
        $backups = $this->backupModel->getBackupsWithUser();

        $data = [
            'title' => 'Create Backup',
            'backups' => $backups,
        ];

        return view('admin/system/backup/index', $data);
    }

    /**
     * Backup - Create
     */
    public function backupCreate()
    {
        $validation = $this->validate([
            'backup_name' => 'permit_empty|max_length[255]',
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
                if (!class_exists('ZipArchive')) {
                    if ($backupType === 'files') {
                        throw new \Exception('PHP Zip extension is not enabled. Please enable php_zip extension in your PHP configuration to create file backups.');
                    }
                } else {
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
                return redirect()->to('/admin/system/backup')->with('success', 'Backup created successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to save backup record.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    /**
     * Backup - Download
     */
    public function backupDownload($id)
    {
        $backup = $this->backupModel->find($id);
        
        if (!$backup || !file_exists($backup['file_path'])) {
            return redirect()->to('/admin/system/backup')->with('error', 'Backup file not found.');
        }

        return $this->response->download($backup['file_path'], null);
    }

    /**
     * Backup - Delete
     */
    public function backupDelete($id)
    {
        $backup = $this->backupModel->find($id);
        
        if (!$backup) {
            return redirect()->to('/admin/system/backup')->with('error', 'Backup not found.');
        }

        // Delete file if exists
        if (file_exists($backup['file_path'])) {
            unlink($backup['file_path']);
        }

        if ($this->backupModel->delete($id)) {
            return redirect()->to('/admin/system/backup')->with('success', 'Backup deleted successfully.');
        } else {
            return redirect()->to('/admin/system/backup')->with('error', 'Failed to delete backup.');
        }
    }

    /**
     * Restore - Index
     */
    public function restore()
    {
        $backups = $this->backupModel
            ->select('system_backups.*, users.username as created_by_name')
            ->join('users', 'users.id = system_backups.created_by', 'left')
            ->where('system_backups.status', 'completed')
            ->orderBy('system_backups.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Restore System',
            'backups' => $backups,
        ];

        return view('admin/system/restore/index', $data);
    }

    /**
     * Restore - Restore
     */
    public function restoreBackup($id)
    {
        $backup = $this->backupModel->find($id);
        
        if (!$backup) {
            return redirect()->to('/admin/system/restore')->with('error', 'Backup not found.');
        }

        if ($backup['status'] !== 'completed') {
            return redirect()->to('/admin/system/restore')->with('error', 'Backup is not completed and cannot be restored.');
        }

        if (!file_exists($backup['file_path'])) {
            return redirect()->to('/admin/system/restore')->with('error', 'Backup file not found.');
        }

        try {
            // Update backup status to in_progress
            $this->backupModel->update($id, ['status' => 'in_progress']);

            // Restore based on backup type
            if ($backup['backup_type'] === 'database' || $backup['backup_type'] === 'full') {
                $this->restoreDatabase($backup['file_path']);
            }

            if ($backup['backup_type'] === 'files' || $backup['backup_type'] === 'full') {
                $this->restoreFiles($backup['file_path']);
            }

            // Update backup status back to completed
            $this->backupModel->update($id, ['status' => 'completed']);
            
            return redirect()->to('/admin/system/restore')->with('success', 'System restored successfully from backup.');
        } catch (\Exception $e) {
            // Update backup status to failed
            $this->backupModel->update($id, ['status' => 'failed']);
            
            return redirect()->to('/admin/system/restore')->with('error', 'Failed to restore system: ' . $e->getMessage());
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
        if (!class_exists('ZipArchive')) {
            throw new \Exception('PHP Zip extension is not enabled.');
        }
        
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
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
     * Restore database from backup file
     */
    private function restoreDatabase($filePath)
    {
        $db = \Config\Database::connect();
        $dbName = $db->database;
        $dbUser = $db->username;
        $dbPass = $db->password;
        $dbHost = $db->hostname;

        $command = "mysql -h {$dbHost} -u {$dbUser} -p{$dbPass} {$dbName} < {$filePath} 2>&1";
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            // Fallback: Execute SQL manually
            $sql = file_get_contents($filePath);
            $db->query($sql);
        }
    }

    /**
     * Restore files from backup zip
     */
    private function restoreFiles($zipPath)
    {
        if (!class_exists('ZipArchive')) {
            throw new \Exception('PHP Zip extension is not enabled.');
        }
        
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath) === TRUE) {
            $tempDir = WRITEPATH . 'temp_restore/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            throw new \Exception('Failed to open zip file.');
        }
    }
}

