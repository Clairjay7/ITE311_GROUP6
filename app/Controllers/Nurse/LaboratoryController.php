<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\LabRequestModel;
use App\Models\LabResultModel;
use App\Models\LabStatusHistoryModel;
use App\Models\AdminPatientModel;
use App\Models\NurseNotificationModel;

class LaboratoryController extends BaseController
{
    public function request()
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $labRequestModel = new LabRequestModel();
        $db = \Config\Database::connect();

        // Get all patients
        $patients = $patientModel
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        // Get all active lab tests
        $labTests = [];
        if ($db->tableExists('lab_tests')) {
            $labTests = $db->table('lab_tests')
                ->where('is_active', 1)
                ->where('deleted_at', null)
                ->orderBy('test_type', 'ASC')
                ->orderBy('test_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get pending lab requests
        $pendingRequests = $labRequestModel
            ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as doctor_name')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users', 'users.id = lab_requests.doctor_id', 'left')
            ->where('lab_requests.status', 'pending')
            ->orderBy('lab_requests.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Create Lab Request',
            'patients' => $patients,
            'labTests' => $labTests,
            'pendingRequests' => $pendingRequests,
            'validation' => \Config\Services::validation()
        ];

        return view('nurse/laboratory/request', $data);
    }

    public function storeRequest()
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $labRequestModel = new LabRequestModel();
        $nurseId = session()->get('user_id');

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'test_type' => 'required|max_length[255]',
            'test_name' => 'required|max_length[255]',
            'priority' => 'required|in_list[routine,urgent,stat]',
            'requested_date' => 'required|valid_date',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Get patient's assigned doctor
        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($this->request->getPost('patient_id'));
        $assignedDoctorId = $patient['doctor_id'] ?? null;

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'nurse_id' => $nurseId,
            'doctor_id' => $assignedDoctorId, // Set to patient's assigned doctor
            'test_type' => $this->request->getPost('test_type'),
            'test_name' => $this->request->getPost('test_name'),
            'requested_by' => 'nurse',
            'priority' => $this->request->getPost('priority'),
            'instructions' => $this->request->getPost('instructions'),
            'status' => 'pending', // Will be changed to 'in_progress' when doctor confirms
            'requested_date' => $this->request->getPost('requested_date'),
        ];

        if ($labRequestModel->insert($data)) {
            // Log status change
            $historyModel = new LabStatusHistoryModel();
            $historyModel->insert([
                'lab_request_id' => $labRequestModel->getInsertID(),
                'status' => 'pending',
                'changed_by' => $nurseId,
                'notes' => 'Lab request created by nurse',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/nurse/laboratory/request')->with('success', 'Lab request created successfully. Waiting for doctor confirmation.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create lab request.');
        }
    }

    public function updateRequestStatus($id)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $labRequestModel = new LabRequestModel();
        $historyModel = new LabStatusHistoryModel();
        $nurseId = session()->get('user_id');

        $request = $labRequestModel->find($id);
        if (!$request) {
            return redirect()->back()->with('error', 'Lab request not found.');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        // Nurses CANNOT mark lab requests as 'completed' - only lab staff can do this
        if ($newStatus === 'completed') {
            return redirect()->back()->with('error', 'You do not have permission to mark lab requests as completed. Only laboratory staff can complete lab tests.');
        }

        // Nurses can only update to pending, in_progress, or cancelled (not completed)
        $validation = $this->validate([
            'status' => 'required|in_list[pending,in_progress,cancelled]',
        ]);

        if (!$validation) {
            return redirect()->back()->with('error', 'Invalid status. Nurses cannot mark lab requests as completed.');
        }

        if ($labRequestModel->update($id, ['status' => $newStatus])) {
            // Log status change
            $historyModel->insert([
                'lab_request_id' => $id,
                'status' => $newStatus,
                'changed_by' => $nurseId,
                'notes' => $notes . ' (Updated by nurse - cannot mark as completed)',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()->with('success', 'Lab request status updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update lab request status.');
        }
    }

    public function testresult()
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $labRequestModel = new LabRequestModel();
        $labResultModel = new LabResultModel();

        // Get all lab requests with results
        $labRequests = $labRequestModel
            ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as doctor_name, lab_results.result, lab_results.result_file, lab_results.completed_at')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users', 'users.id = lab_requests.doctor_id', 'left')
            ->join('lab_results', 'lab_results.lab_request_id = lab_requests.id', 'left')
            ->orderBy('lab_requests.created_at', 'DESC')
            ->findAll();

        // Get completed requests
        $completedRequests = $labRequestModel
            ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as doctor_name, lab_results.result, lab_results.result_file, lab_results.completed_at')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users', 'users.id = lab_requests.doctor_id', 'left')
            ->join('lab_results', 'lab_results.lab_request_id = lab_requests.id', 'left')
            ->where('lab_requests.status', 'completed')
            ->orderBy('lab_requests.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Results Inquiry',
            'labRequests' => $labRequests,
            'completedRequests' => $completedRequests
        ];

        return view('nurse/laboratory/testresult', $data);
    }

    public function uploadResult($requestId)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $labRequestModel = new LabRequestModel();
        $labResultModel = new LabResultModel();
        $nurseId = session()->get('user_id');

        $request = $labRequestModel->find($requestId);
        if (!$request) {
            return redirect()->back()->with('error', 'Lab request not found.');
        }

        // Nurses CANNOT upload results or mark lab requests as completed
        // Only laboratory staff can complete lab tests and upload results
        return redirect()->back()->with('error', 'You do not have permission to upload lab results. Only laboratory staff can complete lab tests and upload results. Please contact the laboratory department.');
    }
}

