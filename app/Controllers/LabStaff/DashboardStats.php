<?php

namespace App\Controllers\LabStaff;

use App\Controllers\BaseController;
use App\Models\LabRequestModel;
use App\Models\LabResultModel;

class DashboardStats extends BaseController
{
    public function stats()
    {
        // Check if user is logged in and is lab staff
        if (!session()->get('logged_in') || session()->get('role') !== 'lab_staff') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        try {
            $labRequestModel = new LabRequestModel();
            $labResultModel = new LabResultModel();

            // Pending tests
            $pendingTests = $labRequestModel
                ->where('status', 'pending')
                ->orWhere('status', 'in_progress')
                ->countAllResults();

            // Completed today
            $completedToday = $labRequestModel
                ->where('status', 'completed')
                ->where('DATE(updated_at)', $today)
                ->countAllResults();

            // Monthly tests
            $monthlyTests = $labRequestModel
                ->where('created_at >=', $monthStart)
                ->where('created_at <=', $monthEnd . ' 23:59:59')
                ->countAllResults();

            // Get pending tests list
            $pendingTestsList = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->where('lab_requests.status', 'pending')
                ->orWhere('lab_requests.status', 'in_progress')
                ->orderBy('lab_requests.created_at', 'ASC')
                ->limit(10)
                ->findAll();

            // Get completed today list
            $completedTodayList = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->where('lab_requests.status', 'completed')
                ->where('DATE(lab_requests.updated_at)', $today)
                ->orderBy('lab_requests.updated_at', 'DESC')
                ->limit(10)
                ->findAll();

            $data = [
                'pending_tests' => $pendingTests,
                'completed_today' => $completedToday,
                'monthly_tests' => $monthlyTests,
                'pending_tests_list' => $pendingTestsList,
                'completed_today_list' => $completedTodayList,
                'last_updated' => date('Y-m-d H:i:s')
            ];

            return $this->response->setJSON($data);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching Lab Staff Dashboard Stats: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to fetch stats'])->setStatusCode(500);
        }
    }
}

