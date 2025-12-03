<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\RoomModel;
use App\Models\BedModel;
use App\Models\AdminPatientModel;
use App\Models\PatientModel;
use App\Models\TriageModel;
use App\Models\AdmissionModel;

class ERBedController extends BaseController
{
    protected $roomModel;
    protected $bedModel;
    protected $adminPatientModel;
    protected $patientModel;
    protected $triageModel;
    protected $admissionModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->bedModel = new BedModel();
        $this->adminPatientModel = new AdminPatientModel();
        $this->patientModel = new PatientModel();
        $this->triageModel = new TriageModel();
        $this->admissionModel = new AdmissionModel();
    }

    /**
     * ER Bed Dashboard - View all ER beds and their status
     * Accessible by: Doctors (after triage), Nurses, Receptionists
     */
    public function index()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['doctor', 'nurse', 'receptionist', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();

        // Get ER rooms
        $erRooms = [];
        if ($db->tableExists('rooms')) {
            $erRooms = $db->table('rooms')
                ->where('room_type', 'ER')
                ->orWhere('ward', 'Emergency')
                ->orWhere('ward', 'ER')
                ->orderBy('room_number', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get ER beds with patient information
        $erBeds = [];
        if ($db->tableExists('beds')) {
            $erBeds = $db->table('beds')
                ->select('beds.*, rooms.room_number, rooms.ward, rooms.room_type,
                         admin_patients.firstname, admin_patients.lastname,
                         patients.full_name as patient_full_name,
                         triage.triage_level, triage.chief_complaint,
                         admissions.admission_date, admissions.status as admission_status')
                ->join('rooms', 'rooms.id = beds.room_id', 'left')
                ->join('admin_patients', 'admin_patients.id = beds.current_patient_id', 'left')
                ->join('patients', 'patients.patient_id = beds.current_patient_id', 'left')
                ->join('triage', 'triage.patient_id = beds.current_patient_id AND triage.status = "completed"', 'left')
                ->join('admissions', 'admissions.patient_id = beds.current_patient_id AND admissions.status = "admitted"', 'left')
                ->where('rooms.room_type', 'ER')
                ->orWhere('rooms.ward', 'Emergency')
                ->orWhere('rooms.ward', 'ER')
                ->orderBy('rooms.room_number', 'ASC')
                ->orderBy('beds.bed_number', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get patients in ER (from triage with status "in_er")
        // Also get patients approved by doctor for admission (ready for receptionist to assign ER bed)
        $erPatients = [];
        $doctorApprovedPatients = [];
        
        // Get patients approved by doctor for admission (receptionist assigns ER bed)
        if ($db->tableExists('admission_requests')) {
            $approvedRequests = $db->table('admission_requests')
                ->select('admission_requests.*, 
                         admin_patients.firstname, admin_patients.lastname, admin_patients.id as admin_patient_id,
                         patients.full_name, patients.patient_id as hms_patient_id,
                         triage.triage_level, triage.chief_complaint, triage.id as triage_id')
                ->join('admin_patients', 'admin_patients.id = admission_requests.patient_id', 'left')
                ->join('patients', 'patients.patient_id = admission_requests.patient_id', 'left')
                ->join('triage', 'triage.id = admission_requests.triage_id', 'left')
                ->where('admission_requests.status', 'doctor_approved') // Doctor approved - ready for ER bed assignment
                ->orderBy('admission_requests.approved_at', 'DESC')
                ->get()
                ->getResultArray();
            
            foreach ($approvedRequests as $request) {
                $patientId = $request['admin_patient_id'] ?? $request['hms_patient_id'] ?? $request['patient_id'] ?? null;
                $patientName = '';
                if ($request['firstname'] && $request['lastname']) {
                    $patientName = $request['firstname'] . ' ' . $request['lastname'];
                } elseif ($request['full_name']) {
                    $patientName = $request['full_name'];
                } else {
                    $patientName = 'Patient ' . $patientId;
                }
                
                // Check if patient already has a bed assigned
                $hasBed = false;
                if ($db->tableExists('beds')) {
                    $bed = $db->table('beds')
                        ->where('current_patient_id', $patientId)
                        ->where('status', 'occupied')
                        ->get()
                        ->getRowArray();
                    $hasBed = !empty($bed);
                }
                
                if (!$hasBed) {
                    $doctorApprovedPatients[] = [
                        'patient_id' => $patientId,
                        'patient_name' => $patientName,
                        'patient_source' => $request['admin_patient_id'] ? 'admin_patients' : 'patients',
                        'admission_request_id' => $request['id'] ?? null,
                        'triage_id' => $request['triage_id'] ?? null,
                        'triage_level' => $request['triage_level'] ?? 'N/A',
                        'chief_complaint' => $request['chief_complaint'] ?? 'N/A',
                        'admission_reason' => $request['admission_reason'] ?? '',
                        'created_at' => $request['approved_at'] ?? $request['created_at'] ?? date('Y-m-d H:i:s'),
                        'approved_by_doctor' => true,
                    ];
                }
            }
        }
        
        if ($db->tableExists('triage')) {
            $erTriages = $this->triageModel
                ->where('disposition', 'ER')
                ->where('status', 'completed')
                ->orderBy('created_at', 'DESC')
                ->findAll();

            foreach ($erTriages as $triage) {
                $patientId = $triage['patient_id'];
                $patient = null;
                $patientName = 'Unknown Patient';
                $patientSource = 'patients';

                // Check admin_patients first
                $adminPatient = $this->adminPatientModel->find($patientId);
                if ($adminPatient) {
                    $patient = $adminPatient;
                    $patientName = ($adminPatient['firstname'] ?? '') . ' ' . ($adminPatient['lastname'] ?? '');
                    $patientSource = 'admin_patients';
                } else {
                    // Check patients table
                    $hmsPatient = $this->patientModel->find($patientId);
                    if ($hmsPatient) {
                        $patient = $hmsPatient;
                        $patientName = $hmsPatient['full_name'] ?? 'Patient ' . $patientId;
                        $patientSource = 'patients';
                    }
                }

                // Check if patient already has a bed assigned
                $hasBed = false;
                foreach ($erBeds as $bed) {
                    if ($bed['current_patient_id'] == $patientId) {
                        $hasBed = true;
                        break;
                    }
                }

                if ($patient && !$hasBed) {
                    $erPatients[] = [
                        'patient_id' => $patientId,
                        'patient_name' => $patientName,
                        'patient_source' => $patientSource,
                        'triage_id' => $triage['id'],
                        'triage_level' => $triage['triage_level'],
                        'chief_complaint' => $triage['chief_complaint'],
                        'created_at' => $triage['created_at'],
                    ];
                }
            }
        }

        return view('nurse/er-beds/index', [
            'title' => 'ER Bed Management',
            'erRooms' => $erRooms,
            'erBeds' => $erBeds,
            'erPatients' => $erPatients,
            'doctorApprovedPatients' => $doctorApprovedPatients, // Patients approved by doctor (receptionist assigns bed)
            'isReceptionist' => $isReceptionist, // Flag to show/hide assign bed buttons
        ]);
    }

    /**
     * Assign patient to ER bed
     * Accessible by: Receptionists ONLY (after doctor approval)
     * FLOW: Nurse marks for admission → Doctor approves → Receptionist assigns ER bed
     */
    public function assign()
    {
        $userRole = session()->get('role');
        // Only receptionist can assign ER beds after doctor approval
        if (!session()->get('logged_in') || !in_array($userRole, ['receptionist', 'admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied. Only Receptionist can assign ER beds after doctor approval.'])->setStatusCode(401);
        }

        $patientId = $this->request->getPost('patient_id');
        $bedId = $this->request->getPost('bed_id');
        $triageId = $this->request->getPost('triage_id');

        if (!$patientId || !$bedId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient ID and Bed ID are required']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get bed
            $bed = $this->bedModel->find($bedId);
            if (!$bed) {
                throw new \Exception('Bed not found');
            }

            if ($bed['status'] !== 'available') {
                throw new \Exception('Bed is not available');
            }

            // Get patient info
            $patientSource = 'patients';
            $adminPatient = $this->adminPatientModel->find($patientId);
            if ($adminPatient) {
                $patientSource = 'admin_patients';
                $patientName = ($adminPatient['firstname'] ?? '') . ' ' . ($adminPatient['lastname'] ?? '');
            } else {
                $patient = $this->patientModel->find($patientId);
                if (!$patient) {
                    throw new \Exception('Patient not found');
                }
                $patientName = $patient['full_name'] ?? 'Patient ' . $patientId;
            }

            // Update bed
            $this->bedModel->update($bedId, [
                'status' => 'occupied',
                'current_patient_id' => $patientId,
            ]);

            // Update room status if needed
            if ($bed['room_id'] && $db->tableExists('rooms')) {
                $room = $this->roomModel->find($bed['room_id']);
                if ($room && $room['status'] === 'available') {
                    $this->roomModel->update($bed['room_id'], ['status' => 'occupied']);
                }
            }

            // Update triage with bed assignment
            if ($triageId) {
                $this->triageModel->update($triageId, [
                    'er_bed_id' => $bedId,
                ]);
            }
            
            // Update admission request status to "bed_assigned" (if admission_request_id exists)
            if ($admissionRequestId && $db->tableExists('admission_requests')) {
                $db->table('admission_requests')
                    ->where('id', $admissionRequestId)
                    ->update([
                        'status' => 'bed_assigned',
                        'er_bed_id' => $bedId,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            // Update admission record if exists
            if ($db->tableExists('admissions') && $patientSource === 'admin_patients') {
                $admission = $db->table('admissions')
                    ->where('patient_id', $patientId)
                    ->where('status', 'admitted')
                    ->orderBy('admission_date', 'DESC')
                    ->get()
                    ->getRowArray();

                if ($admission) {
                    $room = $this->roomModel->find($bed['room_id']);
                    $db->table('admissions')
                        ->where('id', $admission['id'])
                        ->update([
                            'room_id' => $bed['room_id'],
                            'bed_number' => $bed['bed_number'],
                            'room_type' => $room['room_type'] ?? 'ER',
                        ]);
                }
            }

            // Audit log
            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->insert([
                    'action' => 'er_bed_assigned',
                    'user_id' => session()->get('user_id'),
                    'user_role' => session()->get('role'),
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                    'description' => "Patient {$patientName} assigned to ER Bed: {$bed['bed_number']}",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'patient_id' => $patientId,
                        'bed_id' => $bedId,
                        'bed_number' => $bed['bed_number'],
                        'triage_id' => $triageId,
                    ]),
                    'ip_address' => $this->request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Patient assigned to ER Bed: {$bed['bed_number']}"
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'ER bed assignment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Release ER bed (patient discharged or transferred)
     * Accessible by: Doctors, Nurses, Receptionists
     */
    public function release()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['doctor', 'nurse', 'receptionist', 'admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(401);
        }

        $bedId = $this->request->getPost('bed_id');

        if (!$bedId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bed ID is required']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $bed = $this->bedModel->find($bedId);
            if (!$bed) {
                throw new \Exception('Bed not found');
            }

            $patientId = $bed['current_patient_id'];
            $bedNumber = $bed['bed_number'];

            // Release bed
            $this->bedModel->update($bedId, [
                'status' => 'available',
                'current_patient_id' => null,
            ]);

            // Check if room has other occupied beds
            if ($bed['room_id'] && $db->tableExists('beds')) {
                $occupiedBeds = $db->table('beds')
                    ->where('room_id', $bed['room_id'])
                    ->where('status', 'occupied')
                    ->countAllResults();

                if ($occupiedBeds === 0 && $db->tableExists('rooms')) {
                    $this->roomModel->update($bed['room_id'], ['status' => 'available']);
                }
            }

            // Update triage
            if ($patientId && $db->tableExists('triage')) {
                $db->table('triage')
                    ->where('patient_id', $patientId)
                    ->where('er_bed_id', $bedId)
                    ->update(['er_bed_id' => null]);
            }

            // Audit log
            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->insert([
                    'action' => 'er_bed_released',
                    'user_id' => session()->get('user_id'),
                    'user_role' => session()->get('role'),
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                    'description' => "ER Bed {$bedNumber} released",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'bed_id' => $bedId,
                        'bed_number' => $bedNumber,
                        'patient_id' => $patientId,
                    ]),
                    'ip_address' => $this->request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "ER Bed {$bedNumber} released successfully"
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'ER bed release error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get available ER beds (AJAX)
     * Accessible by: Doctors, Nurses, Receptionists
     */
    public function getAvailableBeds()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['doctor', 'nurse', 'receptionist', 'admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $availableBeds = [];

        if ($db->tableExists('beds') && $db->tableExists('rooms')) {
            $availableBeds = $db->table('beds')
                ->select('beds.*, rooms.room_number, rooms.ward, rooms.room_type')
                ->join('rooms', 'rooms.id = beds.room_id', 'left')
                ->where('beds.status', 'available')
                ->where('rooms.room_type', 'ER')
                ->orWhere('rooms.ward', 'Emergency')
                ->orWhere('rooms.ward', 'ER')
                ->orderBy('rooms.room_number', 'ASC')
                ->orderBy('beds.bed_number', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $this->response->setJSON([
            'success' => true,
            'beds' => $availableBeds
        ]);
    }
}

