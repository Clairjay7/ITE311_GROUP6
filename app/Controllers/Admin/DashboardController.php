<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        // Load models for dashboard stats
        $patientModel = new \App\Models\AdminPatientModel();
        
        // Get dashboard statistics
        $totalDoctors = $this->getTotalDoctors();
        $totalPatients = $patientModel->countAllResults();
        
        // Get today's appointments from schedules
        $scheduleModel = new \App\Models\ScheduleModel();
        $todaysAppointments = $scheduleModel
            ->where('date', date('Y-m-d'))
            ->countAllResults();
        $pendingBills = $this->getPendingBills();
        
        // Get recent activity from schedules
        $recentActivity = $scheduleModel
            ->select('schedules.*, admin_patients.firstname as patient_first_name, admin_patients.lastname as patient_last_name')
            ->join('admin_patients', 'admin_patients.id = schedules.patient_id', 'left')
            ->orderBy('schedules.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Admin Dashboard',
            'totalDoctors' => $totalDoctors,
            'totalPatients' => $totalPatients,
            'todaysAppointments' => $todaysAppointments,
            'pendingBills' => $pendingBills,
            'recentActivity' => $recentActivity,
        ];

        return view('admin/dashboard', $data);
    }

    private function getTotalDoctors()
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('doctors')) {
            return $db->table('doctors')->countAllResults();
        }
        return 0;
    }

    private function getPendingBills()
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('billing')) {
            return $db->table('billing')
                ->where('status', 'pending')
                ->countAllResults();
        }
        return 0;
    }
}

