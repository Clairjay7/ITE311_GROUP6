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

        // Get all patients
        $patients = $patientModel
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

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

        $validation = $this->validate([
            'status' => 'required|in_list[pending,in_progress,completed,cancelled]',
        ]);

        if (!$validation) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        if ($labRequestModel->update($id, ['status' => $newStatus])) {
            // Log status change
            $historyModel->insert([
                'lab_request_id' => $id,
                'status' => $newStatus,
                'changed_by' => $nurseId,
                'notes' => $notes,
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

        $validation = $this->validate([
            'result' => 'permit_empty|max_length[2000]',
            'result_file' => 'permit_empty|uploaded[result_file]|max_size[result_file,10240]|ext_in[result_file,pdf,jpg,jpeg,png]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $file = $this->request->getFile('result_file');
        $resultFile = null;
        $resultFileType = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/lab_results/', $newName);
            $resultFile = $newName;
            $resultFileType = $file->getClientMimeType();
        }

        // Check if result already exists
        $existingResult = $labResultModel->where('lab_request_id', $requestId)->first();
        
        $resultData = [
            'lab_request_id' => $requestId,
            'result' => $this->request->getPost('result'),
            'interpretation' => $this->request->getPost('interpretation'),
            'completed_by' => $nurseId,
            'completed_at' => date('Y-m-d H:i:s'),
        ];

        if ($resultFile) {
            $resultData['result_file'] = $resultFile;
            $resultData['result_file_type'] = $resultFileType;
        }

        $isNewResult = !$existingResult;
        
        if ($existingResult) {
            $labResultModel->update($existingResult['id'], $resultData);
        } else {
            $labResultModel->insert($resultData);
        }

        // Update request status to completed
        $wasCompleted = $request['status'] === 'completed';
        $labRequestModel->update($requestId, ['status' => 'completed']);

        // Log status change
        $historyModel = new LabStatusHistoryModel();
        $historyModel->insert([
            'lab_request_id' => $requestId,
            'status' => 'completed',
            'changed_by' => $nurseId,
            'notes' => 'Lab result uploaded',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Create notification for the nurse who requested it (when result is first created or status changes to completed)
        if ($request['nurse_id'] && ($isNewResult || !$wasCompleted)) {
            $patientModel = new AdminPatientModel();
            $patient = $patientModel->find($request['patient_id']);
            $notificationModel = new NurseNotificationModel();
            $notificationModel->insert([
                'nurse_id' => $request['nurse_id'],
                'type' => 'lab_result_ready',
                'title' => 'Lab Result Ready',
                'message' => 'Lab result for ' . ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . ' (' . $request['test_name'] . ') is now available.',
                'related_id' => $requestId,
                'related_type' => 'lab_result',
                'is_read' => 0,
            ]);
        }

        return redirect()->to('/nurse/laboratory/testresult')->with('success', 'Lab result uploaded successfully.');
    }
}

