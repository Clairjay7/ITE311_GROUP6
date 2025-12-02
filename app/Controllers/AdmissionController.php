<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use App\Models\ConsultationModel;
use App\Models\AdminPatientModel;
use App\Models\RoomModel;
use App\Models\BedModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;

class AdmissionController extends BaseController
{
    protected $admissionModel;
    protected $consultationModel;
    protected $patientModel;
    protected $roomModel;
    protected $bedModel;
    protected $chargeModel;
    protected $billingItemModel;

    public function __construct()
    {
        $this->admissionModel = new AdmissionModel();
        $this->consultationModel = new ConsultationModel();
        $this->patientModel = new AdminPatientModel();
        $this->roomModel = new RoomModel();
        $this->bedModel = new BedModel();
        $this->chargeModel = new ChargeModel();
        $this->billingItemModel = new BillingItemModel();
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

        // Get doctors for attending physician
        $doctors = $db->table('users u')
            ->select('u.id, u.username, r.name as role_name')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('r.name', 'doctor')
            ->where('u.status', 'active')
            ->get()
            ->getResultArray();

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
        if (!$room || $room['status'] !== 'Available') {
            return redirect()->back()->withInput()->with('error', 'Selected room is not available.');
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
            if ($bed['status'] !== 'available') {
                return redirect()->back()->withInput()->with('error', 'Selected bed is not available.');
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
            // Validate admission data before insert
            if (!$this->admissionModel->insert($admissionData)) {
                $errors = $this->admissionModel->errors();
                $errorMessage = 'Failed to create admission record';
                if (!empty($errors)) {
                    $errorMessage .= ': ' . implode(', ', array_values($errors));
                }
                throw new \Exception($errorMessage);
            }
            $admissionId = $this->admissionModel->getInsertID();

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


            // Generate admission charge
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
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/admission/view/' . $admissionId)->with('success', 'Patient admitted successfully. Admission charge has been generated.');

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
     */
    public function getBeds($roomId)
    {
        $role = session()->get('role');
        if (!session()->get('logged_in') || !in_array($role, ['doctor', 'nurse', 'receptionist'])) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $beds = $this->bedModel
            ->where('room_id', $roomId)
            ->where('status', 'available')
            ->orderBy('bed_number', 'ASC')
            ->findAll();

        return $this->response->setJSON(['beds' => $beds]);
    }
}

