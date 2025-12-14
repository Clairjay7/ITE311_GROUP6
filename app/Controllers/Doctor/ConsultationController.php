<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LabTestModel;
use App\Models\LabRequestModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;

class ConsultationController extends BaseController
{
    public function start($patientId = null, $source = 'admin_patients')
    {
        helper('form');
        
        // Check if user is doctor or admin
        $role = session()->get('role');
        if (!in_array($role, ['doctor', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access.');
        }

        if (!$patientId) {
            return redirect()->to('doctor/patients')->with('error', 'Patient ID is required.');
        }

        $db = \Config\Database::connect();
        
        // Get patient data based on source
        $patient = null;
        if ($source === 'patients') {
            // From receptionist patients table
            $patient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->get()
                ->getRowArray();
        } else {
            // From admin_patients table
            $patient = $db->table('admin_patients')
                ->where('id', $patientId)
                ->get()
                ->getRowArray();
        }

        if (!$patient) {
            return redirect()->to('doctor/patients')->with('error', 'Patient not found.');
        }

        // Get doctor info
        $doctorId = session()->get('user_id');
        $doctor = $db->table('users')
            ->where('id', $doctorId)
            ->get()
            ->getRowArray();

        // Get appointment date and time from existing consultation record (upcoming/approved status)
        $appointmentDate = date('Y-m-d');
        $appointmentTime = date('H:i');
        
        if ($db->tableExists('consultations')) {
            // Get the most recent upcoming/approved consultation for this patient
            $upcomingConsultation = $db->table('consultations')
                ->where('patient_id', $patientId)
                ->where('doctor_id', $doctorId)
                ->whereIn('status', ['upcoming', 'approved'])
                ->orderBy('consultation_date', 'ASC')
                ->orderBy('consultation_time', 'ASC')
                ->get()
                ->getRowArray();
            
            if ($upcomingConsultation) {
                $appointmentDate = $upcomingConsultation['consultation_date'];
                // Convert time from HH:MM:SS to HH:MM format
                $appointmentTime = substr($upcomingConsultation['consultation_time'], 0, 5);
            }
            
            // Check if consultation already exists for today (completed)
            $existingConsultation = $db->table('consultations')
                ->where('patient_id', $patientId)
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', date('Y-m-d'))
                ->where('status', 'completed')
                ->get()
                ->getRowArray();
        } else {
            $existingConsultation = null;
        }

        // Get all available medicines from pharmacy (quantity > 0, exclude IV Fluids category)
        $medicines = [];
        if ($db->tableExists('pharmacy')) {
            $medicines = $db->table('pharmacy')
                ->where('quantity >', 0)
                ->where('category !=', 'IV Fluids / Electrolytes')
                ->where('category IS NOT NULL', null, false)
                ->orderBy('category', 'ASC')
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get all active lab tests from lab_tests table
        $labTests = [];
        $labTestModel = new LabTestModel();
        if ($db->tableExists('lab_tests')) {
            $labTestsRaw = $labTestModel
                ->where('is_active', 1)
                ->where('deleted_at', null)
                ->orderBy('test_type', 'ASC')
                ->orderBy('test_name', 'ASC')
                ->findAll();
            
            // Deduplicate by test_name (keep the first occurrence)
            $seen = [];
            $labTests = [];
            foreach ($labTestsRaw as $test) {
                $testName = $test['test_name'] ?? '';
                if (!empty($testName) && !isset($seen[$testName])) {
                    $seen[$testName] = true;
                    $labTests[] = $test;
                }
            }
        }

        // Get available nurses (for specimen collection)
        // Check nurse availability for consultation date
        $allNurses = $this->getNursesWithSchedules();
        $consultationDate = $appointmentDate ?? date('Y-m-d');
        
        $nurses = [];
        foreach ($allNurses as $nurse) {
            $nurseId = $nurse['id'];
            $isAvailable = false;
            
            // Check if nurse has an active schedule for the consultation date
            if ($db->tableExists('nurse_schedules')) {
                $schedule = $db->table('nurse_schedules')
                    ->where('nurse_id', $nurseId)
                    ->where('shift_date', $consultationDate)
                    ->where('status', 'active')
                    ->get()
                    ->getRowArray();
                
                if ($schedule) {
                    $isAvailable = true;
                }
            }
            
            // Add availability flag
            $nurse['is_available'] = $isAvailable;
            $nurses[] = $nurse;
        }
        
        $data = [
            'title' => 'Start Consultation',
            'patient' => $patient,
            'doctor' => $doctor,
            'patientId' => $patientId,
            'source' => $source,
            'existingConsultation' => $existingConsultation,
            'appointmentDate' => $appointmentDate,
            'appointmentTime' => $appointmentTime,
            'medicines' => $medicines,
            'labTests' => $labTests,
            'nurses' => $nurses,
        ];

        return view('doctor/consultations/start', $data);
    }
    
    /**
     * Create consultation charge when consultation is completed
     * @param int $consultationId
     * @param int $patientId (admin_patients.id)
     * @return bool
     */
    private function createConsultationCharge($consultationId, $patientId)
    {
        $db = \Config\Database::connect();
        
        // Check if charge already exists for this consultation to avoid duplicates
        $existingCharge = $db->table('charges')
            ->where('consultation_id', $consultationId)
            ->where('patient_id', $patientId)
            ->get()
            ->getRowArray();
        
        if ($existingCharge) {
            log_message('info', "Consultation charge already exists for Consultation #{$consultationId} - Charge ID: {$existingCharge['id']}");
            return true; // Charge already exists
        }
        
        if (!$db->tableExists('charges') || !$db->tableExists('billing_items')) {
            log_message('error', "Cannot create consultation charge - charges or billing_items table does not exist");
            return false;
        }
        
        try {
            $chargeModel = new ChargeModel();
            $billingItemModel = new BillingItemModel();
            
            // Consultation fee (default: 500 PHP, can be configured)
            $consultationFee = 500.00; // Default consultation fee
            
            // Generate charge number
            $chargeNumber = $chargeModel->generateChargeNumber();
            
            // Create charge record
            $chargeData = [
                'consultation_id' => $consultationId,
                'patient_id' => $patientId, // Use admin_patients.id
                'charge_number' => $chargeNumber,
                'total_amount' => $consultationFee,
                'status' => 'pending',
                'notes' => 'Consultation Fee - Consultation #' . $consultationId,
            ];
            
            if ($chargeModel->insert($chargeData)) {
                $chargeId = $chargeModel->getInsertID();
                
                // Create billing item for consultation fee
                $billingItemData = [
                    'charge_id' => $chargeId,
                    'item_type' => 'consultation',
                    'item_name' => 'Consultation Fee',
                    'description' => 'Doctor Consultation - Consultation #' . $consultationId,
                    'quantity' => 1.00,
                    'unit_price' => $consultationFee,
                    'total_price' => $consultationFee,
                    'related_id' => $consultationId,
                    'related_type' => 'consultation',
                ];
                
                if ($billingItemModel->insert($billingItemData)) {
                    log_message('info', "✅✅✅ Consultation charge created successfully - Charge ID: {$chargeId}, Consultation ID: {$consultationId}, Patient ID: {$patientId}, Amount: {$consultationFee}");
                    return true;
                } else {
                    log_message('error', "❌ Failed to create billing item for consultation charge - Charge ID: {$chargeId}");
                    return false;
                }
            } else {
                log_message('error', "❌ Failed to create consultation charge - Consultation ID: {$consultationId}, Patient ID: {$patientId}");
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "❌ Exception creating consultation charge: " . $e->getMessage());
            log_message('error', "Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function store()
    {
        helper('form');
        
        // Check if user is doctor or admin
        $role = session()->get('role');
        if (!in_array($role, ['doctor', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access.');
        }

        $db = \Config\Database::connect();
        $doctorId = session()->get('user_id');
        
        // Get form data
        $patientId = $this->request->getPost('patient_id');
        $source = $this->request->getPost('source') ?? 'admin_patients';
        
        // Convert patient_id to admin_patients.id if source is 'patients' (receptionist)
        // consultations and lab_requests tables have foreign key to admin_patients.id
        $adminPatientId = $patientId;
        if ($source === 'patients') {
            // Try to find corresponding admin_patients record
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                // Try to find matching admin_patients by name and birthdate
                $adminPatient = $db->table('admin_patients')
                    ->where('firstname', $hmsPatient['first_name'] ?? '')
                    ->where('lastname', $hmsPatient['last_name'] ?? '')
                    ->where('birthdate', $hmsPatient['date_of_birth'] ?? '')
                    ->get()
                    ->getRowArray();
                
                if ($adminPatient) {
                    $adminPatientId = $adminPatient['id'];
                } else {
                    // Create admin_patients record if not found
                    $adminPatientData = [
                        'firstname' => $hmsPatient['first_name'] ?? '',
                        'lastname' => $hmsPatient['last_name'] ?? '',
                        'birthdate' => $hmsPatient['date_of_birth'] ?? null,
                        'gender' => $hmsPatient['gender'] ?? '',
                        'contact' => $hmsPatient['contact'] ?? null,
                        'address' => $hmsPatient['address'] ?? null,
                        'doctor_id' => $doctorId,
                        'visit_type' => $hmsPatient['visit_type'] ?? 'Consultation',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    
                    $db->table('admin_patients')->insert($adminPatientData);
                    $adminPatientId = $db->insertID();
                    
                    // Verify admin_patients record was created
                    if (!$adminPatientId) {
                        return redirect()->back()->withInput()->with('error', 'Failed to create patient record. Please try again.');
                    }
                }
            } else {
                return redirect()->back()->withInput()->with('error', 'Patient not found.');
            }
        } else {
            // Source is 'admin_patients', verify patient exists
            $adminPatient = $db->table('admin_patients')
                ->where('id', $patientId)
                ->get()
                ->getRowArray();
            
            if (!$adminPatient) {
                return redirect()->back()->withInput()->with('error', 'Patient not found in admin_patients table.');
            }
        }
        
        // Validation
        $rules = [
            'patient_id' => 'required',
            'chief_complaint' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get consultation data - use adminPatientId for foreign key constraint
        // Use current date and time for consultation (no longer using form fields)
        $consultationData = [
            'patient_id' => $adminPatientId, // Use admin_patients.id for foreign key
            'doctor_id' => $doctorId,
            'chief_complaint' => $this->request->getPost('chief_complaint'),
            'consultation_date' => date('Y-m-d'), // Use current date
            'consultation_time' => date('H:i:s'), // Use current time
            'type' => 'completed',
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        // Handle prescriptions if selected
        $prescriptions = $this->request->getPost('prescription');
        $prescriptionDetails = $this->request->getPost('prescription_details');
        if (!empty($prescriptions) && is_array($prescriptions)) {
            $consultationData['prescriptions'] = json_encode($prescriptions);
            if (!empty($prescriptionDetails) && is_array($prescriptionDetails)) {
                $consultationData['prescription_details'] = json_encode($prescriptionDetails);
            }
        }
        
        // Handle lab tests if selected
        $labTests = $this->request->getPost('lab_test');
        $hasLabTests = !empty($labTests) && is_array($labTests) && count($labTests) > 0;
        
        if ($hasLabTests) {
            $consultationData['lab_tests'] = json_encode($labTests);
            // If lab tests are selected, set status to 'pending' and type to 'upcoming' to wait for lab results
            $consultationData['type'] = 'upcoming';
            $consultationData['status'] = 'pending';
        }
        
        // Handle follow-up if checked
        $followUp = $this->request->getPost('follow_up');
        if ($followUp) {
            $consultationData['follow_up'] = 1;
            $followUpDate = $this->request->getPost('follow_up_date') ?? null;
            $followUpTime = $this->request->getPost('follow_up_time') ?? null;
            $followUpReason = $this->request->getPost('follow_up_reason') ?? null;
            $consultationData['follow_up_date'] = $followUpDate;
            $consultationData['follow_up_time'] = $followUpTime;
            $consultationData['follow_up_reason'] = $followUpReason;
        } else {
            $consultationData['follow_up'] = 0;
        }

        // Insert consultation
        if ($db->tableExists('consultations')) {
            $db->table('consultations')->insert($consultationData);
            $consultationId = $db->insertID();
            
            // Create appointment for follow-up if checked
            if ($followUp && !empty($followUpDate) && !empty($followUpTime) && $consultationId) {
                // Get patient info from admin_patients
                $patientInfo = $db->table('admin_patients')
                    ->where('id', $adminPatientId)
                    ->get()
                    ->getRowArray();
                
                if ($patientInfo) {
                    // Find or get patient_id from patients table
                    $patientRecord = $db->table('patients')
                        ->where('first_name', $patientInfo['firstname'] ?? '')
                        ->where('last_name', $patientInfo['lastname'] ?? '')
                        ->where('contact', $patientInfo['contact'] ?? '')
                        ->get()
                        ->getRowArray();
                    
                    $appointmentPatientId = $patientRecord['patient_id'] ?? null;
                    
                    // If patient doesn't exist in patients table, create one
                    if (!$appointmentPatientId) {
                        $newPatientData = [
                            'first_name' => $patientInfo['firstname'] ?? '',
                            'last_name' => $patientInfo['lastname'] ?? '',
                            'contact' => $patientInfo['contact'] ?? '',
                            'address' => $patientInfo['address'] ?? '',
                            'gender' => $patientInfo['gender'] ?? 'other',
                            'date_of_birth' => $patientInfo['birthdate'] ?? null,
                            'status' => 'active',
                            'type' => 'walkin'
                        ];
                        $db->table('patients')->insert($newPatientData);
                        $appointmentPatientId = $db->insertID();
                    }
                    
                    // Create appointment for follow-up
                    if ($appointmentPatientId && $db->tableExists('appointments')) {
                        // Convert time format if needed
                        $appointmentTime = $followUpTime;
                        if (strlen($appointmentTime) === 5) {
                            $appointmentTime = $appointmentTime . ':00';
                        }
                        
                        $appointmentData = [
                            'patient_id' => $appointmentPatientId,
                            'doctor_id' => $doctorId,
                            'appointment_date' => $followUpDate,
                            'appointment_time' => $appointmentTime,
                            'appointment_type' => 'follow_up',
                            'reason' => $followUpReason ?: 'Follow-up consultation',
                            'status' => 'scheduled',
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        
                        try {
                            $db->table('appointments')->insert($appointmentData);
                            log_message('info', "Follow-up appointment created: patient_id={$appointmentPatientId}, doctor_id={$doctorId}, date={$followUpDate}, time={$appointmentTime}");
                        } catch (\Exception $e) {
                            log_message('error', "Failed to create follow-up appointment: " . $e->getMessage());
                        }
                    }
                }
            }
            
            if ($consultationId) {
                // Create consultation charge when consultation is completed (no lab tests pending)
                // Only create charge if consultation is actually completed (not pending for lab results)
                if (!$hasLabTests && $consultationData['status'] === 'completed' && $consultationData['type'] === 'completed') {
                    $this->createConsultationCharge($consultationId, $adminPatientId);
                }
                
                // Create lab requests if lab tests are selected
                if ($hasLabTests && !empty($labTests)) {
                    $labRequestModel = new LabRequestModel();
                    $chargeModel = new ChargeModel();
                    $labTestModel = new LabTestModel();
                    
                    foreach ($labTests as $labTestId) {
                        // Get lab test details
                        $labTest = $labTestModel->find($labTestId);
                        if (!$labTest) {
                            continue;
                        }
                        
                        $testName = $labTest['test_name'] ?? '';
                        $testType = $labTest['test_type'] ?? 'Laboratory';
                        $testPrice = (float)($labTest['price'] ?? 0);
                        $specimenCategory = $labTest['specimen_category'] ?? 'with_specimen';
                        $requiresSpecimen = ($specimenCategory === 'with_specimen');
                        
                        // Get nurse_id from form if test requires specimen
                        $nurseId = null;
                        if ($requiresSpecimen) {
                            $nurseId = $this->request->getPost('nurse_id');
                            if (empty($nurseId)) {
                                $nurseId = null; // Allow null for now, can be assigned later
                            }
                        }
                        
                        // Create lab request
                        // Use adminPatientId for lab_requests (foreign key constraint)
                        $labRequestData = [
                            'patient_id' => $adminPatientId, // Use admin_patients.id for foreign key
                            'doctor_id' => $doctorId,
                            'test_type' => $testType,
                            'test_name' => $testName,
                            'requested_by' => 'doctor',
                            'priority' => 'routine',
                            'instructions' => 'From Consultation #' . $consultationId . ' | SPECIMEN_CATEGORY:' . $specimenCategory,
                            'status' => 'pending',
                            'requested_date' => date('Y-m-d'),
                            'payment_status' => 'pending',
                        ];
                        
                        // Add nurse_id if test requires specimen and nurse is selected
                        if ($requiresSpecimen && !empty($nurseId)) {
                            $labRequestData['nurse_id'] = $nurseId;
                        }
                        
                        // Insert lab request
                        $labRequestModel->skipValidation(true);
                        if ($labRequestModel->insert($labRequestData)) {
                            $labRequestId = $labRequestModel->getInsertID();
                            
                            // Create charge for lab test
                            if ($testPrice > 0) {
                                $chargeNumber = $chargeModel->generateChargeNumber();
                                $chargeData = [
                                    'patient_id' => $adminPatientId, // Use admin_patients.id for charges
                                    'charge_number' => $chargeNumber,
                                    'total_amount' => $testPrice,
                                    'status' => 'pending',
                                    'notes' => 'Lab Test: ' . $testName . ' (Consultation #' . $consultationId . ')',
                                ];
                                
                                if ($chargeModel->insert($chargeData)) {
                                    $chargeId = $chargeModel->getInsertID();
                                    // Update lab request with charge_id
                                    $labRequestModel->update($labRequestId, ['charge_id' => $chargeId]);
                                }
                            }
                        }
                        $labRequestModel->skipValidation(false);
                    }
                    
                    // If lab tests are pending, redirect back to patient list with message
                    return redirect()->to('doctor/patients')->with('success', 'Consultation saved. Lab test requests have been created. Waiting for lab test results. Patient will remain in your list until results are ready.');
                } else {
                    // If no lab tests, redirect to view/print page
                    return redirect()->to('doctor/consultations/view/' . $consultationId)->with('success', 'Consultation completed successfully.');
                }
            }
        }

        return redirect()->back()->withInput()->with('error', 'Failed to save consultation.');
    }

    public function view($consultationId = null)
    {
        if (!$consultationId) {
            return redirect()->to('doctor/patients')->with('error', 'Consultation ID is required.');
        }

        $db = \Config\Database::connect();
        
        // Get consultation with patient and doctor details
        $consultation = $db->table('consultations')
            ->where('consultations.id', $consultationId)
            ->get()
            ->getRowArray();

        if (!$consultation) {
            return redirect()->to('doctor/patients')->with('error', 'Consultation not found.');
        }

        // Get patient details
        $patient = null;
        if (!empty($consultation['patient_id'])) {
            // Try admin_patients first
            $patient = $db->table('admin_patients')
                ->where('id', $consultation['patient_id'])
                ->get()
                ->getRowArray();
            
            // If not found, try patients table
            if (!$patient) {
                $patient = $db->table('patients')
                    ->where('patient_id', $consultation['patient_id'])
                    ->get()
                    ->getRowArray();
            }
        }

        // Get doctor details
        $doctor = null;
        if (!empty($consultation['doctor_id'])) {
            $doctor = $db->table('users')
                ->where('id', $consultation['doctor_id'])
                ->get()
                ->getRowArray();
            
            // Get doctor full name if available
            if ($doctor && $db->tableExists('doctors')) {
                $doctorInfo = $db->table('doctors')
                    ->where('user_id', $consultation['doctor_id'])
                    ->get()
                    ->getRowArray();
                if ($doctorInfo) {
                    $doctor['doctor_name'] = $doctorInfo['doctor_name'] ?? $doctor['username'];
                }
            }
        }

        // Parse prescription details
        $prescriptionDetails = [];
        if (!empty($consultation['prescription_details'])) {
            $prescriptionDetails = json_decode($consultation['prescription_details'], true) ?? [];
        }

        // Get medicine names for prescriptions
        $prescriptions = [];
        if (!empty($consultation['prescriptions'])) {
            $prescriptionIds = json_decode($consultation['prescriptions'], true) ?? [];
            if (!empty($prescriptionIds) && $db->tableExists('pharmacy')) {
                $medicines = $db->table('pharmacy')
                    ->whereIn('id', $prescriptionIds)
                    ->get()
                    ->getResultArray();
                
                foreach ($prescriptionDetails as $index => $detail) {
                    $medicineId = $detail['medicine_id'] ?? null;
                    $medicine = array_filter($medicines, function($m) use ($medicineId) {
                        return $m['id'] == $medicineId;
                    });
                    $medicine = !empty($medicine) ? reset($medicine) : null;
                    
                    $prescriptions[] = [
                        'medicine' => $medicine,
                        'details' => $detail
                    ];
                }
            }
        }

        // Get lab test names
        $labTests = [];
        if (!empty($consultation['lab_tests'])) {
            $labTestIds = json_decode($consultation['lab_tests'], true) ?? [];
            if (!empty($labTestIds) && $db->tableExists('lab_tests')) {
                $labTestModel = new LabTestModel();
                $labTests = $labTestModel->whereIn('id', $labTestIds)->findAll();
            }
        }

        $data = [
            'title' => 'Consultation Details',
            'consultation' => $consultation,
            'patient' => $patient,
            'doctor' => $doctor,
            'prescriptions' => $prescriptions,
            'labTests' => $labTests,
        ];

        return view('doctor/consultations/view', $data);
    }

    public function completed()
    {
        // Check if user is doctor or admin
        $role = session()->get('role');
        if (!in_array($role, ['doctor', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access.');
        }

        $db = \Config\Database::connect();
        $doctorId = session()->get('user_id');

        // Get all completed consultations for this doctor
        $completedConsultations = [];
        if ($db->tableExists('consultations')) {
            $consultations = $db->table('consultations')
                ->where('doctor_id', $doctorId)
                ->where('type', 'completed')
                ->where('status', 'completed')
                ->orderBy('consultation_date', 'DESC')
                ->orderBy('consultation_time', 'DESC')
                ->get()
                ->getResultArray();

            // Get patient and doctor details for each consultation
            foreach ($consultations as $consultation) {
                $patientId = $consultation['patient_id'];
                
                // Try to get patient from admin_patients first
                $patient = $db->table('admin_patients')
                    ->where('id', $patientId)
                    ->get()
                    ->getRowArray();
                
                // If not found, try patients table
                if (!$patient) {
                    $patient = $db->table('patients')
                        ->where('patient_id', $patientId)
                        ->get()
                        ->getRowArray();
                    
                    // Format patient data to match admin_patients structure
                    if ($patient) {
                        $patient['id'] = $patient['patient_id'];
                        $patient['firstname'] = $patient['first_name'] ?? '';
                        $patient['lastname'] = $patient['last_name'] ?? '';
                        $patient['source'] = 'receptionist';
                    }
                } else {
                    $patient['source'] = 'admin';
                }

                // Get doctor details
                $doctor = $db->table('users')
                    ->where('id', $doctorId)
                    ->get()
                    ->getRowArray();
                
                if ($doctor && $db->tableExists('doctors')) {
                    $doctorInfo = $db->table('doctors')
                        ->where('user_id', $doctorId)
                        ->get()
                        ->getRowArray();
                    if ($doctorInfo) {
                        $doctor['doctor_name'] = $doctorInfo['doctor_name'] ?? $doctor['username'];
                    }
                }

                if ($patient) {
                    $completedConsultations[] = [
                        'consultation' => $consultation,
                        'patient' => $patient,
                        'doctor' => $doctor,
                    ];
                }
            }
        }

        $data = [
            'title' => 'Completed Consultations',
            'consultations' => $completedConsultations,
        ];

        return view('doctor/consultations/completed', $data);
    }
}

