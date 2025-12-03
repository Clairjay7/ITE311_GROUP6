<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use App\Models\ConsultationModel;
use App\Models\AdminPatientModel;

class AdmissionController extends BaseController
{
    protected $admissionModel;
    protected $consultationModel;
    protected $patientModel;

    public function __construct()
    {
        $this->admissionModel = new AdmissionModel();
        $this->consultationModel = new ConsultationModel();
        $this->patientModel = new AdminPatientModel();
    }

    /**
     * List pending admissions (for Nurse/Receptionist)
     */
    public function pending()
    {
        // Check if user is logged in and is a nurse or receptionist
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['nurse', 'receptionist'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();

        // Get ER admission requests that need room assignment (for Receptionist)
        $erAdmissionRequests = [];
        if ($db->tableExists('admission_requests')) {
            $erAdmissionRequests = $db->table('admission_requests ar')
                ->select('ar.*, 
                         ap.firstname, ap.lastname, ap.contact, ap.id as admin_patient_id,
                         patients.full_name, patients.patient_id as hms_patient_id,
                         triage.triage_level, triage.chief_complaint, triage.disposition,
                         doctor.username as doctor_name')
                ->join('admin_patients ap', 'ap.id = ar.patient_id', 'left')
                ->join('patients', 'patients.patient_id = ar.patient_id', 'left')
                ->join('triage', 'triage.id = ar.triage_id', 'left')
                ->join('users as doctor', 'doctor.id = ar.doctor_id', 'left')
                ->where('ar.status', 'pending_room_assignment')
                ->where('triage.disposition', 'ER')
                ->orderBy('ar.created_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        // Get consultations marked for admission but not yet admitted (for regular admissions)
        $pendingAdmissions = $db->table('consultations c')
            ->select('c.*, ap.firstname, ap.lastname, ap.contact, 
                     u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->join('users u', 'u.id = c.doctor_id', 'left')
            ->where('c.for_admission', 1)
            ->where('c.type', 'completed')
            ->where('c.status', 'approved')
            ->where('c.deleted_at', null)
            ->get()
            ->getResultArray();

        // Filter out already admitted
        $filtered = [];
        foreach ($pendingAdmissions as $consultation) {
            $existingAdmission = $db->table('admissions')
                ->where('consultation_id', $consultation['id'])
                ->where('status !=', 'discharged')
                ->where('status !=', 'cancelled')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            
            if (!$existingAdmission) {
                $filtered[] = $consultation;
            }
        }

        // Get available ER rooms
        $erRooms = [];
        if ($db->tableExists('rooms')) {
            $erRooms = $db->table('rooms')
                ->where('room_type', 'ER')
                ->orWhere('ward', 'Emergency')
                ->orWhere('ward', 'ER')
                ->where('status', 'Available')
                ->orWhere('status', 'available')
                ->orderBy('room_number', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Pending Admissions',
            'pendingAdmissions' => $filtered,
            'erAdmissionRequests' => $erAdmissionRequests,
            'erRooms' => $erRooms,
        ];

        return view('nurse/admission/pending', $data);
    }

    /**
     * Assign ER room to admission request
     */
    public function assignERRoom()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to access this page.'
            ])->setStatusCode(401);
        }

        $role = session()->get('role');
        if (!in_array($role, ['nurse', 'receptionist'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $admissionRequestId = $this->request->getPost('admission_request_id');
        $roomId = $this->request->getPost('room_id');
        $bedNumber = $this->request->getPost('bed_number') ?? null;

        if (empty($admissionRequestId) || empty($roomId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admission request ID and Room ID are required.'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        // Get admission request
        $admissionRequest = $db->table('admission_requests')
            ->where('id', $admissionRequestId)
            ->where('status', 'pending_room_assignment')
            ->get()
            ->getRowArray();

        if (!$admissionRequest) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admission request not found or already processed.'
            ])->setStatusCode(404);
        }

        // Get room
        $room = $db->table('rooms')->where('id', $roomId)->get()->getRowArray();
        if (!$room) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Room not found.'
            ])->setStatusCode(404);
        }

        // Check if room is available
        if (($room['status'] ?? '') !== 'Available' && ($room['status'] ?? '') !== 'available') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Selected room is not available.'
            ])->setStatusCode(400);
        }

        // Start transaction
        $db->transStart();

        try {
            // Update room status
            $db->table('rooms')
                ->where('id', $roomId)
                ->update([
                    'current_patient_id' => $admissionRequest['patient_id'],
                    'status' => 'Occupied',
                ]);

            // Update admission request with room assignment and change status to pending_doctor_approval
            $db->table('admission_requests')
                ->where('id', $admissionRequestId)
                ->update([
                    'room_id' => $roomId,
                    'bed_number' => $bedNumber,
                    'status' => 'pending_doctor_approval', // Now ready for doctor approval
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Update patient status
            if ($db->tableExists('admin_patients')) {
                $db->table('admin_patients')
                    ->where('id', $admissionRequest['patient_id'])
                    ->update([
                        'triage_status' => 'pending_doctor_approval',
                        'room_number' => $room['room_number'],
                    ]);
            }

            // Notify doctor about admission request ready for approval
            if ($db->tableExists('doctor_notifications') && !empty($admissionRequest['doctor_id'])) {
                $patient = $db->table('admin_patients')
                    ->where('id', $admissionRequest['patient_id'])
                    ->get()
                    ->getRowArray();
                
                $patientName = ($patient ? ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '') : 'Patient');
                
                $db->table('doctor_notifications')->insert([
                    'doctor_id' => $admissionRequest['doctor_id'],
                    'type' => 'admission_request',
                    'title' => 'ER Admission Request - Approval Required',
                    'message' => "Patient {$patientName} has been assigned ER room {$room['room_number']}. Please review and approve the admission request.",
                    'related_id' => $admissionRequestId,
                    'related_type' => 'admission_request',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to assign ER room. Please try again.'
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'ER room assigned successfully. Doctor has been notified for approval.'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Failed to assign ER room: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

