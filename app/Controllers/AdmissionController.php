<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConsultationModel;
use App\Models\AdminPatientModel;
use App\Models\RoomModel;
use App\Models\BedModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;
use App\Models\DoctorModel;

class AdmissionController extends BaseController
{
    protected $consultationModel;
    protected $patientModel;
    protected $roomModel;
    protected $bedModel;
    protected $chargeModel;
    protected $billingItemModel;
    protected $doctorModel;

    public function __construct()
    {
        $this->consultationModel = new ConsultationModel();
        $this->patientModel = new AdminPatientModel();
        $this->roomModel = new RoomModel();
        $this->bedModel = new BedModel();
        $this->chargeModel = new ChargeModel();
        $this->billingItemModel = new BillingItemModel();
        $this->doctorModel = new DoctorModel();
    }

    /**
     * Show admission form
     * Accessible by: Nurse, Receptionist ONLY
     * Doctor can only mark "For Admission" but cannot process admission
     */
    public function create($consultationId = null)
    {
        $role = session()->get('role');
        // Only Nurse and Receptionist can process admission
        // Doctor can only mark "For Admission" in consultation, but cannot assign rooms/beds
        if (!in_array($role, ['nurse', 'receptionist'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Nurse and Receptionist can process patient admission.');
        }

        $db = \Config\Database::connect();
        
        // Get consultation details
        $consultation = null;
        $patient = null;
        
        if ($consultationId) {
            $consultation = $this->consultationModel->find($consultationId);
            if (!$consultation) {
                return redirect()->back()->with('error', 'Consultation not found.');
            }
            
            // Check if already admitted
            $existingAdmission = $db->table('admissions')
                ->where('consultation_id', $consultationId)
                ->where('status !=', 'discharged')
                ->where('status !=', 'cancelled')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            
            if ($existingAdmission) {
                return redirect()->back()->with('error', 'Patient is already admitted or admission is pending.');
            }
            
            $patient = $this->patientModel->find($consultation['patient_id']);
        }

        // Get available rooms grouped by ward
        $rooms = $this->roomModel
            ->where('status', 'Available')
            ->orderBy('ward', 'ASC')
            ->orderBy('room_number', 'ASC')
            ->findAll();

        // Get doctors from doctors table
        // Only show doctors who have schedules
        // Doctors without schedules cannot be assigned as attending physician
        $doctors = $this->doctorModel->getDoctorsWithSchedules();

        // Group rooms by ward
        $roomsByWard = [];
        foreach ($rooms as $room) {
            $ward = $room['ward'] ?? 'Other';
            if (!isset($roomsByWard[$ward])) {
                $roomsByWard[$ward] = [];
            }
            $roomsByWard[$ward][] = $room;
        }

        $data = [
            'title' => 'Admit Patient',
            'consultation' => $consultation,
            'patient' => $patient,
            'rooms' => $rooms,
            'roomsByWard' => $roomsByWard,
            'doctors' => $doctors,
            'validation' => \Config\Services::validation(),
        ];

        return view('admission/create', $data);
    }

    /**
     * Process admission
     */
    public function store()
    {
        $role = session()->get('role');
        // Only Nurse and Receptionist can process admission
        // Doctor can only mark "For Admission" but cannot assign rooms/beds
        if (!in_array($role, ['nurse', 'receptionist'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Nurse and Receptionist can process patient admission.');
        }

        $validation = $this->validate([
            'consultation_id' => 'permit_empty|integer|greater_than[0]',
            'patient_id' => 'required|integer|greater_than[0]',
            'room_id' => 'required|integer|greater_than[0]',
            'bed_id' => 'permit_empty|integer|greater_than[0]', // For finding bed, not storing
            'bed_number' => 'permit_empty|max_length[50]',
            'room_type' => 'permit_empty|max_length[100]',
            'admission_reason' => 'required|max_length[2000]',
            'attending_physician_id' => 'required|integer|greater_than[0]',
            'initial_notes' => 'permit_empty|max_length[2000]',
            'admission_date' => 'required|valid_date',
        ]);

        if (!$validation) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validation failed: ' . implode(', ', array_values($errors));
            return redirect()->back()->withInput()->with('error', $errorMessage)->with('validation', $this->validator);
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Check if room is available
        $room = $this->roomModel->find($this->request->getPost('room_id'));
        if (!$room) {
            return redirect()->back()->withInput()->with('error', 'Selected room not found.');
        }
        
        // STRICT VALIDATION: Check if room is already occupied by ANY patient
        $roomStatus = strtolower(trim($room['status'] ?? ''));
        $isRoomOccupied = ($roomStatus === 'occupied') || !empty($room['current_patient_id']);
        
        if ($isRoomOccupied) {
            // Room is occupied - REJECT assignment regardless of which patient
            $currentRoomPatientId = $room['current_patient_id'] ?? null;
            $db = \Config\Database::connect();
            $occupiedPatient = $db->table('admin_patients')
                ->where('id', $currentRoomPatientId)
                ->get()
                ->getRowArray();
            $occupiedPatientName = $occupiedPatient ? ($occupiedPatient['firstname'] ?? '') . ' ' . ($occupiedPatient['lastname'] ?? '') : 'Patient ID: ' . $currentRoomPatientId;
            
            return redirect()->back()->withInput()->with('error', "Room {$room['room_number']} is already occupied by {$occupiedPatientName}. Please select a different room.");
        }

        // Check if bed is available (if bed_id is provided)
        $bedId = $this->request->getPost('bed_id');
        $bed = null;
        $bedNumber = null;
        
        if ($bedId) {
            $bed = $this->bedModel->find($bedId);
            if (!$bed) {
                return redirect()->back()->withInput()->with('error', 'Selected bed not found.');
            }
            
            // STRICT VALIDATION: Check if bed is already occupied by ANY patient
            $bedStatus = strtolower(trim($bed['status'] ?? ''));
            $isBedOccupied = ($bedStatus === 'occupied') || !empty($bed['current_patient_id']);
            
            if ($isBedOccupied) {
                // Bed is occupied - REJECT assignment regardless of which patient
                $currentBedPatientId = $bed['current_patient_id'] ?? null;
                $occupiedPatient = $db->table('admin_patients')
                    ->where('id', $currentBedPatientId)
                    ->get()
                    ->getRowArray();
                $occupiedPatientName = $occupiedPatient ? ($occupiedPatient['firstname'] ?? '') . ' ' . ($occupiedPatient['lastname'] ?? '') : 'Patient ID: ' . $currentBedPatientId;
                
                return redirect()->back()->withInput()->with('error', "Bed {$bed['bed_number']} is already occupied by {$occupiedPatientName}. Please select a different bed.");
            }
            
            if ($bed['room_id'] != $this->request->getPost('room_id')) {
                return redirect()->back()->withInput()->with('error', 'Selected bed does not belong to the selected room.');
            }
            $bedNumber = $bed['bed_number'];
        } else {
            // If no bed_id, try to get bed_number from form
            $bedNumber = $this->request->getPost('bed_number') ?: null;
        }

        // Create admission record
        $admissionData = [
            'consultation_id' => $this->request->getPost('consultation_id') ?: null,
            'patient_id' => $this->request->getPost('patient_id'),
            'room_id' => $this->request->getPost('room_id'),
            'bed_number' => $bedNumber ?: null,
            'room_type' => $this->request->getPost('room_type') ?: ($room['room_type'] ?? 'ward'),
            'admission_reason' => $this->request->getPost('admission_reason'),
            'attending_physician_id' => $this->request->getPost('attending_physician_id'),
            'initial_notes' => $this->request->getPost('initial_notes'),
            'admission_date' => $this->request->getPost('admission_date') . ' ' . date('H:i:s'),
            'status' => 'admitted',
        ];

        $db->transStart();

        try {
            // Insert admission data using direct database query
            if (!$db->table('admissions')->insert($admissionData)) {
                $errorMessage = 'Failed to create admission record';
                throw new \Exception($errorMessage);
            }
            $admissionId = $db->insertID();

            // Update room status - check if room exists first
            $roomToUpdate = $this->roomModel->find($this->request->getPost('room_id'));
            if (!$roomToUpdate) {
                throw new \Exception('Room not found');
            }
            
            $roomUpdateData = [];
            if ($roomToUpdate['status'] !== 'Occupied') {
                $roomUpdateData['status'] = 'Occupied';
            }
            if ($db->fieldExists('current_patient_id', 'rooms')) {
                $roomUpdateData['current_patient_id'] = $this->request->getPost('patient_id');
            }
            
            if (!empty($roomUpdateData)) {
                $roomUpdateResult = $this->roomModel->update($this->request->getPost('room_id'), $roomUpdateData);
                if (!$roomUpdateResult) {
                    throw new \Exception('Failed to update room status');
                }
            }

            // Update bed status if bed provided
            if ($bed) {
                $bedUpdateData = [];
                if ($bed['status'] !== 'occupied') {
                    $bedUpdateData['status'] = 'occupied';
                }
                if ($db->fieldExists('current_patient_id', 'beds')) {
                    $bedUpdateData['current_patient_id'] = $this->request->getPost('patient_id');
                }
                
                if (!empty($bedUpdateData)) {
                    $bedUpdateResult = $this->bedModel->update($bed['id'], $bedUpdateData);
                    if (!$bedUpdateResult) {
                        throw new \Exception('Failed to update bed status');
                    }
                }
            }

            // Note: admin_patients table doesn't have a 'type' field
            // We'll track patient status through admissions table instead
            // Update patients table if it exists and has type field
            if ($db->tableExists('patients')) {
                $adminPatient = $this->patientModel->find($this->request->getPost('patient_id'));
                if ($adminPatient) {
                    // Try to find matching patient in patients table
                    $hmsPatient = $db->table('patients')
                        ->where('first_name', $adminPatient['firstname'] ?? '')
                        ->where('last_name', $adminPatient['lastname'] ?? '')
                        ->get()
                        ->getRowArray();
                    
                    if ($hmsPatient) {
                        $db->table('patients')
                            ->where('patient_id', $hmsPatient['patient_id'])
                            ->update(['type' => 'In-Patient']);
                    }
                }
            }


            // Generate admission charge and room charge
            if ($db->tableExists('charges') && $db->tableExists('billing_items')) {
                $chargeNumber = $this->chargeModel->generateChargeNumber();
                $admissionFee = 1000.00; // Default admission fee
                
                $chargeData = [
                    'consultation_id' => $this->request->getPost('consultation_id') ?: null,
                    'patient_id' => $this->request->getPost('patient_id'),
                    'charge_number' => $chargeNumber,
                    'total_amount' => $admissionFee,
                    'status' => 'pending',
                    'notes' => 'Admission charge - Room: ' . ($room['room_number'] ?? 'N/A'),
                ];

                if ($this->chargeModel->insert($chargeData)) {
                    $chargeId = $this->chargeModel->getInsertID();
                    
                    // Add billing item
                    $this->billingItemModel->insert([
                        'charge_id' => $chargeId,
                        'item_type' => 'procedure',
                        'item_name' => 'Hospital Admission',
                        'description' => 'Admission to ' . ($room['room_number'] ?? 'N/A') . ' - ' . ($admissionData['room_type'] ?? 'Ward'),
                        'quantity' => 1.00,
                        'unit_price' => $admissionFee,
                        'total_price' => $admissionFee,
                        'related_id' => $admissionId,
                        'related_type' => 'admission',
                    ]);
                }

                // Generate room charge (daily rate) - ALWAYS CREATE THIS
                // Get room price, if not set, use default prices based on room type
                $roomPricePerDay = (float)($room['price'] ?? 0);
                $roomType = $admissionData['room_type'] ?? $room['room_type'] ?? 'Ward';
                
                // If room doesn't have price set, use default prices based on room type
                if ($roomPricePerDay <= 0) {
                    $defaultPrices = [
                        'Private' => 5000.00,
                        'Semi-Private' => 3000.00,
                        'Ward' => 1000.00,
                        'ICU' => 8000.00,
                        'Isolation' => 6000.00,
                        'NICU' => 10000.00,
                        'OR' => 15000.00,
                    ];
                    $roomPricePerDay = $defaultPrices[$roomType] ?? 1000.00; // Default to Ward price if unknown
                }
                
                // Always create room charge - this is REQUIRED
                $roomChargeNumber = $this->chargeModel->generateChargeNumber();
                $roomChargeAmount = $roomPricePerDay; // Initial charge for 1 day
                
                $roomChargeData = [
                    'consultation_id' => $this->request->getPost('consultation_id') ?: null,
                    'patient_id' => $this->request->getPost('patient_id'),
                    'charge_number' => $roomChargeNumber,
                    'total_amount' => $roomChargeAmount,
                    'status' => 'pending',
                    'notes' => 'Room charge - ' . ($room['room_number'] ?? 'N/A') . ' (' . $roomType . ') - Day 1',
                ];

                // Insert room charge - throw exception if it fails
                if (!$this->chargeModel->insert($roomChargeData)) {
                    $errors = $this->chargeModel->errors();
                    $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Database insert failed';
                    log_message('error', 'CRITICAL: Failed to create room charge for admission ID: ' . $admissionId . ', errors: ' . $errorMsg . ', data: ' . json_encode($roomChargeData));
                    throw new \Exception('Failed to create room charge: ' . $errorMsg);
                }
                
                $roomChargeId = $this->chargeModel->getInsertID();
                
                if (!$roomChargeId) {
                    log_message('error', 'CRITICAL: Room charge insert succeeded but no ID returned for admission ID: ' . $admissionId);
                    throw new \Exception('Room charge created but no ID returned');
                }
                
                log_message('info', 'Room charge created successfully - charge_id: ' . $roomChargeId . ', patient_id: ' . $this->request->getPost('patient_id') . ', amount: ' . $roomChargeAmount);
                
                // Add room charge billing item - throw exception if it fails
                $bedInfo = $bedNumber ? ' - Bed ' . $bedNumber : '';
                $billingItemData = [
                    'charge_id' => $roomChargeId,
                    'item_type' => 'room_charge',
                    'item_name' => 'Room Charge',
                    'description' => 'Room: ' . ($room['room_number'] ?? 'N/A') . $bedInfo . ' - ' . $roomType . ' (Day 1)',
                    'quantity' => 1.00, // 1 day
                    'unit_price' => $roomPricePerDay,
                    'total_price' => $roomChargeAmount,
                    'related_id' => $admissionId,
                    'related_type' => 'admission',
                ];
                
                if (!$this->billingItemModel->insert($billingItemData)) {
                    $errors = $this->billingItemModel->errors();
                    $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Database insert failed';
                    log_message('error', 'CRITICAL: Failed to create room charge billing item - charge_id: ' . $roomChargeId . ', errors: ' . $errorMsg);
                    throw new \Exception('Failed to create room charge billing item: ' . $errorMsg);
                }
                
                log_message('info', 'Room charge billing item created successfully - charge_id: ' . $roomChargeId);
                
                // Verify the charge was actually created
                $verifyCharge = $this->chargeModel->find($roomChargeId);
                if (!$verifyCharge) {
                    log_message('error', 'CRITICAL: Room charge verification failed - charge_id: ' . $roomChargeId . ' not found after creation');
                    throw new \Exception('Room charge verification failed - charge not found in database');
                }
            } else {
                log_message('error', 'CRITICAL: charges or billing_items table does not exist');
                throw new \Exception('Required tables (charges, billing_items) do not exist');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/admission/view/' . $admissionId)->with('success', 'Patient admitted successfully. Admission charge and room charge have been generated.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to admit patient: ' . $e->getMessage());
        }
    }

    /**
     * View admission details
     */
    public function view($id)
    {
        $role = session()->get('role');
        // Only Nurse and Receptionist can view admission details
        // Doctor can view consultation but not admission details
        if (!in_array($role, ['nurse', 'receptionist'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Nurse and Receptionist can view admission details.');
        }

        $db = \Config\Database::connect();

        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.contact, ap.address,
                     r.ward, r.room_number, r.room_type as room_type_name,
                     u.username as attending_physician_name,
                     req.username as requested_by_name,
                     proc.username as processed_by_name')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('users u', 'u.id = a.attending_physician_id', 'left')
            ->join('users req', 'req.id = a.requested_by', 'left')
            ->join('users proc', 'proc.id = a.processed_by', 'left')
            ->where('a.id', $id)
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->back()->with('error', 'Admission not found.');
        }

        $data = [
            'title' => 'Admission Details',
            'admission' => $admission,
        ];

        return view('admission/view', $data);
    }

    /**
     * Get available beds for a room (AJAX)
     * Only returns beds with status 'available' - excludes occupied and maintenance beds
     */
    public function getBeds($roomId)
    {
        $role = session()->get('role');
        if (!session()->get('logged_in') || !in_array($role, ['doctor', 'nurse', 'receptionist'])) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        // Only get beds that are available (not occupied or in maintenance)
        // Use database query builder for case-insensitive check and current_patient_id check
        $db = \Config\Database::connect();
        $beds = $db->table('beds')
            ->select('id, bed_number, status, current_patient_id, room_id')
            ->where('room_id', $roomId)
            ->where('(LOWER(status) = "available" OR status IS NULL)', null, false)
            ->where('(LOWER(status) != "occupied" OR status IS NULL)', null, false)
            ->where('current_patient_id IS NULL', null, false)
            ->orderBy('bed_number', 'ASC')
            ->get()
            ->getResultArray();

        // Log for debugging
        log_message('info', "Admission getBeds: Found " . count($beds) . " available beds for room_id={$roomId}");

        return $this->response->setJSON(['beds' => $beds]);
    }
}

