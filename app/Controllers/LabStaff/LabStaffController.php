<?php

namespace App\Controllers\LabStaff;

use App\Controllers\BaseController;
use App\Models\LabRequestModel;
use App\Models\LabResultModel;
use App\Models\AdminPatientModel;

class LabStaffController extends BaseController
{
    protected $labRequestModel;
    protected $labResultModel;
    protected $patientModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->labRequestModel = new LabRequestModel();
        $this->labResultModel = new LabResultModel();
        $this->patientModel = new AdminPatientModel();
    }

    /**
     * Lab Staff Dashboard - Summary statistics
     */
    public function dashboard()
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        // Get pending test requests
        $pendingTests = $this->labRequestModel
            ->where('status', 'pending')
            ->countAllResults();

        // Get completed tests today
        $completedToday = $this->labRequestModel
            ->where('status', 'completed')
            ->where('DATE(updated_at)', $today)
            ->countAllResults();

        // Get total tests this month
        $monthlyTests = $this->labRequestModel
            ->where('status', 'completed')
            ->where('DATE(updated_at) >=', $monthStart)
            ->countAllResults();

        // Get pending specimens (lab requests with status pending or in_progress)
        $pendingSpecimens = $this->labRequestModel
            ->whereIn('status', ['pending', 'in_progress'])
            ->countAllResults();

        $data = [
            'title' => 'Lab Staff Dashboard',
            'pendingTests' => $pendingTests,
            'completedToday' => $completedToday,
            'monthlyTests' => $monthlyTests,
            'pendingSpecimens' => $pendingSpecimens,
        ];

        return view('labstaff/dashboard', $data);
    }

    /**
     * Test Requests - All test requests from doctors/nurses
     */
    public function testRequests()
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $db = \Config\Database::connect();

        // Get all test requests with patient and doctor/nurse info
        $testRequests = $db->table('lab_requests')
            ->select('lab_requests.*, 
                admin_patients.firstname as patient_firstname, 
                admin_patients.lastname as patient_lastname,
                admin_patients.contact as patient_contact,
                doctor.username as doctor_name,
                nurse.username as nurse_name')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->orderBy('lab_requests.priority', 'ASC') // Urgent/Stat first
            ->orderBy('lab_requests.requested_date', 'ASC')
            ->orderBy('lab_requests.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Test Requests',
            'testRequests' => $testRequests,
        ];

        return view('labstaff/test_requests', $data);
    }

    /**
     * Pending Specimens - Specimens waiting to be processed
     */
    public function pendingSpecimens()
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $db = \Config\Database::connect();

        // Get pending and in_progress specimens
        $pendingSpecimens = $db->table('lab_requests')
            ->select('lab_requests.*, 
                admin_patients.firstname as patient_firstname, 
                admin_patients.lastname as patient_lastname,
                admin_patients.contact as patient_contact,
                doctor.username as doctor_name,
                nurse.username as nurse_name')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->whereIn('lab_requests.status', ['pending', 'in_progress'])
            ->orderBy('lab_requests.priority', 'ASC') // Urgent/Stat first
            ->orderBy('lab_requests.requested_date', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Pending Specimens',
            'pendingSpecimens' => $pendingSpecimens,
        ];

        return view('labstaff/pending_specimens', $data);
    }

    /**
     * Completed Tests - All completed test results
     */
    public function completedTests()
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $db = \Config\Database::connect();

        // Get completed tests with results
        $completedTests = $db->table('lab_requests')
            ->select('lab_requests.*, 
                admin_patients.firstname as patient_firstname, 
                admin_patients.lastname as patient_lastname,
                admin_patients.contact as patient_contact,
                doctor.username as doctor_name,
                nurse.username as nurse_name,
                lab_results.result as test_result,
                lab_results.result_file,
                lab_results.interpretation,
                lab_results.completed_at,
                completed_by.username as completed_by_name')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->join('lab_results', 'lab_results.lab_request_id = lab_requests.id', 'left')
            ->join('users as completed_by', 'completed_by.id = lab_results.completed_by', 'left')
            ->where('lab_requests.status', 'completed')
            ->orderBy('lab_requests.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Completed Tests',
            'completedTests' => $completedTests,
        ];

        return view('labstaff/completed_tests', $data);
    }

    /**
     * Mark Specimen as Collected - Update status to in_progress
     */
    public function markCollected($requestId)
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized. Please log in as lab staff.'
            ])->setStatusCode(401);
        }

        $request = $this->labRequestModel->find($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test request not found'
            ])->setStatusCode(404);
        }

        // Update status to in_progress
        $this->labRequestModel->update($requestId, [
            'status' => 'in_progress',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Log status change
        $db = \Config\Database::connect();
        if ($db->tableExists('lab_status_history')) {
            $db->table('lab_status_history')->insert([
                'lab_request_id' => $requestId,
                'status' => 'in_progress',
                'changed_by' => session()->get('user_id'),
                'notes' => 'Specimen collected by lab staff',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Specimen marked as collected'
        ]);
    }

    /**
     * Mark Test as Completed - Update status and create result record
     */
    public function markCompleted($requestId)
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized. Please log in as lab staff.'
            ])->setStatusCode(401);
        }

        $request = $this->labRequestModel->find($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test request not found'
            ])->setStatusCode(404);
        }

        $result = $this->request->getPost('result');
        $interpretation = $this->request->getPost('interpretation');
        $resultFile = $this->request->getPost('result_file');

        // Update lab request status
        $this->labRequestModel->update($requestId, [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Create or update lab result
        $existingResult = $this->labResultModel
            ->where('lab_request_id', $requestId)
            ->first();

        $resultData = [
            'lab_request_id' => $requestId,
            'result' => $result,
            'interpretation' => $interpretation,
            'result_file' => $resultFile,
            'completed_by' => session()->get('user_id'),
            'completed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existingResult) {
            $this->labResultModel->update($existingResult['id'], $resultData);
        } else {
            $resultData['created_at'] = date('Y-m-d H:i:s');
            $this->labResultModel->insert($resultData);
        }

        // Log status change
        $db = \Config\Database::connect();
        if ($db->tableExists('lab_status_history')) {
            $db->table('lab_status_history')->insert([
                'lab_request_id' => $requestId,
                'status' => 'completed',
                'changed_by' => session()->get('user_id'),
                'notes' => 'Test completed by lab staff',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Test marked as completed'
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth')->with('success', 'You have been logged out successfully.');
    }
}

