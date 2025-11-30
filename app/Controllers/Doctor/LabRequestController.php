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

        // Get pending lab requests from nurses for assigned patients
        $pendingRequests = [];
        if (!empty($patientIds)) {
            $pendingRequests = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as nurse_name')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->join('users', 'users.id = lab_requests.nurse_id', 'left')
                ->whereIn('lab_requests.patient_id', $patientIds)
                ->where('lab_requests.status', 'pending')
                ->where('lab_requests.requested_by', 'nurse')
                ->orderBy('lab_requests.created_at', 'DESC')
                ->findAll();
        }

        // Get confirmed/in-progress requests
        $confirmedRequests = [];
        if (!empty($patientIds)) {
            $confirmedRequests = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as nurse_name')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->join('users', 'users.id = lab_requests.nurse_id', 'left')
                ->whereIn('lab_requests.patient_id', $patientIds)
                ->whereIn('lab_requests.status', ['in_progress', 'completed'])
                ->where('lab_requests.requested_by', 'nurse')
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

