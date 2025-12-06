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
        // Join with doctors table to get doctor name from patient's assigned doctor
        $db = \Config\Database::connect();
        $recentActivity = $db->table('schedules')
            ->select('schedules.*, admin_patients.firstname as patient_first_name, admin_patients.lastname as patient_last_name, 
                     COALESCE(doctors.doctor_name, schedules.doctor, "N/A") as doctor')
            ->join('admin_patients', 'admin_patients.id = schedules.patient_id', 'left')
            ->join('doctors', 'doctors.user_id = admin_patients.doctor_id', 'left')
            ->where('schedules.deleted_at IS NULL', null, false)
            ->orderBy('schedules.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get pending lab requests from nurses
        $labRequestModel = new \App\Models\LabRequestModel();
        $pendingLabRequests = $labRequestModel
            ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as nurse_name')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users', 'users.id = lab_requests.nurse_id', 'left')
            ->where('lab_requests.status', 'pending')
            ->where('lab_requests.requested_by', 'nurse')
            ->orderBy('lab_requests.created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        $pendingLabRequestsCount = $labRequestModel
            ->where('status', 'pending')
            ->where('requested_by', 'nurse')
            ->countAllResults();

        $data = [
            'title' => 'Admin Dashboard',
            'totalDoctors' => $totalDoctors,
            'totalPatients' => $totalPatients,
            'todaysAppointments' => $todaysAppointments,
            'pendingBills' => $pendingBills,
            'pendingLabRequestsCount' => $pendingLabRequestsCount,
            'pendingLabRequests' => $pendingLabRequests,
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

