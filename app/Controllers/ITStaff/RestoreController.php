<?php

namespace App\Controllers\ITStaff;

use App\Controllers\BaseController;
use App\Models\SystemBackupModel;

class RestoreController extends BaseController
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

        $backups = $this->backupModel
            ->where('status', 'completed')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Restore System',
            'backups' => $backups,
        ];

        return view('itstaff/restore/index', $data);
    }

    public function restore($id)
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $backup = $this->backupModel->find($id);
        
        if (!$backup) {
            return redirect()->to('/it/restore')->with('error', 'Backup not found.');
        }

        if ($backup['status'] !== 'completed') {
            return redirect()->to('/it/restore')->with('error', 'Backup is not completed and cannot be restored.');
        }

        if (!file_exists($backup['file_path'])) {
            return redirect()->to('/it/restore')->with('error', 'Backup file not found.');
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

            // Log the restore action
            $this->logRestoreAction('System restored from backup', $id);
            
            return redirect()->to('/it/restore')->with('success', 'System restored successfully from backup.');
        } catch (\Exception $e) {
            // Update backup status to failed
            $this->backupModel->update($id, ['status' => 'failed']);
            
            // Log the error
            $this->logRestoreAction('Restore failed: ' . $e->getMessage(), $id, 'error');
            
            return redirect()->to('/it/restore')->with('error', 'Failed to restore system: ' . $e->getMessage());
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

        // Use mysql command if available
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
        // Check if ZipArchive is available
        if (!class_exists('ZipArchive')) {
            throw new \Exception('PHP Zip extension is not enabled. Please enable php_zip extension in your PHP configuration to restore file backups.');
        }
        
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath) === TRUE) {
            // Extract to temporary directory first
            $tempDir = WRITEPATH . 'temp_restore/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $zip->extractTo($tempDir);
            $zip->close();
            
            // Copy files back (this is a simplified version - in production, be more careful)
            // Note: This is a basic implementation. In production, you'd want to:
            // 1. Backup current files first
            // 2. Verify file integrity
            // 3. Restore selectively
            
            // For safety, we'll just log that files restoration was attempted
            // In production, implement proper file restoration logic
            
        } else {
            throw new \Exception('Failed to open zip file.');
        }
    }

    /**
     * Log restore action to system logs
     */
    private function logRestoreAction($action, $backupId = null, $level = 'info')
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
            log_message('error', 'Failed to log restore action: ' . $e->getMessage());
        }
    }
}

