<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\HMSPatientModel;
use App\Models\RoomModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;
use App\Models\BedModel;
use App\Models\DoctorModel;

class SurgeryController extends BaseController
{
    protected $adminPatientModel;
    protected $hmsPatientModel;
    protected $roomModel;
    protected $chargeModel;
    protected $billingItemModel;
    protected $bedModel;
    protected $doctorModel;

    public function __construct()
    {
        $this->adminPatientModel = new AdminPatientModel();
        $this->hmsPatientModel = new HMSPatientModel();
        $this->roomModel = new RoomModel();
        $this->chargeModel = new ChargeModel();
        $this->billingItemModel = new BillingItemModel();
        $this->bedModel = new BedModel();
        $this->doctorModel = new DoctorModel();
        helper('form');
    }

    public function index()
    {
        $role = session()->get('role');
        if ($role !== 'doctor') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only doctors can view surgeries.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get all surgeries for this doctor
        $surgeries = [];
        if ($db->tableExists('surgeries')) {
            $surgeries = $db->table('surgeries')
                ->where('doctor_id', $doctorId)
                ->where('deleted_at', null)
                ->orderBy('surgery_date', 'DESC')
                ->orderBy('surgery_time', 'DESC')
                ->get()
                ->getResultArray();

            // Get patient names and room info
            foreach ($surgeries as &$surgery) {
                // Get patient name
                $patientName = 'Unknown';
                if ($surgery['patient_source'] === 'admin') {
                    $patient = $this->adminPatientModel->find($surgery['patient_id']);
                    if ($patient) {
                        $patientName = ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '');
                    }
                } else {
                    $patient = $this->hmsPatientModel->find($surgery['patient_id']);
                    if ($patient) {
                        $patientName = ($patient['first_name'] ?? $patient['firstname'] ?? '') . ' ' . ($patient['last_name'] ?? $patient['lastname'] ?? '');
                    }
                }
                $surgery['patient_name'] = trim($patientName);

                // Get OR room info
                if (!empty($surgery['or_room_id'])) {
                    $orRoom = $this->roomModel->find($surgery['or_room_id']);
                    $surgery['or_room_number'] = $orRoom['room_number'] ?? 'N/A';
                }

                // Calculate countdown
                if (!empty($surgery['surgery_date']) && !empty($surgery['surgery_time'])) {
                    $surgeryDateTime = $surgery['surgery_date'] . ' ' . $surgery['surgery_time'];
                    $surgeryStart = strtotime($surgeryDateTime);
                    $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                    $now = time();
                    
                    if ($surgery['status'] === 'scheduled' && $now < $surgeryEnd) {
                        $surgery['countdown_ends'] = date('Y-m-d H:i:s', $surgeryEnd);
                        $surgery['countdown_remaining'] = $surgeryEnd - $now;
                    } else {
                        $surgery['countdown_ends'] = null;
                        $surgery['countdown_remaining'] = 0;
                    }
                }
            }
        }

        $data = [
            'title' => 'Surgery List',
            'surgeries' => $surgeries,
        ];

        return view('doctor/surgery/index', $data);
    }

    public function create($patientId)
    {
        $role = session()->get('role');
        if ($role !== 'doctor') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only doctors can schedule surgeries.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get patient - check both admin_patients and patients tables
        $patient = null;
        $patientSource = null;

        // Try admin_patients first
        $adminPatient = $this->adminPatientModel->find($patientId);
        if ($adminPatient && ($adminPatient['doctor_id'] == $doctorId || $adminPatient['assigned_doctor_id'] == $doctorId)) {
            $patient = $adminPatient;
            $patientSource = 'admin';
        } else {
            // Try patients table
            $hmsPatient = $this->hmsPatientModel->find($patientId);
            if ($hmsPatient && ($hmsPatient['doctor_id'] == $doctorId)) {
                $patient = $hmsPatient;
                $patientSource = 'patients';
            }
        }

        if (!$patient) {
            return redirect()->to('doctor/patients')->with('error', 'Patient not found or not assigned to you.');
        }

        // Get available OR rooms
        $orRooms = $this->roomModel
            ->where('room_type', 'OR')
            ->where('status', 'Available')
            ->orderBy('room_number', 'ASC')
            ->findAll();

        // Get only doctors who have schedules (doctors without schedules cannot be assigned)
        $doctorsWithSchedules = $this->doctorModel->getDoctorsWithSchedules();
        
        // Format doctors data for the view
        $doctors = [];
        foreach ($doctorsWithSchedules as $doctor) {
            $doctors[] = [
                'user_id' => $doctor['user_id'] ?? $doctor['id'] ?? null,
                'doctor_name' => $doctor['doctor_name'] ?? 'Dr. ' . ($doctor['first_name'] ?? '') . ' ' . ($doctor['last_name'] ?? ''),
                'first_name' => $doctor['first_name'] ?? '',
                'last_name' => $doctor['last_name'] ?? '',
                'specialization' => $doctor['specialization'] ?? '',
            ];
        }

        $data = [
            'title' => 'Schedule Surgery',
            'patient' => $patient,
            'patientSource' => $patientSource,
            'orRooms' => $orRooms,
            'doctors' => $doctors,
            'validation' => \Config\Services::validation(),
        ];

        return view('doctor/surgery/create', $data);
    }

    public function store()
    {
        $role = session()->get('role');
        if ($role !== 'doctor') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only doctors can schedule surgeries.');
        }

        $doctorId = session()->get('user_id');

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'surgery_type' => 'required|in_list[General Surgery,Orthopedic Surgery,OB-Gyne Surgery,ENT Surgery,Urologic Surgery]',
            'assigned_doctor_id' => 'required|integer|greater_than[0]',
            'or_room_id' => 'required|integer|greater_than[0]',
            'surgery_date' => 'required|valid_date',
            'notes' => 'permit_empty|max_length[2000]',
        ]);

        if (!$validation) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validation failed: ' . implode(', ', array_values($errors));
            return redirect()->back()->withInput()->with('error', $errorMessage)->with('validation', $this->validator);
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Check if patient exists and is assigned to this doctor
        $patientId = $this->request->getPost('patient_id');
        $patient = null;
        $patientSource = null;

        $adminPatient = $this->adminPatientModel->find($patientId);
        if ($adminPatient && ($adminPatient['doctor_id'] == $doctorId || $adminPatient['assigned_doctor_id'] == $doctorId)) {
            $patient = $adminPatient;
            $patientSource = 'admin';
        } else {
            $hmsPatient = $this->hmsPatientModel->find($patientId);
            if ($hmsPatient && ($hmsPatient['doctor_id'] == $doctorId)) {
                $patient = $hmsPatient;
                $patientSource = 'patients';
            }
        }

        if (!$patient) {
            return redirect()->back()->withInput()->with('error', 'Patient not found or not assigned to you.');
        }

        // Check if OR room exists and is available
        $orRoomId = $this->request->getPost('or_room_id');
        $bedId = $this->request->getPost('bed_id');
        $orRoom = $this->roomModel->find($orRoomId);
        if (!$orRoom || $orRoom['room_type'] !== 'OR') {
            return redirect()->back()->withInput()->with('error', 'Invalid OR room selected.');
        }

        if ($orRoom['status'] !== 'Available') {
            return redirect()->back()->withInput()->with('error', 'Selected OR room is not available.');
        }

        // Check if bed exists and is available (if bed_id is provided)
        if (!empty($bedId)) {
            $bed = $this->bedModel->find($bedId);
            if (!$bed || $bed['room_id'] != $orRoomId) {
                return redirect()->back()->withInput()->with('error', 'Invalid bed selected for the OR room.');
            }
            if ($bed['status'] !== 'available') {
                return redirect()->back()->withInput()->with('error', 'Selected bed is not available.');
            }
        }

        // Check if surgeries table exists, if not create it
        if (!$db->tableExists('surgeries')) {
            $this->createSurgeriesTable($db);
        }

        // Get patient's current room (previous room before moving to OR)
        $previousRoomId = $patient['room_id'] ?? null;
        $previousRoomNumber = $patient['room_number'] ?? null;

        // Get assigned doctor and surgery type
        $assignedDoctorId = $this->request->getPost('assigned_doctor_id');
        $surgeryType = $this->request->getPost('surgery_type');
        $surgeryDate = $this->request->getPost('surgery_date');
        
        // Get operation duration based on surgery type (from dropdown data-duration attribute)
        $operationDurations = [
            'General Surgery' => 3,
            'Orthopedic Surgery' => 4,
            'OB-Gyne Surgery' => 2,
            'ENT Surgery' => 2,
            'Urologic Surgery' => 3,
        ];
        $operationHours = $operationDurations[$surgeryType] ?? 2;
        
        // Calculate surgery time based on operation duration (default to 8:00 AM, end time = start + duration)
        $surgeryStartTime = '08:00:00'; // Default start time
        $surgeryEndTime = date('H:i:s', strtotime($surgeryStartTime . ' +' . $operationHours . ' hours'));
        
        // Create surgery record
        $surgeryData = [
            'patient_id' => $patientId,
            'patient_source' => $patientSource,
            'doctor_id' => $doctorId,
            'assigned_doctor_id' => $assignedDoctorId,
            'surgery_type' => $surgeryType,
            'or_room_id' => $orRoomId,
            'previous_room_id' => $previousRoomId, // Save previous room
            'surgery_date' => $surgeryDate,
            'surgery_time' => $surgeryStartTime, // Store start time (calculated)
            'surgery_end_time' => $surgeryEndTime, // Store calculated end time
            'operation_duration' => $operationHours, // Store duration in hours
            'notes' => $this->request->getPost('notes') ?: null,
            'status' => 'scheduled',
            'created_by' => $userId,
        ];

        $db->transStart();

        try {
            // Insert surgery
            if (!$db->table('surgeries')->insert($surgeryData)) {
                throw new \Exception('Failed to create surgery record');
            }
            $surgeryId = $db->insertID();

            // Create OR room charge (2 hours = 1 day charge for OR)
            $orRoomPrice = (float)($orRoom['price'] ?? 15000.00); // Default ₱15,000/day
            $orChargeAmount = $orRoomPrice; // Charge for 1 use (2 hours = 1 day charge)
            
            // Get the correct patient_id for charges (must be admin_patients.id)
            // Charges table uses admin_patients.id as patient_id
            $chargePatientId = $patientId;
            if ($patientSource === 'patients') {
                // If patient is from patients table, find corresponding admin_patients record
                $hmsPatient = $this->hmsPatientModel->find($patientId);
                if ($hmsPatient) {
                    // Try to find by matching firstname, lastname, and contact
                    $firstName = $hmsPatient['first_name'] ?? $hmsPatient['firstname'] ?? '';
                    $lastName = $hmsPatient['last_name'] ?? $hmsPatient['lastname'] ?? '';
                    $contact = $hmsPatient['contact'] ?? '';
                    
                    log_message('info', "OR Charge: Looking for admin_patients record - first_name={$firstName}, last_name={$lastName}, contact={$contact}");
                    
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $firstName)
                        ->where('lastname', $lastName)
                        ->where('contact', $contact)
                        ->where('deleted_at', null)
                        ->get()
                        ->getRowArray();
                    if ($adminPatient) {
                        $chargePatientId = $adminPatient['id'];
                        log_message('info', "OR Charge: Found admin_patients.id={$chargePatientId} for patients.patient_id={$patientId}");
                    } else {
                        // If no match found, try to create admin_patients record or use alternative matching
                        log_message('warning', "OR Charge: Could not find admin_patients record for patients.patient_id={$patientId}, name={$firstName} {$lastName}");
                        
                        // Try alternative: find by doctor_id if available
                        if (!empty($hmsPatient['doctor_id'])) {
                            $adminPatientByDoctor = $db->table('admin_patients')
                                ->where('firstname', $firstName)
                                ->where('lastname', $lastName)
                                ->where('doctor_id', $hmsPatient['doctor_id'])
                                ->where('deleted_at', null)
                                ->get()
                                ->getRowArray();
                            if ($adminPatientByDoctor) {
                                $chargePatientId = $adminPatientByDoctor['id'];
                                log_message('info', "OR Charge: Found admin_patients.id={$chargePatientId} by doctor_id match");
                            }
                        }
                        
                        // If still no match, the charge will use the original patientId (which might not work)
                        // But we'll log it as an error
                        if ($chargePatientId == $patientId) {
                            log_message('error', "OR Charge: WARNING - Using patients.patient_id={$patientId} for charge, which may not match admin_patients.id. Charge may not appear in billing!");
                        }
                    }
                }
            } else {
                // Patient is from admin_patients, so patientId is already correct
                log_message('info', "OR Charge: Patient from admin_patients, using patient_id={$patientId}");
            }
            
            // Generate charge number
            $chargeNumber = $this->chargeModel->generateChargeNumber();
            
            // Create charge record for OR room
            $surgeryType = $this->request->getPost('surgery_type');
            $orRoomNumber = $orRoom['room_number'];
            
            $chargeData = [
                'patient_id' => $chargePatientId, // Use admin_patients.id for charges
                'charge_number' => $chargeNumber,
                'total_amount' => $orChargeAmount,
                'status' => 'pending',
                'notes' => 'OR Room Charge: ' . $orRoomNumber . ' - ' . $surgeryType . ' (' . $operationHours . ' hrs)',
            ];
            
            log_message('info', "Creating OR charge: patient_id={$chargePatientId}, amount={$orChargeAmount}, room={$orRoomNumber}");
            
            if (!$this->chargeModel->insert($chargeData)) {
                $errors = $this->chargeModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Database insert failed';
                log_message('error', "Failed to create OR charge: {$errorMsg}");
                throw new \Exception('Failed to create OR room charge: ' . $errorMsg);
            }
            $chargeId = $this->chargeModel->getInsertID();
            
            if (empty($chargeId)) {
                log_message('error', 'OR charge created but chargeId is empty');
                throw new \Exception('Failed to get OR charge ID after insert');
            }
            
            log_message('info', "OR charge created successfully: charge_id={$chargeId}, charge_number={$chargeNumber}");
            
            // Create billing item for OR room
            $billingItemData = [
                'charge_id' => $chargeId,
                'item_type' => 'procedure', // Using 'procedure' for surgery/OR
                'item_name' => 'OR Room: ' . $orRoomNumber,
                'description' => 'Operating Room Charge: ' . $surgeryType . ' - ' . $orRoomNumber . ' (' . $operationHours . ' hrs)',
                'quantity' => 1.00,
                'unit_price' => $orChargeAmount,
                'total_price' => $orChargeAmount,
                'related_id' => $surgeryId,
                'related_type' => 'surgery',
            ];
            
            log_message('info', "Creating OR billing item: " . json_encode($billingItemData));
            
            if (!$this->billingItemModel->insert($billingItemData)) {
                $errors = $this->billingItemModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Billing item insert failed';
                log_message('error', "Failed to create OR billing item: {$errorMsg}");
                throw new \Exception('Failed to create OR room billing item: ' . $errorMsg);
            }
            
            log_message('info', "OR billing item created successfully for charge_id={$chargeId}");

            // Create doctor fee billing
            if ($assignedDoctorId) {
                // Get assigned doctor info
                $assignedDoctor = $db->table('doctors d')
                    ->select('d.*, u.first_name, u.last_name, u.specialization')
                    ->join('users u', 'u.id = d.user_id', 'left')
                    ->where('d.user_id', $assignedDoctorId)
                    ->get()
                    ->getRowArray();
                
                if ($assignedDoctor) {
                    // Doctor fee based on surgery type
                    $doctorFees = [
                        'General Surgery' => 50000.00,
                        'Orthopedic Surgery' => 60000.00,
                        'OB-Gyne Surgery' => 45000.00,
                        'ENT Surgery' => 40000.00,
                        'Urologic Surgery' => 50000.00,
                    ];
                    $doctorFee = $doctorFees[$surgeryType] ?? 40000.00;
                    
                    // Generate charge number for doctor fee
                    $doctorChargeNumber = $this->chargeModel->generateChargeNumber();
                    
                    // Create charge record for doctor fee
                    $doctorChargeData = [
                        'patient_id' => $chargePatientId,
                        'charge_number' => $doctorChargeNumber,
                        'total_amount' => $doctorFee,
                        'status' => 'pending',
                        'notes' => 'Surgeon Fee: ' . ($assignedDoctor['doctor_name'] ?? $assignedDoctor['first_name'] . ' ' . $assignedDoctor['last_name']) . ' - ' . $surgeryType,
                    ];
                    
                    if ($this->chargeModel->insert($doctorChargeData)) {
                        $doctorChargeId = $this->chargeModel->getInsertID();
                        
                        // Create billing item for doctor fee
                        $doctorBillingItemData = [
                            'charge_id' => $doctorChargeId,
                            'item_type' => 'doctor_fee',
                            'item_name' => 'Surgeon Fee: ' . ($assignedDoctor['doctor_name'] ?? $assignedDoctor['first_name'] . ' ' . $assignedDoctor['last_name']),
                            'description' => 'Surgeon Fee for ' . $surgeryType . ' - Dr. ' . ($assignedDoctor['doctor_name'] ?? $assignedDoctor['first_name'] . ' ' . $assignedDoctor['last_name']),
                            'quantity' => 1.00,
                            'unit_price' => $doctorFee,
                            'total_price' => $doctorFee,
                            'related_id' => $surgeryId,
                            'related_type' => 'surgery',
                        ];
                        
                        $this->billingItemModel->insert($doctorBillingItemData);
                        log_message('info', "Doctor fee charge created: charge_id={$doctorChargeId}, amount={$doctorFee}");
                    }
                }
            }

            // Update OR room status to Occupied
            log_message('info', "Updating OR room #{$orRoomId} to Occupied for patient #{$patientId}");
            $roomUpdateResult = $this->roomModel->update($orRoomId, [
                'status' => 'Occupied',
                'current_patient_id' => $patientId,
            ]);

            // Update bed status to occupied if bed_id is provided
            if (!empty($bedId)) {
                log_message('info', "Updating bed #{$bedId} to occupied for patient #{$patientId}");
                $bedUpdateResult = $this->bedModel->update($bedId, [
                    'status' => 'occupied',
                    'current_patient_id' => $patientId,
                ]);
                if (!$bedUpdateResult) {
                    $bedError = $this->bedModel->errors();
                    $bedErrorMsg = !empty($bedError) ? implode(', ', $bedError) : 'Bed update failed';
                    log_message('error', "Failed to update bed: {$bedErrorMsg}");
                    throw new \Exception('Failed to update bed status: ' . $bedErrorMsg);
                }
                log_message('info', "Bed #{$bedId} updated successfully to occupied");
            }
            
            // Check for errors (update returns false on error, or number of affected rows on success)
            $roomErrors = $this->roomModel->errors();
            if (!empty($roomErrors)) {
                $errorMsg = implode(', ', $roomErrors);
                log_message('error', "Failed to update OR room: {$errorMsg}");
                throw new \Exception('Failed to update OR room status: ' . $errorMsg);
            }
            log_message('info', "OR room #{$orRoomId} updated successfully (affected rows: " . ($roomUpdateResult !== false ? $roomUpdateResult : 0) . ")");

            // If patient had a previous room, release it to make it available
            if ($previousRoomId && $previousRoomId != $orRoomId) {
                $previousRoom = $this->roomModel->find($previousRoomId);
                if ($previousRoom) {
                    // Release previous room - make it available since patient is now in OR
                    $this->roomModel->update($previousRoomId, [
                        'status' => 'Available',
                        'current_patient_id' => null,
                    ]);
                    log_message('info', "Released previous room #{$previousRoomId} - now available (patient moved to OR)");

                    // Also release any beds in the previous room that were assigned to this patient
                    $previousRoomBeds = $this->bedModel
                        ->where('room_id', $previousRoomId)
                        ->where('current_patient_id', $patientId)
                        ->where('status', 'occupied')
                        ->findAll();

                    foreach ($previousRoomBeds as $bed) {
                        $this->bedModel->update($bed['id'], [
                            'status' => 'available',
                            'current_patient_id' => null,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        log_message('info', "Released bed #{$bed['id']} (Bed {$bed['bed_number']}) in previous room #{$previousRoomId}");
                    }
                }
            }

            // Move patient to OR room - update patient's room_id and room_number
            log_message('info', "Moving patient #{$patientId} to OR room #{$orRoomId}, source={$patientSource}");
            if ($patientSource === 'admin') {
                // Update admin_patients table
                // First, check if room_id column exists
                $columns = $db->getFieldNames('admin_patients');
                $hasRoomId = in_array('room_id', $columns);
                $hasRoomNumber = in_array('room_number', $columns);
                
                log_message('info', "Checking admin_patients table columns. Has room_id: " . ($hasRoomId ? 'YES' : 'NO') . ", Has room_number: " . ($hasRoomNumber ? 'YES' : 'NO'));
                
                $updateData = ['updated_at' => date('Y-m-d H:i:s')];
                
                if ($hasRoomId) {
                    $updateData['room_id'] = $orRoomId;
                }
                if ($hasRoomNumber) {
                    $updateData['room_number'] = $orRoom['room_number'];
                }
                
                if (empty($updateData)) {
                    log_message('warning', "No updatable fields found for admin_patients table");
                } else {
                    $updateResult = $db->table('admin_patients')
                        ->where('id', $patientId)
                        ->update($updateData);
                    
                    if (!$updateResult) {
                        $error = $db->error();
                        $errorMsg = !empty($error['message']) ? $error['message'] : 'Patient update failed';
                        log_message('error', "Failed to update admin_patients: {$errorMsg}");
                        throw new \Exception('Failed to update patient room: ' . $errorMsg);
                    }
                    log_message('info', "Admin patient #{$patientId} updated successfully");
                }
            } else {
                // Update patients table
                // First, check if room_id column exists
                $columns = $db->getFieldNames('patients');
                $hasRoomId = in_array('room_id', $columns);
                $hasRoomNumber = in_array('room_number', $columns);
                
                log_message('info', "Checking patients table columns. Has room_id: " . ($hasRoomId ? 'YES' : 'NO') . ", Has room_number: " . ($hasRoomNumber ? 'YES' : 'NO'));
                
                $updateData = ['updated_at' => date('Y-m-d H:i:s')];
                
                if ($hasRoomId) {
                    $updateData['room_id'] = $orRoomId;
                }
                if ($hasRoomNumber) {
                    $updateData['room_number'] = $orRoom['room_number'];
                }
                
                if (empty($updateData)) {
                    log_message('warning', "No updatable fields found for patients table");
                } else {
                    $updateResult = $db->table('patients')
                        ->where('patient_id', $patientId)
                        ->update($updateData);
                    
                    if (!$updateResult) {
                        $error = $db->error();
                        $errorMsg = !empty($error['message']) ? $error['message'] : 'Patient update failed';
                        log_message('error', "Failed to update patients table: {$errorMsg}");
                        throw new \Exception('Failed to update patient room: ' . $errorMsg);
                    }
                    log_message('info', "Patient #{$patientId} updated successfully");
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                $errorMsg = !empty($error['message']) ? $error['message'] : 'Transaction failed';
                log_message('error', "Transaction failed: {$errorMsg}");
                throw new \Exception('Transaction failed: ' . $errorMsg);
            }
            
            log_message('info', "Surgery scheduled successfully: surgery_id={$surgeryId}, charge_id={$chargeId}, patient_id={$patientId}");

            return redirect()->to('doctor/patients/view/' . $patientId)->with('success', 'Surgery scheduled successfully. Patient assigned to OR room.');
        } catch (\Exception $e) {
            $db->transRollback();
            $errorMessage = $e->getMessage();
            $dbError = $db->error();
            if (!empty($dbError['message'])) {
                $errorMessage .= ' (Database: ' . $dbError['message'] . ')';
            }
            log_message('error', "Surgery scheduling failed: {$errorMessage}");
            log_message('error', "Stack trace: " . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Failed to schedule surgery: ' . $errorMessage);
        }
    }

    private function createSurgeriesTable($db)
    {
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
            'patient_source' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'admin or patients',
            ],
            'doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'surgery_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'or_room_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'previous_room_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Previous room before moving to OR',
            ],
            'surgery_date' => [
                'type' => 'DATE',
            ],
            'surgery_time' => [
                'type' => 'TIME',
            ],
            'surgery_end_time' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Calculated end time based on operation duration',
            ],
            'operation_duration' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Duration in hours',
            ],
            'assigned_doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Doctor assigned to perform the surgery',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'scheduled',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $forge = \Config\Database::forge();
        $forge->addField($fields);
        $forge->addKey('id', true);
        $forge->createTable('surgeries', true);
    }

    /**
     * Check and move patients back from OR room after countdown ends
     * This can be called via AJAX from the frontend when countdown reaches zero
     * Or periodically (via cron or scheduled task) for all patients
     */
    public function checkAndMovePatientsBack()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('surgeries')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Surgeries table does not exist']);
            }
            return 0;
        }

        // Get patient_id from request if provided (for specific patient)
        $requestData = $this->request->getJSON(true) ?? [];
        $requestPatientId = $this->request->getPost('patient_id') ?? $requestData['patient_id'] ?? null;
        
        log_message('info', "checkAndMovePatientsBack called with patient_id={$requestPatientId}");
        
        // Get all scheduled surgeries that should be completed (2 hours passed)
        // Also check for surgeries that might already be in OR but countdown finished
        $query = $db->table('surgeries')
            ->whereIn('status', ['scheduled', 'in_progress']) // Also check in_progress status
            ->where('deleted_at', null);
        
        // If specific patient_id provided, filter by it
        if ($requestPatientId) {
            $query->where('patient_id', $requestPatientId);
        }
        
        $surgeries = $query->get()->getResultArray();
        
        log_message('info', "Found " . count($surgeries) . " surgeries to check");

        $now = time();
        $movedCount = 0;
        $movedPatients = [];

        foreach ($surgeries as $surgery) {
            $shouldMove = false;
            
            // If surgery has date/time, check if countdown has ended
            if (!empty($surgery['surgery_date']) && !empty($surgery['surgery_time'])) {
                $surgeryDateTime = $surgery['surgery_date'] . ' ' . $surgery['surgery_time'];
                $surgeryStart = strtotime($surgeryDateTime);
                $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours

                // If countdown has ended (current time >= surgery end time)
                if ($now >= $surgeryEnd) {
                    $shouldMove = true;
                    log_message('info', "Surgery #{$surgery['id']} countdown ended - moving patient back");
                }
            } else {
                // If no date/time but status is scheduled and patient is in OR, also move back
                // This handles cases where countdown already finished
                if ($surgery['status'] === 'scheduled') {
                    // Check if patient is actually in OR room
                    $orRoom = $db->table('rooms')
                        ->where('id', $surgery['or_room_id'])
                        ->where('room_type', 'OR')
                        ->where('current_patient_id', $surgery['patient_id'])
                        ->get()
                        ->getRowArray();
                    
                    if ($orRoom) {
                        $shouldMove = true;
                        log_message('info', "Surgery #{$surgery['id']} - patient still in OR, forcing move back");
                    }
                }
            }
            
            if ($shouldMove) {
                $result = $this->movePatientBackFromOR($surgery);
                if ($result) {
                    $movedCount++;
                    $movedPatients[] = $surgery['patient_id'];
                    log_message('info', "Successfully moved patient #{$surgery['patient_id']} back from OR");
                } else {
                    log_message('error', "Failed to move patient #{$surgery['patient_id']} back from OR");
                }
            }
        }

        // If called via AJAX, return JSON response
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Moved {$movedCount} patient(s) back from OR room",
                'moved_count' => $movedCount,
                'moved_patients' => $movedPatients
            ]);
        }

        return $movedCount;
    }

    /**
     * Move patient back to previous room from OR
     */
    public function movePatientBackFromOR($surgery)
    {
        $db = \Config\Database::connect();
        
        $db->transStart();

        try {
            $patientId = $surgery['patient_id'];
            $patientSource = $surgery['patient_source'] ?? 'admin';
            $orRoomId = $surgery['or_room_id'];
            $previousRoomId = $surgery['previous_room_id'] ?? null;

            // Update surgery status to completed
            $db->table('surgeries')
                ->where('id', $surgery['id'])
                ->update([
                    'status' => 'completed',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Release OR room
            $this->roomModel->update($orRoomId, [
                'status' => 'Available',
                'current_patient_id' => null,
            ]);

            // Release bed in OR room (if bed was assigned)
            $orRoomBeds = $this->bedModel
                ->where('room_id', $orRoomId)
                ->where('current_patient_id', $patientId)
                ->where('status', 'occupied')
                ->findAll();

            foreach ($orRoomBeds as $bed) {
                $this->bedModel->update($bed['id'], [
                    'status' => 'available',
                    'current_patient_id' => null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                log_message('info', "Released bed #{$bed['id']} (Bed {$bed['bed_number']}) in OR room #{$orRoomId}");
            }

            // Move patient back to previous room if exists
            if ($previousRoomId) {
                $previousRoom = $this->roomModel->find($previousRoomId);
                
                if ($previousRoom) {
                    // Update patient's room back to previous room
                    if ($patientSource === 'admin') {
                        // Check if room_id column exists in admin_patients table
                        $columns = $db->getFieldNames('admin_patients');
                        $hasRoomId = in_array('room_id', $columns);
                        $hasRoomNumber = in_array('room_number', $columns);
                        
                        $updateData = ['updated_at' => date('Y-m-d H:i:s')];
                        if ($hasRoomId) {
                            $updateData['room_id'] = $previousRoomId;
                        }
                        if ($hasRoomNumber) {
                            $updateData['room_number'] = $previousRoom['room_number'];
                        }
                        
                        if (!empty($updateData)) {
                            $db->table('admin_patients')
                                ->where('id', $patientId)
                                ->update($updateData);
                        }
                    } else {
                        // Check if room_id column exists in patients table
                        $columns = $db->getFieldNames('patients');
                        $hasRoomId = in_array('room_id', $columns);
                        $hasRoomNumber = in_array('room_number', $columns);
                        
                        $updateData = ['updated_at' => date('Y-m-d H:i:s')];
                        if ($hasRoomId) {
                            $updateData['room_id'] = $previousRoomId;
                        }
                        if ($hasRoomNumber) {
                            $updateData['room_number'] = $previousRoom['room_number'];
                        }
                        
                        if (!empty($updateData)) {
                            $db->table('patients')
                                ->where('patient_id', $patientId)
                                ->update($updateData);
                        }
                    }

                    // Update previous room status to Occupied
                    $this->roomModel->update($previousRoomId, [
                        'status' => 'Occupied',
                        'current_patient_id' => $patientId,
                    ]);
                }
            } else {
                // If no previous room, just remove from OR room
                if ($patientSource === 'admin') {
                    // Check if room_id column exists in admin_patients table
                    $columns = $db->getFieldNames('admin_patients');
                    $hasRoomId = in_array('room_id', $columns);
                    $hasRoomNumber = in_array('room_number', $columns);
                    
                    $updateData = ['updated_at' => date('Y-m-d H:i:s')];
                    if ($hasRoomId) {
                        $updateData['room_id'] = null;
                    }
                    if ($hasRoomNumber) {
                        $updateData['room_number'] = null;
                    }
                    
                    if (!empty($updateData)) {
                        $db->table('admin_patients')
                            ->where('id', $patientId)
                            ->update($updateData);
                    }
                } else {
                    // Check if room_id column exists in patients table
                    $columns = $db->getFieldNames('patients');
                    $hasRoomId = in_array('room_id', $columns);
                    $hasRoomNumber = in_array('room_number', $columns);
                    
                    $updateData = ['updated_at' => date('Y-m-d H:i:s')];
                    if ($hasRoomId) {
                        $updateData['room_id'] = null;
                    }
                    if ($hasRoomNumber) {
                        $updateData['room_number'] = null;
                    }
                    
                    if (!empty($updateData)) {
                        $db->table('patients')
                            ->where('patient_id', $patientId)
                            ->update($updateData);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            log_message('info', "Patient #{$patientId} moved back from OR room #{$orRoomId} to previous room #{$previousRoomId}");
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Failed to move patient back from OR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get beds for a room (AJAX endpoint)
     */
    public function getBeds($roomId)
    {
        // Allow both AJAX and regular requests for debugging
        // if (!$this->request->isAJAX()) {
        //     return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        // }

        log_message('info', "Getting beds for room ID: {$roomId}");

        $beds = $this->bedModel
            ->where('room_id', $roomId)
            ->orderBy('bed_number', 'ASC')
            ->findAll();

        log_message('info', "Found " . count($beds) . " bed(s) for room ID: {$roomId}");

        // If no beds found, try to create them using BedSeeder logic
        if (empty($beds)) {
            log_message('warning', "No beds found for room ID: {$roomId}, attempting to create bed");
            
            // Get room info
            $room = $this->roomModel->find($roomId);
            if ($room) {
                $bedCount = (int)($room['bed_count'] ?? 1);
                if ($bedCount < 1) {
                    $bedCount = 1; // Default to 1 bed for OR rooms
                }
                
                // Create bed(s) for this room
                $now = date('Y-m-d H:i:s');
                for ($i = 1; $i <= $bedCount; $i++) {
                    $bedData = [
                        'room_id' => $roomId,
                        'bed_number' => (string)$i,
                        'status' => 'available',
                        'current_patient_id' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    
                    if ($this->bedModel->insert($bedData)) {
                        log_message('info', "Created bed #{$i} for room ID: {$roomId}");
                    }
                }
                
                // Fetch beds again after creation
                $beds = $this->bedModel
                    ->where('room_id', $roomId)
                    ->orderBy('bed_number', 'ASC')
                    ->findAll();
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'beds' => $beds
        ]);
    }

}

