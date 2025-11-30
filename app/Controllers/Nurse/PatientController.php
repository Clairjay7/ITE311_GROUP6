<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\PatientVitalModel;
use App\Models\NurseNoteModel;
use App\Models\DoctorOrderModel;
use App\Models\OrderStatusLogModel;
use App\Models\DoctorNotificationModel;

class PatientController extends BaseController
{
    public function view()
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $patientModel = new AdminPatientModel();
        
        // Get all patients (nurses can view all patients)
        $patients = $patientModel
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Patient Information',
            'patients' => $patients
        ];

        return view('nurse/patients/view', $data);
    }

    public function details($id)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $vitalModel = new PatientVitalModel();
        $noteModel = new NurseNoteModel();
        $orderModel = new DoctorOrderModel();

        $patient = $patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/nurse/patients/view')->with('error', 'Patient not found.');
        }

        // Get patient vitals (latest 10)
        $vitals = $vitalModel
            ->where('patient_id', $id)
            ->orderBy('recorded_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get nurse notes (latest 10)
        $notes = $noteModel
            ->select('nurse_notes.*, users.username as nurse_name')
            ->join('users', 'users.id = nurse_notes.nurse_id', 'left')
            ->where('nurse_notes.patient_id', $id)
            ->orderBy('nurse_notes.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get doctor orders
        $orders = $orderModel
            ->select('doctor_orders.*, users.username as doctor_name')
            ->join('users', 'users.id = doctor_orders.doctor_id', 'left')
            ->where('doctor_orders.patient_id', $id)
            ->orderBy('doctor_orders.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Patient Details',
            'patient' => $patient,
            'vitals' => $vitals,
            'notes' => $notes,
            'orders' => $orders
        ];

        return view('nurse/patients/details', $data);
    }

    public function addVitals($patientId)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($patientId);
        
        if (!$patient) {
            return redirect()->to('/nurse/patients/view')->with('error', 'Patient not found.');
        }

        $data = [
            'title' => 'Add Vital Signs',
            'patient' => $patient,
            'validation' => \Config\Services::validation()
        ];

        return view('nurse/patients/add_vitals', $data);
    }

    public function storeVitals($patientId)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $vitalModel = new PatientVitalModel();
        $nurseId = session()->get('user_id');

        $validation = $this->validate([
            'blood_pressure_systolic' => 'permit_empty|integer|greater_than[0]|less_than[300]',
            'blood_pressure_diastolic' => 'permit_empty|integer|greater_than[0]|less_than[300]',
            'heart_rate' => 'permit_empty|integer|greater_than[0]|less_than[300]',
            'temperature' => 'permit_empty|decimal|greater_than[0]|less_than[120]',
            'oxygen_saturation' => 'permit_empty|integer|greater_than[0]|less_than[101]',
            'respiratory_rate' => 'permit_empty|integer|greater_than[0]|less_than[100]',
            'weight' => 'permit_empty|decimal|greater_than[0]',
            'height' => 'permit_empty|decimal|greater_than[0]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'patient_id' => $patientId,
            'nurse_id' => $nurseId,
            'blood_pressure_systolic' => $this->request->getPost('blood_pressure_systolic') ?: null,
            'blood_pressure_diastolic' => $this->request->getPost('blood_pressure_diastolic') ?: null,
            'heart_rate' => $this->request->getPost('heart_rate') ?: null,
            'temperature' => $this->request->getPost('temperature') ?: null,
            'oxygen_saturation' => $this->request->getPost('oxygen_saturation') ?: null,
            'respiratory_rate' => $this->request->getPost('respiratory_rate') ?: null,
            'weight' => $this->request->getPost('weight') ?: null,
            'height' => $this->request->getPost('height') ?: null,
            'notes' => $this->request->getPost('notes'),
            'recorded_at' => $this->request->getPost('recorded_at') ?: date('Y-m-d H:i:s'),
        ];

        if ($vitalModel->insert($data)) {
            return redirect()->to('/nurse/patients/details/' . $patientId)->with('success', 'Vital signs recorded successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to record vital signs.');
        }
    }

    public function addNote($patientId)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($patientId);
        
        if (!$patient) {
            return redirect()->to('/nurse/patients/view')->with('error', 'Patient not found.');
        }

        $data = [
            'title' => 'Add Nurse Note',
            'patient' => $patient,
            'validation' => \Config\Services::validation()
        ];

        return view('nurse/patients/add_note', $data);
    }

    public function storeNote($patientId)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $noteModel = new NurseNoteModel();
        $nurseId = session()->get('user_id');

        $validation = $this->validate([
            'note_type' => 'required|in_list[progress,observation,medication,incident,other]',
            'note' => 'required',
            'priority' => 'required|in_list[low,normal,high,urgent]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'patient_id' => $patientId,
            'nurse_id' => $nurseId,
            'note_type' => $this->request->getPost('note_type'),
            'note' => $this->request->getPost('note'),
            'priority' => $this->request->getPost('priority'),
        ];

        if ($noteModel->insert($data)) {
            return redirect()->to('/nurse/patients/details/' . $patientId)->with('success', 'Nurse note added successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add nurse note.');
        }
    }

    public function updateOrderStatus($orderId)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();
        $nurseId = session()->get('user_id');

        $order = $orderModel->find($orderId);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $validation = $this->validate([
            'status' => 'required|in_list[pending,in_progress,completed,cancelled]',
        ]);

        if (!$validation) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        $updateData = [
            'status' => $newStatus,
        ];

        if ($newStatus === 'completed') {
            $updateData['completed_by'] = $nurseId;
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        if ($orderModel->update($orderId, $updateData)) {
            // Log status change
            $logModel->insert([
                'order_id' => $orderId,
                'status' => $newStatus,
                'changed_by' => $nurseId,
                'notes' => $notes,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Create notification for the doctor
            $patientModel = new AdminPatientModel();
            $patient = $patientModel->find($order['patient_id']);
            $notificationModel = new DoctorNotificationModel();
            
            $notificationType = $newStatus === 'completed' ? 'order_completed' : 'order_updated';
            $notificationTitle = $newStatus === 'completed' ? 'Order Completed' : 'Order Updated';
            $notificationMessage = $newStatus === 'completed' 
                ? 'Your ' . $order['order_type'] . ' order for ' . ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . ' has been completed by a nurse.'
                : 'Your ' . $order['order_type'] . ' order for ' . ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . ' status has been updated to ' . str_replace('_', ' ', $newStatus) . ' by a nurse.';

            $notificationModel->insert([
                'doctor_id' => $order['doctor_id'],
                'type' => $notificationType,
                'title' => $notificationTitle,
                'message' => $notificationMessage,
                'related_id' => $orderId,
                'related_type' => 'doctor_order',
                'is_read' => 0,
            ]);

            return redirect()->back()->with('success', 'Order status updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update order status.');
        }
    }
}

