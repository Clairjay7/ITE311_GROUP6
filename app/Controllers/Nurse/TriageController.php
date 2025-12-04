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
        // Include both patients and admin_patients
        $triagedPatients = [];
        
        // From patients table
        if ($db->tableExists('patients')) {
            $patientsTriage = $this->triageModel
                ->select('triage.*, patients.full_name as patient_name, patients.patient_id')
                ->join('patients', 'patients.patient_id = triage.patient_id', 'left')
                ->where('triage.status', 'completed')
                ->where('triage.sent_to_doctor', 0)
                ->where('patients.patient_id IS NOT NULL')
                ->orderBy('triage.created_at', 'DESC')
                ->findAll();
            
            foreach ($patientsTriage as $triage) {
                $triagedPatients[] = $triage;
            }
        }
        
        // From admin_patients table
        if ($db->tableExists('admin_patients')) {
            $adminTriage = $this->triageModel
                ->select('triage.*, admin_patients.firstname, admin_patients.lastname, admin_patients.id as patient_id')
                ->join('admin_patients', 'admin_patients.id = triage.patient_id', 'left')
                ->where('triage.status', 'completed')
                ->where('triage.sent_to_doctor', 0)
                ->where('admin_patients.id IS NOT NULL')
                ->orderBy('triage.created_at', 'DESC')
                ->findAll();
            
            foreach ($adminTriage as $triage) {
                // Format patient name
                $triage['patient_name'] = ($triage['firstname'] ?? '') . ' ' . ($triage['lastname'] ?? '');
                $triagedPatients[] = $triage;
            }
        }
        
        // Sort by created_at DESC
        usort($triagedPatients, function($a, $b) {
            $dateA = strtotime($a['created_at'] ?? '1970-01-01');
            $dateB = strtotime($b['created_at'] ?? '1970-01-01');
            return $dateB <=> $dateA;
        });

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
        $disposition = $this->request->getPost('disposition');
        $doctorId = $this->request->getPost('doctor_id'); // Doctor ID for inpatients and doctor assign
        // Removed: Nurse can no longer mark for admission
        // Doctor will mark for admission during consultation
        $nurseRecommendation = $this->request->getPost('nurse_recommendation');
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
        
        // Validate doctor_id for inpatients and doctor assign
        if (($disposition === 'Others' || $disposition === 'Doctor Assign') && empty($doctorId)) {
            $message = $disposition === 'Others' 
                ? 'Doctor selection is required for in-patient admission'
                : 'Doctor selection is required for consultation';
            return $this->response->setJSON([
                'success' => false,
                'message' => $message
            ])->setStatusCode(400);
        }

        // Auto-set disposition based on triage level if not provided
        if (empty($disposition)) {
            if ($triageLevel === 'Critical') {
                $disposition = 'ER';
            } elseif (in_array($triageLevel, ['Moderate', 'Minor'])) {
                $disposition = 'OPD';
            } else {
                $disposition = 'Pending';
            }
        }

        // Removed: for_admission logic - doctor will mark for admission

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Save triage record
            $triageData = [
                'patient_id' => $patientId,
                'nurse_id' => $nurseId,
                'triage_level' => $triageLevel,
                'disposition' => $disposition,
                // Removed: for_admission - doctor will mark for admission
                'vital_signs' => json_encode($vitalSigns),
                'chief_complaint' => $chiefComplaint,
                'notes' => $notes . (!empty($nurseRecommendation) ? "\n\n[Nurse Recommendation]\n" . $nurseRecommendation : ''),
                'status' => 'completed',
                'sent_to_doctor' => ($triageLevel === 'Critical' || $disposition === 'ER' || $disposition === 'Others' || $disposition === 'Doctor Assign') ? 1 : 0,
            ];
            
            // Add doctor_id for inpatients (disposition = "Others") and doctor assign
            if (($disposition === 'Others' || $disposition === 'Doctor Assign') && !empty($doctorId)) {
                $triageData['doctor_id'] = $doctorId;
                $triageData['assigned_doctor_id'] = $doctorId;
            }

            // Check if triage exists
            $existingTriage = $this->triageModel
                ->where('patient_id', $patientId)
                ->where('status', 'pending')
                ->first();

            if ($existingTriage) {
                $updateResult = $this->triageModel->update($existingTriage['id'], $triageData);
                if (!$updateResult) {
                    $errors = $this->triageModel->errors();
                    throw new \Exception('Failed to update triage: ' . (!empty($errors) ? implode(', ', $errors) : 'Database update failed'));
                }
                $triageId = $existingTriage['id'];
            } else {
                // Skip validation temporarily to avoid issues with new fields
                $this->triageModel->skipValidation(true);
                $insertResult = $this->triageModel->insert($triageData);
                if (!$insertResult) {
                    $errors = $this->triageModel->errors();
                    throw new \Exception('Failed to insert triage: ' . (!empty($errors) ? implode(', ', $errors) : 'Database insert failed'));
                }
                $triageId = $this->triageModel->getInsertID();
                $this->triageModel->skipValidation(false);
            }

            // Update patient triage_status and visit_type
            $patientUpdateData = [
                'triage_status' => strtolower($triageLevel),
            ];
            
            // Set visit_type based on disposition
            if ($disposition === 'ER' || $triageLevel === 'Critical') {
                $patientUpdateData['visit_type'] = 'Emergency';
            } elseif ($disposition === 'OPD') {
                $patientUpdateData['visit_type'] = 'Consultation';
            } elseif ($disposition === 'Doctor Assign') {
                $patientUpdateData['visit_type'] = 'Consultation';
                // Assign doctor for consultation
                if (!empty($doctorId)) {
                    $patientUpdateData['doctor_id'] = $doctorId;
                }
            } elseif ($disposition === 'Others') {
                $patientUpdateData['visit_type'] = 'Emergency'; // Will be admitted
                // Assign doctor for inpatients
                if (!empty($doctorId)) {
                    $patientUpdateData['doctor_id'] = $doctorId;
                }
            }
            
            if ($patientSource === 'patients') {
                $this->patientModel->update($patientId, $patientUpdateData);
            } else {
                $this->adminPatientModel->update($patientId, $patientUpdateData);
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

            // Route patient based on disposition
            if ($disposition === 'ER') {
                // ER Workflow: Create admission request → Doctor approves → Receptionist assigns ER bed
                $erDoctor = $this->findEmergencyDoctor();
                
                if ($erDoctor) {
                    $this->triageModel->update($triageId, [
                        'doctor_id' => $erDoctor['id'],
                        'assigned_doctor_id' => $erDoctor['id'], // Also set assigned_doctor_id for admission request matching
                        'sent_to_doctor' => 1,
                        'disposition' => 'ER',
                    ]);

                    // Update patient with doctor assignment and status
                    if ($patientSource === 'patients') {
                        $this->patientModel->update($patientId, [
                            'doctor_id' => $erDoctor['id'],
                            'triage_status' => 'pending_room_assignment', // Status: Waiting for receptionist to assign ER room
                            'visit_type' => 'Emergency',
                        ]);
                    } else {
                        $this->adminPatientModel->update($patientId, [
                            'doctor_id' => $erDoctor['id'],
                            'triage_status' => 'pending_room_assignment', // Status: Waiting for receptionist to assign ER room
                            'visit_type' => 'Emergency',
                        ]);
                    }

                    // Create consultation record for ER
                    $consultationId = null;
                    if ($db->tableExists('consultations') && $patientSource === 'admin_patients') {
                        try {
                            $consultationNotes = "Emergency Room (ER) - {$triageLevel} triage level.\n";
                            $consultationNotes .= "Chief Complaint: {$chiefComplaint}\n";
                            if (!empty($nurseRecommendation)) {
                                $consultationNotes .= "\nNurse Recommendation: {$nurseRecommendation}";
                            }
                            $consultationNotes .= "\n\n[ER PATIENT - Receptionist needs to assign ER room first, then doctor will approve admission]";

                            $db->table('consultations')->insert([
                                'doctor_id' => $erDoctor['id'],
                                'patient_id' => $patientId,
                                'consultation_date' => date('Y-m-d'),
                                'consultation_time' => date('H:i:s'),
                                'type' => 'upcoming',
                                'status' => 'approved',
                                'notes' => $consultationNotes,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                            $consultationId = $db->insertID();
                        } catch (\Exception $e) {
                            log_message('error', 'Failed to create ER consultation: ' . $e->getMessage());
                        }
                    }

                    // Create admission request with status "pending_room_assignment" (receptionist must assign ER room first)
                    if ($db->tableExists('admission_requests')) {
                        try {
                            $admissionReason = "Emergency Room (ER) Admission Request - {$triageLevel} Triage Level\n";
                            $admissionReason .= "Chief Complaint: {$chiefComplaint}\n";
                            if (!empty($nurseRecommendation)) {
                                $admissionReason .= "\nNurse Recommendation: {$nurseRecommendation}";
                            }
                            $admissionReason .= "\n\n[ER PATIENT - Receptionist needs to assign ER room first, then doctor will approve admission]";
                            
                            $admissionRequestData = [
                                'patient_id' => $patientId,
                                'triage_id' => $triageId,
                                'consultation_id' => $consultationId,
                                'doctor_id' => $erDoctor['id'], // Store doctor_id directly in admission_requests
                                'requested_by' => $nurseId,
                                'requested_by_role' => 'nurse',
                                'status' => 'pending_room_assignment', // Pending receptionist to assign ER room
                                'admission_reason' => $admissionReason,
                                'nurse_recommendation' => $nurseRecommendation ?? '',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            
                            $db->table('admission_requests')->insert($admissionRequestData);
                            $admissionRequestId = $db->insertID();
                            
                            log_message('info', "ER admission request created for patient {$patientId} (Request ID: {$admissionRequestId}) - Pending ER room assignment by receptionist");
                            
                            // Notify receptionist about pending ER room assignment (if notification system exists)
                            // Note: Receptionist will see this in their pending admissions dashboard
                        } catch (\Exception $e) {
                            log_message('error', 'Failed to create ER admission request: ' . $e->getMessage());
                        }
                    }

                    // Audit log for ER
                    if ($db->tableExists('audit_logs')) {
                        $db->table('audit_logs')->insert([
                            'action' => 'er_admission_request_created',
                            'user_id' => $nurseId,
                            'user_role' => 'nurse',
                            'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                            'description' => "Patient {$patientName} routed to ER. Admission request created and assigned to Dr. {$erDoctor['name']}. Receptionist must assign ER room first, then doctor will approve admission.",
                            'related_id' => $patientId,
                            'related_type' => 'patient',
                            'metadata' => json_encode([
                                'patient_id' => $patientId,
                                'triage_level' => $triageLevel,
                                'disposition' => 'ER',
                                'doctor_id' => $erDoctor['id'],
                                'admission_request_status' => 'pending_room_assignment',
                            ]),
                            'ip_address' => $this->request->getIPAddress(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            } elseif ($disposition === 'Doctor Assign') {
                // Doctor Assign Workflow - Send to selected doctor for consultation
                // Doctor can mark for admission if needed
                if (!empty($doctorId)) {
                    // Use the selected doctor from dropdown
                    $this->triageModel->update($triageId, [
                        'doctor_id' => $doctorId,
                        'assigned_doctor_id' => $doctorId,
                        'sent_to_doctor' => 1,
                        'disposition' => 'Doctor Assign',
                    ]);

                    // Update patient with doctor assignment
                    if ($patientSource === 'patients') {
                        $this->patientModel->update($patientId, ['doctor_id' => $doctorId]);
                    } else {
                        $this->adminPatientModel->update($patientId, ['doctor_id' => $doctorId]);
                    }

                    // Create consultation for doctor
                    if ($db->tableExists('consultations') && $patientSource === 'admin_patients') {
                        try {
                            $consultationNotes = "Triage Level: {$triageLevel}. Chief Complaint: {$chiefComplaint}\n";
                            if (!empty($nurseRecommendation)) {
                                $consultationNotes .= "\nNurse Recommendation: {$nurseRecommendation}";
                            }
                            $consultationNotes .= "\n\n[DOCTOR ASSIGN - Doctor can mark for admission if needed]";

                            $db->table('consultations')->insert([
                                'doctor_id' => $doctorId,
                                'patient_id' => $patientId,
                                'consultation_date' => date('Y-m-d'),
                                'consultation_time' => date('H:i:s'),
                                'type' => 'upcoming',
                                'status' => 'approved',
                                'notes' => $consultationNotes,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        } catch (\Exception $e) {
                            log_message('error', 'Failed to create doctor assign consultation: ' . $e->getMessage());
                        }
                    }

                    // Get doctor name for audit log
                    $doctorName = 'Doctor';
                    if ($db->tableExists('users')) {
                        $doctor = $db->table('users')->where('id', $doctorId)->get()->getRowArray();
                        if ($doctor) {
                            $doctorName = $doctor['username'] ?? 'Dr. ' . $doctorId;
                        }
                    }
                    
                    // Audit log
                    if ($db->tableExists('audit_logs')) {
                        $db->table('audit_logs')->insert([
                            'action' => 'patient_sent_to_doctor',
                            'user_id' => $nurseId,
                            'user_role' => 'nurse',
                            'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                            'description' => "Patient {$patientName} sent to Dr. {$doctorName} for consultation. Doctor can mark for admission if needed.",
                            'related_id' => $patientId,
                            'related_type' => 'patient',
                            'metadata' => json_encode([
                                'patient_id' => $patientId,
                                'triage_level' => $triageLevel,
                                'disposition' => 'Doctor Assign',
                                'doctor_id' => $doctorId,
                            ]),
                            'ip_address' => $this->request->getIPAddress(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            } elseif ($disposition === 'OPD') {
                // ER/ED Workflow - Auto-assign to ER doctor
                // IMPORTANT: Only Critical cases are AUTO-ADMITTED
                // Non-emergency ER cases (Moderate/Minor with ER disposition) need doctor approval
                $isCritical = ($triageLevel === 'Critical');
                $erDoctor = $this->findEmergencyDoctor();
                
                if ($erDoctor) {
                    $this->triageModel->update($triageId, [
                        'doctor_id' => $erDoctor['id'],
                        'sent_to_doctor' => 1,
                        'disposition' => 'ER',
                        'for_admission' => 1, // Mark for admission
                    ]);

                    // Update patient with doctor assignment
                    if ($patientSource === 'patients') {
                        $this->patientModel->update($patientId, ['doctor_id' => $erDoctor['id']]);
                    } else {
                        $this->adminPatientModel->update($patientId, ['doctor_id' => $erDoctor['id']]);
                    }

                    // Create consultation record for ER - IMMEDIATE consultation for critical cases
                    // ER consultations happen immediately, especially for critical cases
                    $consultationId = null;
                    if ($db->tableExists('consultations') && $patientSource === 'admin_patients') {
                        try {
                            $consultationNotes = "Emergency Room (ER) - {$triageLevel} triage level.\n";
                            $consultationNotes .= "Chief Complaint: {$chiefComplaint}\n";
                            if (!empty($nurseRecommendation)) {
                                $consultationNotes .= "\nNurse Recommendation: {$nurseRecommendation}";
                            }
                            $consultationNotes .= "\n\n[IMMEDIATE ER CONSULTATION REQUIRED - Patient is in ER]";
                            
                            $db->table('consultations')->insert([
                                'doctor_id' => $erDoctor['id'],
                                'patient_id' => $patientId,
                                'consultation_date' => date('Y-m-d'),
                                'consultation_time' => date('H:i:s'),
                                'type' => 'upcoming',
                                'status' => 'approved', // Approved immediately for ER
                                'notes' => $consultationNotes,
                                'is_er_consultation' => 1, // Flag for ER consultation
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                            $consultationId = $db->insertID();
                            
                            log_message('info', "ER consultation created immediately for patient {$patientId} (Consultation ID: {$consultationId})");
                        } catch (\Exception $e) {
                            log_message('error', 'Failed to create ER consultation: ' . $e->getMessage());
                        }
                    }

                    // AUTO-ADMIT: Only Critical/Life-threatening cases are automatically admitted to ER
                    // Non-emergency ER cases (Moderate/Minor) need doctor approval before admission
                    // IMPORTANT: admissions table has FK to admin_patients, so only create admission for admin_patients
                    if ($isCritical && $db->tableExists('admissions') && $patientSource === 'admin_patients') {
                        try {
                            // Try to find ER room/bed
                            $erRoom = null;
                            $erBedNumber = 'ER-' . date('YmdHis');
                            
                            if ($db->tableExists('rooms')) {
                                // Look for ER room
                                $erRoom = $db->table('rooms')
                                    ->where('room_type', 'ER')
                                    ->orWhere('ward', 'Emergency')
                                    ->orWhere('ward', 'ER')
                                    ->where('status', 'available')
                                    ->get()
                                    ->getRowArray();
                                
                                // If no ER room, try any available room
                                if (!$erRoom) {
                                    $erRoom = $db->table('rooms')
                                        ->where('status', 'available')
                                        ->orderBy('room_number', 'ASC')
                                        ->limit(1)
                                        ->get()
                                        ->getRowArray();
                                }
                                
                                if ($erRoom) {
                                    $erBedNumber = $erRoom['bed_number'] ?? 'ER-' . date('YmdHis');
                                }
                            }
                            
                            $admissionReason = "Emergency Room (ER) Admission - {$triageLevel} Triage Level\n";
                            $admissionReason .= "Chief Complaint: {$chiefComplaint}\n";
                            if (!empty($nurseRecommendation)) {
                                $admissionReason .= "\nNurse Recommendation: {$nurseRecommendation}";
                            }
                            $admissionReason .= "\n\n[AUTO-ADMITTED from ER Triage - Life-threatening condition detected]";
                            
                            // Create admission record with status "admitted"
                            // Note: Only for admin_patients due to FK constraint
                            $admissionData = [
                                'consultation_id' => $consultationId,
                                'patient_id' => $patientId,
                                'room_id' => $erRoom['id'] ?? null,
                                'bed_number' => $erBedNumber,
                                'room_type' => $erRoom['room_type'] ?? 'ER',
                                'admission_reason' => $admissionReason,
                                'attending_physician_id' => $erDoctor['id'],
                                'initial_notes' => "Auto-admitted from ER Triage. {$triageLevel} triage level. Life-threatening condition - requires immediate monitoring.",
                                'admission_date' => date('Y-m-d H:i:s'),
                                'status' => 'admitted', // AUTO-ADMIT: Status is "admitted" immediately
                            ];
                            
                            $db->table('admissions')->insert($admissionData);
                            $admissionId = $db->insertID();
                            
                            // Update patient status to "In ER"
                            if ($patientSource === 'admin_patients') {
                                $this->adminPatientModel->update($patientId, [
                                    'visit_type' => 'Emergency',
                                    'triage_status' => 'in_er', // Status: In ER
                                ]);
                            } else {
                                // For patients table, update if field exists
                                if ($db->tableExists('patients')) {
                                    $db->table('patients')
                                        ->where('patient_id', $patientId)
                                        ->update([
                                            'visit_type' => 'Emergency',
                                            'triage_status' => 'in_er', // Status: In ER
                                        ]);
                                }
                            }
                            
                            // Update room status if room was assigned
                            if (!empty($erRoom['id']) && $db->tableExists('rooms')) {
                                $db->table('rooms')
                                    ->where('id', $erRoom['id'])
                                    ->update(['status' => 'occupied']);
                            }
                            
                            // Also create admission request for tracking (marked as auto-approved)
                            if ($db->tableExists('admission_requests')) {
                                $db->table('admission_requests')->insert([
                                    'patient_id' => $patientId,
                                    'triage_id' => $triageId,
                                    'consultation_id' => $consultationId,
                                    'admission_id' => $admissionId, // Link to actual admission
                                    'requested_by' => $nurseId,
                                    'requested_by_role' => 'nurse',
                                    'status' => 'approved', // Auto-approved for ER/Critical
                                    'admission_reason' => $admissionReason,
                                    'auto_admitted' => 1, // Flag for auto-admission
                                    'created_at' => date('Y-m-d H:i:s'),
                                ]);
                            }
                            
                            log_message('info', "ER patient {$patientId} auto-admitted to ER (Admission ID: {$admissionId})");
                            
                        } catch (\Exception $e) {
                            // Log error but don't fail the transaction
                            log_message('error', 'Failed to auto-admit ER patient: ' . $e->getMessage());
                        }
                    } elseif ($isCritical && $patientSource === 'patients') {
                        // For patients from 'patients' table, we can't create admission due to FK constraint
                        // But we still mark them as auto-admitted in admission_requests and update status to "In ER"
                        // Update patient status to "In ER"
                        if ($db->tableExists('patients')) {
                            $db->table('patients')
                                ->where('patient_id', $patientId)
                                ->update([
                                    'visit_type' => 'Emergency',
                                    'triage_status' => 'in_er', // Status: In ER
                                ]);
                        }
                        
                        if ($db->tableExists('admission_requests')) {
                            $admissionReason = "Emergency Room (ER) Admission - {$triageLevel} Triage Level\n";
                            $admissionReason .= "Chief Complaint: {$chiefComplaint}\n";
                            if (!empty($nurseRecommendation)) {
                                $admissionReason .= "\nNurse Recommendation: {$nurseRecommendation}";
                            }
                            $admissionReason .= "\n\n[AUTO-ADMITTED from ER Triage - Life-threatening condition detected]\n";
                            $admissionReason .= "Note: Patient from 'patients' table - admission record cannot be created due to FK constraint.";
                            
                            try {
                                $db->table('admission_requests')->insert([
                                    'patient_id' => $patientId,
                                    'triage_id' => $triageId,
                                    'consultation_id' => $consultationId,
                                    'requested_by' => $nurseId,
                                    'requested_by_role' => 'nurse',
                                    'status' => 'approved', // Auto-approved for ER/Critical
                                    'admission_reason' => $admissionReason,
                                    'auto_admitted' => 1, // Flag for auto-admission
                                    'created_at' => date('Y-m-d H:i:s'),
                                ]);
                                
                                log_message('info', "ER patient {$patientId} (from patients table) - Auto-admission request created (cannot create admission record due to FK)");
                            } catch (\Exception $e) {
                                log_message('error', 'Failed to create ER admission request for patients table: ' . $e->getMessage());
                            }
                        }
                    }

                    // Audit log for ER
                    if ($db->tableExists('audit_logs')) {
                        if ($isCritical) {
                            // Critical: Auto-admitted
                            $db->table('audit_logs')->insert([
                                'action' => 'er_patient_auto_admitted',
                                'user_id' => $nurseId,
                                'user_role' => 'nurse',
                                'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                                'description' => "Patient {$patientName} AUTO-ADMITTED to ER (Life-threatening condition). Assigned to Dr. " . ($erDoctor['name'] ?? 'ER Doctor') . ". Status: Admitted",
                                'related_id' => $patientId,
                                'related_type' => 'patient',
                                'metadata' => json_encode([
                                    'patient_id' => $patientId,
                                    'triage_level' => $triageLevel,
                                    'disposition' => 'ER',
                                    'doctor_id' => $erDoctor['id'],
                                    'auto_admitted' => true,
                                    'admission_status' => 'admitted',
                                ]),
                                'ip_address' => $this->request->getIPAddress(),
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        } else {
                            // Non-emergency: Pending doctor approval
                            $db->table('audit_logs')->insert([
                                'action' => 'er_patient_assigned',
                                'user_id' => $nurseId,
                                'user_role' => 'nurse',
                                'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                                'description' => "Patient {$patientName} routed to ER (Non-emergency). Assigned to Dr. " . ($erDoctor['name'] ?? 'ER Doctor') . ". Admission request pending doctor approval.",
                                'related_id' => $patientId,
                                'related_type' => 'patient',
                                'metadata' => json_encode([
                                    'patient_id' => $patientId,
                                    'triage_level' => $triageLevel,
                                    'disposition' => 'ER',
                                    'doctor_id' => $erDoctor['id'],
                                    'auto_admitted' => false,
                                    'admission_status' => 'pending_approval',
                                ]),
                                'ip_address' => $this->request->getIPAddress(),
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            } elseif ($disposition === 'OPD') {
                // OPD/Clinic Workflow - Add to OPD queue
                if ($db->tableExists('opd_queue')) {
                    // Get next queue number
                    $lastQueue = $db->table('opd_queue')
                        ->selectMax('queue_number')
                        ->where('DATE(created_at)', date('Y-m-d'))
                        ->get()
                        ->getRowArray();
                    
                    $nextQueueNumber = ($lastQueue['queue_number'] ?? 0) + 1;
                    
                    // Add to OPD queue
                    $opdQueueData = [
                        'patient_id' => $patientId,
                        'triage_id' => $triageId,
                        'queue_number' => $nextQueueNumber,
                        'status' => 'waiting',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    
                    $db->table('opd_queue')->insert($opdQueueData);
                    
                    // Update triage with queue number
                    $this->triageModel->update($triageId, [
                        'opd_queue_number' => $nextQueueNumber,
                    ]);
                    
                    // Audit log
                    if ($db->tableExists('audit_logs')) {
                        $db->table('audit_logs')->insert([
                            'action' => 'opd_queue_added',
                            'user_id' => $nurseId,
                            'user_role' => 'nurse',
                            'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                            'description' => "Patient added to OPD queue. Queue #{$nextQueueNumber}",
                            'related_id' => $patientId,
                            'related_type' => 'patient',
                            'metadata' => json_encode([
                                'patient_id' => $patientId,
                                'triage_level' => $triageLevel,
                                'disposition' => 'OPD',
                                'queue_number' => $nextQueueNumber,
                            ]),
                            'ip_address' => $this->request->getIPAddress(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            } elseif ($disposition === 'Others') {
                // Others - In-Patient Admission
                // Doctor has been assigned, create admission request
                if (!empty($doctorId) && $db->tableExists('admission_requests')) {
                    try {
                        $admissionReason = "In-Patient Admission Request - {$triageLevel} Triage Level\n";
                        $admissionReason .= "Chief Complaint: {$chiefComplaint}\n";
                        if (!empty($nurseRecommendation)) {
                            $admissionReason .= "\nNurse Recommendation: {$nurseRecommendation}";
                        }
                        $admissionReason .= "\n\n[IN-PATIENT - Doctor assigned, waiting for admission processing]";
                        
                        $admissionRequestData = [
                            'patient_id' => $patientId,
                            'triage_id' => $triageId,
                            'consultation_id' => null,
                            'doctor_id' => $doctorId,
                            'requested_by' => $nurseId,
                            'requested_by_role' => 'nurse',
                            'status' => 'pending_room_assignment', // Receptionist needs to assign room
                            'admission_reason' => $admissionReason,
                            'nurse_recommendation' => $nurseRecommendation ?? '',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        
                        $db->table('admission_requests')->insert($admissionRequestData);
                        $admissionRequestId = $db->insertID();
                        
                        log_message('info', "In-patient admission request created for patient {$patientId} (Request ID: {$admissionRequestId}) - Doctor assigned: {$doctorId}");
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to create in-patient admission request: ' . $e->getMessage());
                    }
                }
                
                // Audit log
                if ($db->tableExists('audit_logs')) {
                    $db->table('audit_logs')->insert([
                        'action' => 'inpatient_triage',
                        'user_id' => $nurseId,
                        'user_role' => 'nurse',
                        'user_name' => session()->get('username') ?? session()->get('name') ?? 'Nurse',
                        'description' => "Patient {$patientName} triaged for in-patient admission. Doctor assigned: " . ($doctorId ? "Doctor ID {$doctorId}" : "None"),
                        'related_id' => $patientId,
                        'related_type' => 'patient',
                        'metadata' => json_encode([
                            'patient_id' => $patientId,
                            'triage_level' => $triageLevel,
                            'disposition' => 'Others',
                            'doctor_id' => $doctorId,
                            'chief_complaint' => $chiefComplaint,
                            'nurse_recommendation' => $nurseRecommendation,
                        ]),
                        'ip_address' => $this->request->getIPAddress(),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
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
            $errorMessage = $e->getMessage();
            log_message('error', 'Triage save error: ' . $errorMessage);
            
            // Provide more user-friendly error messages
            if (strpos($errorMessage, 'foreign key constraint') !== false) {
                $errorMessage = 'Database constraint error. Please contact system administrator.';
            } elseif (strpos($errorMessage, 'Duplicate entry') !== false) {
                $errorMessage = 'Triage record already exists for this patient.';
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $errorMessage
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
            
            // Determine patient source by checking which table has the patient
            $patientSource = 'patients';
            $adminPatient = $this->adminPatientModel->find($patientId);
            if ($adminPatient) {
                $patientSource = 'admin_patients';
                $this->adminPatientModel->update($patientId, ['doctor_id' => $doctorId]);
            } else {
                $this->patientModel->update($patientId, ['doctor_id' => $doctorId]);
            }

            // Create consultation for doctor to see patient
            // IMPORTANT: Use 'approved' status so doctor can see it in dashboard
            if ($db->tableExists('consultations')) {
                try {
                    // Check if consultation already exists
                    $existingConsultation = $db->table('consultations')
                        ->where('doctor_id', $doctorId)
                        ->where('patient_id', $patientId)
                        ->where('consultation_date', date('Y-m-d'))
                        ->where('type', 'upcoming')
                        ->get()
                        ->getRowArray();
                    
                    if (!$existingConsultation) {
                        // Create new consultation
                        // For patients from 'patients' table, we need to handle the foreign key constraint
                        // Try to create consultation - if it fails due to FK, we'll still update triage and patient
                        try {
                            $consultationData = [
                                'doctor_id' => $doctorId,
                                'patient_id' => $patientId,
                                'consultation_date' => date('Y-m-d'),
                                'consultation_time' => date('H:i:s'),
                                'type' => 'upcoming',
                                'status' => 'approved', // Use 'approved' so doctor can see it immediately
                                'notes' => "Referred from Nurse Triage. Triage Level: {$triage['triage_level']}. " . 
                                          ($triage['chief_complaint'] ?? ''),
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                            
                            $db->table('consultations')->insert($consultationData);
                        } catch (\Exception $e) {
                            // If FK constraint fails (patient from 'patients' table), log but continue
                            // The patient is still assigned to doctor, just no consultation record
                            log_message('info', 'Could not create consultation (may be FK constraint): ' . $e->getMessage());
                            
                            // For patients table, we can't create consultation due to FK, but patient is still assigned
                            // Doctor can see patient in assigned patients list
                        }
                    } else {
                        // Update existing consultation to approved status
                        $db->table('consultations')
                            ->where('id', $existingConsultation['id'])
                            ->update([
                                'status' => 'approved',
                                'notes' => ($existingConsultation['notes'] ?? '') . "\n\nReferred from Nurse Triage. Triage Level: {$triage['triage_level']}",
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Failed to create/update consultation in sendToDoctor: ' . $e->getMessage());
                }
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
     * Get available doctors for assignment (for nurses) with schedule availability
     */
    public function getAvailableDoctors()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['nurse', 'receptionist'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $time = $this->request->getGet('time') ?: date('H:i:s');

        $availableDoctors = [];
        
        // Get doctors from users table
        if ($db->tableExists('users')) {
            $userDoctors = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'doctor')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($userDoctors as $userDoctor) {
                // If time is provided, check if doctor is already booked at that time
                if (!empty($time)) {
                    $hasConflict = false;
                    
                    // Check for appointment conflict
                    if ($db->tableExists('appointments')) {
                        $appointmentConflict = $db->table('appointments')
                            ->where('doctor_id', $userDoctor['id'])
                            ->where('appointment_date', $date)
                            ->where('appointment_time', $time)
                            ->whereNotIn('status', ['cancelled', 'no_show'])
                            ->countAllResults();
                        
                        if ($appointmentConflict > 0) {
                            $hasConflict = true;
                        }
                    }
                    
                    // Check for consultation conflict
                    if (!$hasConflict && $db->tableExists('consultations')) {
                        $consultationConflict = $db->table('consultations')
                            ->where('doctor_id', $userDoctor['id'])
                            ->where('consultation_date', $date)
                            ->where('consultation_time', $time)
                            ->whereNotIn('status', ['cancelled'])
                            ->countAllResults();
                        
                        if ($consultationConflict > 0) {
                            $hasConflict = true;
                        }
                    }
                    
                    // Skip this doctor if they have a conflict at the requested time
                    if ($hasConflict) {
                        continue;
                    }
                }
                
                // Get specialization from doctors table if exists
                $specialization = 'General Practice';
                if ($db->tableExists('doctors')) {
                    $doctorInfo = $db->table('doctors')
                        ->where('id', $userDoctor['id'])
                        ->get()
                        ->getRowArray();
                    if ($doctorInfo) {
                        $specialization = $doctorInfo['specialization'] ?? 'General Practice';
                    }
                }

                // Check schedule availability
                $scheduleInfo = $this->getDoctorScheduleInfo($userDoctor['id'], $date, $time, $db);
                
                $availableDoctors[] = [
                    'id' => $userDoctor['id'],
                    'name' => $userDoctor['username'] ?? 'Dr. ' . $userDoctor['id'],
                    'specialization' => $specialization,
                    'schedule_status' => $scheduleInfo['status'], // 'available', 'scheduled', 'full', 'off_duty'
                    'schedule_time' => $scheduleInfo['schedule_time'],
                    'current_appointments' => $scheduleInfo['current_appointments'],
                    'max_capacity' => $scheduleInfo['max_capacity'],
                ];
            }
        }

        // Also include doctors from doctors table if not already in list
        if ($db->tableExists('doctors')) {
            $doctors = $this->doctorModel->findAll();
            foreach ($doctors as $doctor) {
                // Check if already added
                $alreadyAdded = false;
                foreach ($availableDoctors as $doc) {
                    if ($doc['id'] == $doctor['id']) {
                        $alreadyAdded = true;
                        break;
                    }
                }

                if (!$alreadyAdded) {
                    // If time is provided, check if doctor is already booked at that time
                    if (!empty($time)) {
                        $hasConflict = false;
                        
                        // Check for appointment conflict
                        if ($db->tableExists('appointments')) {
                            $appointmentConflict = $db->table('appointments')
                                ->where('doctor_id', $doctor['id'])
                                ->where('appointment_date', $date)
                                ->where('appointment_time', $time)
                                ->whereNotIn('status', ['cancelled', 'no_show'])
                                ->countAllResults();
                            
                            if ($appointmentConflict > 0) {
                                $hasConflict = true;
                            }
                        }
                        
                        // Check for consultation conflict
                        if (!$hasConflict && $db->tableExists('consultations')) {
                            $consultationConflict = $db->table('consultations')
                                ->where('doctor_id', $doctor['id'])
                                ->where('consultation_date', $date)
                                ->where('consultation_time', $time)
                                ->whereNotIn('status', ['cancelled'])
                                ->countAllResults();
                            
                            if ($consultationConflict > 0) {
                                $hasConflict = true;
                            }
                        }
                        
                        // Skip this doctor if they have a conflict at the requested time
                        if ($hasConflict) {
                            continue;
                        }
                    }
                    
                    // Check schedule availability
                    $scheduleInfo = $this->getDoctorScheduleInfo($doctor['id'], $date, $time, $db);
                    
                    $availableDoctors[] = [
                        'id' => $doctor['id'],
                        'name' => $doctor['doctor_name'] ?? 'Dr. ' . $doctor['id'],
                        'specialization' => $doctor['specialization'] ?? 'General Practice',
                        'schedule_status' => $scheduleInfo['status'],
                        'schedule_time' => $scheduleInfo['schedule_time'],
                        'current_appointments' => $scheduleInfo['current_appointments'],
                        'max_capacity' => $scheduleInfo['max_capacity'],
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'doctors' => $availableDoctors,
            'date' => $date,
            'time' => $time
        ]);
    }

    /**
     * Get doctor schedule information for a specific date/time
     */
    private function getDoctorScheduleInfo($doctorId, $date, $time, $db)
    {
        $scheduleInfo = [
            'status' => 'off_duty',
            'schedule_time' => null,
            'current_appointments' => 0,
            'max_capacity' => 0,
        ];

        // Check if doctor has schedule for this date
        if ($db->tableExists('doctor_schedules')) {
            $schedule = $db->table('doctor_schedules')
                ->where('doctor_id', $doctorId)
                ->where('shift_date', $date)
                ->where('status !=', 'cancelled')
                ->orderBy('start_time', 'ASC')
                ->get()
                ->getRowArray();

            if ($schedule) {
                $scheduleStart = $schedule['start_time'];
                $scheduleEnd = $schedule['end_time'];
                $scheduleInfo['schedule_time'] = $scheduleStart . ' - ' . $scheduleEnd;

                // Check if current time is within schedule
                $currentTime = date('H:i:s');
                if ($time) {
                    $currentTime = date('H:i:s', strtotime($time));
                }

                if ($currentTime >= $scheduleStart && $currentTime <= $scheduleEnd) {
                    // Count current appointments/consultations for this doctor on this date
                    $currentAppointments = 0;
                    
                    // Count consultations
                    if ($db->tableExists('consultations')) {
                        $consultations = $db->table('consultations')
                            ->where('doctor_id', $doctorId)
                            ->where('consultation_date', $date)
                            ->where('type !=', 'cancelled')
                            ->where('deleted_at', null)
                            ->countAllResults();
                        $currentAppointments += $consultations;
                    }

                    // Count appointments
                    if ($db->tableExists('appointments')) {
                        $appointments = $db->table('appointments')
                            ->where('doctor_id', $doctorId)
                            ->where('appointment_date', $date)
                            ->whereNotIn('status', ['cancelled', 'no_show'])
                            ->countAllResults();
                        $currentAppointments += $appointments;
                    }

                    // Count triage patients assigned to this doctor
                    if ($db->tableExists('triage')) {
                        $triagePatients = $db->table('triage')
                            ->where('doctor_id', $doctorId)
                            ->where('DATE(created_at)', $date)
                            ->where('status', 'pending')
                            ->countAllResults();
                        $currentAppointments += $triagePatients;
                    }

                    $scheduleInfo['current_appointments'] = $currentAppointments;
                    
                    // Estimate max capacity (assuming 1 patient per 30 minutes in 8-hour shift)
                    $start = strtotime($scheduleStart);
                    $end = strtotime($scheduleEnd);
                    $duration = ($end - $start) / 3600; // hours
                    $maxCapacity = (int)($duration * 2); // 2 patients per hour
                    $scheduleInfo['max_capacity'] = $maxCapacity;

                    // Determine status
                    if ($currentAppointments >= $maxCapacity) {
                        $scheduleInfo['status'] = 'full';
                    } elseif ($currentAppointments >= ($maxCapacity * 0.8)) {
                        $scheduleInfo['status'] = 'busy';
                    } else {
                        $scheduleInfo['status'] = 'available';
                    }
                } else {
                    $scheduleInfo['status'] = 'off_duty';
                }
            } else {
                // No schedule for this date - check if doctor is generally available
                // For now, mark as available but with warning
                $scheduleInfo['status'] = 'no_schedule';
            }
        } else {
            // No schedule table - assume available
            $scheduleInfo['status'] = 'available';
        }

        return $scheduleInfo;
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

