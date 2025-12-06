<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\ScheduleModel;
use App\Models\LabRequestModel;
use App\Models\BillingModel;

class DashboardStats extends BaseController
{
    public function stats()
    {
        $patientModel = new AdminPatientModel();
        $scheduleModel = new ScheduleModel();
        $labRequestModel = new LabRequestModel();
        $billingModel = new BillingModel();
        
        // Get dashboard statistics
        $totalDoctors = $this->getTotalDoctors();
        $totalPatients = $patientModel->countAllResults();
        
        $today = date('Y-m-d');
        $todaysAppointments = $scheduleModel
            ->where('date', $today)
            ->countAllResults();
        
        $pendingBills = $billingModel
            ->where('status', 'pending')
            ->countAllResults();
        
        // Get pending lab requests from nurses
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

        $data = [
            'totalDoctors' => $totalDoctors,
            'totalPatients' => $totalPatients,
            'todaysAppointments' => $todaysAppointments,
            'pendingBills' => $pendingBills,
            'pendingLabRequestsCount' => $pendingLabRequestsCount,
            'pendingLabRequests' => $pendingLabRequests,
            'recentActivity' => $recentActivity,
            'last_updated' => date('Y-m-d H:i:s')
        ];

        return $this->response->setJSON($data);
    }

    private function getTotalDoctors()
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('users')) {
            return $db->table('users')->where('role_id', 2)->countAllResults();
        }
        return 0;
    }
}

