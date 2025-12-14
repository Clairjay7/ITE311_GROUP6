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

        $nurseId = session()->get('user_id');
        $db = \Config\Database::connect();
        $patientModel = new AdminPatientModel();
        
        // Get only patients assigned to this nurse
        // From admin_patients table (directly assigned via assigned_nurse_id)
        $assignedPatientsFromAdmin = [];
        if ($db->tableExists('admin_patients')) {
            $assignedPatientsFromAdmin = $db->table('admin_patients')
                ->where('assigned_nurse_id', $nurseId)
                ->where('deleted_at IS NULL', null, false)
                ->orderBy('lastname', 'ASC')
                ->orderBy('firstname', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // From patients table (directly assigned via assigned_nurse_id)
        $assignedPatientsFromHms = [];
        if ($db->tableExists('patients')) {
            $assignedPatientsFromHmsRaw = $db->table('patients')
                ->where('assigned_nurse_id', $nurseId)
                ->orderBy('last_name', 'ASC')
                ->orderBy('first_name', 'ASC')
                ->get()
                ->getResultArray();
            
            // Format HMS patients to match admin_patients structure
            foreach ($assignedPatientsFromHmsRaw as $patient) {
                $nameParts = [];
                $fullName = '';
                
                if (!empty($patient['first_name']) && !empty($patient['last_name'])) {
                    $nameParts = [$patient['first_name'], $patient['last_name']];
                    $fullName = trim($patient['first_name'] . ' ' . $patient['last_name']);
                } elseif (!empty($patient['full_name'])) {
                    $fullName = trim($patient['full_name']);
                    $parts = explode(' ', $fullName, 3);
                    if (count($parts) >= 2) {
                        $nameParts = [$parts[0], $parts[count($parts) - 1]];
                    } else {
                        $nameParts = [$parts[0] ?? '', ''];
                    }
                }
                
                // Try to find corresponding admin_patients record
                $adminPatient = null;
                if (!empty($nameParts[0]) && !empty($nameParts[1]) && $db->tableExists('admin_patients')) {
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $nameParts[0])
                        ->where('lastname', $nameParts[1])
                        ->where('assigned_nurse_id', $nurseId)
                        ->where('deleted_at IS NULL', null, false)
                        ->get()
                        ->getRowArray();
                }
                
                // Only add if no corresponding admin_patients record exists (avoid duplicates)
                if (!$adminPatient) {
                    $assignedPatientsFromHms[] = [
                        'id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'birthdate' => $patient['date_of_birth'] ?? $patient['birthdate'] ?? null,
                        'gender' => strtolower($patient['gender'] ?? ''),
                        'contact' => $patient['contact'] ?? null,
                        'address' => $patient['address'] ?? null,
                    ];
                }
            }
        }
        
        // Merge and remove duplicates
        $allPatients = array_merge($assignedPatientsFromAdmin, $assignedPatientsFromHms);
        
        // Remove duplicates based on name + birthdate
        $uniquePatients = [];
        $seenKeys = [];
        
        foreach ($allPatients as $patient) {
            $firstName = strtolower(trim($patient['firstname'] ?? ''));
            $lastName = strtolower(trim($patient['lastname'] ?? ''));
            $birthdate = $patient['birthdate'] ?? '';
            $uniqueKey = md5($firstName . '|' . $lastName . '|' . $birthdate);
            
            if (!isset($seenKeys[$uniqueKey])) {
                $uniquePatients[] = $patient;
                $seenKeys[$uniqueKey] = true;
            }
        }
        
        // Sort by lastname, then firstname
        usort($uniquePatients, function($a, $b) {
            $lastA = strtolower($a['lastname'] ?? '');
            $lastB = strtolower($b['lastname'] ?? '');
            if ($lastA !== $lastB) {
                return strcmp($lastA, $lastB);
            }
            $firstA = strtolower($a['firstname'] ?? '');
            $firstB = strtolower($b['firstname'] ?? '');
            return strcmp($firstA, $firstB);
        });

        $data = [
            'title' => 'My Assigned Patients',
            'patients' => $uniquePatients
        ];

        return view('nurse/patients/view', $data);
    }

    public function details($id)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $nurseId = session()->get('user_id');
        $patientModel = new AdminPatientModel();
        $vitalModel = new PatientVitalModel();
        $noteModel = new NurseNoteModel();
        $orderModel = new DoctorOrderModel();
        $db = \Config\Database::connect();

        // Get patient and verify nurse is assigned
        $patient = $patientModel->find($id);
        $patientSource = 'admin_patients';
        
        // If not found in admin_patients, check patients table
        if (!$patient && $db->tableExists('patients')) {
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $id)
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
                
                if (!empty($nameParts[0]) && !empty($nameParts[1]) && $db->tableExists('admin_patients')) {
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $nameParts[0])
                        ->where('lastname', $nameParts[1])
                        ->where('deleted_at IS NULL', null, false)
                        ->get()
                        ->getRowArray();
                    
                    if ($adminPatient) {
                        $patient = $adminPatient;
                        $id = $adminPatient['id']; // Use admin_patients.id for queries
                    }
                }
            }
        }
        
        if (!$patient) {
            return redirect()->to('/nurse/patients/view')->with('error', 'Patient not found.');
        }
        
        // VALIDATION: Verify the nurse is assigned to this patient
        $assignedNurseId = $patient['assigned_nurse_id'] ?? null;
        if ($assignedNurseId != $nurseId) {
            return redirect()->to('/nurse/patients/view')->with('error', 'You are not assigned to this patient. You can only view patients assigned to you.');
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

        // BACKEND VALIDATION: Check if doctor has checked the patient and status is 'pending_nurse'
        // Nurse cannot check vitals unless doctor has clicked "Check" button and status is 'pending_nurse'
        $isDoctorChecked = $patient['is_doctor_checked'] ?? 0;
        $doctorCheckStatus = $patient['doctor_check_status'] ?? 'available';
        
        if (!$isDoctorChecked) {
            return redirect()->to('/nurse/dashboard')->with('error', 'You can only add vital signs when the doctor requests a vitals check via the "Check" button in My Patients page.');
        }
        
        // BACKEND VALIDATION: Prevent nurse from accessing vitals form if status is not 'pending_nurse'
        if ($doctorCheckStatus !== 'pending_nurse') {
            return redirect()->to('/nurse/dashboard')->with('error', 'Vitals check is not currently pending. Doctor must click "Check" button first.');
        }

        // Get previous vitals for comparison
        $previousVitals = null;
        if ($db->tableExists('patient_vitals')) {
            $previousVitals = $db->table('patient_vitals')
                ->where('patient_id', $patient['id'])
                ->orderBy('created_at', 'DESC')
                ->orderBy('recorded_at', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
        }

        $data = [
            'title' => 'Add Vital Signs',
            'patient' => $patient,
            'previousVitals' => $previousVitals,
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

        // BACKEND VALIDATION: Check if doctor has checked the patient and status is pending_nurse
        // Nurse cannot check vitals unless doctor has clicked "Check" button and status is 'pending_nurse'
        $isDoctorChecked = null;
        $doctorCheckStatus = null;
        if ($patient) {
            $isDoctorChecked = $patient['is_doctor_checked'] ?? 0;
            $doctorCheckStatus = $patient['doctor_check_status'] ?? 'available';
        } else {
            $adminPatient = $db->table('admin_patients')->where('id', $adminPatientId)->get()->getRowArray();
            $isDoctorChecked = $adminPatient['is_doctor_checked'] ?? 0;
            $doctorCheckStatus = $adminPatient['doctor_check_status'] ?? 'available';
        }

        if (!$isDoctorChecked) {
            return redirect()->back()->withInput()->with('error', 'You can only add vital signs when the doctor requests a vitals check via the "Check" button in My Patients page.');
        }

        // BACKEND VALIDATION: Prevent nurse from checking vitals if status is not 'pending_nurse'
        if ($doctorCheckStatus !== 'pending_nurse') {
            return redirect()->back()->withInput()->with('error', 'Vitals check is not currently pending. Doctor must click "Check" button first.');
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
            $vitalId = $vitalModel->getInsertID();
            
            // WORKFLOW UPDATE: After nurse saves vitals, set status to 'pending_order'
            // Doctor must now create an order from the vital signs before Check button is enabled again
            $updateData = [
                'is_doctor_checked' => 1, // Keep as checked
                'doctor_check_status' => 'pending_order', // Lock button - waiting for doctor to create order
                'nurse_vital_status' => 'completed', // Mark vitals as completed
            ];
            
            $patientModel->update($adminPatientId, $updateData);
            
            // Also update patients table if corresponding record exists
            if ($db->tableExists('patients')) {
                $adminPatientForUpdate = $db->table('admin_patients')->where('id', $adminPatientId)->get()->getRowArray();
                if ($adminPatientForUpdate) {
                    $nameParts = [
                        $adminPatientForUpdate['firstname'] ?? '',
                        $adminPatientForUpdate['lastname'] ?? ''
                    ];
                    
                    if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                        $hmsPatient = $db->table('patients')
                            ->where('first_name', $nameParts[0])
                            ->where('last_name', $nameParts[1])
                            ->where('doctor_id', $adminPatientForUpdate['doctor_id'] ?? null)
                            ->get()
                            ->getRowArray();
                        
                        if ($hmsPatient) {
                            $db->table('patients')
                                ->where('patient_id', $hmsPatient['patient_id'])
                                ->update($updateData);
                        }
                    }
                }
            }
            
            // Get patient information for notification
            $patientForNotification = $patient;
            if (!$patientForNotification) {
                $patientForNotification = $db->table('admin_patients')->where('id', $adminPatientId)->get()->getRowArray();
            }
            
            // Notify the attending doctor
            if ($patientForNotification && !empty($patientForNotification['doctor_id'])) {
                $doctorNotificationModel = new DoctorNotificationModel();
                $patientName = trim(($patientForNotification['firstname'] ?? '') . ' ' . ($patientForNotification['lastname'] ?? ''));
                if (empty($patientName)) {
                    $patientName = 'Patient';
                }
                
                $doctorNotificationModel->insert([
                    'doctor_id' => $patientForNotification['doctor_id'],
                    'type' => 'vitals_recorded',
                    'title' => 'Vital Signs Recorded',
                    'message' => 'Vital signs have been recorded for ' . $patientName . '. Please perform Doctor Initial Assessment.',
                    'related_id' => $adminPatientId,
                    'related_type' => 'patient_vitals',
                    'is_read' => 0,
                ]);
            }
            
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
        $db = \Config\Database::connect(); // Initialize database connection

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

            // WORKFLOW UNLOCK: If ANY order type is completed and patient status is 'pending_order', unlock Check button
            // This applies to ALL order types: medication, lab_test, procedure, diet, diagnostic_imaging, nursing_order, treatment_order, iv_fluids_order, reassessment_order, stat_order
            if ($newStatus === 'completed') {
                $patientModel = new AdminPatientModel();
                $patient = $patientModel->find($order['patient_id']);
                
                if ($patient && ($patient['doctor_check_status'] ?? 'available') === 'pending_order') {
                    // Check if there are any other pending orders for this patient (not just vital-linked)
                    // If no other pending orders exist, unlock the Check button
                    $hasOtherPendingOrders = false;
                    if ($db->tableExists('doctor_orders')) {
                        $otherPendingOrders = $db->table('doctor_orders')
                            ->where('patient_id', $order['patient_id'])
                            ->where('id !=', $orderId)
                            ->where('status !=', 'completed')
                            ->where('status !=', 'cancelled')
                            ->countAllResults();
                        
                        $hasOtherPendingOrders = $otherPendingOrders > 0;
                    }
                    
                    // Unlock if there are no other pending orders
                    // This works for ANY order type - once completed, unlock the Check button
                    if (!$hasOtherPendingOrders) {
                        $unlockData = [
                            'is_doctor_checked' => 0,
                            'doctor_check_status' => 'available', // Unlock Check button
                            'nurse_vital_status' => 'completed',
                        ];
                        
                        // Add doctor_order_status only if column exists
                        if ($db->fieldExists('doctor_order_status', 'admin_patients')) {
                            $unlockData['doctor_order_status'] = 'not_required';
                        }
                        
                        $patientModel->update($order['patient_id'], $unlockData);
                        
                        // Also update patients table if corresponding record exists
                        if ($db->tableExists('patients')) {
                            $nameParts = [
                                $patient['firstname'] ?? '',
                                $patient['lastname'] ?? ''
                            ];
                            
                            if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                                $hmsPatient = $db->table('patients')
                                    ->where('first_name', $nameParts[0])
                                    ->where('last_name', $nameParts[1])
                                    ->where('doctor_id', $patient['doctor_id'] ?? null)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($hmsPatient) {
                                    $db->table('patients')
                                        ->where('patient_id', $hmsPatient['patient_id'])
                                        ->update($unlockData);
                                }
                            }
                        }
                    }
                }
            }

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

    public function startMonitoring($orderId)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $nurseId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $db = \Config\Database::connect();

        // Get the order
        $order = $orderModel->find($orderId);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Check if order is completed
        if ($order['status'] !== 'completed') {
            return redirect()->back()->with('error', 'Order must be completed before starting continuous monitoring.');
        }

        // Check if order is assigned to this nurse
        if ($order['nurse_id'] != $nurseId) {
            return redirect()->back()->with('error', 'This order is not assigned to you.');
        }

        // Create or update patient_monitoring table if it doesn't exist
        if (!$db->tableExists('patient_monitoring')) {
            // Create the table
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'patient_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'order_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'nurse_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'stopped'],
                    'default' => 'active',
                ],
                'started_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'stopped_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ];
            
            $db->query("CREATE TABLE IF NOT EXISTS patient_monitoring (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                patient_id INT(11) UNSIGNED NOT NULL,
                order_id INT(11) UNSIGNED NOT NULL,
                nurse_id INT(11) UNSIGNED NOT NULL,
                status ENUM('active', 'stopped') DEFAULT 'active',
                started_at DATETIME NULL,
                stopped_at DATETIME NULL,
                notes TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX idx_patient_id (patient_id),
                INDEX idx_order_id (order_id),
                INDEX idx_status (status)
            )");
        }

        // Check if monitoring already exists for this order
        $existingMonitoring = $db->table('patient_monitoring')
            ->where('order_id', $orderId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if ($existingMonitoring) {
            return redirect()->back()->with('info', 'Continuous monitoring is already active for this order.');
        }

        // Start monitoring
        $monitoringData = [
            'patient_id' => $order['patient_id'],
            'order_id' => $orderId,
            'nurse_id' => $nurseId,
            'status' => 'active',
            'started_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($db->table('patient_monitoring')->insert($monitoringData)) {
            // Create notification for the doctor
            $patientModel = new AdminPatientModel();
            $patient = $patientModel->find($order['patient_id']);
            $notificationModel = new DoctorNotificationModel();
            
            $patientName = $patient ? ($patient['firstname'] . ' ' . $patient['lastname']) : 'patient';
            
            $notificationModel->insert([
                'doctor_id' => $order['doctor_id'],
                'type' => 'system',
                'title' => 'Continuous Monitoring Started',
                'message' => 'Nurse has started continuous monitoring for ' . $patientName . ' after completing the ' . $order['order_type'] . ' order.',
                'related_id' => $order['patient_id'],
                'related_type' => 'patient_monitoring',
                'is_read' => 0,
            ]);

            return redirect()->back()->with('success', 'Continuous monitoring started successfully. Please monitor the patient\'s vital signs regularly.');
        } else {
            return redirect()->back()->with('error', 'Failed to start continuous monitoring.');
        }
    }
}

