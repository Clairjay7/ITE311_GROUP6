<?php

namespace App\Controllers\ITStaff;

use App\Controllers\BaseController;
use App\Models\SystemLogModel;
use App\Models\SystemBackupModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $db = \Config\Database::connect();
        $logModel = new SystemLogModel();
        $backupModel = new SystemBackupModel();
        $userModel = new UserModel();

        // Get statistics
        $activeUsers = $userModel->where('status', 'active')->countAllResults();
        
        // Get recent system logs count
        $recentLogsCount = $logModel
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();
        
        $errorLogsCount = $logModel
            ->whereIn('level', ['error', 'critical', 'alert', 'emergency'])
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->countAllResults();
        
        // Get backup statistics
        $totalBackups = $backupModel->countAllResults();
        $recentBackups = $backupModel
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->countAllResults();
        
        // Get recent logs
        $recentLogs = $logModel
            ->select('system_logs.*, users.username as user_name')
            ->join('users', 'users.id = system_logs.user_id', 'left')
            ->orderBy('system_logs.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get recent backups
        $recentBackupsList = $backupModel
            ->select('system_backups.*, users.username as created_by_name')
            ->join('users', 'users.id = system_backups.created_by', 'left')
            ->orderBy('system_backups.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Calculate system uptime (simplified - in production, track actual uptime)
        $systemUptime = '99.8%';

        // Get users by role for monitoring
        $usersByRole = $db->table('users u')
            ->select('r.name as role_name, COUNT(u.id) as user_count, SUM(CASE WHEN u.status = "active" THEN 1 ELSE 0 END) as active_count')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('r.name !=', 'patient')
            ->groupBy('r.id', 'r.name')
            ->orderBy('r.name', 'ASC')
            ->get()->getResultArray();

        // Get module activity from system logs (last 24 hours)
        $moduleActivity = $logModel
            ->select('module, COUNT(*) as activity_count, MAX(created_at) as last_activity')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->where('module IS NOT NULL')
            ->groupBy('module')
            ->orderBy('activity_count', 'DESC')
            ->findAll();

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
            
            // Get module-specific data
            $moduleName = str_replace('_', '_', $roleName);
            $moduleLogs = $logModel
                ->where('module', $moduleName)
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                ->countAllResults();
            
            // Check if module tables exist (basic health check)
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
            'title' => 'IT Staff Dashboard',
            'name' => session()->get('name'),
            'systemUptime' => $systemUptime,
            'activeUsers' => $activeUsers,
            'systemAlerts' => $errorLogsCount,
            'pendingTasks' => 0,
            'recentLogsCount' => $recentLogsCount,
            'errorLogsCount' => $errorLogsCount,
            'totalBackups' => $totalBackups,
            'recentBackups' => $recentBackups,
            'recentLogs' => $recentLogs,
            'recentBackupsList' => $recentBackupsList,
            'usersByRole' => $usersByRole,
            'moduleActivity' => $moduleActivity,
            'roleStats' => $roleStats,
            'totalPatients' => $totalPatients,
            'totalAppointments' => $totalAppointments,
            'totalBills' => $totalBills,
            'totalLabRequests' => $totalLabRequests,
            'totalOrders' => $totalOrders,
        ];

        return view('itstaff/dashboard', $data);
    }
}

