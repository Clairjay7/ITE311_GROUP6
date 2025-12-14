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

        // Get all active lab tests grouped by category
        $labTests = [];
        if ($db->tableExists('lab_tests')) {
            $labTestModel = new \App\Models\LabTestModel();
            $labTests = $labTestModel->getActiveTestsGroupedByCategory();
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

    /**
     * Mark Specimen as Collected - For "with specimen" tests after payment is paid
     */
    public function markSpecimenCollected($id)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized. Please log in as a nurse.'
            ])->setStatusCode(401);
        }

        $labRequestModel = new LabRequestModel();
        $historyModel = new LabStatusHistoryModel();
        $nurseId = session()->get('user_id');
        $db = \Config\Database::connect();

        $request = $labRequestModel->find($id);
        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lab request not found.'
            ])->setStatusCode(404);
        }

        // Verify this request is assigned to this nurse
        if (empty($request['nurse_id']) || $request['nurse_id'] != $nurseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are not assigned to collect specimen for this lab request.'
            ])->setStatusCode(403);
        }

        // Check payment status - Payment must be approved/paid OR patient must be admitted
        $paymentStatus = $request['payment_status'] ?? 'unpaid';
        $isAdmitted = $this->isPatientAdmitted($request['patient_id']);
        
        if (!in_array($paymentStatus, ['approved', 'paid']) && !$isAdmitted) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment must be approved by accountant before collecting specimen, unless patient is admitted. Current status: ' . ucfirst($paymentStatus)
            ])->setStatusCode(400);
        }

        // Check if status is pending (ready for collection)
        if ($request['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Specimen can only be collected for pending requests. Current status: ' . ucfirst(str_replace('_', ' ', $request['status']))
            ])->setStatusCode(400);
        }

        // Update status to 'specimen_collected' (ready for lab)
        if ($labRequestModel->update($id, [
            'status' => 'specimen_collected',
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            // Log status change
            $historyModel->insert([
                'lab_request_id' => $id,
                'status' => 'specimen_collected',
                'changed_by' => $nurseId,
                'notes' => 'Specimen collected by nurse - ready for laboratory testing',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Notify lab staff that specimen is ready
            if ($db->tableExists('accountant_notifications')) {
                $patientModel = new AdminPatientModel();
                $patient = $patientModel->find($request['patient_id']);
                $patientName = ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'Patient');
                
                // Use accountant_notifications as a general notification system for lab staff
                $db->table('accountant_notifications')->insert([
                    'type' => 'lab_specimen_ready',
                    'title' => 'Lab Specimen Ready for Testing',
                    'message' => 'Specimen collected for ' . $patientName . ' - Test: ' . ($request['test_name'] ?? 'Lab Test') . '. Ready for laboratory testing.',
                    'related_id' => $id,
                    'related_type' => 'lab_request',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Specimen marked as collected. Request is now ready for laboratory testing.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark specimen as collected.'
            ])->setStatusCode(500);
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
    
    /**
     * Check if patient is admitted
     * @param int $patientId Admin patient ID
     * @return bool
     */
    private function isPatientAdmitted($patientId)
    {
        if (empty($patientId)) {
            return false;
        }
        
        $db = \Config\Database::connect();
        
        // Check if patient has active admission
        if ($db->tableExists('admissions')) {
            $activeAdmission = $db->table('admissions')
                ->where('patient_id', $patientId)
                ->where('status', 'admitted')
                ->where('discharge_status', 'admitted')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            
            if ($activeAdmission) {
                return true;
            }
        }
        
        // Also check admin_patients table for visit_type = 'Admission' or 'ADMISSION'
        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($patientId);
        if ($patient) {
            $visitType = strtoupper(trim($patient['visit_type'] ?? ''));
            if ($visitType === 'ADMISSION') {
                return true;
            }
        }
        
        // Check patients table (HMS patients) for type = 'In-Patient' and visit_type = 'Admission'
        if ($db->tableExists('patients')) {
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->where('LOWER(type)', 'in-patient')
                ->where('LOWER(visit_type)', 'admission')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            if ($hmsPatient) {
                return true;
            }
        }
        
        return false;
    }
}

