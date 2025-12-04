<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\LabRequestModel;
use App\Models\LabStatusHistoryModel;
use App\Models\AdminPatientModel;
use App\Models\NurseNotificationModel;

class LabRequestController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $labRequestModel = new LabRequestModel();
        $patientModel = new AdminPatientModel();

        // Get assigned patient IDs
        $assignedPatientIds = $patientModel
            ->select('id')
            ->where('doctor_id', $doctorId)
            ->findAll();
        $patientIds = array_column($assignedPatientIds, 'id');

        // Get pending lab requests from nurses AND doctors for assigned patients
        $db = \Config\Database::connect();
        
        // Also get patient IDs from patients table (receptionist-registered)
        $hmsPatientIds = [];
        if ($db->tableExists('patients')) {
            $hmsPatientsRaw = $db->table('patients')
                ->select('patients.patient_id')
                ->where('patients.doctor_id', $doctorId)
                ->where('patients.doctor_id IS NOT NULL')
                ->where('patients.doctor_id !=', 0)
                ->get()
                ->getResultArray();
            $hmsPatientIds = array_column($hmsPatientsRaw, 'patient_id');
        }
        
        // Find corresponding admin_patients IDs for hms patients
        $allPatientIds = $patientIds;
        if (!empty($hmsPatientIds)) {
            foreach ($hmsPatientIds as $hmsPatientId) {
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $hmsPatientId)
                    ->get()
                    ->getRowArray();
                
                if ($hmsPatient) {
                    $nameParts = [];
                    if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                    if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                    if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                        $parts = explode(' ', $hmsPatient['full_name'], 2);
                        $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                    }
                    
                    if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                        $adminPatient = $db->table('admin_patients')
                            ->where('firstname', $nameParts[0])
                            ->where('lastname', $nameParts[1])
                            ->where('doctor_id', $doctorId)
                            ->get()
                            ->getRowArray();
                        
                        if ($adminPatient && !in_array($adminPatient['id'], $allPatientIds)) {
                            $allPatientIds[] = $adminPatient['id'];
                        }
                    }
                }
            }
        }
        
        $pendingRequests = [];
        if (!empty($allPatientIds)) {
            $pendingRequests = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as nurse_name, doctor_users.username as doctor_name')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->join('users', 'users.id = lab_requests.nurse_id', 'left')
                ->join('users as doctor_users', 'doctor_users.id = lab_requests.doctor_id', 'left')
                ->whereIn('lab_requests.patient_id', $allPatientIds)
                ->where('lab_requests.status', 'pending')
                ->whereIn('lab_requests.requested_by', ['nurse', 'doctor'])
                ->orderBy('lab_requests.created_at', 'DESC')
                ->findAll();
        }

        // Get confirmed/in-progress requests
        $confirmedRequests = [];
        if (!empty($allPatientIds)) {
            $confirmedRequests = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as nurse_name, doctor_users.username as doctor_name')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->join('users', 'users.id = lab_requests.nurse_id', 'left')
                ->join('users as doctor_users', 'doctor_users.id = lab_requests.doctor_id', 'left')
                ->whereIn('lab_requests.patient_id', $allPatientIds)
                ->whereIn('lab_requests.status', ['in_progress', 'completed'])
                ->whereIn('lab_requests.requested_by', ['nurse', 'doctor'])
                ->orderBy('lab_requests.updated_at', 'DESC')
                ->limit(20)
                ->findAll();
        }

        $data = [
            'title' => 'Lab Requests from Nurses',
            'pendingRequests' => $pendingRequests,
            'confirmedRequests' => $confirmedRequests
        ];

        return view('doctor/lab_requests/index', $data);
    }

    public function confirm($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $labRequestModel = new LabRequestModel();
        $historyModel = new LabStatusHistoryModel();
        $patientModel = new AdminPatientModel();

        // Get the lab request
        $request = $labRequestModel->find($id);
        if (!$request) {
            return redirect()->back()->with('error', 'Lab request not found.');
        }

        // Verify that the patient is assigned to this doctor
        $patient = $patientModel->find($request['patient_id']);
        if (!$patient || $patient['doctor_id'] != $doctorId) {
            return redirect()->back()->with('error', 'You are not authorized to confirm this lab request.');
        }

        // Check if already confirmed
        if ($request['status'] !== 'pending') {
            return redirect()->back()->with('error', 'This lab request has already been processed.');
        }

        // Update the request: set doctor_id and change status to in_progress
        $updateData = [
            'doctor_id' => $doctorId,
            'status' => 'in_progress'
        ];

        if ($labRequestModel->update($id, $updateData)) {
            // Log status change
            $historyModel->insert([
                'lab_request_id' => $id,
                'status' => 'in_progress',
                'changed_by' => $doctorId,
                'notes' => 'Lab request confirmed by doctor',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Create notification for the nurse who requested it
            if ($request['nurse_id']) {
                $notificationModel = new NurseNotificationModel();
                $notificationModel->insert([
                    'nurse_id' => $request['nurse_id'],
                    'type' => 'lab_request_approved',
                    'title' => 'Lab Request Approved',
                    'message' => 'Your lab request for ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' (' . $request['test_name'] . ') has been approved by the doctor.',
                    'related_id' => $id,
                    'related_type' => 'lab_request',
                    'is_read' => 0,
                ]);
            }

            return redirect()->back()->with('success', 'Lab request confirmed successfully. The request is now in progress.');
        } else {
            return redirect()->back()->with('error', 'Failed to confirm lab request.');
        }
    }

    public function reject($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $labRequestModel = new LabRequestModel();
        $historyModel = new LabStatusHistoryModel();
        $patientModel = new AdminPatientModel();

        // Get the lab request
        $request = $labRequestModel->find($id);
        if (!$request) {
            return redirect()->back()->with('error', 'Lab request not found.');
        }

        // Verify that the patient is assigned to this doctor
        $patient = $patientModel->find($request['patient_id']);
        if (!$patient || $patient['doctor_id'] != $doctorId) {
            return redirect()->back()->with('error', 'You are not authorized to reject this lab request.');
        }

        // Check if already processed
        if ($request['status'] !== 'pending') {
            return redirect()->back()->with('error', 'This lab request has already been processed.');
        }

        $rejectionNotes = $this->request->getPost('rejection_notes') ?? 'Rejected by doctor';

        // Update the request: set doctor_id and change status to cancelled
        $updateData = [
            'doctor_id' => $doctorId,
            'status' => 'cancelled'
        ];

        if ($labRequestModel->update($id, $updateData)) {
            // Log status change
            $historyModel->insert([
                'lab_request_id' => $id,
                'status' => 'cancelled',
                'changed_by' => $doctorId,
                'notes' => $rejectionNotes,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()->with('success', 'Lab request rejected successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to reject lab request.');
        }
    }
}

