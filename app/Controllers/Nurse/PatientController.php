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
        
        // For lab_test orders, check if corresponding lab_request is completed
        $db = \Config\Database::connect();
        foreach ($orders as &$order) {
            if ($order['order_type'] === 'lab_test') {
                // Find corresponding lab_request
                $labRequest = null;
                if ($db->tableExists('lab_requests')) {
                    $labRequest = $db->table('lab_requests')
                        ->where('patient_id', $order['patient_id'])
                        ->where('doctor_id', $order['doctor_id'])
                        ->where('test_name', $order['order_description'])
                        ->orderBy('created_at', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRowArray();
                }
                
                // Add lab_request status and result info to order
                $order['lab_request_status'] = $labRequest['status'] ?? 'not_found';
                $order['lab_request_id'] = $labRequest['id'] ?? null;
                $order['has_lab_result'] = false;
                
                // Check if lab result exists
                if ($labRequest && $db->tableExists('lab_results')) {
                    $labResult = $db->table('lab_results')
                        ->where('lab_request_id', $labRequest['id'])
                        ->get()
                        ->getRowArray();
                    $order['has_lab_result'] = !empty($labResult);
                }
            }
        }

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
        $db = \Config\Database::connect();
        $nurseId = session()->get('user_id');
        
        // If not found in admin_patients, check patients table
        if (!$patient && $db->tableExists('patients')) {
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                // Find corresponding admin_patients record
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
                        ->where('doctor_id', $hmsPatient['doctor_id'] ?? null)
                        ->where('deleted_at IS NULL', null, false)
                        ->get()
                        ->getRowArray();
                    
                    if ($adminPatient) {
                        $patient = $adminPatient;
                    } else {
                        // Create admin_patients record if it doesn't exist
                        $adminPatientData = [
                            'firstname' => $nameParts[0],
                            'lastname' => $nameParts[1],
                            'birthdate' => $hmsPatient['date_of_birth'] ?? $hmsPatient['birthdate'] ?? null,
                            'gender' => strtolower($hmsPatient['gender'] ?? 'other'),
                            'contact' => $hmsPatient['contact'] ?? null,
                            'address' => $hmsPatient['address'] ?? null,
                            'doctor_id' => $hmsPatient['doctor_id'] ?? null,
                            'visit_type' => $hmsPatient['visit_type'] ?? null,
                            'assigned_nurse_id' => $hmsPatient['assigned_nurse_id'] ?? null,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        
                        $db->table('admin_patients')->insert($adminPatientData);
                        $patient = $db->table('admin_patients')->where('id', $db->insertID())->get()->getRowArray();
                    }
                }
            }
        }
        
        if (!$patient) {
            return redirect()->to('/nurse/patients/view')->with('error', 'Patient not found.');
        }

        // Verify the nurse is assigned to this patient
        $assignedNurseId = $patient['assigned_nurse_id'] ?? null;
        if ($assignedNurseId != $nurseId) {
            return redirect()->to('/nurse/dashboard')->with('error', 'You are not assigned to this patient.');
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
        $db = \Config\Database::connect();

        // Verify patient exists and get the correct admin_patients.id
        // patient_vitals.patient_id must reference admin_patients.id
        $adminPatientId = null;
        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($patientId);
        
        if ($patient) {
            // Found in admin_patients table
            $adminPatientId = $patientId;
        } else {
            // Not found in admin_patients, check if it's from patients table
            if ($db->tableExists('patients')) {
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->get()
                    ->getRowArray();
                
                if ($hmsPatient) {
                    // Find corresponding admin_patients record
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
                            ->where('doctor_id', $hmsPatient['doctor_id'] ?? null)
                            ->where('deleted_at IS NULL', null, false)
                            ->get()
                            ->getRowArray();
                        
                        if ($adminPatient) {
                            $adminPatientId = $adminPatient['id'];
                        } else {
                            // Create admin_patients record if it doesn't exist
                            $adminPatientData = [
                                'firstname' => $nameParts[0],
                                'lastname' => $nameParts[1],
                                'birthdate' => $hmsPatient['date_of_birth'] ?? $hmsPatient['birthdate'] ?? null,
                                'gender' => strtolower($hmsPatient['gender'] ?? 'other'),
                                'contact' => $hmsPatient['contact'] ?? null,
                                'address' => $hmsPatient['address'] ?? null,
                                'doctor_id' => $hmsPatient['doctor_id'] ?? null,
                                'visit_type' => $hmsPatient['visit_type'] ?? null,
                                'assigned_nurse_id' => $hmsPatient['assigned_nurse_id'] ?? null,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            
                            $db->table('admin_patients')->insert($adminPatientData);
                            $adminPatientId = $db->insertID();
                        }
                    }
                }
            }
        }

        if (!$adminPatientId) {
            return redirect()->back()->withInput()->with('error', 'Patient not found. Cannot record vital signs.');
        }

        // Verify the nurse is assigned to this patient
        $assignedNurseId = null;
        if ($patient) {
            $assignedNurseId = $patient['assigned_nurse_id'] ?? null;
        } else {
            $adminPatient = $db->table('admin_patients')->where('id', $adminPatientId)->get()->getRowArray();
            $assignedNurseId = $adminPatient['assigned_nurse_id'] ?? null;
        }

        if ($assignedNurseId != $nurseId) {
            return redirect()->back()->withInput()->with('error', 'You are not assigned to this patient. Cannot record vital signs.');
        }

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
            'patient_id' => $adminPatientId, // Always use admin_patients.id
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
            return redirect()->to('/nurse/patients/details/' . $adminPatientId)->with('success', 'Vital signs recorded successfully.');
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

        // For lab_test orders: Nurses CANNOT mark as complete unless lab staff has completed the lab request
        if ($order['order_type'] === 'lab_test' && $newStatus === 'completed') {
            $db = \Config\Database::connect();
            
            // Find the corresponding lab_request
            $labRequest = null;
            
            // Try to find by extracting link info from doctor_order instructions/remarks
            // Or find by matching patient_id, doctor_id, and test_name
            if ($db->tableExists('lab_requests')) {
                // First, try to find by matching criteria
                $labRequest = $db->table('lab_requests')
                    ->where('patient_id', $order['patient_id'])
                    ->where('doctor_id', $order['doctor_id'])
                    ->where('test_name', $order['order_description'])
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
                
                // If not found, try to find by admission_id if available
                if (!$labRequest && !empty($order['admission_id'])) {
                    $labRequest = $db->table('lab_requests')
                        ->where('patient_id', $order['patient_id'])
                        ->where('doctor_id', $order['doctor_id'])
                        ->where('test_name', $order['order_description'])
                        ->orderBy('created_at', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRowArray();
                }
            }
            
            // Check if lab_request exists and is completed
            if (!$labRequest) {
                return redirect()->back()->with('error', 'Cannot mark lab test order as complete. Lab request not found. Please contact the laboratory department.');
            }
            
            if ($labRequest['status'] !== 'completed') {
                return redirect()->back()->with('error', 'Cannot mark lab test order as complete. Laboratory staff has not yet completed the lab test. Current lab status: ' . ucfirst(str_replace('_', ' ', $labRequest['status'])) . '. Please wait for laboratory to complete the test first.');
            }
            
            // Check if lab result exists
            if ($db->tableExists('lab_results')) {
                $labResult = $db->table('lab_results')
                    ->where('lab_request_id', $labRequest['id'])
                    ->get()
                    ->getRowArray();
                
                if (!$labResult) {
                    return redirect()->back()->with('error', 'Cannot mark lab test order as complete. Lab result is not yet available. Please wait for laboratory to upload the results.');
                }
            }
        }

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

