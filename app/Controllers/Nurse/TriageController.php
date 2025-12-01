<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\HMSPatientModel;
use App\Models\AdminPatientModel;
use App\Models\TriageModel;
use App\Models\AuditLogModel;
use App\Models\DoctorDirectoryModel;

class TriageController extends BaseController
{
    protected $patientModel;
    protected $adminPatientModel;
    protected $triageModel;
    protected $auditLogModel;
    protected $doctorModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->patientModel = new HMSPatientModel();
        $this->adminPatientModel = new AdminPatientModel();
        $this->triageModel = new TriageModel();
        $this->auditLogModel = new AuditLogModel();
        $this->doctorModel = new DoctorDirectoryModel();
    }

    /**
     * Display triage dashboard - list of emergency patients awaiting triage
     */
    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $db = \Config\Database::connect();
        $nurseId = session()->get('user_id');

        // Get emergency patients awaiting triage
        $emergencyPatients = [];

        // From patients table
        if ($db->tableExists('patients')) {
            $patients = $this->patientModel
                ->where('visit_type', 'Emergency')
                ->where('triage_status', 'pending')
                ->orderBy('created_at', 'DESC')
                ->findAll();

            foreach ($patients as $patient) {
                // Check if triage already exists
                $existingTriage = $this->triageModel
                    ->where('patient_id', $patient['patient_id'])
                    ->where('status', 'pending')
                    ->first();

                if (!$existingTriage) {
                    $age = $patient['age'] ?? null;
                    if (empty($age) && !empty($patient['date_of_birth'])) {
                        try {
                            $birth = new \DateTime($patient['date_of_birth']);
                            $today = new \DateTime();
                            $age = (int)$today->diff($birth)->y;
                        } catch (\Exception $e) {
                            $age = null;
                        }
                    }

                    $emergencyPatients[] = [
                        'id' => $patient['patient_id'],
                        'source' => 'patients',
                        'name' => $patient['full_name'] ?? ($patient['first_name'] . ' ' . $patient['last_name']),
                        'age' => $age,
                        'gender' => $patient['gender'] ?? 'N/A',
                        'purpose' => $patient['purpose'] ?? 'Emergency',
                        'registration_date' => $patient['created_at'] ?? $patient['registration_date'] ?? date('Y-m-d'),
                    ];
                }
            }
        }

        // From admin_patients table
        if ($db->tableExists('admin_patients')) {
            $adminPatients = $this->adminPatientModel
                ->where('visit_type', 'Emergency')
                ->where('triage_status', 'pending')
                ->orderBy('created_at', 'DESC')
                ->findAll();

            foreach ($adminPatients as $patient) {
                $existingTriage = $this->triageModel
                    ->where('patient_id', $patient['id'])
                    ->where('status', 'pending')
                    ->first();

                if (!$existingTriage) {
                    $age = null;
                    if (!empty($patient['birthdate'])) {
                        try {
                            $birth = new \DateTime($patient['birthdate']);
                            $today = new \DateTime();
                            $age = (int)$today->diff($birth)->y;
                        } catch (\Exception $e) {
                            $age = null;
                        }
                    }

                    $emergencyPatients[] = [
                        'id' => $patient['id'],
                        'source' => 'admin_patients',
                        'name' => ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''),
                        'age' => $age,
                        'gender' => $patient['gender'] ?? 'N/A',
                        'purpose' => 'Emergency',
                        'registration_date' => $patient['created_at'] ?? date('Y-m-d'),
                    ];
                }
            }
        }

        // Get triaged patients (completed triage, may need doctor assignment)
        $triagedPatients = $this->triageModel
            ->select('triage.*, patients.full_name as patient_name, patients.patient_id')
            ->join('patients', 'patients.patient_id = triage.patient_id', 'left')
            ->where('triage.status', 'completed')
            ->where('triage.sent_to_doctor', 0)
            ->orderBy('triage.created_at', 'DESC')
            ->findAll();

        return view('nurse/triage/index', [
            'title' => 'Nurse Triage',
            'emergencyPatients' => $emergencyPatients,
            'triagedPatients' => $triagedPatients,
        ]);
    }

    /**
     * Show triage form for a specific patient
     */
    public function triage($patientId, $source = 'patients')
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'Unauthorized');
        }

        $nurseId = session()->get('user_id');

        // Get patient data
        if ($source === 'patients') {
            $patient = $this->patientModel->find($patientId);
        } else {
            $patient = $this->adminPatientModel->find($patientId);
        }

        if (!$patient) {
            return redirect()->to('/nurse/triage')->with('error', 'Patient not found');
        }

        // Check if triage already exists
        $existingTriage = $this->triageModel
            ->where('patient_id', $patientId)
            ->where('status', 'pending')
            ->first();

        return view('nurse/triage/form', [
            'title' => 'Perform Triage',
            'patient' => $patient,
            'patientSource' => $source,
            'existingTriage' => $existingTriage,
        ]);
    }

    /**
     * Save triage data
     */
    public function save()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $nurseId = session()->get('user_id');
        $patientId = $this->request->getPost('patient_id');
        $patientSource = $this->request->getPost('patient_source');
        $triageLevel = $this->request->getPost('triage_level');
        $chiefComplaint = $this->request->getPost('chief_complaint');
        $notes = $this->request->getPost('notes');

        // Vital signs
        $vitalSigns = [
            'heart_rate' => $this->request->getPost('heart_rate'),
            'blood_pressure_systolic' => $this->request->getPost('blood_pressure_systolic'),
            'blood_pressure_diastolic' => $this->request->getPost('blood_pressure_diastolic'),
            'temperature' => $this->request->getPost('temperature'),
            'oxygen_saturation' => $this->request->getPost('oxygen_saturation'),
            'respiratory_rate' => $this->request->getPost('respiratory_rate'),
        ];

        // Validation
        if (empty($patientId) || empty($triageLevel)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Patient ID and Triage Level are required'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Save triage record
            $triageData = [
                'patient_id' => $patientId,
                'nurse_id' => $nurseId,
                'triage_level' => $triageLevel,
                'vital_signs' => json_encode($vitalSigns),
                'chief_complaint' => $chiefComplaint,
                'notes' => $notes,
                'status' => 'completed',
                'sent_to_doctor' => ($triageLevel === 'Critical') ? 1 : 0,
            ];

            // Check if triage exists
            $existingTriage = $this->triageModel
                ->where('patient_id', $patientId)
                ->where('status', 'pending')
                ->first();

            if ($existingTriage) {
                $this->triageModel->update($existingTriage['id'], $triageData);
                $triageId = $existingTriage['id'];
            } else {
                $this->triageModel->insert($triageData);
                $triageId = $this->triageModel->getInsertID();
            }

            // Update patient triage_status
            if ($patientSource === 'patients') {
                $this->patientModel->update($patientId, [
                    'triage_status' => strtolower($triageLevel),
                ]);
            } else {
                $this->adminPatientModel->update($patientId, [
                    'triage_status' => strtolower($triageLevel),
                ]);
            }

            // Get patient name for audit log
            if ($patientSource === 'patients') {
                $patient = $this->patientModel->find($patientId);
                $patientName = $patient['full_name'] ?? 'Patient ' . $patientId;
            } else {
                $patient = $this->adminPatientModel->find($patientId);
                $patientName = ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '');
            }

            // Audit log
            $db = \Config\Database::connect();
            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->insert([
                    'action' => 'triage_performed',
                    'user_id' => $nurseId,
                    'user_role' => 'nurse',
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                    'description' => "Nurse performed triage for patient: {$patientName}. Triage Level: {$triageLevel}",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'patient_id' => $patientId,
                        'patient_source' => $patientSource,
                        'triage_level' => $triageLevel,
                        'chief_complaint' => $chiefComplaint,
                    ]),
                    'ip_address' => $this->request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // If Critical, automatically assign to ER doctor or on-duty doctor
            if ($triageLevel === 'Critical') {
                // Find available ER doctor or on-duty doctor
                $erDoctor = $this->findEmergencyDoctor();
                
                if ($erDoctor) {
                    $triageData['doctor_id'] = $erDoctor['id'];
                    $triageData['sent_to_doctor'] = 1;
                    $this->triageModel->update($triageId, [
                        'doctor_id' => $erDoctor['id'],
                        'sent_to_doctor' => 1,
                    ]);

                    // Update patient with doctor assignment
                    if ($patientSource === 'patients') {
                        $this->patientModel->update($patientId, ['doctor_id' => $erDoctor['id']]);
                    } else {
                        $this->adminPatientModel->update($patientId, ['doctor_id' => $erDoctor['id']]);
                    }

                    // Create consultation record
                    if ($db->tableExists('consultations')) {
                        $db->table('consultations')->insert([
                            'doctor_id' => $erDoctor['id'],
                            'patient_id' => $patientId,
                            'consultation_date' => date('Y-m-d'),
                            'consultation_time' => date('H:i:s'),
                            'type' => 'upcoming',
                            'status' => 'pending',
                            'notes' => "Emergency case - Critical triage level. {$chiefComplaint}",
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }

                    // Audit log for automatic doctor assignment
                    if ($db->tableExists('audit_logs')) {
                        $db->table('audit_logs')->insert([
                            'action' => 'emergency_doctor_assignment',
                            'user_id' => $nurseId,
                            'user_role' => 'nurse',
                            'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                            'description' => "Critical patient automatically assigned to Dr. {$erDoctor['name']}",
                            'related_id' => $patientId,
                            'related_type' => 'patient',
                            'metadata' => json_encode([
                                'patient_id' => $patientId,
                                'patient_source' => $patientSource,
                                'doctor_id' => $erDoctor['id'],
                                'doctor_name' => $erDoctor['name'],
                                'triage_level' => $triageLevel,
                            ]),
                            'ip_address' => $this->request->getIPAddress(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to save triage data'
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Triage completed successfully',
                'critical' => ($triageLevel === 'Critical'),
                'redirect' => ($triageLevel === 'Critical') ? '/nurse/triage' : '/nurse/triage'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Triage save error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Send patient to doctor after triage (for Moderate/Minor cases)
     */
    public function sendToDoctor()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $triageId = $this->request->getPost('triage_id');
        $doctorId = $this->request->getPost('doctor_id');

        if (empty($triageId) || empty($doctorId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Triage ID and Doctor ID are required'
            ])->setStatusCode(400);
        }

        $triage = $this->triageModel->find($triageId);
        if (!$triage) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Triage record not found'
            ])->setStatusCode(404);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update triage
            $this->triageModel->update($triageId, [
                'doctor_id' => $doctorId,
                'sent_to_doctor' => 1,
            ]);

            // Update patient
            $patientId = $triage['patient_id'];
            $this->patientModel->update($patientId, ['doctor_id' => $doctorId]);

            // Create consultation
            if ($db->tableExists('consultations')) {
                $db->table('consultations')->insert([
                    'doctor_id' => $doctorId,
                    'patient_id' => $patientId,
                    'consultation_date' => date('Y-m-d'),
                    'consultation_time' => date('H:i:s'),
                    'type' => 'upcoming',
                    'status' => 'pending',
                    'notes' => "Referred from Nurse Triage. Triage Level: {$triage['triage_level']}",
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Audit log
            $nurseId = session()->get('user_id');
            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->insert([
                    'action' => 'send_to_doctor_from_triage',
                    'user_id' => $nurseId,
                    'user_role' => 'nurse',
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                    'description' => "Nurse sent patient to doctor after triage",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'patient_id' => $patientId,
                        'patient_source' => $patientSource,
                        'doctor_id' => $doctorId,
                        'triage_id' => $triageId,
                    ]),
                    'ip_address' => $this->request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transComplete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Patient sent to doctor successfully'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Find available ER doctor or on-duty doctor
     */
    private function findEmergencyDoctor()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // First, try to find ER doctor
        if ($db->tableExists('doctors')) {
            $erDoctor = $db->table('doctors')
                ->where('specialization', 'Emergency Medicine')
                ->orWhere('specialization', 'ER')
                ->orWhere('specialization', 'Emergency')
                ->get()
                ->getRowArray();

            if ($erDoctor && $db->tableExists('users')) {
                $userDoctor = $db->table('users')
                    ->join('roles', 'roles.id = users.role_id', 'left')
                    ->where('users.id', $erDoctor['id'])
                    ->where('roles.name', 'doctor')
                    ->where('users.status', 'active')
                    ->get()
                    ->getRowArray();

                if ($userDoctor) {
                    return [
                        'id' => $userDoctor['id'],
                        'name' => $userDoctor['username'] ?? 'Dr. ' . $userDoctor['id'],
                    ];
                }
            }
        }

        // If no ER doctor, find any on-duty doctor
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $onDutyDoctor = $db->table('users')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('roles.name', 'doctor')
                ->where('users.status', 'active')
                ->get()
                ->getRowArray();

            if ($onDutyDoctor) {
                return [
                    'id' => $onDutyDoctor['id'],
                    'name' => $onDutyDoctor['username'] ?? 'Dr. ' . $onDutyDoctor['id'],
                ];
            }
        }

        return null;
    }
}

