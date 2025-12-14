<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\DoctorOrderModel;
use App\Models\OrderStatusLogModel;
use App\Models\AdminPatientModel;
use App\Models\DoctorNotificationModel;
use App\Models\NurseNotificationModel;
use App\Models\LabTestModel;
use App\Models\LabRequestModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;

class OrderController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get all orders by this doctor
        // Need to check both admin_patients and patients tables for patient names
        // Also check orders linked via admission_id
        $allOrdersRaw = $db->table('doctor_orders')
            ->select('doctor_orders.*, 
                     admin_patients.firstname as ap_firstname,
                     admin_patients.lastname as ap_lastname,
                     patients.first_name as p_firstname,
                     patients.last_name as p_lastname,
                     patients.full_name as p_fullname,
                     admission_patients.firstname as adm_ap_firstname,
                     admission_patients.lastname as adm_ap_lastname,
                     admission_hms_patients.first_name as adm_p_firstname,
                     admission_hms_patients.last_name as adm_p_lastname,
                     admission_hms_patients.full_name as adm_p_fullname,
                     users.username as completed_by_name, 
                     nurse_users.username as nurse_name')
            ->join('admin_patients', 'admin_patients.id = doctor_orders.patient_id', 'left')
            ->join('patients', 'patients.patient_id = doctor_orders.patient_id', 'left')
            ->join('admissions', 'admissions.id = doctor_orders.admission_id', 'left')
            ->join('admin_patients as admission_patients', 'admission_patients.id = admissions.patient_id', 'left')
            ->join('patients as admission_hms_patients', 'admission_hms_patients.patient_id = admissions.patient_id', 'left')
            ->join('users', 'users.id = doctor_orders.completed_by', 'left')
            ->join('users as nurse_users', 'nurse_users.id = doctor_orders.nurse_id', 'left')
            ->where('doctor_orders.doctor_id', $doctorId)
            ->orderBy('doctor_orders.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Process orders to get patient names from multiple sources
        $allOrders = [];
        foreach ($allOrdersRaw as $order) {
            // Get firstname from various sources
            $firstname = $order['ap_firstname'] ?? 
                        $order['p_firstname'] ?? 
                        $order['adm_ap_firstname'] ?? 
                        $order['adm_p_firstname'] ?? 
                        null;
            
            // If no firstname found, try to extract from full_name
            if (empty($firstname)) {
                $fullName = $order['p_fullname'] ?? $order['adm_p_fullname'] ?? null;
                if (!empty($fullName)) {
                    $nameParts = explode(' ', trim($fullName), 2);
                    $firstname = $nameParts[0] ?? null;
                }
            }
            
            // Get lastname from various sources
            $lastname = $order['ap_lastname'] ?? 
                       $order['p_lastname'] ?? 
                       $order['adm_ap_lastname'] ?? 
                       $order['adm_p_lastname'] ?? 
                       null;
            
            // If no lastname found, try to extract from full_name
            if (empty($lastname)) {
                $fullName = $order['p_fullname'] ?? $order['adm_p_fullname'] ?? null;
                if (!empty($fullName)) {
                    $nameParts = explode(' ', trim($fullName), 2);
                    $lastname = $nameParts[1] ?? null;
                }
            }
            
            // Add firstname and lastname to order array
            $order['firstname'] = $firstname ?? 'Unknown';
            $order['lastname'] = $lastname ?? 'Patient';
            
            // Remove temporary fields
            unset($order['ap_firstname'], $order['ap_lastname'], 
                  $order['p_firstname'], $order['p_lastname'], $order['p_fullname'],
                  $order['adm_ap_firstname'], $order['adm_ap_lastname'],
                  $order['adm_p_firstname'], $order['adm_p_lastname'], $order['adm_p_fullname']);
            
            $allOrders[] = $order;
        }

        // Get orders by status
        $pendingOrders = array_filter($allOrders, fn($order) => $order['status'] === 'pending');
        $inProgressOrders = array_filter($allOrders, fn($order) => $order['status'] === 'in_progress');
        $completedOrders = array_filter($allOrders, fn($order) => $order['status'] === 'completed');
        $cancelledOrders = array_filter($allOrders, fn($order) => $order['status'] === 'cancelled');

        // Check if there's a consultation_id in query string and if it's marked for admission
        $consultationId = $this->request->getGet('consultation_id');
        $consultation = null;
        $showAdmitInfo = false;
        
        if ($consultationId) {
            $db = \Config\Database::connect();
            $consultation = $db->table('consultations')
                ->where('id', $consultationId)
                ->where('doctor_id', $doctorId)
                ->get()
                ->getRowArray();
            
            if ($consultation && !empty($consultation['for_admission'])) {
                // Check if not already admitted
                $existingAdmission = $db->table('admissions')
                    ->where('consultation_id', $consultationId)
                    ->where('status !=', 'discharged')
                    ->where('status !=', 'cancelled')
                    ->where('deleted_at', null)
                    ->get()
                    ->getRowArray();
                
                $showAdmitInfo = !$existingAdmission;
            }
        }

        $data = [
            'title' => 'Doctor Orders',
            'allOrders' => $allOrders,
            'pendingOrders' => $pendingOrders,
            'inProgressOrders' => $inProgressOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'consultation' => $consultation,
            'showAdmitInfo' => $showAdmitInfo,
        ];

        return view('doctor/orders/index', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $patientModel = new AdminPatientModel();
        $db = \Config\Database::connect();

        // Get assigned patients from admin_patients table
        $adminPatientsRaw = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();
        
        // Format admin patients
        $adminPatients = [];
        foreach ($adminPatientsRaw as $patient) {
            $adminPatients[] = [
                'id' => $patient['id'],
                'patient_id' => $patient['id'],
                'firstname' => $patient['firstname'] ?? '',
                'lastname' => $patient['lastname'] ?? '',
                'full_name' => ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''),
                'source' => 'admin_patients',
            ];
        }

        // Get patients from patients table (receptionist-registered patients)
        $hmsPatients = [];
        if ($db->tableExists('patients') && $db->tableExists('admin_patients')) {
            $hmsPatientsRaw = $db->table('patients')
                ->select('patients.*')
                ->where('patients.doctor_id', $doctorId)
                ->where('patients.doctor_id IS NOT NULL')
                ->where('patients.doctor_id !=', 0)
                ->orderBy('patients.last_name', 'ASC')
                ->orderBy('patients.first_name', 'ASC')
                ->get()
                ->getResultArray();
            
            // Format hmsPatients to match admin_patients structure
            foreach ($hmsPatientsRaw as $patient) {
                $nameParts = [];
                if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                
                // If no first_name/last_name, parse full_name
                if (empty($nameParts) && !empty($patient['full_name'])) {
                    $parts = explode(' ', $patient['full_name'], 2);
                    $nameParts = [
                        $parts[0] ?? '',
                        $parts[1] ?? ''
                    ];
                }
                
                // Find corresponding admin_patients record
                // When receptionist assigns doctor, it creates/updates admin_patients record
                $adminPatientId = null;
                
                // Strategy 1: Try exact match with doctor_id
                $adminPatient = $db->table('admin_patients')
                    ->where('firstname', $nameParts[0] ?? '')
                    ->where('lastname', $nameParts[1] ?? '')
                    ->where('doctor_id', $doctorId)
                    ->where('deleted_at IS NULL', null, false) // Exclude soft-deleted
                    ->get()
                    ->getRowArray();
                
                if ($adminPatient) {
                    $adminPatientId = $adminPatient['id'];
                } else {
                    // Strategy 2: Try without doctor_id constraint (in case doctor was reassigned)
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $nameParts[0] ?? '')
                        ->where('lastname', $nameParts[1] ?? '')
                        ->where('deleted_at IS NULL', null, false) // Exclude soft-deleted
                        ->get()
                        ->getRowArray();
                    
                    if ($adminPatient) {
                        $adminPatientId = $adminPatient['id'];
                        // Update doctor_id to current doctor
                        $db->table('admin_patients')
                            ->where('id', $adminPatientId)
                            ->update(['doctor_id' => $doctorId, 'updated_at' => date('Y-m-d H:i:s')]);
                    } else {
                        // Strategy 3: Try with LIKE matching
                        $adminPatient = $db->table('admin_patients')
                            ->like('firstname', $nameParts[0] ?? '', 'after')
                            ->like('lastname', $nameParts[1] ?? '', 'after')
                            ->where('doctor_id', $doctorId)
                            ->where('deleted_at IS NULL', null, false) // Exclude soft-deleted
                            ->get()
                            ->getRowArray();
                        
                        if ($adminPatient) {
                            $adminPatientId = $adminPatient['id'];
                        } else {
                            // Create admin_patients record if it doesn't exist
                            $adminPatientData = [
                                'firstname' => $nameParts[0] ?? '',
                                'lastname' => $nameParts[1] ?? '',
                                'birthdate' => $patient['date_of_birth'] ?? null,
                                'gender' => strtolower($patient['gender'] ?? 'other'),
                                'contact' => $patient['contact'] ?? null,
                                'address' => $patient['address'] ?? null,
                                'doctor_id' => $doctorId,
                                'visit_type' => $patient['visit_type'] ?? null, // Include visit_type
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            
                            try {
                                $db->table('admin_patients')->insert($adminPatientData);
                                $adminPatientId = $db->insertID();
                                
                                if (empty($adminPatientId)) {
                                    log_message('error', "OrderController::create() - Failed to create admin_patients record. Patient: " . json_encode($patient));
                                    // Continue anyway - the store() method will handle it
                                }
                            } catch (\Exception $e) {
                                log_message('error', "OrderController::create() - Exception creating admin_patients: " . $e->getMessage());
                                // Continue anyway - the store() method will handle it
                            }
                        }
                    }
                }
                
                // Only add to list if admin_patients record exists or was successfully created
                if (!empty($adminPatientId)) {
                    $hmsPatients[] = [
                        'id' => $adminPatientId, // Use admin_patients.id for doctor_orders
                        'patient_id' => $patient['patient_id'] ?? $patient['id'] ?? null, // Keep original for reference
                        'admin_patient_id' => $adminPatientId, // Explicit admin_patients.id
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'full_name' => $patient['full_name'] ?? implode(' ', $nameParts),
                        'source' => 'receptionist',
                    ];
                } else {
                    // Log warning but don't fail - the store() method will handle creating the record
                    log_message('warning', "OrderController::create() - Could not find/create admin_patients for patient_id: " . ($patient['patient_id'] ?? 'unknown'));
                }
            }
        }
        
        // Merge both patient lists
        $merged = array_merge($adminPatients, $hmsPatients);
        
        // Deduplicate: If same patient exists in both tables (same name + doctor_id), keep only admin_patients version
        $patients = [];
        $seenKeys = [];
        
        foreach ($merged as $patient) {
            // Create a unique key based on name (case-insensitive) and doctor_id
            $nameKey = strtolower(trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')));
            $key = md5($nameKey . '|' . $doctorId);
            
            // If we've seen this patient before, prefer admin_patients version (source = 'admin_patients')
            if (isset($seenKeys[$key])) {
                // If current patient is from admin_patients and previous was from receptionist, replace it
                $currentSource = $patient['source'] ?? 'admin_patients';
                $prevSource = $seenKeys[$key]['source'] ?? 'admin_patients';
                if ($currentSource === 'admin_patients' && $prevSource === 'receptionist') {
                    $patients[$seenKeys[$key]['index']] = $patient;
                    $seenKeys[$key] = ['index' => $seenKeys[$key]['index'], 'source' => $currentSource];
                }
                // Otherwise, skip this duplicate
                continue;
            }
            
            // Add to deduplicated list
            $index = count($patients);
            $patients[] = $patient;
            $seenKeys[$key] = ['index' => $index, 'source' => $patient['source'] ?? 'admin_patients'];
        }
        
        // Re-index array
        $patients = array_values($patients);
        
        // Sort by lastname, then firstname
        usort($patients, function($a, $b) {
            $lastnameCompare = strcasecmp($a['lastname'] ?? '', $b['lastname'] ?? '');
            if ($lastnameCompare !== 0) {
                return $lastnameCompare;
            }
            return strcasecmp($a['firstname'] ?? '', $b['firstname'] ?? '');
        });

        // Get all active nurses
        // Only show nurses who have schedules
        $nurses = $this->getNursesWithSchedules();

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

        // Get IV Fluids from pharmacy (category = "IV Fluids / Electrolytes")
        $ivFluids = [];
        if ($db->tableExists('pharmacy')) {
            $ivFluids = $db->table('pharmacy')
                ->where('category', 'IV Fluids / Electrolytes')
                ->where('status', 'active')
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get all active lab tests from lab_tests table (exclude soft-deleted and deduplicate)
        $labTests = [];
        $labTestModel = new LabTestModel();
        if ($db->tableExists('lab_tests')) {
            $labTestsRaw = $labTestModel
                ->where('is_active', 1)
                ->where('deleted_at', null) // Explicitly exclude soft-deleted records
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

        // Get patient_id and vital_id from query string if provided (e.g., from consultation redirect or vital signs history)
        $selectedPatientId = $this->request->getGet('patient_id');
        $selectedOrderType = $this->request->getGet('order_type'); // Optional: pre-select order type
        $selectedVitalId = $this->request->getGet('vital_id'); // Link order to vital record

        $data = [
            'title' => 'Create Medical Order',
            'patients' => $patients,
            'nurses' => $nurses,
            'medicines' => $medicines,
            'labTests' => $labTests, // Pass lab tests to view
            'ivFluids' => $ivFluids, // Pass IV Fluids to view
            'selected_patient_id' => $selectedPatientId, // Pass to view for pre-selection
            'selected_order_type' => $selectedOrderType, // Pass to view for pre-selection
            'selected_vital_id' => $selectedVitalId, // Pass to view for linking
        ];

        return view('doctor/orders/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();
        $nurseNotificationModel = new NurseNotificationModel();
        $db = \Config\Database::connect();

        // Get order types as array (multi-select)
        $orderTypes = $this->request->getPost('order_type');
        if (!is_array($orderTypes)) {
            $orderTypes = [$orderTypes];
        }
        $orderTypes = array_filter($orderTypes); // Remove empty values
        
        // Validation rules
        $rules = [
            'patient_id' => 'required|integer|greater_than[0]',
            'order_type' => 'required',
        ];
        
        // Nurse ID is only required for certain order types (will be validated per order type)

        // Validate each order type
        $validOrderTypes = ['medication', 'lab_test', 'iv_fluids_order', 'reassessment_order', 'diet', 'activity', 'other'];
        foreach ($orderTypes as $type) {
            if (!in_array($type, $validOrderTypes)) {
                return redirect()->back()->withInput()->with('error', 'Invalid order type selected: ' . $type);
            }
        }

        // Note: Medication-specific validation is done later when processing order_details
        // because medication fields are nested in order_details[index][medicines][drugName][...]
        
        $validation = $this->validate($rules);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        if (empty($orderTypes)) {
            return redirect()->back()->withInput()->with('error', 'Please select at least one order type.');
        }

        // Get submitted patient_id and convert to admin_patients.id if needed
        $submittedPatientId = (int)$this->request->getPost('patient_id');
        $adminPatientId = $submittedPatientId;
        
        // Check if patient_id exists in admin_patients
        $adminPatientModel = new AdminPatientModel();
        $adminPatient = $adminPatientModel->find($submittedPatientId);
        
        if (!$adminPatient) {
            // Patient not found in admin_patients
            // The dropdown should always have admin_patients.id, but if it doesn't exist,
            // we need to find the original patient and create/find the admin_patients record
            
            // First, check if this ID might be a patient_id from patients table
            // (This shouldn't happen if dropdown is correct, but handle it anyway)
            if ($db->tableExists('patients')) {
                // Try to find in patients table by patient_id
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $submittedPatientId)
                    ->where('doctor_id', $doctorId) // Must be assigned to this doctor
                    ->get()
                    ->getRowArray();
                
                if (!$hmsPatient) {
                    // Also try without doctor_id check (in case doctor was reassigned)
                    $hmsPatient = $db->table('patients')
                        ->where('patient_id', $submittedPatientId)
                        ->get()
                        ->getRowArray();
                }
                
                if ($hmsPatient) {
                    // Extract name parts
                    $nameParts = [];
                    if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                    if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                    
                    if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                        $parts = explode(' ', $hmsPatient['full_name'], 2);
                        $nameParts = [
                            $parts[0] ?? '',
                            $parts[1] ?? ''
                        ];
                    }
                    
                    // Find admin_patients record - try multiple matching strategies
                    $existingAdminPatient = null;
                    
                    // Strategy 1: Exact match with doctor_id
                    if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                        $existingAdminPatient = $db->table('admin_patients')
                            ->where('firstname', $nameParts[0])
                            ->where('lastname', $nameParts[1])
                            ->where('doctor_id', $doctorId)
                            ->get()
                            ->getRowArray();
                    }
                    
                    // Strategy 2: Try without doctor_id constraint (in case doctor was reassigned)
                    if (!$existingAdminPatient && !empty($nameParts[0]) && !empty($nameParts[1])) {
                        $existingAdminPatient = $db->table('admin_patients')
                            ->where('firstname', $nameParts[0])
                            ->where('lastname', $nameParts[1])
                            ->get()
                            ->getRowArray();
                    }
                    
                    // Strategy 3: Try with LIKE matching (for slight name variations)
                    if (!$existingAdminPatient && !empty($nameParts[0])) {
                        $existingAdminPatient = $db->table('admin_patients')
                            ->like('firstname', $nameParts[0], 'after')
                            ->like('lastname', $nameParts[1] ?? '', 'after')
                            ->where('doctor_id', $doctorId)
                            ->get()
                            ->getRowArray();
                    }
                    
                    if ($existingAdminPatient) {
                        $adminPatientId = $existingAdminPatient['id'];
                        // Update doctor_id and visit_type if needed
                        $updateData = [];
                        if ($existingAdminPatient['doctor_id'] != $doctorId) {
                            $updateData['doctor_id'] = $doctorId;
                        }
                        if (empty($existingAdminPatient['visit_type']) && !empty($hmsPatient['visit_type'])) {
                            $updateData['visit_type'] = $hmsPatient['visit_type'];
                        }
                        if (!empty($updateData)) {
                            $updateData['updated_at'] = date('Y-m-d H:i:s');
                            $db->table('admin_patients')
                                ->where('id', $adminPatientId)
                                ->update($updateData);
                        }
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
                            'visit_type' => $hmsPatient['visit_type'] ?? null, // Include visit_type from patients table
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        try {
                            $db->table('admin_patients')->insert($adminPatientData);
                            $adminPatientId = $db->insertID();
                            
                            if (empty($adminPatientId)) {
                                // Check for database errors
                                $error = $db->error();
                                log_message('error', "OrderController: Failed to create admin_patients record for patient_id: {$submittedPatientId}. DB Error: " . json_encode($error));
                                
                                // Try one more time with minimal data
                                $minimalData = [
                                    'firstname' => $nameParts[0] ?? 'Unknown',
                                    'lastname' => $nameParts[1] ?? 'Patient',
                                    'doctor_id' => $doctorId,
                                    'visit_type' => $hmsPatient['visit_type'] ?? null, // Include visit_type
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $db->table('admin_patients')->insert($minimalData);
                                $adminPatientId = $db->insertID();
                                
                                if (empty($adminPatientId)) {
                                    return redirect()->back()->withInput()->with('error', 'Failed to create patient record. Please refresh the page and try again, or contact support.');
                                }
                            }
                        } catch (\Exception $e) {
                            log_message('error', "OrderController: Exception creating admin_patients record: " . $e->getMessage());
                            return redirect()->back()->withInput()->with('error', 'Failed to create patient record: ' . $e->getMessage());
                        }
                    }
                } else {
                    // Patient not found in patients table either
                    // Check if it's a soft-deleted admin_patients record
                    $deletedPatient = $db->table('admin_patients')
                        ->where('id', $submittedPatientId)
                        ->where('deleted_at IS NOT NULL', null, false)
                        ->get()
                        ->getRowArray();
                    
                    if ($deletedPatient) {
                        log_message('error', "OrderController: Patient ID {$submittedPatientId} is soft-deleted in admin_patients");
                        return redirect()->back()->withInput()->with('error', 'This patient record has been deleted. Please select a different patient.');
                    }
                    
                    // Last resort: Check if admin_patients record exists but was filtered out
                    $checkAdminPatient = $db->table('admin_patients')
                        ->where('id', $submittedPatientId)
                        ->get()
                        ->getRowArray();
                    
                    if ($checkAdminPatient) {
                        // Record exists but might not be assigned to this doctor
                        $adminPatientId = $checkAdminPatient['id'];
                        // Update doctor_id to current doctor
                        $db->table('admin_patients')
                            ->where('id', $adminPatientId)
                            ->update(['doctor_id' => $doctorId, 'updated_at' => date('Y-m-d H:i:s')]);
                    } else {
                        // Log for debugging
                        log_message('error', "OrderController: Patient ID {$submittedPatientId} not found in patients or admin_patients tables. Doctor ID: {$doctorId}");
                        return redirect()->back()->withInput()->with('error', 'Patient not found. Please refresh the page and select a valid patient from the dropdown. If the problem persists, please contact support.');
                    }
                }
            } else {
                log_message('error', "OrderController: Patient ID {$submittedPatientId} not found in admin_patients and patients table doesn't exist");
                return redirect()->back()->withInput()->with('error', 'Patient not found. Please select a valid patient from the dropdown.');
            }
        } else {
            // Patient found in admin_patients - verify it's assigned to this doctor
            if (($adminPatient['doctor_id'] ?? null) != $doctorId) {
                // Update doctor_id if different (doctor was reassigned) or if null
                $adminPatientModel->update($adminPatientId, [
                    'doctor_id' => $doctorId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        // Final verification: Ensure adminPatientId is valid before proceeding
        if (empty($adminPatientId) || $adminPatientId <= 0) {
            log_message('error', "OrderController: Invalid adminPatientId after lookup. Submitted ID: {$submittedPatientId}, Doctor ID: {$doctorId}");
            return redirect()->back()->withInput()->with('error', 'Invalid patient ID. Please refresh the page and select a valid patient from the dropdown.');
        }
        
        // Double-check that the admin_patients record actually exists
        $finalCheck = $adminPatientModel->find($adminPatientId);
        if (!$finalCheck) {
            log_message('error', "OrderController: admin_patients record {$adminPatientId} does not exist after lookup. Submitted ID: {$submittedPatientId}");
            return redirect()->back()->withInput()->with('error', 'Patient record not found in database. Please refresh the page and try again.');
        }

        // Get vital_id from POST or query string (for orders created from vital signs history)
        $vitalId = $this->request->getPost('vital_id');
        if (empty($vitalId)) {
            $vitalId = $this->request->getGet('vital_id');
        }
        // Also check old input in case of validation errors
        if (empty($vitalId)) {
            $vitalId = old('vital_id');
        }
        
        // BACKEND VALIDATION: If patient status is 'pending_order', require vital_id
        if ($finalCheck && ($finalCheck['doctor_check_status'] ?? 'available') === 'pending_order') {
            if (empty($vitalId)) {
                // Check if there's a vital_id in the referrer URL (in case user came from vital signs history)
                $referrer = $this->request->getServer('HTTP_REFERER');
                if ($referrer && preg_match('/vital_id=(\d+)/', $referrer, $matches)) {
                    $vitalId = $matches[1];
                } else {
                    return redirect()->back()->withInput()->with('error', 'You must create an order from the Vital Signs History. Please go to the patient details page and click "Create Order" on a vital signs record.');
                }
            }
            
            // Verify the vital_id belongs to this patient
            if ($db->tableExists('patient_vitals') && !empty($vitalId)) {
                $vitalRecord = $db->table('patient_vitals')
                    ->where('id', $vitalId)
                    ->where('patient_id', $adminPatientId)
                    ->get()
                    ->getRowArray();
                
                if (!$vitalRecord) {
                    return redirect()->back()->withInput()->with('error', 'Invalid vital signs record. Please create an order from the Vital Signs History in the patient details page.');
                }
            }
        }
        
        // Get common fields
        // Instructions and remarks fields removed from form
        // Note: nurse_id is now handled per order type:
        // - Medication orders: Get from order_details (if needed)
        // - Lab test orders: Get from order_details for "with specimen" tests
        // - Other orders: May need nurse_id, will be handled individually
        $orderDetails = $this->request->getPost('order_details') ?? [];
        $orderTypeMapping = $this->request->getPost('order_type_mapping') ?? [];
        
        // Create orders for each selected order type
        $createdOrderIds = [];
        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($adminPatientId);
        $patientName = $patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient';
        $doctorName = session()->get('name') ?? session()->get('username');
        
        foreach ($orderTypes as $orderType) {
            // Find the correct index for this order type using order_type_mapping
            $detailsIndex = null;
            if (!empty($orderTypeMapping)) {
                // Find which index in order_details corresponds to this order type
                foreach ($orderTypeMapping as $idx => $mappedType) {
                    if ($mappedType === $orderType) {
                        $detailsIndex = $idx;
                        break;
                    }
                }
            }
            
            // If mapping not found, try to find by matching order type structure in order_details
            if ($detailsIndex === null) {
                // Try to find the first order_details entry that matches this order type
                // by checking if it has the expected structure
                foreach ($orderDetails as $idx => $detail) {
                    // For medication, check if it has drug_names
                    if ($orderType === 'medication' && isset($detail['drug_names'])) {
                        $detailsIndex = $idx;
                        break;
                    }
                    // For lab_test, check if it has test_names
                    elseif ($orderType === 'lab_test' && isset($detail['test_names'])) {
                        $detailsIndex = $idx;
                        break;
                    }
                    // For other types, use first available index that doesn't have drug_names or test_names
                    elseif ($orderType !== 'medication' && $orderType !== 'lab_test' && !isset($detail['drug_names']) && !isset($detail['test_names']) && !empty($detail)) {
                        $detailsIndex = $idx;
                        break;
                    }
                }
            }
            
            // If still not found, use first available index (fallback)
            if ($detailsIndex === null && !empty($orderDetails)) {
                $detailsIndex = array_key_first($orderDetails);
            }
            
            $details = ($detailsIndex !== null) ? ($orderDetails[$detailsIndex] ?? []) : [];
            
            // Build order description from order type specific fields
            $orderDescription = $this->buildOrderDescription($orderType, $details);
            
            // Get nurse_id per order type (if needed)
            // For medication orders: nurse_id is optional (can be null)
            // For lab test orders: nurse_id is handled in lab test section
            // For other orders: Get from patient's assigned_nurse_id if available
            $orderNurseId = null;
            
            // Get assigned nurse from patient record (for non-medication, non-lab orders)
            if ($orderType !== 'medication' && $orderType !== 'lab_test' && $patient) {
                $orderNurseId = $patient['assigned_nurse_id'] ?? null;
            }
            
            $data = [
                'patient_id' => $adminPatientId,
                'doctor_id' => $doctorId,
                'nurse_id' => $orderNurseId, // Will be set per order type if needed
                'order_type' => $orderType,
                'order_description' => $orderDescription,
                'instructions' => $details['special_instructions'] ?? $details['instructions'] ?? $details['notes'] ?? '',
                'frequency' => $details['frequency'] ?? null,
                'start_date' => $this->request->getPost('start_date') ?: null,
                'end_date' => $this->request->getPost('end_date') ?: null,
                'status' => 'pending',
                'vital_id' => $vitalId ? (int)$vitalId : null,
            ];

            // Handle medication orders with multiple medicine selection
            if ($orderType === 'medication') {
                $drugNames = $details['drug_names'] ?? [];
                if (!is_array($drugNames)) {
                    $drugNames = [$drugNames];
                }
                $drugNames = array_filter($drugNames); // Remove empty values
                
                // Get individual medicine dosage data
                $medicinesData = $details['medicines'] ?? [];
                
                // Validation for medication
                if (empty($drugNames)) {
                    return redirect()->back()->withInput()->with('error', 'Please select at least one medicine for the medication order.');
                }
                
                // VALIDATE ALL MEDICINES FIRST before creating any orders (prevent partial creation)
                $validationErrors = [];
                foreach ($drugNames as $drugName) {
                    $medicineDosage = $medicinesData[$drugName] ?? [];
                    if (empty($medicineDosage['dosage']) || empty($medicineDosage['duration'])) {
                        $validationErrors[] = 'Please fill in all required fields (Dosage, Duration) for medicine: ' . $drugName;
                    }
                }
                
                // If any validation errors, return immediately without creating any orders
                if (!empty($validationErrors)) {
                    return redirect()->back()->withInput()->with('error', implode(' | ', $validationErrors));
                }
                
                // Start database transaction to ensure all-or-nothing creation
                $db->transStart();
                
                try {
                    // Create a separate order for each selected medicine
                    foreach ($drugNames as $drugName) {
                        // Get dosage data for this specific medicine
                        $medicineDosage = $medicinesData[$drugName] ?? [];
                        
                        $medicationData = $data;
                        $medicationData['medicine_name'] = $drugName;
                        $medicationData['dosage'] = $medicineDosage['dosage'] ?? '';
                        $medicationData['duration'] = $medicineDosage['duration'] ?? '';
                        $medicationData['remarks'] = 'Route: ' . ($medicineDosage['route'] ?? 'N/A');
                        $medicationData['pharmacy_status'] = 'pending'; // Auto-route to Pharmacy
                        $medicationData['order_description'] = $drugName . ' - ' . ($medicineDosage['dosage'] ?? 'N/A') . ' ' . ($medicineDosage['frequency'] ?? 'N/A') . ' ' . ($medicineDosage['route'] ?? 'N/A') . ' for ' . ($medicineDosage['duration'] ?? 'N/A');
                        
                        if (!$orderModel->insert($medicationData)) {
                            throw new \Exception('Failed to create order for medicine: ' . $drugName);
                        }
                        
                        $orderId = $orderModel->getInsertID();
                        $createdOrderIds[] = $orderId;

                        // Log initial status with timestamp
                        if (!$logModel->insert([
                            'order_id' => $orderId,
                            'status' => 'pending',
                            'changed_by' => $doctorId,
                            'notes' => 'Order created by doctor at ' . date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                        ])) {
                            throw new \Exception('Failed to create order log for order: ' . $orderId);
                        }

                        // Notify Pharmacy instantly
                        if ($db->tableExists('pharmacy_notifications')) {
                            if (!$db->table('pharmacy_notifications')->insert([
                                'type' => 'new_medication_order',
                                'title' => 'New Medication Order',
                                'message' => 'Dr. ' . $doctorName . ' has created a medication order for ' . $patientName . '. Medicine: ' . $drugName,
                                'related_id' => $orderId,
                                'related_type' => 'doctor_order',
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ])) {
                                throw new \Exception('Failed to create pharmacy notification for order: ' . $orderId);
                            }
                        }
                    }
                    
                    // Commit transaction if all orders created successfully
                    $db->transComplete();
                    
                    if ($db->transStatus() === false) {
                        throw new \Exception('Transaction failed while creating medication orders');
                    }
                    
                } catch (\Exception $e) {
                    // Rollback transaction on any error
                    $db->transRollback();
                    log_message('error', 'Failed to create medication orders: ' . $e->getMessage());
                    return redirect()->back()->withInput()->with('error', 'Failed to create medication orders: ' . $e->getMessage());
                }
                
                continue; // Skip the default order creation below for medication
            }
            
            // Handle lab test orders with multiple test selection
            if ($orderType === 'lab_test') {
                $testNames = $details['test_names'] ?? [];
                if (!is_array($testNames)) {
                    $testNames = [$testNames];
                }
                $testNames = array_filter($testNames); // Remove empty values
                
                // Validation for lab test
                if (empty($testNames)) {
                    return redirect()->back()->withInput()->with('error', 'Please select at least one lab test for the laboratory test request.');
                }
                if (empty($details['priority'])) {
                    return redirect()->back()->withInput()->with('error', 'Please select a priority for the lab test request.');
                }
                
                // Get nurse_id from lab test details (for "with specimen" tests)
                $labTestNurseId = $details['nurse_id'] ?? null;
                
                // Create a separate order for each selected lab test
                $labRequestModel = new LabRequestModel();
                $labTestModel = new LabTestModel();
                
                // Check if any selected test requires specimen collection
                $hasWithSpecimen = false;
                foreach ($testNames as $testName) {
                    $testDetails = $labTestModel
                        ->where('test_name', $testName)
                        ->where('is_active', 1)
                        ->where('deleted_at', null) // Exclude soft-deleted records
                        ->first();
                    
                    // Only check if test was found
                    if ($testDetails) {
                        $specimenCategory = $testDetails['specimen_category'] ?? 'without_specimen'; // Default to without_specimen if not set
                        if ($specimenCategory === 'with_specimen') {
                            $hasWithSpecimen = true;
                            break;
                        }
                    }
                }
                
                // Validate nurse_id is provided ONLY if any "with specimen" test is selected
                // If all tests are "without_specimen", nurse_id is not required
                if ($hasWithSpecimen && empty($labTestNurseId)) {
                    return redirect()->back()->withInput()->with('error', 'Please assign a nurse for specimen collection. At least one selected test requires specimen collection.');
                }
                
                foreach ($testNames as $testName) {
                    $labTestData = $data;
                    $labTestData['order_description'] = 'Lab Test: ' . $testName . ' (Priority: ' . ($details['priority'] ?? 'Routine') . ')';
                    $labTestData['remarks'] = 'Priority: ' . ($details['priority'] ?? 'Routine');
                    // Set nurse_id to null for lab test orders (nurse assignment is handled in lab_request)
                    $labTestData['nurse_id'] = null;
                    
                    if ($orderModel->insert($labTestData)) {
                        $orderId = $orderModel->getInsertID();
                        $createdOrderIds[] = $orderId;

                        // Log initial status with timestamp
                        $logModel->insert([
                            'order_id' => $orderId,
                            'status' => 'pending',
                            'changed_by' => $doctorId,
                            'notes' => 'Order created by doctor at ' . date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);

                        // Create corresponding lab_request record so it appears in lab staff views
                        // Get test details from lab_tests table
                        $testDetails = $labTestModel
                            ->where('test_name', $testName)
                            ->where('is_active', 1)
                            ->where('deleted_at', null) // Exclude soft-deleted records
                            ->first();
                        
                        $testType = $testDetails['test_type'] ?? 'Other';
                        $specimenCategory = $testDetails['specimen_category'] ?? 'without_specimen'; // Default to without_specimen if not set
                        $requiresSpecimen = ($specimenCategory === 'with_specimen');
                        
                        // Prepare priority - convert 'stat' to 'stat', 'routine' to 'routine', etc.
                        $priority = strtolower($details['priority'] ?? 'routine');
                        if ($priority === 'stat') {
                            $priority = 'stat';
                        } elseif ($priority === 'urgent') {
                            $priority = 'urgent';
                        } else {
                            $priority = 'routine';
                        }
                        
                        // Determine nurse_id: only assign if test requires specimen
                        $assignedNurseId = null;
                        if ($requiresSpecimen) {
                            $assignedNurseId = $labTestNurseId;
                        }
                        
                        // Create lab_request record
                        $labRequestData = [
                            'patient_id' => $adminPatientId,
                            'doctor_id' => $doctorId,
                            'nurse_id' => $assignedNurseId, // Only assign nurse if specimen is needed
                            'test_type' => $testType,
                            'test_name' => $testName,
                            'requested_by' => 'doctor',
                            'priority' => $priority,
                            'instructions' => 'Doctor Order #' . $orderId . ' | LINK:' . json_encode(['doctor_order_id' => $orderId]) . ' | SPECIMEN_CATEGORY:' . $specimenCategory,
                            'status' => 'pending',
                            'requested_date' => date('Y-m-d'),
                            'payment_status' => 'pending', // Will be processed by accountant
                            'charge_id' => null, // Will be set when accountant processes payment
                        ];
                        
                        // Insert lab_request (skip validation for nurse_id if not required)
                        if ($labRequestModel->insert($labRequestData)) {
                            $labRequestId = $labRequestModel->getInsertID();
                            log_message('info', "Lab request #{$labRequestId} created from doctor order #{$orderId} for test: {$testName} (Specimen: " . ($requiresSpecimen ? 'Yes' : 'No') . ")");
                            
                            // Automatically create charge for lab test - goes to patient billing
                            $chargeId = null;
                            $chargeModel = new ChargeModel();
                            $billingItemModel = new BillingItemModel();
                            $db = \Config\Database::connect();
                            
                            // Get lab test price
                            $testPrice = (float)($testDetails['price'] ?? 0);
                            if ($testPrice <= 0) {
                                $testPrice = 300.00; // Default price if not set
                            }
                            
                            // Generate charge number
                            $chargeNumber = $chargeModel->generateChargeNumber();
                            
                            // Create charge record - automatically goes to patient billing
                            $chargeData = [
                                'patient_id' => $adminPatientId,
                                'charge_number' => $chargeNumber,
                                'total_amount' => $testPrice,
                                'status' => 'pending', // Will be updated to 'approved' or 'paid' by accountant
                                'notes' => 'Lab Test: ' . $testName . ' (Doctor Order #' . $orderId . ')',
                            ];
                            
                            if ($chargeModel->insert($chargeData)) {
                                $chargeId = $chargeModel->getInsertID();
                                
                                // Create billing item
                                $billingItemData = [
                                    'charge_id' => $chargeId,
                                    'item_type' => 'lab_test',
                                    'item_name' => $testName,
                                    'description' => 'Lab Test: ' . $testType . ' - ' . $testName . ' (Priority: ' . ucfirst($priority) . ')',
                                    'quantity' => 1.00,
                                    'unit_price' => $testPrice,
                                    'total_price' => $testPrice,
                                    'related_id' => $labRequestId,
                                    'related_type' => 'lab_request',
                                ];
                                
                                if ($billingItemModel->insert($billingItemData)) {
                                    log_message('info', "Billing item created for lab request #{$labRequestId}, charge #{$chargeId}");
                                } else {
                                    log_message('error', "Failed to create billing item for lab request #{$labRequestId}. Errors: " . json_encode($billingItemModel->errors()));
                                }
                                
                                // Update lab_request with charge_id
                                $labRequestModel->update($labRequestId, [
                                    'charge_id' => $chargeId,
                                    'payment_status' => 'pending', // Will be updated by accountant
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                                
                                log_message('info', "Charge #{$chargeId} created for lab test: {$testName} - Amount: ₱{$testPrice} - Goes to patient billing");
                            } else {
                                log_message('error', "Failed to create charge for lab request #{$labRequestId}. Errors: " . json_encode($chargeModel->errors()));
                            }
                            
                            // For "without specimen" tests: go directly to lab staff (status remains 'pending', no nurse_id)
                            // For "with specimen" tests: nurse must collect specimen first (status remains 'pending', has nurse_id)
                            
                            // Create notification for nurse ONLY if test requires specimen
                            if ($requiresSpecimen && $assignedNurseId) {
                                $nurseNotificationModel->insert([
                                    'nurse_id' => $assignedNurseId,
                                    'type' => 'new_doctor_order',
                                    'title' => 'New Laboratory Test Request - Specimen Collection Required',
                                    'message' => 'Dr. ' . $doctorName . ' has created a lab test request for ' . $patientName . '. Test: ' . $testName . ' (Priority: ' . ($details['priority'] ?? 'Routine') . '). Please collect specimen and mark as collected.',
                                    'related_id' => $orderId,
                                    'related_type' => 'doctor_order',
                                    'is_read' => 0,
                                ]);
                            } else if (!$requiresSpecimen) {
                                // For "without specimen" tests: notify lab staff directly (using accountant_notifications as general notification system)
                                if ($db->tableExists('accountant_notifications')) {
                                    $db->table('accountant_notifications')->insert([
                                        'type' => 'new_lab_request',
                                        'title' => 'New Laboratory Test Request - Ready for Testing',
                                        'message' => 'Dr. ' . $doctorName . ' has created a lab test request for ' . $patientName . '. Test: ' . $testName . ' (Priority: ' . ($details['priority'] ?? 'Routine') . '). No specimen collection needed - ready for testing.',
                                        'related_id' => $labRequestId,
                                        'related_type' => 'lab_request',
                                        'is_read' => 0,
                                        'created_at' => date('Y-m-d H:i:s'),
                                    ]);
                                }
                            }
                        } else {
                            log_message('error', "Failed to create lab_request for doctor order #{$orderId}. Errors: " . json_encode($labRequestModel->errors()));
                        }
                    }
                }
                continue; // Skip the default order creation below for lab_test
            }

            // Handle IV Fluids orders with multiple fluid selection
            if ($orderType === 'iv_fluids_order') {
                $fluidNames = $details['fluid_names'] ?? [];
                if (!is_array($fluidNames)) {
                    $fluidNames = [$fluidNames];
                }
                $fluidNames = array_filter($fluidNames); // Remove empty values
                
                // Get individual IV fluid volume/rate data
                $ivFluidsData = $details['iv_fluids'] ?? [];
                
                // Validation for IV fluids
                if (empty($fluidNames)) {
                    return redirect()->back()->withInput()->with('error', 'Please select at least one IV fluid for the IV Fluids order.');
                }
                
                // Create a separate order for each selected IV fluid
                foreach ($fluidNames as $fluidName) {
                    // Get volume/rate data for this specific IV fluid
                    $fluidDetails = $ivFluidsData[$fluidName] ?? [];
                    
                    // Validation for individual IV fluid details
                    if (empty($fluidDetails['volume']) || empty($fluidDetails['rate'])) {
                        return redirect()->back()->withInput()->with('error', 'Please fill in all required fields (Volume, Rate) for IV fluid: ' . $fluidName);
                    }
                    
                    $ivFluidData = $data;
                    $ivFluidData['nurse_id'] = null; // Nurse assignment happens after pharmacy dispenses
                    $ivFluidData['pharmacy_status'] = 'pending'; // Auto-route to Pharmacy
                    $ivFluidData['order_description'] = 'IV Fluid: ' . $fluidName . ' - Volume: ' . ($fluidDetails['volume'] ?? 'N/A') . ', Rate: ' . ($fluidDetails['rate'] ?? 'N/A');
                    $ivFluidData['remarks'] = 'Volume: ' . ($fluidDetails['volume'] ?? 'N/A') . ' | Rate: ' . ($fluidDetails['rate'] ?? 'N/A');
                    
                    if ($orderModel->insert($ivFluidData)) {
                        $orderId = $orderModel->getInsertID();
                        $createdOrderIds[] = $orderId;

                        // Log initial status with timestamp
                        $logModel->insert([
                            'order_id' => $orderId,
                            'status' => 'pending',
                            'changed_by' => $doctorId,
                            'notes' => 'Order created by doctor at ' . date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);

                        // Notify Pharmacy instantly (same as medication orders)
                        if ($db->tableExists('pharmacy_notifications')) {
                            $db->table('pharmacy_notifications')->insert([
                                'type' => 'new_iv_fluids_order',
                                'title' => 'New IV Fluids Order',
                                'message' => 'Dr. ' . $doctorName . ' has created an IV Fluids order for ' . $patientName . '. Fluid: ' . $fluidName . ' (Volume: ' . ($fluidDetails['volume'] ?? 'N/A') . ', Rate: ' . ($fluidDetails['rate'] ?? 'N/A') . ')',
                                'related_id' => $orderId,
                                'related_type' => 'doctor_order',
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }

                        // Note: IV Fluids orders don't notify nurse at creation time
                        // The nurse will be notified later when pharmacy dispenses the IV fluid
                        // This is handled in the pharmacy workflow
                    }
                }
                continue; // Skip the default order creation below for iv_fluids_order
            }

            if ($orderModel->insert($data)) {
                $orderId = $orderModel->getInsertID();
                $createdOrderIds[] = $orderId;

                // Log initial status with timestamp
                $logModel->insert([
                    'order_id' => $orderId,
                    'status' => 'pending',
                    'changed_by' => $doctorId,
                    'notes' => 'Order created by doctor at ' . date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // If medication or IV fluids order, notify Pharmacy instantly
                if ($orderType === 'medication') {
                    if ($db->tableExists('pharmacy_notifications')) {
                        $db->table('pharmacy_notifications')->insert([
                            'type' => 'new_medication_order',
                            'title' => 'New Medication Order',
                            'message' => 'Dr. ' . $doctorName . ' has created a medication order for ' . $patientName . '. Medicine: ' . ($data['medicine_name'] ?? 'N/A'),
                            'related_id' => $orderId,
                            'related_type' => 'doctor_order',
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                // Create notification for nurse (only for non-medication, non-IV-fluids orders)
                // Medication and IV Fluids orders: Nurse will be notified after Pharmacy dispenses
                if ($orderType !== 'medication' && $orderType !== 'iv_fluids_order' && $orderNurseId) {
                    $orderTypeLabel = ucwords(str_replace('_', ' ', $orderType));
                    $nurseNotificationModel->insert([
                        'nurse_id' => $orderNurseId,
                        'type' => 'new_doctor_order',
                        'title' => 'New ' . $orderTypeLabel . ' Order',
                        'message' => 'Dr. ' . $doctorName . ' has created a new ' . $orderTypeLabel . ' order for ' . $patientName . '. Please execute this order.',
                        'related_id' => $orderId,
                        'related_type' => 'doctor_order',
                        'is_read' => 0,
                    ]);
                }
            }
        }
        
        if (!empty($createdOrderIds)) {
            $orderCount = count($createdOrderIds);
            $orderTypeLabels = array_map(function($type) {
                return ucwords(str_replace('_', ' ', $type));
            }, $orderTypes);
            
            $successMessage = $orderCount . ' medical order(s) created successfully: ' . implode(', ', $orderTypeLabels) . '.';
            if (in_array('medication', $orderTypes)) {
                $successMessage .= ' Medication order(s) have been automatically routed to Pharmacy for preparation.';
            }
            if (in_array('iv_fluids_order', $orderTypes)) {
                $successMessage .= ' IV Fluids order(s) have been automatically routed to Pharmacy for preparation.';
            }
            if (!in_array('medication', $orderTypes) && !in_array('iv_fluids_order', $orderTypes)) {
                $successMessage .= ' The assigned nurse has been notified.';
            }

            return redirect()->to('/doctor/orders')->with('success', $successMessage);
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create medical orders.');
        }
    }
    
    /**
     * Build order description from order type specific fields
     */
    private function buildOrderDescription($orderType, $details)
    {
        $description = [];
        
        switch ($orderType) {
            case 'medication':
                $description[] = 'Drug: ' . ($details['drug_name'] ?? 'N/A');
                $description[] = 'Dosage: ' . ($details['dosage'] ?? 'N/A');
                $description[] = 'Frequency: ' . ($details['frequency'] ?? 'N/A');
                $description[] = 'Route: ' . ($details['route'] ?? 'N/A');
                $description[] = 'Duration: ' . ($details['duration'] ?? 'N/A');
                break;
                
            case 'lab_test':
                $description[] = 'Test Name: ' . ($details['test_name'] ?? 'N/A');
                $description[] = 'Priority: ' . strtoupper($details['priority'] ?? 'routine');
                break;
                
            case 'iv_fluids_order':
                // Handle multiple IV fluids (from checkboxes)
                $fluidNames = $details['fluid_names'] ?? [];
                if (!is_array($fluidNames)) {
                    $fluidNames = [$fluidNames];
                }
                $fluidNames = array_filter($fluidNames);
                
                if (!empty($fluidNames)) {
                    $ivFluidsData = $details['iv_fluids'] ?? [];
                    foreach ($fluidNames as $fluidName) {
                        $fluidDetails = $ivFluidsData[$fluidName] ?? [];
                        $description[] = 'IV Fluid: ' . $fluidName;
                        $description[] = 'Volume: ' . ($fluidDetails['volume'] ?? 'N/A');
                        $description[] = 'Rate: ' . ($fluidDetails['rate'] ?? 'N/A');
                    }
                } else {
                    // Fallback for old single selection format
                    $description[] = 'Fluid Type: ' . ucfirst(str_replace('_', ' ', $details['fluid_type'] ?? 'N/A'));
                    $description[] = 'Volume: ' . ($details['volume'] ?? 'N/A');
                    $description[] = 'Rate: ' . ($details['rate'] ?? 'N/A');
                }
                break;
                
            case 'reassessment_order':
                $description[] = 'Next Vitals Time: ' . ($details['next_vitals_time'] ?? 'N/A');
                if (!empty($details['notes'])) {
                    $description[] = 'Notes: ' . $details['notes'];
                }
                break;
                
            default:
                $description[] = 'Order details';
        }
        
        return implode(' | ', $description);
    }

    public function printPrescription($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $db = \Config\Database::connect();

        // Get order with patient and doctor info
        $order = $orderModel
            ->select('doctor_orders.*, admin_patients.firstname, admin_patients.lastname, admin_patients.birthdate, admin_patients.gender, admin_patients.contact, admin_patients.address, users.username as doctor_name')
            ->join('admin_patients', 'admin_patients.id = doctor_orders.patient_id', 'left')
            ->join('users', 'users.id = doctor_orders.doctor_id', 'left')
            ->where('doctor_orders.id', $id)
            ->where('doctor_orders.doctor_id', $doctorId)
            ->where('doctor_orders.order_type', 'medication')
            ->where('doctor_orders.purchase_location', 'outside')
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Prescription not found or not available for printing.');
        }

        // Get consultation info if available
        $consultation = null;
        if ($db->tableExists('consultations')) {
            $consultation = $db->table('consultations')
                ->where('patient_id', $order['patient_id'])
                ->where('doctor_id', $doctorId)
                ->where('type', 'completed')
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getRowArray();
        }

        $data = [
            'title' => 'Prescription',
            'order' => $order,
            'consultation' => $consultation,
        ];

        return view('doctor/orders/print_prescription', $data);
    }


    public function view($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();

        $order = $orderModel
            ->select('doctor_orders.*, admin_patients.firstname, admin_patients.lastname, admin_patients.birthdate, admin_patients.gender, 
                      users.username as completed_by_name, nurse_users.username as nurse_name, 
                      completed_nurse.username as administered_by_name')
            ->join('admin_patients', 'admin_patients.id = doctor_orders.patient_id', 'left')
            ->join('users', 'users.id = doctor_orders.completed_by', 'left')
            ->join('users as nurse_users', 'nurse_users.id = doctor_orders.nurse_id', 'left')
            ->join('users as completed_nurse', 'completed_nurse.id = doctor_orders.completed_by', 'left')
            ->where('doctor_orders.id', $id)
            ->where('doctor_orders.doctor_id', $doctorId)
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Order not found.');
        }

        // Get audit trail (status logs)
        $auditTrail = $logModel
            ->select('order_status_logs.*, users.username as changed_by_name')
            ->join('users', 'users.id = order_status_logs.changed_by', 'left')
            ->where('order_status_logs.order_id', $id)
            ->orderBy('order_status_logs.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Order Details',
            'order' => $order,
            'auditTrail' => $auditTrail
        ];

        return view('doctor/orders/view', $data);
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $patientModel = new AdminPatientModel();

        $order = $orderModel
            ->where('id', $id)
            ->where('doctor_id', $doctorId)
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Order not found.');
        }

        // RESTRICTION: Doctors cannot edit medication orders (read-only)
        if ($order['order_type'] === 'medication') {
            return redirect()->to('/doctor/orders/view/' . $id)->with('error', 'Medication orders cannot be edited. They are read-only and managed by Pharmacy.');
        }

        // Check if order can be edited (only pending or in_progress orders)
        if (!in_array($order['status'], ['pending', 'in_progress'])) {
            return redirect()->to('/doctor/orders')->with('error', 'This order cannot be edited as it has been completed or cancelled.');
        }

        // Get assigned patients
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Edit Medical Order',
            'order' => $order,
            'patients' => $patients,
        ];

        return view('doctor/orders/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();

        $order = $orderModel
            ->where('id', $id)
            ->where('doctor_id', $doctorId)
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Order not found.');
        }

        // RESTRICTION: Doctors cannot update medication orders
        if ($order['order_type'] === 'medication') {
            return redirect()->to('/doctor/orders/view/' . $id)->with('error', 'Medication orders cannot be updated. They are read-only and managed by Pharmacy.');
        }

        // Check if order can be edited
        if (!in_array($order['status'], ['pending', 'in_progress'])) {
            return redirect()->to('/doctor/orders')->with('error', 'This order cannot be edited as it has been completed or cancelled.');
        }

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'nurse_id' => 'required|integer|greater_than[0]',
            'order_type' => 'required|in_list[medication,lab_test,procedure,diet,activity,other]',
            'order_description' => 'required',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'patient_id' => $this->request->getPost('patient_id'),
            'nurse_id' => $this->request->getPost('nurse_id'),
            'order_type' => $this->request->getPost('order_type'),
            'order_description' => $this->request->getPost('order_description'),
            'instructions' => $this->request->getPost('instructions'),
            'frequency' => $this->request->getPost('frequency'),
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
        ];

        if ($orderModel->update($id, $updateData)) {
            // Log update
            $logModel->insert([
                'order_id' => $id,
                'status' => $order['status'], // Keep current status
                'changed_by' => $doctorId,
                'notes' => 'Order updated by doctor',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/doctor/orders')->with('success', 'Medical order updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update medical order.');
        }
    }

    public function cancel($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();

        $order = $orderModel
            ->where('id', $id)
            ->where('doctor_id', $doctorId)
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Order not found.');
        }

        // RESTRICTION: Doctors cannot cancel medication orders once sent to Pharmacy
        if ($order['order_type'] === 'medication' && $order['pharmacy_status'] !== 'pending') {
            return redirect()->to('/doctor/orders/view/' . $id)->with('error', 'Medication orders cannot be cancelled once Pharmacy has started processing them.');
        }

        if ($order['status'] === 'completed') {
            return redirect()->to('/doctor/orders')->with('error', 'Cannot cancel a completed order.');
        }

        if ($orderModel->update($id, ['status' => 'cancelled'])) {
            // Log cancellation
            $logModel->insert([
                'order_id' => $id,
                'status' => 'cancelled',
                'changed_by' => $doctorId,
                'notes' => $this->request->getPost('cancellation_reason') ?? 'Order cancelled by doctor',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/doctor/orders')->with('success', 'Medical order cancelled successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to cancel medical order.');
        }
    }

}

