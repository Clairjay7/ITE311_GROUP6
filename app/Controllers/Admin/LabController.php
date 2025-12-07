<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LabServiceModel;
use App\Models\AdminPatientModel;
use App\Models\HMSPatientModel;
use App\Models\LabTestModel;
use App\Models\LabRequestModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;
use App\Models\DoctorModel;

class LabController extends BaseController
{
    protected $labServiceModel;
    protected $patientModel;
    protected $hmsPatientModel;
    protected $labTestModel;
    protected $labRequestModel;
    protected $chargeModel;
    protected $billingItemModel;
    protected $doctorModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->labServiceModel = new LabServiceModel();
        $this->patientModel = new AdminPatientModel();
        $this->hmsPatientModel = new HMSPatientModel();
        $this->labTestModel = new LabTestModel();
        $this->labRequestModel = new LabRequestModel();
        $this->chargeModel = new ChargeModel();
        $this->billingItemModel = new BillingItemModel();
        $this->doctorModel = new DoctorModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get lab services with related information
        $labServices = $this->labServiceModel
            ->select('lab_services.*, 
                     admin_patients.firstname, 
                     admin_patients.lastname,
                     admin_patients.contact,
                     admin_patients.birthdate,
                     admin_patients.visit_type,
                     lab_requests.status as request_status,
                     lab_requests.payment_status,
                     lab_requests.test_name,
                     lab_requests.test_type,
                     lab_requests.requested_date,
                     lab_requests.requested_by,
                     lab_results.result as lab_result,
                     lab_results.interpretation,
                     lab_results.completed_at,
                     users_nurse.username as nurse_name,
                     users_doctor.username as doctor_name,
                     users_completed.username as completed_by_name')
            ->join('admin_patients', 'admin_patients.id = lab_services.patient_id', 'left')
            ->join('lab_requests', 'lab_requests.id = lab_services.lab_request_id', 'left')
            ->join('lab_results', 'lab_results.lab_request_id = lab_requests.id', 'left')
            ->join('users as users_nurse', 'users_nurse.id = lab_services.nurse_id', 'left')
            ->join('users as users_doctor', 'users_doctor.id = lab_services.doctor_id', 'left')
            ->join('users as users_completed', 'users_completed.id = lab_results.completed_by', 'left')
            ->where('lab_services.deleted_at', null) // Only show non-deleted records
            ->orderBy('lab_services.created_at', 'DESC')
            ->findAll();
        
        $data = [
            'title' => 'Lab Services',
            'labServices' => $labServices,
        ];

        return view('admin/lab/index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        
        // Get patients from admin_patients table
        $adminPatients = $this->patientModel->findAll();
        
        // Get patients from patients table (receptionist-registered)
        $hmsPatients = [];
        if ($db->tableExists('patients')) {
            $hmsPatientsRaw = $this->hmsPatientModel->findAll();
            // Format to match admin_patients structure
            foreach ($hmsPatientsRaw as $patient) {
                $hmsPatients[] = [
                    'id' => $patient['patient_id'] ?? $patient['id'],
                    'firstname' => $patient['first_name'] ?? '',
                    'lastname' => $patient['last_name'] ?? '',
                    'full_name' => ($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''),
                ];
            }
        }
        
        // Get active lab tests grouped by category
        $labTests = [];
        if ($db->tableExists('lab_tests')) {
            $labTests = $this->labTestModel->getActiveTestsGroupedByCategory();
        }
        
        // Get available nurses (nurses collect specimens)
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        $data = [
            'title' => 'Create Lab Service',
            'adminPatients' => $adminPatients,
            'hmsPatients' => $hmsPatients,
            'labTests' => $labTests,
            'nurses' => $nurses,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/lab/create', $data);
    }
    
    /**
     * Get available doctors with schedule info (AJAX)
     */
    public function getAvailableDoctors()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $date = $this->request->getGet('date') ?: date('Y-m-d');

        $availableDoctors = [];
        
        // Get doctors from doctors table (primary source)
        $doctors = $this->doctorModel->getAllDoctors();
        
        foreach ($doctors as $doctor) {
            // Get doctor's schedule for the selected date
            $doctorSchedule = $db->table('doctor_schedules')
                ->where('doctor_id', $doctor['id'])
                ->where('shift_date', $date)
                ->where('status', 'active')
                ->orderBy('start_time', 'ASC')
                ->get()
                ->getResultArray();

            $scheduleStatus = 'no_schedule';
            $scheduleTime = '';
            $currentAppointments = 0;
            $maxCapacity = 0;
            $availableSlots = 0;

            if (!empty($doctorSchedule)) {
                $scheduleStatus = 'available';
                $scheduleTimes = [];
                foreach ($doctorSchedule as $schedule) {
                    $scheduleTimes[] = substr($schedule['start_time'], 0, 5) . ' - ' . substr($schedule['end_time'], 0, 5);
                    // Calculate max capacity based on 1-hour slots
                    $start = strtotime($schedule['start_time']);
                    $end = strtotime($schedule['end_time']);
                    $maxCapacity += floor(($end - $start) / 3600); // Assuming 1-hour slots
                }
                $scheduleTime = implode(', ', $scheduleTimes);

                // Get current appointments for this doctor on this date
                $currentAppointments = $db->table('consultations')
                    ->where('doctor_id', $doctor['id'])
                    ->where('consultation_date', $date)
                    ->whereNotIn('status', ['cancelled'])
                    ->countAllResults();
                
                $availableSlots = $maxCapacity - $currentAppointments;

                if ($maxCapacity > 0) {
                    $occupancy = ($currentAppointments / $maxCapacity) * 100;
                    if ($occupancy >= 100) {
                        $scheduleStatus = 'full';
                    } elseif ($occupancy >= 80) {
                        $scheduleStatus = 'busy';
                    }
                } else {
                    $scheduleStatus = 'no_capacity';
                }
            } else {
                // Check if doctor has any schedule at all
                $anySchedule = $db->table('doctor_schedules')
                    ->where('doctor_id', $doctor['id'])
                    ->countAllResults();
                if ($anySchedule > 0) {
                    $scheduleStatus = 'off_duty'; // Has schedule but not for today
                } else {
                    $scheduleStatus = 'no_schedule'; // No schedule ever set
                }
            }

            $availableDoctors[] = [
                'id' => $doctor['id'],
                'name' => $doctor['doctor_name'] ?? 'Dr. ' . $doctor['id'],
                'specialization' => $doctor['specialization'] ?? 'General Practice',
                'schedule_status' => $scheduleStatus,
                'schedule_time' => $scheduleTime,
                'current_appointments' => $currentAppointments,
                'max_capacity' => $maxCapacity,
                'available_slots' => $availableSlots,
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'doctors' => $availableDoctors,
            'date' => $date
        ]);
    }

    public function store()
    {
        // Log incoming request for debugging
        log_message('debug', 'Lab Service Store - Post Data: ' . json_encode($this->request->getPost()));
        
        $patientSource = $this->request->getPost('patient_source');
        $db = \Config\Database::connect();
        
        // Determine test name based on patient source
        if ($patientSource === 'walkin') {
            $testName = $this->request->getPost('walkin_test_type');
            $nurseId = $this->request->getPost('walkin_nurse_id');
        } else {
            $testName = $this->request->getPost('test_type');
            $nurseId = $this->request->getPost('nurse_id');
        }
        
        // Check if test requires specimen (needs nurse)
        $requiresSpecimen = true; // Default to true for safety
        
        // First, check if nurse_id is provided - if not provided and test is without_specimen, that's OK
        // Handle both empty string and null/not set
        if ($nurseId === '' || $nurseId === null) {
            $nurseId = null;
        }
        
        // IMPORTANT: Check test type FIRST before validation
        if ($db->tableExists('lab_tests') && !empty($testName)) {
            $labTest = $db->table('lab_tests')
                ->where('test_name', $testName)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            if ($labTest) {
                $specimenCategory = $labTest['specimen_category'] ?? 'with_specimen';
                $requiresSpecimen = ($specimenCategory === 'with_specimen');
                log_message('debug', 'Lab Service Store - Test: ' . $testName . ', Specimen Category: ' . $specimenCategory . ', Requires Specimen: ' . ($requiresSpecimen ? 'yes' : 'no'));
            } else {
                log_message('warning', 'Lab Service Store - Test not found in database: ' . $testName);
                // If test not found, default to requiring specimen for safety
                $requiresSpecimen = true;
            }
        } else {
            log_message('warning', 'Lab Service Store - lab_tests table does not exist or test_name is empty. Test name: ' . ($testName ?? 'NULL'));
            // If we can't check, default to requiring specimen for safety
            $requiresSpecimen = true;
        }
        
        log_message('debug', 'Lab Service Store - Requires Specimen: ' . ($requiresSpecimen ? 'yes' : 'no') . ', Nurse ID provided: ' . (!empty($nurseId) ? 'yes (' . $nurseId . ')' : 'no'));
        
        // Different validation rules based on patient source
        if ($patientSource === 'walkin') {
            $rules = [
                'patient_source' => 'required|in_list[walkin,patient]',
                'walkin_full_name' => 'required|max_length[255]',
                'walkin_gender' => 'required|in_list[male,female,other]',
                'walkin_contact' => 'required|max_length[255]',
                'walkin_test_type' => 'required|max_length[255]',
                'walkin_request_date' => 'required|valid_date',
            ];
            
            // Nurse field required only if test requires specimen (same logic as patient form)
            if ($requiresSpecimen) {
                $rules['walkin_nurse_id'] = 'required|integer';
                log_message('debug', 'Lab Service Store - Validation: walkin_nurse_id is REQUIRED (test requires specimen)');
            }
        } else {
            $rules = [
                'patient_source' => 'required|in_list[walkin,patient]',
                'patient_id' => 'required|integer',
                'test_type' => 'required|max_length[255]',
                'result' => 'permit_empty|max_length[500]',
                'remarks' => 'permit_empty|max_length[500]',
            ];
            
            // Nurse is only required if test requires specimen
            if ($requiresSpecimen) {
                $rules['nurse_id'] = 'required|integer';
                log_message('debug', 'Lab Service Store - Validation: nurse_id is REQUIRED (test requires specimen)');
            }
        }
        // For without_specimen, don't add nurse_id to rules - it's completely optional
        // This way CodeIgniter won't validate it at all

        // Validate with the rules
        $isValid = $this->validate($rules);
        $errors = $this->validator->getErrors();
        
        // If validation failed, check if it's only because of nurse_id for without_specimen tests
        if (!$isValid) {
            log_message('error', 'Lab Service Validation Failed: ' . json_encode($errors));
            log_message('error', 'Post Data: ' . json_encode($this->request->getPost()));
            log_message('error', 'Requires Specimen: ' . ($requiresSpecimen ? 'yes' : 'no'));
            log_message('error', 'Nurse ID: ' . ($nurseId ?? 'not provided'));
            log_message('error', 'Test Name: ' . ($testName ?? 'not provided'));
            
            // If the error is about nurse_id/walkin_nurse_id and test doesn't require specimen, remove that error
            if ($patientSource === 'walkin') {
                if (isset($errors['walkin_nurse_id']) && !$requiresSpecimen) {
                    unset($errors['walkin_nurse_id']);
                    log_message('debug', 'Lab Service Store - Removed walkin_nurse_id validation error for without_specimen test');
                }
            } else {
                if (isset($errors['nurse_id']) && !$requiresSpecimen) {
                    unset($errors['nurse_id']);
                    log_message('debug', 'Lab Service Store - Removed nurse_id validation error for without_specimen test');
                }
            }
            
            // If there are still errors after removing nurse_id error, return with errors
            if (!empty($errors)) {
                return redirect()->back()->withInput()->with('errors', $errors)->with('error', 'Please fix the validation errors below.');
            }
            // If no errors remain, continue with the rest of the code (validation passed)
            log_message('debug', 'Lab Service Store - Validation passed after removing nurse_id error');
        } else {
            log_message('debug', 'Lab Service Store - Validation passed successfully');
        }
        
        log_message('debug', 'Lab Service Store - Validation passed');

        $db = \Config\Database::connect();
        $patientSource = $this->request->getPost('patient_source');
        
        // STEP 1: Handle walk-in patient creation if needed
        $patientId = null;
        if ($patientSource === 'walkin') {
            // Create walk-in patient record in admin_patients
            $walkinData = [
                'firstname' => $this->request->getPost('walkin_full_name'),
                'lastname' => '', // Walk-in patients don't have separate first/last
                'gender' => $this->request->getPost('walkin_gender'),
                'contact' => $this->request->getPost('walkin_contact'),
                'address' => $this->request->getPost('walkin_address') ?? '',
                'birthdate' => $this->request->getPost('walkin_date_of_birth') ?: null,
                'visit_type' => 'Walk-in',
            ];
            
            // Calculate age if provided
            if ($this->request->getPost('walkin_age')) {
                // Age is already provided, we can store it in a note or calculate from DOB
            }
            
            $this->patientModel->skipValidation(true);
            $patientId = $this->patientModel->insert($walkinData);
            $this->patientModel->skipValidation(false);
            
            if (!$patientId) {
                return redirect()->back()->withInput()->with('error', 'Failed to create walk-in patient record.');
            }
            
            $testName = $this->request->getPost('walkin_test_type');
            $nurseId = $this->request->getPost('walkin_nurse_id');
            if ($nurseId === '' || $nurseId === null) {
                $nurseId = null;
            }
        } else {
            $patientId = $this->request->getPost('patient_id');
            $testName = $this->request->getPost('test_type');
            $nurseId = $this->request->getPost('nurse_id');
            if ($nurseId === '' || $nurseId === null) {
                $nurseId = null;
            }
        }
        
        // STEP 2: Create lab service FIRST (without transaction - save immediately)
        try {
            // Get test info from lab_tests table
            $testType = '';
            if ($db->tableExists('lab_tests')) {
                $labTest = $db->table('lab_tests')
                    ->where('test_name', $testName)
                    ->where('is_active', 1)
                    ->get()
                    ->getRowArray();
                if ($labTest) {
                    $testType = $labTest['test_type'] ?? 'Laboratory';
                }
            }
            if (empty($testType)) {
                $testType = 'Laboratory';
            }

            // Create lab service - SAVE FIRST
            $labServiceData = [
                'patient_id' => $patientId,
                'test_type' => $testName,
                'result' => null, // Results will be entered by labstaff
                'remarks' => null, // Remarks will be entered by labstaff
            ];
            
            // Only add nurse_id if provided (required for with_specimen, optional for without_specimen)
            if (!empty($nurseId)) {
                $labServiceData['nurse_id'] = $nurseId;
            }

            // Log the data being inserted for debugging
            log_message('debug', 'Lab Service Data: ' . json_encode($labServiceData));

            if (!$this->labServiceModel->insert($labServiceData)) {
                $errors = $this->labServiceModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Database insert failed';
                log_message('error', 'Lab Service Insert Failed: ' . $errorMsg);
                log_message('error', 'Lab Service Data: ' . json_encode($labServiceData));
                throw new \Exception('Failed to create lab service: ' . $errorMsg);
            }
            $labServiceId = $this->labServiceModel->getInsertID();
            
            if (empty($labServiceId)) {
                log_message('error', 'Lab Service Insert ID is empty after insert');
                throw new \Exception('Failed to get lab service ID after insert');
            }
            
            log_message('debug', 'Lab Service Created with ID: ' . $labServiceId);

            // STEP 2: Create lab request (in transaction for consistency)
            $db->transStart();
            
            try {
                // Create lab request so it appears in laboratory system
                // Nurse will collect specimen and pass to lab (only if test requires specimen)
                // Match doctor's logic exactly - same for both walk-in and patient
                $labRequestData = [
                    'patient_id' => $patientId, // Use the $patientId variable (works for both walk-in and registered patients)
                    'test_type' => $testType,
                    'test_name' => $testName,
                    'requested_by' => 'admin',
                    'priority' => 'routine',
                    'instructions' => ($this->request->getPost('remarks') ?? '') . ' | SPECIMEN_CATEGORY:' . ($requiresSpecimen ? 'with_specimen' : 'without_specimen'),
                    'status' => 'pending',
                    'requested_date' => date('Y-m-d'),
                    'payment_status' => 'pending', // Pending accountant approval - will be 'approved' then 'paid' by accountant
                ];
                
                // Only add nurse_id if test requires specimen (matching doctor logic - same for both walk-in and patient)
                if ($requiresSpecimen && !empty($nurseId)) {
                    $labRequestData['nurse_id'] = $nurseId;
                }
                // For without_specimen, don't add nurse_id at all

                // Insert lab request (skip validation to avoid nurse_id requirement issues for without_specimen tests - matching doctor logic)
                log_message('debug', 'Admin Lab Service - Lab Request Data: ' . json_encode($labRequestData));
                
                $this->labRequestModel->skipValidation(true);
                $labRequestInserted = $this->labRequestModel->insert($labRequestData);
                $this->labRequestModel->skipValidation(false);
                
                if (!$labRequestInserted) {
                    $errors = $this->labRequestModel->errors();
                    throw new \Exception('Failed to create lab request: ' . (!empty($errors) ? implode(', ', $errors) : 'Database insert failed'));
                }
                $labRequestId = $this->labRequestModel->getInsertID();

                // Link lab_service to lab_request
                $this->labServiceModel->update($labServiceId, [
                    'lab_request_id' => $labRequestId
                ]);

                // STEP 3: Create charge for ALL lab tests (both with_specimen and without_specimen)
                // Both need payment approval before proceeding
                $chargeId = null;
                
                // Get lab test price
                $testPrice = 0.00;
                if ($db->tableExists('lab_tests')) {
                    $labTest = $db->table('lab_tests')
                        ->where('test_name', $testName)
                        ->where('is_active', 1)
                        ->get()
                        ->getRowArray();
                    
                    if ($labTest && isset($labTest['price'])) {
                        $testPrice = (float) $labTest['price'];
                    } else {
                        $testPrice = 300.00; // Default price
                    }
                } else {
                    $testPrice = 300.00; // Default price
                }

                // Generate charge number
                $chargeNumber = $this->chargeModel->generateChargeNumber();

                // Create charge record (pending - waiting for accountant approval)
                $chargeNotes = $requiresSpecimen 
                    ? 'Lab test payment: ' . $testName . ' - Requires accountant approval before proceeding to nurse for specimen collection'
                    : 'Lab test payment: ' . $testName . ' - Requires accountant approval before proceeding to laboratory for testing (no specimen required)';
                
                $chargeData = [
                    'patient_id' => $patientId, // Use the $patientId variable (works for both walk-in and registered patients)
                    'charge_number' => $chargeNumber,
                    'total_amount' => $testPrice,
                    'status' => 'pending', // Pending until accountant approves
                    'notes' => $chargeNotes,
                ];

                log_message('debug', 'Creating charge for lab service: ' . json_encode($chargeData));
                
                if (!$this->chargeModel->insert($chargeData)) {
                    $errors = $this->chargeModel->errors();
                    $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Database insert failed';
                    log_message('error', 'Charge Insert Failed: ' . $errorMsg);
                    log_message('error', 'Charge Data: ' . json_encode($chargeData));
                    throw new \Exception('Failed to create charge: ' . $errorMsg);
                }
                $chargeId = $this->chargeModel->getInsertID();
                
                if (empty($chargeId)) {
                    log_message('error', 'Charge Insert ID is empty after insert');
                    throw new \Exception('Failed to get charge ID after insert');
                }
                
                log_message('debug', 'Charge Created with ID: ' . $chargeId);

                // Create billing item
                $billingItemData = [
                    'charge_id' => $chargeId,
                    'item_type' => 'lab_test',
                    'item_name' => $testName,
                    'description' => 'Lab Test: ' . $testType . ' - ' . $testName,
                    'quantity' => 1.00,
                    'unit_price' => $testPrice,
                    'total_price' => $testPrice,
                    'related_id' => $labRequestId,
                    'related_type' => 'lab_request',
                ];

                log_message('debug', 'Creating billing item: ' . json_encode($billingItemData));
                
                if (!$this->billingItemModel->insert($billingItemData)) {
                    $errors = $this->billingItemModel->errors();
                    $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Database insert failed';
                    log_message('error', 'Billing Item Insert Failed: ' . $errorMsg);
                    log_message('error', 'Billing Item Data: ' . json_encode($billingItemData));
                    throw new \Exception('Failed to create billing item: ' . $errorMsg);
                }
                
                log_message('debug', 'Billing Item Created successfully');

                // Update lab request with charge_id (specimen_category already in instructions - matching doctor logic)
                $updateData = [
                    'charge_id' => $chargeId,
                    'payment_status' => 'pending', // Pending accountant approval
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Ensure specimen category is in instructions (already added above, but check to avoid duplication)
                $existingInstructions = $labRequestData['instructions'] ?? '';
                if (strpos($existingInstructions, 'SPECIMEN_CATEGORY:') === false) {
                    $specimenCategory = $requiresSpecimen ? 'with_specimen' : 'without_specimen';
                    $updateData['instructions'] = $existingInstructions . ' | SPECIMEN_CATEGORY:' . $specimenCategory;
                }
                
                $this->labRequestModel->update($labRequestId, $updateData);

                // Notify Accountant about new payment request (needs approval)
                if ($db->tableExists('accountant_notifications')) {
                    $patient = $this->patientModel->find($this->request->getPost('patient_id'));
                    $patientName = ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'Patient');
                    
                    $notificationMessage = $requiresSpecimen
                        ? 'Payment request for lab test: ' . $testName . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve to proceed to nurse for specimen collection.'
                        : 'Payment request for lab test: ' . $testName . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve to proceed directly to laboratory for testing (no specimen required).';
                    
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

                // Log status change if table exists
                if ($db->tableExists('lab_status_history')) {
                    $db->table('lab_status_history')->insert([
                        'lab_request_id' => $labRequestId,
                        'status' => 'pending',
                        'changed_by' => session()->get('user_id'),
                        'notes' => 'Lab service created by admin' . ($requiresSpecimen ? ' - Charge created, waiting for accountant approval' : ''),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    $error = $db->error();
                    $errorMessage = 'Transaction failed';
                    if (!empty($error['message'])) {
                        $errorMessage .= ': ' . $error['message'];
                    }
                    log_message('error', 'Lab Request/Charge Creation Transaction Failed: ' . $errorMessage);
                    // Don't throw - lab service is already saved, just log the error
                    log_message('warning', 'Lab Service ID ' . $labServiceId . ' was created but lab request/charge creation failed');
                }

                $successMessage = 'Lab service created successfully';
                if ($requiresSpecimen) {
                    $successMessage .= ' and charge created. Waiting for accountant approval.';
                } else {
                    $successMessage .= ' and sent to laboratory.';
                }

                log_message('debug', 'Lab Service Store - Success, redirecting to /admin/lab');
                return redirect()->to('/admin/lab')->with('success', $successMessage);
            } catch (\Exception $e) {
                // If error in lab request/charge creation, rollback only that transaction
                // Lab service is already saved
                if ($db->transStatus() !== false) {
                    $db->transRollback();
                }
                log_message('error', 'Lab Request/Charge Creation Error: ' . $e->getMessage());
                log_message('error', 'Stack Trace: ' . $e->getTraceAsString());
                // Lab service is already saved, so show success but with warning
                return redirect()->to('/admin/lab')->with('success', 'Lab service created successfully, but there was an error creating the lab request/charge: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            // Error in lab service creation - this is critical
            log_message('error', 'Lab Service Creation Error: ' . $e->getMessage());
            log_message('error', 'Stack Trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Failed to create lab service: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $db = \Config\Database::connect();
        
        // Get lab service with all related information
        $labService = $this->labServiceModel
            ->select('lab_services.*, 
                     admin_patients.firstname, 
                     admin_patients.lastname,
                     admin_patients.contact,
                     admin_patients.birthdate,
                     admin_patients.gender,
                     admin_patients.address,
                     lab_requests.status as request_status,
                     lab_requests.payment_status,
                     lab_requests.test_name,
                     lab_requests.test_type,
                     lab_requests.requested_date,
                     lab_requests.requested_by,
                     lab_results.result as lab_result,
                     lab_results.interpretation,
                     lab_results.completed_at,
                     users_nurse.username as nurse_name,
                     users_doctor.username as doctor_name,
                     users_completed.username as completed_by_name,
                     lab_tests.normal_range')
            ->join('admin_patients', 'admin_patients.id = lab_services.patient_id', 'left')
            ->join('lab_requests', 'lab_requests.id = lab_services.lab_request_id', 'left')
            ->join('lab_results', 'lab_results.lab_request_id = lab_requests.id', 'left')
            ->join('users as users_nurse', 'users_nurse.id = lab_services.nurse_id', 'left')
            ->join('users as users_doctor', 'users_doctor.id = lab_services.doctor_id', 'left')
            ->join('users as users_completed', 'users_completed.id = lab_results.completed_by', 'left')
            ->join('lab_tests', 'lab_tests.test_name = lab_requests.test_name AND lab_tests.is_active = 1', 'left')
            ->where('lab_services.id', $id)
            ->where('lab_services.deleted_at', null)
            ->first();
        
        if (!$labService) {
            return redirect()->to('/admin/lab')->with('error', 'Lab service not found.');
        }
        
        $data = [
            'title' => 'View Lab Service',
            'labService' => $labService,
        ];

        return view('admin/lab/view', $data);
    }

    public function edit($id)
    {
        $labService = $this->labServiceModel->find($id);
        
        if (!$labService) {
            return redirect()->to('/admin/lab')->with('error', 'Lab service not found.');
        }

        $patients = $this->patientModel->findAll();
        
        // Get active lab tests grouped by category
        $db = \Config\Database::connect();
        $labTests = [];
        if ($db->tableExists('lab_tests')) {
            $labTests = $this->labTestModel->getActiveTestsGroupedByCategory();
        }
        
        // Get available nurses (nurses collect specimens)
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Edit Lab Service',
            'labService' => $labService,
            'patients' => $patients,
            'labTests' => $labTests,
            'nurses' => $nurses,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/lab/edit', $data);
    }

    public function update($id)
    {
        $labService = $this->labServiceModel->find($id);
        
        if (!$labService) {
            return redirect()->to('/admin/lab')->with('error', 'Lab service not found.');
        }

        // Check if test requires specimen (needs nurse)
        $testName = $this->request->getPost('test_type');
        $requiresSpecimen = true; // Default to true for safety
        $db = \Config\Database::connect();
        if ($db->tableExists('lab_tests') && !empty($testName)) {
            $labTest = $db->table('lab_tests')
                ->where('test_name', $testName)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            if ($labTest) {
                $requiresSpecimen = ($labTest['specimen_category'] ?? 'with_specimen') === 'with_specimen';
            }
        }

        $rules = [
            'patient_id' => 'required|integer',
            'test_type' => 'required|max_length[255]',
            'result' => 'permit_empty|max_length[500]',
            'remarks' => 'permit_empty|max_length[500]',
        ];
        
        // Nurse is only required if test requires specimen
        if ($requiresSpecimen) {
            $rules['nurse_id'] = 'required|integer';
        } else {
            $rules['nurse_id'] = 'permit_empty|integer';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'test_type' => $this->request->getPost('test_type'),
            'result' => $this->request->getPost('result'),
            'remarks' => $this->request->getPost('remarks'),
        ];
        
        // Only add nurse_id if test requires specimen
        $nurseId = $this->request->getPost('nurse_id');
        if ($requiresSpecimen && !empty($nurseId)) {
            $data['nurse_id'] = $nurseId;
        } else {
            // Clear nurse_id if test doesn't require specimen
            $data['nurse_id'] = null;
        }

        $this->labServiceModel->update($id, $data);

        return redirect()->to('/admin/lab')->with('success', 'Lab service updated successfully.');
    }

    public function delete($id)
    {
        $labService = $this->labServiceModel->find($id);
        
        if (!$labService) {
            return redirect()->to('/admin/lab')->with('error', 'Lab service not found.');
        }

        $this->labServiceModel->delete($id);

        return redirect()->to('/admin/lab')->with('success', 'Lab service deleted successfully.');
    }
}

