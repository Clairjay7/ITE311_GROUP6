<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\ConsultationModel;
use App\Models\AdminPatientModel;
use App\Models\PatientVitalModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;
use App\Models\DoctorOrderModel;
use App\Models\OrderStatusLogModel;

class ConsultationController extends BaseController
{
    public function upcoming()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $upcomingConsultations = $consultationModel->getUpcomingConsultations($doctorId);

        $data = [
            'title' => 'Upcoming Consultations',
            'consultations' => $upcomingConsultations
        ];

        return view('doctor/consultations/upcoming', $data);
    }

    public function mySchedule()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        // Get consultations with for_admission field (exclude soft-deleted)
        $db = \Config\Database::connect();
        $mySchedule = $db->table('consultations c')
            ->select('c.*, ap.firstname, ap.lastname')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->where('c.doctor_id', $doctorId)
            ->where('c.deleted_at', null) // Exclude soft-deleted consultations
            ->orderBy('c.consultation_date', 'DESC')
            ->orderBy('c.consultation_time', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'My Schedule',
            'consultations' => $mySchedule
        ];

        return view('doctor/consultations/my_schedule', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');

        // Get patients assigned to this doctor from admin_patients table
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Create Consultation',
            'patients' => $patients,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'consultation_date' => 'required|valid_date',
            'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'type' => 'required|in_list[upcoming,completed]',
            'status' => 'required|in_list[pending,approved,cancelled]',
            'notes' => 'permit_empty|max_length[2000]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'doctor_id' => $doctorId,
            'patient_id' => $this->request->getPost('patient_id'),
            'consultation_date' => $this->request->getPost('consultation_date'),
            'consultation_time' => $this->request->getPost('consultation_time'),
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($consultationModel->insert($data)) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', 'Consultation created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create consultation.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');

        $consultation = $consultationModel->find($id);
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        // Get patients assigned to this doctor from admin_patients table
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Edit Consultation',
            'consultation' => $consultation,
            'patients' => $patients,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $consultation = $consultationModel->find($id);
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'consultation_date' => 'required|valid_date',
            'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'type' => 'required|in_list[upcoming,completed]',
            'status' => 'required|in_list[pending,approved,cancelled]',
            'notes' => 'permit_empty|max_length[2000]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'consultation_date' => $this->request->getPost('consultation_date'),
            'consultation_time' => $this->request->getPost('consultation_time'),
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($consultationModel->update($id, $data)) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', 'Consultation updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update consultation.');
        }
    }

    public function delete($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Try to find the consultation (including soft-deleted ones to check if it exists)
        $consultation = $consultationModel->withDeleted()->find($id);
        
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found. It may have been already deleted.');
        }

        // Check if already soft-deleted
        if (!empty($consultation['deleted_at'])) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'This consultation has already been deleted.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        // Check if consultation has related records (for warning only, not blocking)
        $hasCharges = $db->table('charges')
            ->where('consultation_id', $id)
            ->where('deleted_at', null)
            ->countAllResults() > 0;

        $hasAdmission = $db->table('admissions')
            ->where('consultation_id', $id)
            ->where('status !=', 'cancelled')
            ->where('deleted_at', null)
            ->countAllResults() > 0;

        // Check for discharge orders through admission
        $hasDischargeOrder = false;
        if ($hasAdmission) {
            $admission = $db->table('admissions')
                ->where('consultation_id', $id)
                ->where('status !=', 'cancelled')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            
            if ($admission) {
                $hasDischargeOrder = $db->table('discharge_orders')
                    ->where('admission_id', $admission['id'])
                    ->countAllResults() > 0;
            }
        }

        // Proceed with deletion (allow deletion even with related records)
        if ($consultationModel->delete($id)) {
            $message = 'Consultation deleted successfully.';
            
            // Add warning if there were related records
            if ($hasCharges || $hasAdmission || $hasDischargeOrder) {
                $warnings = [];
                if ($hasCharges) $warnings[] = 'billing charges';
                if ($hasAdmission) $warnings[] = 'admission record';
                if ($hasDischargeOrder) $warnings[] = 'discharge order';
                
                $message .= ' Note: This consultation had associated ' . implode(', ', $warnings) . 
                           '. Related records may need manual review.';
            }
            
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Failed to delete consultation. Please try again.');
        }
    }

    public function startConsultation($patientId = null, $patientSource = 'admin_patients')
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        $patientModel = new AdminPatientModel();
        $consultationModel = new ConsultationModel();

        // Handle missing parameters
        if (empty($patientId)) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient ID is required.');
        }

        // Get patient data based on source
        $patient = null;
        $patientSourceActual = 'admin_patients';
        
        if ($patientSource === 'admin_patients' || $patientSource === 'admin') {
            $patient = $patientModel->find($patientId);
        } else {
            // Try patients table (receptionist patients)
            if ($db->tableExists('patients')) {
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->get()
                    ->getRowArray();
                
                if ($hmsPatient) {
                    // Format patient data to match admin_patients structure
                    $nameParts = [];
                    if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                    if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                    
                    if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                        $parts = explode(' ', $hmsPatient['full_name'], 2);
                        $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                    }
                    
                    // Calculate age
                    $age = null;
                    if (!empty($hmsPatient['date_of_birth'])) {
                        try {
                            $birth = new \DateTime($hmsPatient['date_of_birth']);
                            $today = new \DateTime();
                            $age = (int)$today->diff($birth)->y;
                        } catch (\Exception $e) {
                            $age = $hmsPatient['age'] ?? null;
                        }
                    } elseif (!empty($hmsPatient['age'])) {
                        $age = (int)$hmsPatient['age'];
                    }
                    
                    $patient = [
                        'id' => $hmsPatient['patient_id'] ?? $hmsPatient['id'] ?? null,
                        'patient_id' => $hmsPatient['patient_id'] ?? $hmsPatient['id'] ?? null,
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'full_name' => $hmsPatient['full_name'] ?? implode(' ', $nameParts),
                        'birthdate' => $hmsPatient['date_of_birth'] ?? $hmsPatient['birthdate'] ?? null,
                        'age' => $age,
                        'gender' => strtolower($hmsPatient['gender'] ?? ''),
                        'contact' => $hmsPatient['contact'] ?? null,
                        'address' => $hmsPatient['address'] ?? null,
                        'type' => $hmsPatient['type'] ?? 'Out-Patient',
                        'visit_type' => $hmsPatient['visit_type'] ?? null,
                        'doctor_id' => $hmsPatient['doctor_id'] ?? null,
                        'source' => 'receptionist',
                    ];
                    $patientSourceActual = 'patients';
                }
            }
        }

        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor
        if (($patient['doctor_id'] ?? null) != $doctorId) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        // Calculate age if not already calculated
        if (empty($patient['age']) && !empty($patient['birthdate'])) {
            try {
                $birth = new \DateTime($patient['birthdate']);
                $today = new \DateTime();
                $patient['age'] = (int)$today->diff($birth)->y;
            } catch (\Exception $e) {
                $patient['age'] = null;
            }
        }

        // Get queue number - count existing consultations/appointments for this doctor today
        $today = date('Y-m-d');
        $queueNumber = 1;
        
        if ($db->tableExists('consultations')) {
            $todayConsultations = $db->table('consultations')
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->whereNotIn('status', ['cancelled'])
                ->countAllResults();
            $queueNumber = $todayConsultations + 1;
        }
        
        // Also check appointments table
        if ($db->tableExists('appointments')) {
            $todayAppointments = $db->table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('appointment_date', $today)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->countAllResults();
            $queueNumber = max($queueNumber, $todayAppointments + 1);
        }

        // Get existing consultation for this patient today (if any)
        $existingConsultation = null;
        $patientIdForConsultation = ($patientSourceActual === 'patients') ? ($patient['patient_id'] ?? $patient['id']) : $patient['id'];
        
        // Try to find consultation in consultations table
        // Note: consultations table uses admin_patients.id, so we need to find the admin_patients record
        if ($patientSourceActual === 'patients' && $db->tableExists('admin_patients')) {
            $adminPatient = $db->table('admin_patients')
                ->where('firstname', $patient['firstname'] ?? '')
                ->where('lastname', $patient['lastname'] ?? '')
                ->where('doctor_id', $doctorId)
                ->get()
                ->getRowArray();
            
            if ($adminPatient) {
                $patientIdForConsultation = $adminPatient['id'];
            }
        }
        
        if ($db->tableExists('consultations')) {
            $existingConsultation = $consultationModel
                ->where('patient_id', $patientIdForConsultation)
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->where('type', 'upcoming')
                ->orderBy('created_at', 'DESC')
                ->first();
            
            // Extract queue number from notes if available
            if ($existingConsultation && !empty($existingConsultation['notes'])) {
                if (preg_match('/Queue #(\d+)/', $existingConsultation['notes'], $matches)) {
                    $queueNumber = (int)$matches[1];
                }
            }
        }

        // Get all available medicines from pharmacy for medication prescription
        $medicines = [];
        if ($db->tableExists('pharmacy')) {
            $medicines = $db->table('pharmacy')
                ->where('quantity >', 0)
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get all active nurses (for medication orders)
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get active lab tests grouped by category (for lab test requests)
        $labTests = [];
        if ($db->tableExists('lab_tests')) {
            $labTestModel = new \App\Models\LabTestModel();
            $labTests = $labTestModel->getActiveTestsGroupedByCategory();
        }

        $data = [
            'title' => 'Start Consultation',
            'patient' => $patient,
            'patient_source' => $patientSourceActual,
            'visit_type' => $patient['visit_type'] ?? 'Consultation',
            'queue_number' => $queueNumber,
            'existing_consultation' => $existingConsultation,
            'medicines' => $medicines,
            'nurses' => $nurses,
            'labTests' => $labTests,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/start', $data);
    }

    public function saveConsultation()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'patient_source' => 'required|in_list[admin_patients,patients]',
            'consultation_date' => 'required|valid_date',
            'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'observations' => 'permit_empty|max_length[5000]',
            'diagnosis' => 'permit_empty|max_length[2000]',
            'notes' => 'permit_empty|max_length[2000]',
            'for_admission' => 'permit_empty|in_list[0,1]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $patientId = $this->request->getPost('patient_id');
        $patientSource = $this->request->getPost('patient_source');
        $queueNumber = $this->request->getPost('queue_number');

        // For patients from patients table, find corresponding admin_patients.id
        $adminPatientId = $patientId;
        if ($patientSource === 'patients' && $db->tableExists('admin_patients')) {
            // Get patient from patients table
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                // Find or create admin_patients record
                $nameParts = [];
                if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                    $parts = explode(' ', $hmsPatient['full_name'], 2);
                    $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                }
                
                $adminPatient = $db->table('admin_patients')
                    ->where('firstname', $nameParts[0] ?? '')
                    ->where('lastname', $nameParts[1] ?? '')
                    ->where('doctor_id', $doctorId)
                    ->get()
                    ->getRowArray();
                
                if ($adminPatient) {
                    $adminPatientId = $adminPatient['id'];
                } else {
                    // Create admin_patients record
                    $adminPatientData = [
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'birthdate' => $hmsPatient['date_of_birth'] ?? null,
                        'gender' => strtolower($hmsPatient['gender'] ?? 'other'),
                        'contact' => $hmsPatient['contact'] ?? null,
                        'address' => $hmsPatient['address'] ?? null,
                        'doctor_id' => $doctorId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $db->table('admin_patients')->insert($adminPatientData);
                    $adminPatientId = $db->insertID();
                }
            }
        }

        $data = [
            'doctor_id' => $doctorId,
            'patient_id' => $adminPatientId, // Use admin_patients.id for consultations table
            'consultation_date' => $this->request->getPost('consultation_date'),
            'consultation_time' => $this->request->getPost('consultation_time'),
            'type' => 'completed',
            'status' => 'approved',
            'observations' => $this->request->getPost('observations'),
            'diagnosis' => $this->request->getPost('diagnosis'),
            'notes' => $this->request->getPost('notes') . ($queueNumber ? ' | Queue #' . $queueNumber : ''),
            'for_admission' => $this->request->getPost('for_admission') ? 1 : 0,
        ];

        // Update or create consultation record
        $consultationId = null;
        if ($consultationModel->insert($data)) {
            $consultationId = $consultationModel->getInsertID();
            
            // Update patient status to mark consultation as completed
            // Update in admin_patients table - use direct DB update to ensure updated_at is set
            $db = \Config\Database::connect();
            $db->table('admin_patients')
                ->where('id', $adminPatientId)
                ->update(['updated_at' => date('Y-m-d H:i:s')]);
            
            // If patient is from patients table, also update there
            if ($patientSource === 'patients' && $db->tableExists('patients')) {
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->get()
                    ->getRowArray();
                
                if ($hmsPatient) {
                    $db->table('patients')
                        ->where('patient_id', $patientId)
                        ->update(['updated_at' => date('Y-m-d H:i:s')]);
                }
            }
            
            // AUTO-GENERATE CHARGES: Create charge record and billing items
            $chargeModel = new ChargeModel();
            $billingItemModel = new BillingItemModel();
            
            if ($db->tableExists('charges') && $db->tableExists('billing_items')) {
                // Generate unique charge number
                $chargeNumber = $chargeModel->generateChargeNumber();
                
                // Initialize total amount
                $totalAmount = 0.00;
                $billingItems = [];
                
                // 1. Consultation Fee
                $consultationFee = 500.00; // Default consultation fee
                $billingItems[] = [
                    'item_type' => 'consultation',
                    'item_name' => 'Doctor Consultation',
                    'description' => 'Consultation on ' . date('Y-m-d'),
                    'quantity' => 1.00,
                    'unit_price' => $consultationFee,
                    'total_price' => $consultationFee,
                    'related_id' => $consultationId,
                    'related_type' => 'consultation',
                ];
                $totalAmount += $consultationFee;
                
                // 2. Lab Test Fees (if lab requests exist and are completed)
                if ($db->tableExists('lab_requests')) {
                    $labRequests = $db->table('lab_requests')
                        ->where('patient_id', $adminPatientId)
                        ->where('doctor_id', $doctorId)
                        ->where('status', 'completed')
                        ->where('requested_date', $this->request->getPost('consultation_date'))
                        ->get()
                        ->getResultArray();
                    
                    foreach ($labRequests as $labRequest) {
                        // Default lab test fee (can be made configurable)
                        $labTestFee = 300.00; // Default fee per test
                        $billingItems[] = [
                            'item_type' => 'lab_test',
                            'item_name' => $labRequest['test_name'] ?? $labRequest['test_type'] ?? 'Lab Test',
                            'description' => 'Lab Test: ' . ($labRequest['test_type'] ?? 'N/A'),
                            'quantity' => 1.00,
                            'unit_price' => $labTestFee,
                            'total_price' => $labTestFee,
                            'related_id' => $labRequest['id'],
                            'related_type' => 'lab_request',
                        ];
                        $totalAmount += $labTestFee;
                    }
                }
                
                // 3. Medication Charges (only if dispensed by pharmacy)
                if ($db->tableExists('doctor_orders') && $db->tableExists('pharmacy')) {
                    $medicationOrders = $db->table('doctor_orders')
                        ->where('patient_id', $adminPatientId)
                        ->where('doctor_id', $doctorId)
                        ->where('order_type', 'medication')
                        ->where('pharmacy_status', 'dispensed') // Only dispensed medications
                        ->get()
                        ->getResultArray();
                    
                    foreach ($medicationOrders as $order) {
                        // Get medicine price from pharmacy
                        $medicine = $db->table('pharmacy')
                            ->where('item_name', $order['medicine_name'])
                            ->get()
                            ->getRowArray();
                        
                        if ($medicine) {
                            $medicinePrice = (float)($medicine['price'] ?? 0.00);
                            $quantity = 1.00; // Default quantity
                            
                            $billingItems[] = [
                                'item_type' => 'medication',
                                'item_name' => $order['medicine_name'] ?? 'Medication',
                                'description' => 'Medication: ' . ($order['dosage'] ?? 'N/A'),
                                'quantity' => $quantity,
                                'unit_price' => $medicinePrice,
                                'total_price' => $medicinePrice * $quantity,
                                'related_id' => $order['id'],
                                'related_type' => 'doctor_order',
                            ];
                            $totalAmount += ($medicinePrice * $quantity);
                        }
                    }
                }
                
                // Create charge record
                $chargeData = [
                    'consultation_id' => $consultationId,
                    'patient_id' => $adminPatientId,
                    'charge_number' => $chargeNumber,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'notes' => 'Auto-generated charge for consultation #' . $consultationId,
                ];
                
                if ($chargeModel->insert($chargeData)) {
                    $chargeId = $chargeModel->getInsertID();
                    
                    // Insert all billing items
                    foreach ($billingItems as $item) {
                        $item['charge_id'] = $chargeId;
                        $billingItemModel->insert($item);
                    }
                    
                    // Notify Accountant about new charge
                    if ($db->tableExists('accountant_notifications')) {
                        $db->table('accountant_notifications')->insert([
                            'type' => 'new_charge',
                            'title' => 'New Charge Generated',
                            'message' => 'New charge ' . $chargeNumber . ' generated for patient ID: ' . $adminPatientId . '. Total Amount: ₱' . number_format($totalAmount, 2),
                            'related_id' => $chargeId,
                            'related_type' => 'charge',
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
            
            // Handle medication prescription if provided
            $prescribeMedication = $this->request->getPost('prescribe_medication');
            if ($prescribeMedication === 'yes') {
                $medicineName = $this->request->getPost('medicine_name');
                $dosage = $this->request->getPost('dosage');
                $frequency = $this->request->getPost('frequency');
                $duration = $this->request->getPost('duration');
                $purchaseLocation = $this->request->getPost('purchase_location');
                $nurseId = $this->request->getPost('nurse_id'); // Only required if hospital_pharmacy
                
                if ($medicineName && $dosage && $frequency && $duration && $purchaseLocation) {
                    // Only require nurse_id if hospital pharmacy
                    if ($purchaseLocation === 'hospital_pharmacy' && !$nurseId) {
                        return redirect()->back()->withInput()->with('error', 'Please assign a nurse for hospital pharmacy medication orders.');
                    }
                    
                    // Create medication order
                    $orderModel = new \App\Models\DoctorOrderModel();
                    $orderData = [
                        'patient_id' => $adminPatientId,
                        'doctor_id' => $doctorId,
                        'nurse_id' => $nurseId ?: null, // Only set if hospital pharmacy
                        'order_type' => 'medication',
                        'medicine_name' => $medicineName,
                        'dosage' => $dosage,
                        'frequency' => $frequency,
                        'duration' => $duration,
                        'order_description' => "Prescribed during consultation: {$medicineName} - {$dosage}, {$frequency}, for {$duration}",
                        'status' => $purchaseLocation === 'outside' ? 'completed' : 'pending', // Auto-complete if outside hospital
                        'purchase_location' => $purchaseLocation, // hospital_pharmacy or outside
                        'pharmacy_status' => $purchaseLocation === 'hospital_pharmacy' ? 'pending' : null, // Only set if hospital pharmacy
                        'completed_by' => $purchaseLocation === 'outside' ? $doctorId : null, // Doctor completed the prescription for outside purchase
                        'completed_at' => $purchaseLocation === 'outside' ? date('Y-m-d H:i:s') : null, // Auto-complete timestamp for outside
                    ];
                    
                    if ($orderModel->insert($orderData)) {
                        $orderId = $orderModel->getInsertID();
                        
                        // Create order status log entry
                        if ($db->tableExists('order_status_logs')) {
                            $logModel = new \App\Models\OrderStatusLogModel();
                            $logModel->insert([
                                'order_id' => $orderId,
                                'status' => $orderData['status'],
                                'changed_by' => $doctorId,
                                'notes' => $purchaseLocation === 'outside' 
                                    ? 'Prescription completed by doctor. Patient will purchase medication from outside pharmacy.' 
                                    : 'Medication order created. Sent to hospital pharmacy.',
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        // If hospital pharmacy, notify pharmacy
                        if ($purchaseLocation === 'hospital_pharmacy' && $db->tableExists('pharmacy_notifications')) {
                            $db->table('pharmacy_notifications')->insert([
                                'order_id' => $orderId,
                                'patient_id' => $adminPatientId,
                                'medicine_name' => $medicineName,
                                'message' => "New medication order from Dr. " . (session()->get('username') ?? 'Doctor') . " for patient consultation.",
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        // Notify nurse only if hospital pharmacy (nurse will administer)
                        if ($purchaseLocation === 'hospital_pharmacy' && $nurseId && $db->tableExists('nurse_notifications')) {
                            $db->table('nurse_notifications')->insert([
                                'nurse_id' => $nurseId,
                                'type' => 'new_doctor_order', // Use 'new_doctor_order' as per table enum
                                'title' => 'New Medication Order',
                                'message' => "Dr. " . (session()->get('username') ?? 'Doctor') . " has prescribed {$medicineName} for a patient. Patient will purchase from hospital pharmacy. Please wait for pharmacy to dispense before administering.",
                                'related_id' => $orderId, // Use related_id instead of order_id
                                'related_type' => 'doctor_order', // Use related_type to specify it's a doctor order
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            }
            
            // Handle lab test request if provided
            $requestLabTest = $this->request->getPost('request_lab_test');
            if ($requestLabTest === 'yes') {
                $labTestName = $this->request->getPost('lab_test_name');
                $labNurseId = $this->request->getPost('lab_nurse_id');
                $labTestRemarks = $this->request->getPost('lab_test_remarks');
                
                if (!empty($labTestName)) {
                    // IMPORTANT: Check test type FIRST before processing (matching admin logic)
                    $requiresSpecimen = true; // Default to true for safety
                    $testPrice = 300.00; // Default price
                    $testType = 'General';
                    
                    // Handle both empty string and null/not set for nurse_id
                    if ($labNurseId === '' || $labNurseId === null) {
                        $labNurseId = null;
                    }
                    
                    // Check test type FIRST before validation (matching admin logic)
                    if ($db->tableExists('lab_tests') && !empty($labTestName)) {
                        $labTest = $db->table('lab_tests')
                            ->where('test_name', $labTestName)
                            ->where('is_active', 1)
                            ->get()
                            ->getRowArray();
                        
                        if ($labTest) {
                            $testType = $labTest['test_type'] ?? 'General';
                            $testPrice = (float)($labTest['price'] ?? 300.00);
                            $specimenCategory = $labTest['specimen_category'] ?? 'with_specimen';
                            $requiresSpecimen = ($specimenCategory === 'with_specimen');
                            log_message('debug', 'Doctor Consultation - Lab Test: ' . $labTestName . ', Specimen Category: ' . $specimenCategory . ', Requires Specimen: ' . ($requiresSpecimen ? 'yes' : 'no'));
                        } else {
                            log_message('warning', 'Doctor Consultation - Lab test not found in database: ' . $labTestName);
                            // If test not found, default to requiring specimen for safety
                            $requiresSpecimen = true;
                        }
                    } else {
                        log_message('warning', 'Doctor Consultation - lab_tests table does not exist or test_name is empty. Test name: ' . ($labTestName ?? 'NULL'));
                        // If we can't check, default to requiring specimen for safety
                        $requiresSpecimen = true;
                    }
                    
                    log_message('debug', 'Doctor Consultation - Requires Specimen: ' . ($requiresSpecimen ? 'yes' : 'no') . ', Nurse ID provided: ' . (!empty($labNurseId) ? 'yes (' . $labNurseId . ')' : 'no'));
                    
                    // For without_specimen tests, explicitly unset nurse_id to avoid validation issues
                    if (!$requiresSpecimen && empty($labNurseId)) {
                        $labNurseId = null; // Explicitly set to null
                        // Also unset from POST data to prevent any validation issues
                        $postData = $this->request->getPost();
                        unset($postData['lab_nurse_id']);
                    }
                    
                    // Create lab service (matching admin logic)
                    $labServiceModel = new \App\Models\LabServiceModel();
                    $labServiceData = [
                        'patient_id' => $adminPatientId,
                        'doctor_id' => $doctorId,
                        'test_type' => $labTestName,
                        'result' => null,
                        'remarks' => $labTestRemarks ?? null,
                    ];
                    
                    // Only add nurse_id if provided (required for with_specimen, optional for without_specimen)
                    // Matching admin logic: only add if not empty
                    if (!empty($labNurseId)) {
                        $labServiceData['nurse_id'] = $labNurseId;
                    }
                    
                    log_message('debug', 'Doctor Consultation - Lab Service Data: ' . json_encode($labServiceData));
                    
                    // Skip validation to avoid nurse_id requirement issues (matching admin approach)
                    $labServiceModel->skipValidation(true);
                    $labServiceInserted = $labServiceModel->insert($labServiceData);
                    $labServiceModel->skipValidation(false);
                    
                    if ($labServiceInserted) {
                        $labServiceId = $labServiceModel->getInsertID();
                        
                        // Create lab request (matching admin logic)
                        $labRequestModel = new \App\Models\LabRequestModel();
                        $labRequestData = [
                            'patient_id' => $adminPatientId,
                            'doctor_id' => $doctorId,
                            'test_type' => $testType,
                            'test_name' => $labTestName,
                            'requested_by' => 'doctor',
                            'priority' => 'routine',
                            'instructions' => ($labTestRemarks ?? '') . ' | SPECIMEN_CATEGORY:' . ($requiresSpecimen ? 'with_specimen' : 'without_specimen'),
                            'status' => 'pending',
                            'requested_date' => date('Y-m-d'),
                            'payment_status' => 'pending', // Pending accountant approval - will be 'approved' then 'paid' by accountant
                        ];
                        
                        // Only add nurse_id if test requires specimen (matching admin logic)
                        if ($requiresSpecimen) {
                            if (!empty($labNurseId)) {
                                $labRequestData['nurse_id'] = $labNurseId;
                            }
                        }
                        // For without_specimen, don't add nurse_id at all
                        
                        log_message('debug', 'Doctor Consultation - Lab Request Data: ' . json_encode($labRequestData));
                        
                        // Skip validation to avoid nurse_id requirement issues for without_specimen tests
                        $labRequestModel->skipValidation(true);
                        $labRequestInserted = $labRequestModel->insert($labRequestData);
                        $labRequestModel->skipValidation(false);
                        
                        if ($labRequestInserted) {
                            $labRequestId = $labRequestModel->getInsertID();
                            
                            // Link lab service to lab request
                            $labServiceModel->update($labServiceId, [
                                'lab_request_id' => $labRequestId
                            ]);
                            
                            // Create charge for lab test
                            $chargeModel = new ChargeModel();
                            $billingItemModel = new BillingItemModel();
                            
                            if ($db->tableExists('charges') && $db->tableExists('billing_items')) {
                                $chargeNumber = $chargeModel->generateChargeNumber();
                                
                                // Create charge record (pending - waiting for accountant approval) - matching admin logic
                                $chargeNotes = $requiresSpecimen 
                                    ? 'Lab test payment: ' . $labTestName . ' - Requires accountant approval before proceeding to nurse for specimen collection'
                                    : 'Lab test payment: ' . $labTestName . ' - Requires accountant approval before proceeding to laboratory for testing (no specimen required)';
                                
                                $chargeData = [
                                    'patient_id' => $adminPatientId,
                                    'charge_number' => $chargeNumber,
                                    'total_amount' => $testPrice,
                                    'status' => 'pending', // Pending until accountant approves
                                    'notes' => $chargeNotes,
                                ];
                                
                                log_message('debug', 'Doctor Consultation - Creating charge for lab service: ' . json_encode($chargeData));
                                
                                if ($chargeModel->insert($chargeData)) {
                                    $chargeId = $chargeModel->getInsertID();
                                    
                                    // Create billing item
                                    $billingItemData = [
                                        'charge_id' => $chargeId,
                                        'item_type' => 'lab_test',
                                        'item_name' => $labTestName,
                                        'description' => 'Lab Test: ' . $testType . ' - ' . $labTestName,
                                        'quantity' => 1.00,
                                        'unit_price' => $testPrice,
                                        'total_price' => $testPrice,
                                        'related_id' => $labRequestId,
                                        'related_type' => 'lab_request',
                                    ];
                                    
                                    $billingItemModel->insert($billingItemData);
                                    
                                    // Update lab request with charge_id and store specimen_category for later reference (matching admin logic)
                                    $updateData = [
                                        'charge_id' => $chargeId,
                                        'payment_status' => 'pending', // Pending accountant approval
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ];
                                    
                                    // Store specimen category in instructions for later reference (already done above, but ensure it's there)
                                    $existingInstructions = $labRequestData['instructions'] ?? '';
                                    if (strpos($existingInstructions, 'SPECIMEN_CATEGORY:') === false) {
                                        $specimenCategory = $requiresSpecimen ? 'with_specimen' : 'without_specimen';
                                        $updateData['instructions'] = $existingInstructions . ' | SPECIMEN_CATEGORY:' . $specimenCategory;
                                    }
                                    
                                    $labRequestModel->update($labRequestId, $updateData);
                                    
                                    // Notify Accountant about new payment request (needs approval) - matching admin logic
                                    if ($db->tableExists('accountant_notifications')) {
                                        $patient = $db->table('admin_patients')
                                            ->where('id', $adminPatientId)
                                            ->get()
                                            ->getRowArray();
                                        $patientName = ($patient ? ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '') : 'Patient');
                                        
                                        $notificationMessage = $requiresSpecimen
                                            ? 'Payment request for lab test: ' . $labTestName . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve to proceed to nurse for specimen collection.'
                                            : 'Payment request for lab test: ' . $labTestName . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve to proceed directly to laboratory for testing (no specimen required).';
                                        
                                        $db->table('accountant_notifications')->insert([
                                            'type' => 'lab_payment',
                                            'title' => 'Lab Test Payment Pending Approval',
                                            'message' => $notificationMessage,
                                            'related_id' => $chargeId,
                                            'related_type' => 'charge',
                                            'is_read' => 0,
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Redirect based on admission status
            $forAdmission = $this->request->getPost('for_admission') ? 1 : 0;
            $medicationMessage = '';
            if ($prescribeMedication === 'yes') {
                $purchaseLocation = $this->request->getPost('purchase_location');
                if ($purchaseLocation === 'hospital_pharmacy') {
                    $medicationMessage = ' Medication prescription created and sent to hospital pharmacy.';
                } else {
                    $medicationMessage = ' Medication prescription created. Patient will purchase from outside pharmacy.';
                }
            }
            
            if ($forAdmission) {
                return redirect()->to('/doctor/orders?patient_id=' . $adminPatientId . '&consultation_id=' . $consultationId)->with('success', 'Consultation completed and saved successfully. Patient is marked for admission. A Nurse or Receptionist will process the admission and assign a room/bed.' . $medicationMessage);
            } else {
                return redirect()->to('/doctor/orders?patient_id=' . $adminPatientId)->with('success', 'Consultation completed and saved successfully. Charges generated.' . $medicationMessage);
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to save consultation.');
        }
    }
}
