<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;

class PatientController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get patients assigned to this doctor from admin_patients table
        $adminPatientsRaw = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();
        
        // Format admin patients to include visit_type and source
        $adminPatients = [];
        foreach ($adminPatientsRaw as $patient) {
            $patient['visit_type'] = $patient['visit_type'] ?? null;
            $patient['source'] = 'admin'; // Mark as from admin panel
            $adminPatients[] = $patient;
        }

        // Get patients from patients table (HMSPatientModel) - includes Out-Patients registered by receptionist
        $hmsPatients = [];
        if ($db->tableExists('patients')) {
            $hmsPatientsRaw = $db->table('patients')
                ->select('patients.*')
                ->where('patients.doctor_id', $doctorId)
                ->where('patients.doctor_id IS NOT NULL')
                ->where('patients.doctor_id !=', 0)
                ->orderBy('patients.last_name', 'ASC')
                ->orderBy('patients.first_name', 'ASC')
                ->get()
                ->getResultArray();
            
            // Format hmsPatients to match admin_patients structure for the view
            foreach ($hmsPatientsRaw as $patient) {
                $nameParts = [];
                if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                
                // If no first_name/last_name, try to parse full_name
                if (empty($nameParts) && !empty($patient['full_name'])) {
                    $parts = explode(' ', $patient['full_name'], 2);
                    $nameParts = [
                        $parts[0] ?? '',
                        $parts[1] ?? ''
                    ];
                }
                
                $hmsPatients[] = [
                    'id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                    'patient_id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                    'firstname' => $nameParts[0] ?? '',
                    'lastname' => $nameParts[1] ?? '',
                    'full_name' => $patient['full_name'] ?? implode(' ', $nameParts),
                    'birthdate' => $patient['date_of_birth'] ?? $patient['birthdate'] ?? null,
                    'gender' => strtolower($patient['gender'] ?? ''),
                    'contact' => $patient['contact'] ?? null,
                    'address' => $patient['address'] ?? null,
                    'type' => $patient['type'] ?? 'Out-Patient',
                    'visit_type' => $patient['visit_type'] ?? null,
                    'source' => 'receptionist', // Mark as from receptionist
                ];
            }
        }

        // Merge both patient lists
        $patients = array_merge($adminPatients, $hmsPatients);
        
        // Deduplicate: If same patient exists in both tables (same name + birthdate + doctor_id), keep only admin_patients version
        // Note: We use name + birthdate to identify true duplicates, as same name with different birthdate = different patients
        $deduplicated = [];
        $seenKeys = [];
        
        foreach ($patients as $patient) {
            // Create a unique key based on name (case-insensitive), birthdate, and doctor_id
            $nameKey = strtolower(trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')));
            $birthdate = $patient['birthdate'] ?? '';
            $key = md5($nameKey . '|' . $birthdate . '|' . $doctorId);
            
            // If we've seen this patient before (same name AND birthdate), prefer admin_patients version (source = 'admin')
            if (isset($seenKeys[$key])) {
                // If current patient is from admin_patients and previous was from receptionist, replace it
                if (($patient['source'] ?? '') === 'admin' && ($seenKeys[$key]['source'] ?? '') === 'receptionist') {
                    $deduplicated[$seenKeys[$key]['index']] = $patient;
                    $seenKeys[$key] = ['index' => $seenKeys[$key]['index'], 'source' => $patient['source'] ?? 'admin'];
                }
                // Otherwise, skip this duplicate
                continue;
            }
            
            // Add to deduplicated list
            $index = count($deduplicated);
            $deduplicated[] = $patient;
            $seenKeys[$key] = ['index' => $index, 'source' => $patient['source'] ?? 'unknown'];
        }
        
        // Re-index array
        $patients = array_values($deduplicated);
        
        // Sort by lastname, then firstname
        usort($patients, function($a, $b) {
            $lastA = strtolower($a['lastname'] ?? '');
            $lastB = strtolower($b['lastname'] ?? '');
            if ($lastA === $lastB) {
                $firstA = strtolower($a['firstname'] ?? '');
                $firstB = strtolower($b['firstname'] ?? '');
                return $firstA <=> $firstB;
            }
            return $lastA <=> $lastB;
        });

        // Fetch upcoming consultations and assigned nurse for each patient
        if ($db->tableExists('consultations') || $db->tableExists('users')) {
            foreach ($patients as &$patient) {
                $patientId = $patient['id'] ?? $patient['patient_id'] ?? null;
                $patientSource = $patient['source'] ?? 'admin';
                
                // Find the correct patient ID for consultations table
                $consultationPatientId = null;
                
                if ($patientSource === 'admin' || $patientSource === 'admin_patients') {
                    // For admin_patients, use the ID directly
                    $consultationPatientId = $patientId;
                } else {
                    // For patients table (receptionist), consultations use patients.patient_id
                    $consultationPatientId = $patient['patient_id'] ?? $patientId;
                }
                
                // Get upcoming consultation for this patient
                // Only show consultations where date and time have arrived
                $upcomingConsultation = null;
                if ($consultationPatientId && $db->tableExists('consultations')) {
                    $today = date('Y-m-d');
                    $currentTime = date('H:i:s');
                    
                    if ($patientSource === 'receptionist') {
                        // For receptionist patients, consultations table uses patients.patient_id
                        $upcomingConsultation = $db->table('consultations')
                            ->where('patient_id', $consultationPatientId)
                            ->where('doctor_id', $doctorId)
                            ->where('type', 'upcoming')
                            ->whereIn('status', ['approved', 'pending'])
                            ->groupStart()
                                ->where('consultation_date >', $today) // Future dates
                                ->orGroupStart()
                                    ->where('consultation_date', $today) // Today's date
                                    ->where('consultation_time <=', $currentTime) // Time has arrived
                                ->groupEnd()
                            ->groupEnd()
                            ->orderBy('consultation_date', 'ASC')
                            ->orderBy('consultation_time', 'ASC')
                            ->get()
                            ->getRowArray();
                    } else {
                        // For admin patients, consultations table uses admin_patients.id
                        $upcomingConsultation = $db->table('consultations')
                            ->where('patient_id', $consultationPatientId)
                            ->where('doctor_id', $doctorId)
                            ->where('type', 'upcoming')
                            ->whereIn('status', ['approved', 'pending'])
                            ->groupStart()
                                ->where('consultation_date >', $today) // Future dates
                                ->orGroupStart()
                                    ->where('consultation_date', $today) // Today's date
                                    ->where('consultation_time <=', $currentTime) // Time has arrived
                                ->groupEnd()
                            ->groupEnd()
                            ->orderBy('consultation_date', 'ASC')
                            ->orderBy('consultation_time', 'ASC')
                            ->get()
                            ->getRowArray();
                    }
                }
                
                // Attach appointment info to patient
                if ($upcomingConsultation) {
                    $patient['appointment_date'] = $upcomingConsultation['consultation_date'];
                    $patient['appointment_time'] = $upcomingConsultation['consultation_time'];
                    $patient['appointment_datetime'] = $upcomingConsultation['consultation_date'] . ' ' . $upcomingConsultation['consultation_time'];
                } else {
                    $patient['appointment_date'] = null;
                    $patient['appointment_time'] = null;
                    $patient['appointment_datetime'] = null;
                }
                
                // Get assigned nurse information
                $assignedNurseId = null;
                $assignedNurseName = null;
                
                if ($patientSource === 'admin' || $patientSource === 'admin_patients') {
                    // Check admin_patients table
                    if ($db->tableExists('admin_patients') && $patientId) {
                        $adminPatient = $db->table('admin_patients')
                            ->select('admin_patients.assigned_nurse_id, users.first_name, users.last_name, users.username')
                            ->join('users', 'users.id = admin_patients.assigned_nurse_id', 'left')
                            ->where('admin_patients.id', $patientId)
                            ->get()
                            ->getRowArray();
                        
                        if ($adminPatient && $adminPatient['assigned_nurse_id']) {
                            $assignedNurseId = $adminPatient['assigned_nurse_id'];
                            $nurseName = trim(($adminPatient['first_name'] ?? '') . ' ' . ($adminPatient['last_name'] ?? ''));
                            $assignedNurseName = !empty($nurseName) ? $nurseName : ($adminPatient['username'] ?? 'Nurse ' . $assignedNurseId);
                        }
                    }
                } else {
                    // Check patients table
                    if ($db->tableExists('patients') && $patientId) {
                        $hmsPatient = $db->table('patients')
                            ->select('patients.assigned_nurse_id, users.first_name, users.last_name, users.username')
                            ->join('users', 'users.id = patients.assigned_nurse_id', 'left')
                            ->where('patients.patient_id', $patientId)
                            ->get()
                            ->getRowArray();
                        
                        if ($hmsPatient && $hmsPatient['assigned_nurse_id']) {
                            $assignedNurseId = $hmsPatient['assigned_nurse_id'];
                            $nurseName = trim(($hmsPatient['first_name'] ?? '') . ' ' . ($hmsPatient['last_name'] ?? ''));
                            $assignedNurseName = !empty($nurseName) ? $nurseName : ($hmsPatient['username'] ?? 'Nurse ' . $assignedNurseId);
                        }
                    }
                }
                
                $patient['assigned_nurse_id'] = $assignedNurseId;
                $patient['assigned_nurse_name'] = $assignedNurseName;
            }
            unset($patient); // Break reference
        }

        // Get patients marked for admission by nurse (pending doctor approval)
        $patientsForAdmission = [];
        if ($db->tableExists('admission_requests')) {
            $admissionRequests = $db->table('admission_requests')
                ->select('admission_requests.*, 
                         admin_patients.firstname, admin_patients.lastname, admin_patients.id as admin_patient_id, admin_patients.assigned_nurse_id as admin_patient_nurse_id,
                         patients.full_name, patients.patient_id as hms_patient_id, patients.assigned_nurse_id as hms_patient_nurse_id,
                         triage.triage_level, triage.chief_complaint, triage.disposition, triage.doctor_id as triage_doctor_id, triage.assigned_doctor_id')
                ->join('admin_patients', 'admin_patients.id = admission_requests.patient_id', 'left')
                ->join('patients', 'patients.patient_id = admission_requests.patient_id', 'left')
                ->join('triage', 'triage.id = admission_requests.triage_id', 'left')
                ->where('admission_requests.status', 'pending_doctor_approval')
                ->groupStart()
                    // Get patients assigned to this doctor OR patients from triage assigned to this doctor
                    ->where('admin_patients.doctor_id', $doctorId)
                    ->orWhere('patients.doctor_id', $doctorId)
                    ->orWhere('triage.doctor_id', $doctorId)
                    ->orWhere('triage.assigned_doctor_id', $doctorId)
                ->groupEnd()
                ->orderBy('admission_requests.created_at', 'DESC')
                ->get()
                ->getResultArray();
            
            foreach ($admissionRequests as $request) {
                $patientId = $request['admin_patient_id'] ?? $request['hms_patient_id'] ?? $request['patient_id'] ?? null;
                $patientName = '';
                if ($request['firstname'] && $request['lastname']) {
                    $patientName = $request['firstname'] . ' ' . $request['lastname'];
                } elseif ($request['full_name']) {
                    $patientName = $request['full_name'];
                } else {
                    $patientName = 'Patient ' . $patientId;
                }
                
                // Get assigned nurse information for admission request
                // Priority: admission_requests.assigned_nurse_id > admin_patients.assigned_nurse_id > patients.assigned_nurse_id
                $assignedNurseId = $request['assigned_nurse_id'] ?? null;
                $assignedNurseName = null;
                
                // If not in admission_requests, check patient tables
                if (!$assignedNurseId) {
                    $assignedNurseId = $request['admin_patient_nurse_id'] ?? $request['hms_patient_nurse_id'] ?? null;
                }
                
                // Get nurse name if assigned
                if ($assignedNurseId && $db->tableExists('users')) {
                    $nurse = $db->table('users')
                        ->select('users.first_name, users.last_name, users.username')
                        ->where('users.id', $assignedNurseId)
                        ->get()
                        ->getRowArray();
                    
                    if ($nurse) {
                        $nurseName = trim(($nurse['first_name'] ?? '') . ' ' . ($nurse['last_name'] ?? ''));
                        $assignedNurseName = !empty($nurseName) ? $nurseName : ($nurse['username'] ?? 'Nurse ' . $assignedNurseId);
                    }
                }
                
                $patientsForAdmission[] = [
                    'admission_request_id' => $request['id'] ?? null,
                    'patient_id' => $patientId,
                    'patient_name' => $patientName,
                    'triage_level' => $request['triage_level'] ?? 'N/A',
                    'disposition' => $request['disposition'] ?? 'Admission',
                    'chief_complaint' => $request['chief_complaint'] ?? 'N/A',
                    'admission_reason' => $request['admission_reason'] ?? '',
                    'requested_by' => $request['requested_by'] ?? null,
                    'requested_by_role' => $request['requested_by_role'] ?? 'nurse',
                    'created_at' => $request['created_at'] ?? date('Y-m-d H:i:s'),
                    'assigned_nurse_id' => $assignedNurseId,
                    'assigned_nurse_name' => $assignedNurseName,
                ];
            }
        }

        $data = [
            'title' => 'Patient List',
            'patients' => $patients,
            'patientsForAdmission' => $patientsForAdmission // Patients marked for admission by nurse
        ];

        return view('doctor/patients/index', $data);
    }

    /**
     * Get available nurses based on schedule (AJAX)
     */
    public function getAvailableNurses()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $currentTime = date('H:i:s');

        $availableNurses = [];

        // Get all active nurses
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email, users.first_name, users.last_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->where('users.deleted_at IS NULL', null, false)
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();

            // Check each nurse's schedule availability
            foreach ($nurses as $nurse) {
                $nurseId = $nurse['id'];
                $isAvailable = false;
                $scheduleInfo = null;

                // Check if nurse has an active schedule for today
                if ($db->tableExists('nurse_schedules')) {
                    $schedule = $db->table('nurse_schedules')
                        ->where('nurse_id', $nurseId)
                        ->where('shift_date', $date)
                        ->where('status', 'active')
                        ->get()
                        ->getRowArray();

                    if ($schedule) {
                        $startTime = $schedule['start_time'];
                        $endTime = $schedule['end_time'];

                        // Check if current time is within the shift time
                        if ($currentTime >= $startTime && $currentTime <= $endTime) {
                            $isAvailable = true;
                            $scheduleInfo = [
                                'shift_type' => $schedule['shift_type'] ?? 'N/A',
                                'start_time' => substr($startTime, 0, 5),
                                'end_time' => substr($endTime, 0, 5),
                            ];
                        }
                    }
                }

                // Only include available nurses
                if ($isAvailable) {
                    $nurseName = trim(($nurse['first_name'] ?? '') . ' ' . ($nurse['last_name'] ?? ''));
                    if (empty($nurseName)) {
                        $nurseName = $nurse['username'] ?? 'Nurse ' . $nurseId;
                    }

                    $availableNurses[] = [
                        'id' => $nurseId,
                        'name' => $nurseName,
                        'username' => $nurse['username'] ?? '',
                        'schedule' => $scheduleInfo,
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'nurses' => $availableNurses,
            'date' => $date,
            'time' => $currentTime
        ]);
    }

    /**
     * Assign nurse to patient (AJAX)
     */
    public function assignNurse()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(401);
        }

        $patientId = $this->request->getPost('patient_id');
        $nurseId = $this->request->getPost('nurse_id');
        $admissionRequestId = $this->request->getPost('admission_request_id');

        if (!$patientId || !$nurseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient ID and Nurse ID are required']);
        }

        $db = \Config\Database::connect();
        $doctorId = session()->get('user_id');
        $db->transStart();

        try {
            // Check if patient exists in admin_patients
            $patient = $db->table('admin_patients')
                ->where('id', $patientId)
                ->where('doctor_id', $doctorId)
                ->get()
                ->getRowArray();

            // If not in admin_patients, check patients table
            if (!$patient && $db->tableExists('patients')) {
                $patient = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->where('doctor_id', $doctorId)
                    ->get()
                    ->getRowArray();
            }

            if (!$patient) {
                throw new \Exception('Patient not found or not assigned to you');
            }

            // Verify nurse exists and is active
            $nurse = $db->table('users')
                ->select('users.*, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.id', $nurseId)
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->get()
                ->getRowArray();

            if (!$nurse) {
                throw new \Exception('Nurse not found or not active');
            }

            // Check if nurse is available today
            $date = date('Y-m-d');
            $currentTime = date('H:i:s');
            $isAvailable = false;

            if ($db->tableExists('nurse_schedules')) {
                $schedule = $db->table('nurse_schedules')
                    ->where('nurse_id', $nurseId)
                    ->where('shift_date', $date)
                    ->where('status', 'active')
                    ->get()
                    ->getRowArray();

                if ($schedule) {
                    $startTime = $schedule['start_time'];
                    $endTime = $schedule['end_time'];
                    if ($currentTime >= $startTime && $currentTime <= $endTime) {
                        $isAvailable = true;
                    }
                }
            }

            if (!$isAvailable) {
                throw new \Exception('Nurse is not available at this time. Please select a nurse with an active schedule.');
            }

            // Update patient record with assigned nurse
            // Update the table where the patient was found
            if ($patient && isset($patient['id'])) {
                // Patient found in admin_patients
                if ($db->tableExists('admin_patients')) {
                    $db->table('admin_patients')
                        ->where('id', $patientId)
                        ->update([
                            'assigned_nurse_id' => $nurseId,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
                
                // Also check if same patient exists in patients table (by name and birthdate) and update it too
                if ($db->tableExists('patients')) {
                    $patientName = trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''));
                    $patientBirthdate = $patient['birthdate'] ?? null;
                    
                    if ($patientName && $patientBirthdate) {
                        // Try to find matching patient in patients table
                        $nameParts = explode(' ', $patientName, 2);
                        $matchingPatient = $db->table('patients')
                            ->groupStart()
                                ->where('first_name', $nameParts[0] ?? '')
                                ->where('last_name', $nameParts[1] ?? '')
                                ->orWhere('full_name', $patientName)
                            ->groupEnd()
                            ->where('date_of_birth', $patientBirthdate)
                            ->where('doctor_id', $doctorId)
                            ->get()
                            ->getRowArray();
                        
                        if ($matchingPatient) {
                            $db->table('patients')
                                ->where('patient_id', $matchingPatient['patient_id'])
                                ->update([
                                    'assigned_nurse_id' => $nurseId,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }
                    }
                }
            } else {
                // Patient found in patients table
                if ($db->tableExists('patients')) {
                    $db->table('patients')
                        ->where('patient_id', $patientId)
                        ->update([
                            'assigned_nurse_id' => $nurseId,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
                
                // Also check if same patient exists in admin_patients table and update it too
                if ($db->tableExists('admin_patients') && $patient) {
                    $patientName = trim(($patient['full_name'] ?? ($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')));
                    $patientBirthdate = $patient['date_of_birth'] ?? $patient['birthdate'] ?? null;
                    
                    if ($patientName && $patientBirthdate) {
                        $nameParts = explode(' ', $patientName, 2);
                        $matchingPatient = $db->table('admin_patients')
                            ->where('firstname', $nameParts[0] ?? '')
                            ->where('lastname', $nameParts[1] ?? '')
                            ->where('birthdate', $patientBirthdate)
                            ->where('doctor_id', $doctorId)
                            ->where('deleted_at IS NULL', null, false)
                            ->get()
                            ->getRowArray();
                        
                        if ($matchingPatient) {
                            $db->table('admin_patients')
                                ->where('id', $matchingPatient['id'])
                                ->update([
                                    'assigned_nurse_id' => $nurseId,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }
                    }
                }
            }

            // Update admission request if provided
            if ($admissionRequestId && $db->tableExists('admission_requests')) {
                $db->table('admission_requests')
                    ->where('id', $admissionRequestId)
                    ->update([
                        'assigned_nurse_id' => $nurseId,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            // Audit log
            if ($db->tableExists('audit_logs')) {
                $nurseName = trim(($nurse['first_name'] ?? '') . ' ' . ($nurse['last_name'] ?? ''));
                if (empty($nurseName)) {
                    $nurseName = $nurse['username'] ?? 'Nurse ' . $nurseId;
                }

                $db->table('audit_logs')->insert([
                    'action' => 'nurse_assigned_to_patient',
                    'user_id' => $doctorId,
                    'user_role' => 'doctor',
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Doctor',
                    'description' => "Doctor assigned nurse {$nurseName} to patient ID: {$patientId}",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'patient_id' => $patientId,
                        'nurse_id' => $nurseId,
                        'nurse_name' => $nurseName,
                        'admission_request_id' => $admissionRequestId,
                    ]),
                    'ip_address' => $this->request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            $nurseName = trim(($nurse['first_name'] ?? '') . ' ' . ($nurse['last_name'] ?? ''));
            if (empty($nurseName)) {
                $nurseName = $nurse['username'] ?? 'Nurse ' . $nurseId;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Nurse {$nurseName} assigned successfully.",
                'nurse' => [
                    'id' => $nurseId,
                    'name' => $nurseName,
                ]
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Nurse assignment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Approve admission request (Doctor approves nurse's recommendation)
     */
    public function approveAdmission()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(401);
        }

        $admissionRequestId = $this->request->getPost('admission_request_id');
        $doctorNotes = $this->request->getPost('doctor_notes') ?? '';

        if (!$admissionRequestId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Admission request ID is required']);
        }

        $db = \Config\Database::connect();
        $doctorId = session()->get('user_id');
        $db->transStart();

        try {
            // Get admission request
            $admissionRequest = $db->table('admission_requests')
                ->where('id', $admissionRequestId)
                ->where('status', 'pending_doctor_approval')
                ->get()
                ->getRowArray();

            if (!$admissionRequest) {
                throw new \Exception('Admission request not found or already processed');
            }

            // Update admission request status to "doctor_approved" (ready for receptionist to assign bed)
            $db->table('admission_requests')
                ->where('id', $admissionRequestId)
                ->update([
                    'status' => 'doctor_approved', // Doctor approved - ready for receptionist to assign ER bed
                    'approved_by' => $doctorId,
                    'approved_by_role' => 'doctor',
                    'approved_at' => date('Y-m-d H:i:s'),
                    'doctor_notes' => $doctorNotes,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Update consultation if exists
            if ($db->tableExists('consultations')) {
                $db->table('consultations')
                    ->where('patient_id', $admissionRequest['patient_id'])
                    ->where('consultation_date', date('Y-m-d'))
                    ->update([
                        'for_admission' => 1,
                        'status' => 'approved',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            // Audit log
            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->insert([
                    'action' => 'admission_approved_by_doctor',
                    'user_id' => $doctorId,
                    'user_role' => 'doctor',
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Doctor',
                    'description' => "Doctor approved admission request for patient ID: {$admissionRequest['patient_id']}. Ready for receptionist to assign ER bed.",
                    'related_id' => $admissionRequest['patient_id'],
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'admission_request_id' => $admissionRequestId,
                        'patient_id' => $admissionRequest['patient_id'],
                        'doctor_notes' => $doctorNotes,
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
                'message' => 'Admission approved. Receptionist will now assign ER bed.'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Admission approval error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function view($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Try to find patient in admin_patients table first
        $patient = $patientModel->find($id);
        $patientSource = 'admin_patients';
        
        // If not found in admin_patients, try patients table (receptionist patients)
        if (!$patient && $db->tableExists('patients')) {
            // Get all fields from patients table including all registration form fields
            $patient = $db->table('patients')
                ->select('patients.*')
                ->where('patient_id', $id)
                ->get()
                ->getRowArray();
            $patientSource = 'patients';
        }
        
        // If patient is from admin_patients but is an In-Patient, also fetch comprehensive data from patients table
        if ($patient && $patientSource === 'admin_patients' && $db->tableExists('patients')) {
            $patientType = $patient['type'] ?? '';
            $visitType = strtoupper(trim($patient['visit_type'] ?? ''));
            
            // If it's an In-Patient with Admission visit type, try to get comprehensive data from patients table
            if ($patientType === 'In-Patient' || $visitType === 'ADMISSION') {
                // Try to find matching patient in patients table by name
                $nameParts = [];
                if (!empty($patient['firstname'])) $nameParts[] = $patient['firstname'];
                if (!empty($patient['lastname'])) $nameParts[] = $patient['lastname'];
                
                if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                    $comprehensivePatient = $db->table('patients')
                        ->select('patients.*')
                        ->where('first_name', $nameParts[0])
                        ->where('last_name', $nameParts[1])
                        ->where('doctor_id', $doctorId)
                        ->where('type', 'In-Patient')
                        ->get()
                        ->getRowArray();
                    
                    // Merge comprehensive data with admin_patients data (comprehensive data takes precedence)
                    if ($comprehensivePatient) {
                        $patient = array_merge($patient, $comprehensivePatient);
                        $patientSource = 'patients'; // Mark as from patients table to show all sections
                    }
                }
            }
        }

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        // Get patient consultations
        $consultations = [];
        $adminPatientIdForQueries = $id; // Default to $id for admin_patients
        
        if ($db->tableExists('consultations')) {
            if ($patientSource === 'admin_patients') {
                // For admin_patients, use patient_id directly
                $consultations = $db->table('consultations')
                    ->where('patient_id', $id)
                    ->where('doctor_id', $doctorId)
                    ->orderBy('consultation_date', 'DESC')
                    ->orderBy('consultation_time', 'DESC')
                    ->get()
                    ->getResultArray();
            } else {
                // For patients table (receptionist-registered), find admin_patients record first
                // Consultations are saved with admin_patients.id
                $nameParts = [];
                if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                if (empty($nameParts) && !empty($patient['full_name'])) {
                    $parts = explode(' ', $patient['full_name'], 2);
                    $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                }
                
                // Find admin_patients record
                $adminPatient = null;
                if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $nameParts[0])
                        ->where('lastname', $nameParts[1])
                        ->where('doctor_id', $doctorId)
                        ->get()
                        ->getRowArray();
                }
                
                // If admin_patients record found, get consultations using that ID
                if ($adminPatient) {
                    $adminPatientIdForQueries = $adminPatient['id'];
                    $consultations = $db->table('consultations')
                        ->where('patient_id', $adminPatient['id'])
                        ->where('doctor_id', $doctorId)
                        ->orderBy('consultation_date', 'DESC')
                        ->orderBy('consultation_time', 'DESC')
                        ->get()
                        ->getResultArray();
                }
                
                // Also check consultations directly with patients.patient_id (fallback)
                if (empty($consultations)) {
                    $directConsultations = $db->table('consultations')
                        ->where('patient_id', $id)
                        ->where('doctor_id', $doctorId)
                        ->orderBy('consultation_date', 'DESC')
                        ->orderBy('consultation_time', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    if (!empty($directConsultations)) {
                        $consultations = $directConsultations;
                    }
                }
            }
        }
        
        // For each consultation, fetch related lab tests and prescriptions
        foreach ($consultations as &$consultation) {
            $consultationDate = $consultation['consultation_date'];
            
            // Get lab requests for this consultation (by patient_id, doctor_id, and requested_date)
            $labRequests = [];
            if ($db->tableExists('lab_requests')) {
                $labRequests = $db->table('lab_requests')
                    ->where('patient_id', $adminPatientIdForQueries)
                    ->where('doctor_id', $doctorId)
                    ->where('requested_date', $consultationDate)
                    ->orderBy('created_at', 'ASC')
                    ->get()
                    ->getResultArray();
            }
            $consultation['lab_tests'] = $labRequests;
            
            // Get prescriptions/medications for this consultation (by patient_id, doctor_id, and created_at date)
            $prescriptions = [];
            if ($db->tableExists('doctor_orders')) {
                $prescriptions = $db->table('doctor_orders')
                    ->where('patient_id', $adminPatientIdForQueries)
                    ->where('doctor_id', $doctorId)
                    ->where('order_type', 'medication')
                    ->where('DATE(created_at)', $consultationDate)
                    ->orderBy('created_at', 'ASC')
                    ->get()
                    ->getResultArray();
            }
            $consultation['prescriptions'] = $prescriptions;
        }
        unset($consultation); // Unset reference

        // Get admission request if exists
        $admissionRequest = null;
        if ($db->tableExists('admission_requests')) {
            $admissionRequest = $db->table('admission_requests')
                ->where('patient_id', $id)
                ->where('status', 'pending_doctor_approval')
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getRowArray();
        }

        // Get vital signs from assigned nurse (for Medical Information section)
        $vitalSigns = [];
        $latestVitals = null;
        if ($db->tableExists('patient_vitals')) {
            // Use admin_patients.id for patient_vitals query
            $vitalsPatientId = $adminPatientIdForQueries;
            
            // If patient is from patients table, find corresponding admin_patients record
            if ($patientSource === 'patients') {
                $nameParts = [];
                if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                
                if (empty($nameParts) && !empty($patient['full_name'])) {
                    $parts = explode(' ', $patient['full_name'], 2);
                    $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                }
                
                if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $nameParts[0])
                        ->where('lastname', $nameParts[1])
                        ->where('doctor_id', $doctorId)
                        ->where('deleted_at IS NULL', null, false)
                        ->get()
                        ->getRowArray();
                    
                    if ($adminPatient) {
                        $vitalsPatientId = $adminPatient['id'];
                    }
                }
            }
            
            // Get all vital signs for this patient (ordered by most recent)
            $vitalSigns = $db->table('patient_vitals pv')
                ->select('pv.*, users.first_name as nurse_first_name, users.last_name as nurse_last_name, users.username as nurse_username')
                ->join('users', 'users.id = pv.nurse_id', 'left')
                ->where('pv.patient_id', $vitalsPatientId)
                ->orderBy('pv.created_at', 'DESC')
                ->orderBy('pv.recorded_at', 'DESC')
                ->limit(50) // Get last 50 vital signs records
                ->get()
                ->getResultArray();
            
            // Get the most recent vital signs
            if (!empty($vitalSigns)) {
                $latestVitals = $vitalSigns[0];
            }
        }

        $data = [
            'title' => 'Patient Details',
            'patient' => $patient,
            'patientSource' => $patientSource,
            'consultations' => $consultations,
            'admissionRequest' => $admissionRequest,
            'vitalSigns' => $vitalSigns, // All vital signs history
            'latestVitals' => $latestVitals, // Most recent vital signs
        ];

        return view('doctor/patients/view', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        return view('doctor/patients/create', [
            'title' => 'Register New Patient'
        ]);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');

        $validation = $this->validate([
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birthdate' => $this->request->getPost('birthdate'),
            'gender' => $this->request->getPost('gender'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address'),
            'doctor_id' => $doctorId,
        ];

        if ($patientModel->insert($data)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient registered successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to register patient.');
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($id);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        return view('doctor/patients/edit', [
            'title' => 'Edit Patient',
            'patient' => $patient
        ]);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($id);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        $validation = $this->validate([
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birthdate' => $this->request->getPost('birthdate'),
            'gender' => $this->request->getPost('gender'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address'),
        ];

        if ($patientModel->update($id, $data)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update patient.');
    }

    public function delete($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($id);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        if ($patientModel->delete($id)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete patient.');
    }

    /**
     * View nurse assessment (vital signs) for a patient
     */
    public function nurseAssessment($patientId)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Try to find patient in admin_patients table first
        $patient = $patientModel->find($patientId);
        $patientSource = 'admin_patients';
        
        // If not found in admin_patients, try patients table
        if (!$patient && $db->tableExists('patients')) {
            $patient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->where('doctor_id', $doctorId)
                ->get()
                ->getRowArray();
            $patientSource = 'patients';
        }

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found or not assigned to you.');
        }

        // Get assigned nurse information
        $assignedNurseId = null;
        $assignedNurseName = null;
        
        if ($patientSource === 'admin_patients') {
            $assignedNurseId = $patient['assigned_nurse_id'] ?? null;
        } else {
            $assignedNurseId = $patient['assigned_nurse_id'] ?? null;
        }

        if ($assignedNurseId && $db->tableExists('users')) {
            $nurse = $db->table('users')
                ->select('users.first_name, users.last_name, users.username')
                ->where('users.id', $assignedNurseId)
                ->get()
                ->getRowArray();
            
            if ($nurse) {
                $nurseName = trim(($nurse['first_name'] ?? '') . ' ' . ($nurse['last_name'] ?? ''));
                $assignedNurseName = !empty($nurseName) ? $nurseName : ($nurse['username'] ?? 'Nurse ' . $assignedNurseId);
            }
        }

        // Get recent vital signs from the assigned nurse
        $recentVitals = [];
        $today = date('Y-m-d');
        
        if ($db->tableExists('patient_vitals') && $assignedNurseId) {
            // For admin_patients, patient_vitals.patient_id = admin_patients.id
            // For patients table, we need to find the corresponding admin_patients record first
            $vitalsPatientId = $patientId;
            
            if ($patientSource === 'patients') {
                // Try to find corresponding admin_patients record
                $nameParts = [];
                if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                
                if (empty($nameParts) && !empty($patient['full_name'])) {
                    $parts = explode(' ', $patient['full_name'], 2);
                    $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                }
                
                if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $nameParts[0])
                        ->where('lastname', $nameParts[1])
                        ->where('doctor_id', $doctorId)
                        ->where('deleted_at IS NULL', null, false)
                        ->get()
                        ->getRowArray();
                    
                    if ($adminPatient) {
                        $vitalsPatientId = $adminPatient['id'];
                    }
                }
            }
            
            // Get recent vital signs (last 7 days, not just today)
            $query = $db->table('patient_vitals pv')
                ->select('pv.*, users.first_name as nurse_first_name, users.last_name as nurse_last_name, users.username as nurse_username')
                ->join('users', 'users.id = pv.nurse_id', 'left')
                ->where('pv.patient_id', $vitalsPatientId)
                ->where('pv.nurse_id', $assignedNurseId)
                ->where('DATE(pv.created_at) >=', date('Y-m-d', strtotime('-7 days'))) // Last 7 days
                ->orderBy('pv.created_at', 'DESC')
                ->limit(20) // Show more recent vitals
                ->get()
                ->getResultArray();
            
            $recentVitals = $query;
        }

        // Get nurse notes if available
        $nurseNotes = [];
        if ($db->tableExists('nurse_notes') && $assignedNurseId) {
            // Use the same patient ID logic as vitals
            $notesPatientId = $patientId;
            if ($patientSource === 'patients') {
                // Try to find corresponding admin_patients record
                $nameParts = [];
                if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                
                if (empty($nameParts) && !empty($patient['full_name'])) {
                    $parts = explode(' ', $patient['full_name'], 2);
                    $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                }
                
                if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $nameParts[0])
                        ->where('lastname', $nameParts[1])
                        ->where('doctor_id', $doctorId)
                        ->where('deleted_at IS NULL', null, false)
                        ->get()
                        ->getRowArray();
                    
                    if ($adminPatient) {
                        $notesPatientId = $adminPatient['id'];
                    }
                }
            }
            
            // Get recent nurse notes (last 7 days, not just today)
            $nurseNotes = $db->table('nurse_notes nn')
                ->select('nn.*, users.first_name as nurse_first_name, users.last_name as nurse_last_name, users.username as nurse_username')
                ->join('users', 'users.id = nn.nurse_id', 'left')
                ->where('nn.patient_id', $notesPatientId)
                ->where('nn.nurse_id', $assignedNurseId)
                ->where('DATE(nn.created_at) >=', date('Y-m-d', strtotime('-7 days'))) // Last 7 days
                ->orderBy('nn.created_at', 'DESC')
                ->limit(20) // Show more recent notes
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Nurse Assessment',
            'patient' => $patient,
            'patientSource' => $patientSource,
            'assignedNurseName' => $assignedNurseName,
            'assignedNurseId' => $assignedNurseId,
            'recentVitals' => $recentVitals,
            'nurseNotes' => $nurseNotes,
        ];

        return view('doctor/patients/nurse_assessment', $data);
    }
}
