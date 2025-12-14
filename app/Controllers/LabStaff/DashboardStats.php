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
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['labstaff', 'lab_staff', 'admin'])) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        try {
            $labRequestModel = new LabRequestModel();
            $labResultModel = new LabResultModel();

            // Pending tests (show all, including pending payment)
            $pendingTests = $labRequestModel
                ->where('status', 'pending')
                ->where('status !=', 'cancelled')
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

            // Get completed today list
            $completedTodayList = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->where('lab_requests.status', 'completed')
                ->where('DATE(lab_requests.updated_at)', $today)
                ->orderBy('lab_requests.updated_at', 'DESC')
                ->limit(10)
                ->findAll();

            // Get pending specimens count (show all)
            $pendingSpecimens = $labRequestModel
                ->whereIn('status', ['specimen_collected', 'in_progress'])
                ->countAllResults();

            // Get urgent/stat tests count (show all)
            $urgentTests = $db->table('lab_requests')
                ->groupStart()
                    ->groupStart()
                        ->where('lab_requests.status', 'pending')
                        ->where('lab_requests.nurse_id', null) // Only without_specimen pending tests
                    ->groupEnd()
                ->orGroupStart()
                    ->whereIn('lab_requests.status', ['in_progress', 'specimen_collected'])
                    ->groupEnd()
                ->groupEnd()
                ->whereIn('lab_requests.priority', ['urgent', 'stat'])
                ->where('lab_requests.status !=', 'cancelled')
                ->countAllResults();

            // Get pending tests list with more details (show all, including pending payment)
            $pendingTestsList = $db->table('lab_requests')
                ->select('lab_requests.*, 
                    admin_patients.firstname, 
                    admin_patients.lastname,
                    doctor.username as doctor_name,
                    nurse.username as nurse_name,
                    charges.charge_number,
                    charges.status as charge_status')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
                ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
                ->join('charges', 'charges.id = lab_requests.charge_id', 'left') // LEFT JOIN - show even without charge
                ->where('lab_requests.status !=', 'cancelled')
                ->groupStart()
                    // Include requests that are ready for testing:
                    // 1. Status = 'pending' AND nurse_id IS NULL (without_specimen tests - go directly to lab)
                    ->groupStart()
                        ->where('lab_requests.status', 'pending')
                        ->where('lab_requests.nurse_id', null) // Only show pending requests without nurse (without_specimen)
                    ->groupEnd()
                ->orGroupStart()
                    // 2. Status = 'specimen_collected' or 'in_progress' (with_specimen tests that have been collected)
                    ->whereIn('lab_requests.status', ['specimen_collected', 'in_progress'])
                    ->groupEnd()
                ->groupEnd()
                ->orderBy('lab_requests.priority', 'ASC') // Urgent/Stat first
                ->orderBy('lab_requests.requested_date', 'ASC')
                ->orderBy('lab_requests.created_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $data = [
                'pending_tests' => $pendingTests,
                'pending_specimens' => $pendingSpecimens,
                'completed_today' => $completedToday,
                'monthly_tests' => $monthlyTests,
                'urgent_tests' => $urgentTests,
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

