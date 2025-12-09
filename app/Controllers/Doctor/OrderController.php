<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\DoctorOrderModel;
use App\Models\OrderStatusLogModel;
use App\Models\AdminPatientModel;
use App\Models\DoctorNotificationModel;
use App\Models\NurseNotificationModel;

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

        // Get all available medicines from pharmacy (quantity > 0)
        $medicines = [];
        if ($db->tableExists('pharmacy')) {
            $medicines = $db->table('pharmacy')
                ->where('quantity >', 0)
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get patient_id from query string if provided (e.g., from consultation redirect)
        $selectedPatientId = $this->request->getGet('patient_id');
        $selectedOrderType = $this->request->getGet('order_type'); // Optional: pre-select order type

        $data = [
            'title' => 'Create Medical Order',
            'patients' => $patients,
            'nurses' => $nurses,
            'medicines' => $medicines,
            'selected_patient_id' => $selectedPatientId, // Pass to view for pre-selection
            'selected_order_type' => $selectedOrderType, // Pass to view for pre-selection
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

        $orderType = $this->request->getPost('order_type');
        
        // Validation rules
        $rules = [
            'patient_id' => 'required|integer|greater_than[0]',
            'nurse_id' => 'required|integer|greater_than[0]', // Nurse required for all orders (to administer medication)
            'order_type' => 'required|in_list[medication,lab_test,procedure,diet,activity,other]',
            'order_description' => 'required',
        ];

        // For medication orders, require additional fields
        if ($orderType === 'medication') {
            $rules['medicine_name'] = 'required';
            $rules['dosage'] = 'required';
            $rules['frequency'] = 'required';
            $rules['duration'] = 'required';
        }

        $validation = $this->validate($rules);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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

        $data = [
            'patient_id' => $adminPatientId, // Use admin_patients.id for foreign key constraint
            'doctor_id' => $doctorId,
            'nurse_id' => $this->request->getPost('nurse_id'), // Nurse required to administer medication
            'order_type' => $orderType,
            'order_description' => $this->request->getPost('order_description'),
            'instructions' => $this->request->getPost('instructions'),
            'frequency' => $this->request->getPost('frequency'),
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'status' => 'pending',
        ];

        // Add medication-specific fields
        if ($orderType === 'medication') {
            $data['medicine_name'] = $this->request->getPost('medicine_name');
            $data['dosage'] = $this->request->getPost('dosage');
            $data['duration'] = $this->request->getPost('duration');
            $data['remarks'] = $this->request->getPost('remarks');
            $data['pharmacy_status'] = 'pending'; // Auto-route to Pharmacy
        }

        if ($orderModel->insert($data)) {
            $orderId = $orderModel->getInsertID();

            // Log initial status with timestamp
            $logModel->insert([
                'order_id' => $orderId,
                'status' => 'pending',
                'changed_by' => $doctorId,
                'notes' => 'Order created by doctor at ' . date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // If medication order, notify Pharmacy instantly
            if ($orderType === 'medication') {
                // Create notification for Pharmacy (if notification system exists)
                if ($db->tableExists('pharmacy_notifications')) {
                    $patientModel = new AdminPatientModel();
                    $patient = $patientModel->find($data['patient_id']);
                    
                    $db->table('pharmacy_notifications')->insert([
                        'type' => 'new_medication_order',
                        'title' => 'New Medication Order',
                        'message' => 'Dr. ' . (session()->get('name') ?? session()->get('username')) . ' has created a medication order for ' . ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . '. Medicine: ' . $data['medicine_name'],
                        'related_id' => $orderId,
                        'related_type' => 'doctor_order',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // Create notification for nurse
            $patientModel = new AdminPatientModel();
            $patient = $patientModel->find($data['patient_id']);

            if ($data['nurse_id']) {
                if ($orderType === 'medication') {
                    // For medication: Nurse will be notified after Pharmacy dispenses
                    $nurseNotificationModel->insert([
                        'nurse_id' => $data['nurse_id'],
                        'type' => 'new_doctor_order',
                        'title' => 'New Medication Order',
                        'message' => 'Dr. ' . (session()->get('name') ?? session()->get('username')) . ' has created a medication order for ' . ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . '. Medicine: ' . $data['medicine_name'] . '. Please wait for Pharmacy to dispense before administering.',
                        'related_id' => $orderId,
                        'related_type' => 'doctor_order',
                        'is_read' => 0,
                    ]);
                } else {
                    // For non-medication orders: Nurse can execute immediately
                    $nurseNotificationModel->insert([
                        'nurse_id' => $data['nurse_id'],
                        'type' => 'new_doctor_order',
                        'title' => 'New Doctor Order',
                        'message' => 'Dr. ' . (session()->get('name') ?? session()->get('username')) . ' has created a new ' . $orderType . ' order for ' . ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . '. Please execute this order.',
                        'related_id' => $orderId,
                        'related_type' => 'doctor_order',
                        'is_read' => 0,
                    ]);
                }
            }

            $successMessage = 'Medical order created successfully.';
            if ($orderType === 'medication') {
                $successMessage .= ' Order has been automatically routed to Pharmacy for preparation. The assigned nurse has been notified and will administer the medication after Pharmacy dispenses it.';
            } else {
                $successMessage .= ' The assigned nurse has been notified.';
            }

            return redirect()->to('/doctor/orders')->with('success', $successMessage);
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create medical order.');
        }
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

