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
            ->orderBy('id', 'DESC')
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
                ->orderBy('patients.patient_id', 'DESC')
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
        
        // Filter out patients with completed consultations (but keep those with pending lab tests)
        $patientsWithoutCompleted = [];
        foreach ($patients as $patient) {
            $patientId = $patient['id'] ?? $patient['patient_id'] ?? null;
            $patientSource = $patient['source'] ?? 'admin';
            
            // Find the correct patient ID for consultations table
            $consultationPatientId = null;
            if ($patientSource === 'admin' || $patientSource === 'admin_patients') {
                $consultationPatientId = $patientId;
            } else {
                $consultationPatientId = $patient['patient_id'] ?? $patientId;
            }
            
            // Check if patient has a completed consultation
            $hasCompleted = false;
            $hasPendingLabTests = false;
            
            if ($consultationPatientId && $db->tableExists('consultations')) {
                // Check for completed consultation
                $completedConsultation = $db->table('consultations')
                    ->where('patient_id', $consultationPatientId)
                    ->where('doctor_id', $doctorId)
                    ->where('type', 'completed')
                    ->where('status', 'completed')
                    ->get()
                    ->getRowArray();
                
                if ($completedConsultation) {
                    $hasCompleted = true;
                }
                
                // Check for pending consultation with lab tests (waiting for lab results)
                $pendingLabConsultation = $db->table('consultations')
                    ->where('patient_id', $consultationPatientId)
                    ->where('doctor_id', $doctorId)
                    ->where('type', 'upcoming')
                    ->where('status', 'pending')
                    ->where('lab_tests IS NOT NULL')
                    ->where('lab_tests !=', '')
                    ->get()
                    ->getRowArray();
                
                // If found, check if all lab results are actually ready
                if ($pendingLabConsultation) {
                    $labTestsJson = $pendingLabConsultation['lab_tests'] ?? '';
                    $labTestIds = [];
                    if (!empty($labTestsJson)) {
                        $labTestIds = json_decode($labTestsJson, true);
                        if (!is_array($labTestIds)) {
                            $labTestIds = [];
                        }
                    }
                    
                    // Check if all lab requests from this consultation have results
                    $allResultsReady = true;
                    if (!empty($labTestIds) && $db->tableExists('lab_requests')) {
                        $consultationId = $pendingLabConsultation['id'];
                        $allLabRequests = $db->table('lab_requests')
                            ->where('patient_id', $consultationPatientId)
                            ->where('doctor_id', $doctorId)
                            ->like('instructions', 'From Consultation #' . $consultationId, 'after')
                            ->get()
                            ->getResultArray();
                        
                        if (!empty($allLabRequests)) {
                            $labResultModel = new \App\Models\LabResultModel();
                            foreach ($allLabRequests as $labReq) {
                                $hasResult = $labResultModel
                                    ->where('lab_request_id', $labReq['id'])
                                    ->first();
                                
                                if (!$hasResult) {
                                    $allResultsReady = false;
                                    break;
                                }
                            }
                            
                            // If all results are ready, auto-complete the consultation
                            if ($allResultsReady) {
                                $db->table('consultations')
                                    ->where('id', $consultationId)
                                    ->update([
                                        'status' => 'completed',
                                        'type' => 'completed',
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                
                                // Don't show as waiting for lab results
                                $pendingLabConsultation = null;
                            }
                        }
                    }
                }
                
                if ($pendingLabConsultation) {
                    $hasPendingLabTests = true;
                }
            }
            
            // Include patients without completed consultations OR with pending lab tests
            if (!$hasCompleted || $hasPendingLabTests) {
                $patientsWithoutCompleted[] = $patient;
            }
        }
        
        $patients = $patientsWithoutCompleted;
        
        // Sort by ID (descending - newest first)
        usort($patients, function($a, $b) {
            $idA = (int)($a['id'] ?? $a['patient_id'] ?? 0);
            $idB = (int)($b['id'] ?? $b['patient_id'] ?? 0);
            return $idB <=> $idA; // DESC order (newest first)
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
                $pendingLabConsultation = null;
                
                if ($consultationPatientId && $db->tableExists('consultations')) {
                    $today = date('Y-m-d');
                    $currentTime = date('H:i:s');
                    
                    // Check for pending consultation with lab tests (waiting for lab results)
                    $pendingLabConsultation = $db->table('consultations')
                        ->where('patient_id', $consultationPatientId)
                        ->where('doctor_id', $doctorId)
                        ->where('type', 'upcoming')
                        ->where('status', 'pending')
                        ->where('lab_tests IS NOT NULL')
                        ->where('lab_tests !=', '')
                        ->orderBy('consultation_date', 'DESC')
                        ->orderBy('consultation_time', 'DESC')
                        ->get()
                        ->getRowArray();
                    
                    // If found, check if all lab results are actually ready
                    if ($pendingLabConsultation) {
                        $labTestsJson = $pendingLabConsultation['lab_tests'] ?? '';
                        $labTestIds = [];
                        if (!empty($labTestsJson)) {
                            $labTestIds = json_decode($labTestsJson, true);
                            if (!is_array($labTestIds)) {
                                $labTestIds = [];
                            }
                        }
                        
                        // Check if all lab requests from this consultation have results
                        $allResultsReady = true;
                        if (!empty($labTestIds) && $db->tableExists('lab_requests')) {
                            $consultationId = $pendingLabConsultation['id'];
                            $allLabRequests = $db->table('lab_requests')
                                ->where('patient_id', $consultationPatientId)
                                ->where('doctor_id', $doctorId)
                                ->like('instructions', 'From Consultation #' . $consultationId, 'after')
                                ->get()
                                ->getResultArray();
                            
                            if (!empty($allLabRequests)) {
                                $labResultModel = new \App\Models\LabResultModel();
                                foreach ($allLabRequests as $labReq) {
                                    $hasResult = $labResultModel
                                        ->where('lab_request_id', $labReq['id'])
                                        ->first();
                                    
                                    if (!$hasResult) {
                                        $allResultsReady = false;
                                        break;
                                    }
                                }
                                
                                // If all results are ready, auto-complete the consultation
                                if ($allResultsReady) {
                                    $db->table('consultations')
                                        ->where('id', $consultationId)
                                        ->update([
                                            'status' => 'completed',
                                            'type' => 'completed',
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ]);
                                    
                                    // Create consultation charge when consultation is auto-completed
                                    $this->createConsultationCharge($consultationId, $consultationPatientId);
                                    
                                    // Don't show as waiting for lab results
                                    $pendingLabConsultation = null;
                                }
                            }
                        }
                    }
                    
                    if ($patientSource === 'receptionist') {
                        // For receptionist patients, consultations table uses patients.patient_id
                        $upcomingConsultation = $db->table('consultations')
                            ->where('patient_id', $consultationPatientId)
                            ->where('doctor_id', $doctorId)
                            ->where('type', 'upcoming')
                            ->whereIn('status', ['approved', 'pending'])
                            ->groupStart()
                                ->where('lab_tests IS NULL', null, false)
                                ->orWhere('lab_tests', '')
                            ->groupEnd()
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
                                ->where('lab_tests IS NULL', null, false)
                                ->orWhere('lab_tests', '')
                            ->groupEnd()
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
                
                // Attach pending lab consultation info
                if ($pendingLabConsultation) {
                    $patient['pending_lab_consultation'] = $pendingLabConsultation;
                    $patient['waiting_for_lab_results'] = true;
                } else {
                    $patient['waiting_for_lab_results'] = false;
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
                
                // Check if patient has active admission (for discharge button)
                $activeAdmission = null;
                $admissionId = null;
                $isDirectAdmission = false;
                
                // First, check if patient has visit_type = 'Admission' (direct admission indicator)
                $visitType = strtoupper(trim($patient['visit_type'] ?? ''));
                $patientType = strtoupper(trim($patient['type'] ?? ''));
                if ($visitType === 'ADMISSION' || ($patientType === 'IN-PATIENT' && ($visitType === 'ADMISSION' || empty($visitType)))) {
                    $isDirectAdmission = true;
                }
                
                // Get the correct patient ID for admissions check
                $admissionPatientId = null;
                if ($patientSource === 'admin' || $patientSource === 'admin_patients') {
                    $admissionPatientId = $patientId;
                } else {
                    // For patients table, find corresponding admin_patients.id
                    $nameParts = [];
                    if (!empty($patient['firstname'])) $nameParts[] = $patient['firstname'];
                    if (!empty($patient['lastname'])) $nameParts[] = $patient['lastname'];
                    
                    if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                        $adminPatient = $db->table('admin_patients')
                            ->where('firstname', $nameParts[0])
                            ->where('lastname', $nameParts[1])
                            ->where('doctor_id', $doctorId)
                            ->where('deleted_at IS NULL', null, false)
                            ->get()
                            ->getRowArray();
                        
                        if ($adminPatient) {
                            $admissionPatientId = $adminPatient['id'];
                        }
                    }
                }
                
                // Check for active admission record
                if ($db->tableExists('admissions') && $admissionPatientId) {
                    // Check for active admission - try with attending_physician_id first, then without
                    $activeAdmission = $db->table('admissions')
                        ->where('patient_id', $admissionPatientId)
                        ->groupStart()
                            ->where('attending_physician_id', $doctorId)
                            ->orWhere('attending_physician_id', null) // Also check if no attending physician assigned yet
                        ->groupEnd()
                        ->where('status', 'admitted')
                        ->where('discharge_status', 'admitted')
                        ->where('deleted_at', null)
                        ->orderBy('admission_date', 'DESC')
                        ->get()
                        ->getRowArray();
                    
                    // If not found with doctor filter, try without doctor filter (for patients assigned to this doctor)
                    if (!$activeAdmission) {
                        $activeAdmission = $db->table('admissions')
                            ->where('patient_id', $admissionPatientId)
                            ->where('status', 'admitted')
                            ->where('discharge_status', 'admitted')
                            ->where('deleted_at', null)
                            ->orderBy('admission_date', 'DESC')
                            ->get()
                            ->getRowArray();
                    }
                    
                    if ($activeAdmission) {
                        $admissionId = $activeAdmission['id'];
                    }
                }
                
                // If no admission record but patient is direct admission, use patient ID as reference
                if (!$admissionId && $isDirectAdmission && $admissionPatientId) {
                    $admissionId = $admissionPatientId; // Use patient ID for direct admission
                }
                
                $patient['admission_id'] = $admissionId;
                $patient['has_active_admission'] = !empty($activeAdmission) || $isDirectAdmission;
                $patient['is_direct_admission'] = $isDirectAdmission;
                
                // Check if patient is in OR room (surgery room)
                $isInORRoom = false;
                $surgeryDateTime = null;
                $surgeryEndDateTime = null;
                if ($db->tableExists('rooms') && $db->tableExists('surgeries')) {
                    // Check if patient has a scheduled surgery with OR room
                    $surgery = $db->table('surgeries')
                        ->where('patient_id', $patientId)
                        ->whereIn('status', ['scheduled', 'completed']) // Check both scheduled and completed
                        ->where('deleted_at', null)
                        ->orderBy('surgery_date', 'DESC')
                        ->orderBy('surgery_time', 'DESC')
                        ->get()
                        ->getRowArray();
                    
                    if ($surgery && !empty($surgery['or_room_id'])) {
                        // If surgery is completed, don't show as in OR
                        if ($surgery['status'] === 'completed') {
                            log_message('info', "Patient #{$patientId} surgery is completed - not showing as in OR");
                            $isInORRoom = false;
                        } else if ($surgery['status'] === 'scheduled') {
                            // Check if countdown has finished
                            $countdownFinished = false;
                            if (!empty($surgery['surgery_date']) && !empty($surgery['surgery_time'])) {
                                $surgeryDateTime = $surgery['surgery_date'] . ' ' . $surgery['surgery_time'];
                                $surgeryStart = strtotime($surgeryDateTime);
                                $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                                $countdownFinished = (time() >= $surgeryEnd);
                                
                                log_message('info', "Patient #{$patientId} surgery check - start: {$surgeryDateTime}, end: " . date('Y-m-d H:i:s', $surgeryEnd) . ", now: " . date('Y-m-d H:i:s') . ", finished: " . ($countdownFinished ? 'YES' : 'NO'));
                            }
                            
                            // If countdown finished, move patient back immediately
                            if ($countdownFinished) {
                                log_message('info', "Patient #{$patientId} countdown finished - auto-moving back from OR");
                                try {
                                    $surgeryController = new \App\Controllers\Doctor\SurgeryController();
                                    $moveResult = $surgeryController->movePatientBackFromOR($surgery);
                                    
                                    if ($moveResult) {
                                        log_message('info', "Patient #{$patientId} successfully moved back from OR");
                                        // Re-check patient room after moving back
                                        $patient = $this->adminPatientModel->find($patientId);
                                        if ($patient) {
                                            $patient['room_id'] = $patient['room_id'] ?? null;
                                            $patient['room_number'] = $patient['room_number'] ?? null;
                                        }
                                    } else {
                                        log_message('warning', "Patient #{$patientId} move back returned false");
                                    }
                                    // Always set to false if countdown finished, regardless of move result
                                    $isInORRoom = false;
                                } catch (\Exception $e) {
                                    log_message('error', "Failed to auto-move patient back: " . $e->getMessage());
                                    // Still set to false if countdown finished - don't show as in OR
                                    $isInORRoom = false;
                                }
                            } else {
                                // Check if OR room is occupied by this patient
                                $orRoom = $db->table('rooms')
                                    ->where('id', $surgery['or_room_id'])
                                    ->where('room_type', 'OR')
                                    ->where('current_patient_id', $patientId)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($orRoom) {
                                    // Get surgery datetime for countdown FIRST, before setting isInORRoom
                                    $countdownStillActive = true;
                                    if (!empty($surgery['surgery_date']) && !empty($surgery['surgery_time'])) {
                                        $surgeryDateTime = $surgery['surgery_date'] . ' ' . $surgery['surgery_time'];
                                        // Calculate end time (2 hours from surgery start)
                                        $surgeryStart = strtotime($surgeryDateTime);
                                        $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                                        $surgeryEndDateTime = date('Y-m-d H:i:s', $surgeryEnd);
                                        
                                        // Check if countdown has finished
                                        $countdownStillActive = (time() < $surgeryEnd);
                                        
                                        // If countdown finished, move patient back and don't show as in OR
                                        if (!$countdownStillActive) {
                                            log_message('info', "Patient #{$patientId} countdown finished (OR room check) - auto-moving back from OR");
                                            try {
                                                $surgeryController = new \App\Controllers\Doctor\SurgeryController();
                                                $moveResult = $surgeryController->movePatientBackFromOR($surgery);
                                                $isInORRoom = false;
                                                $surgeryEndDateTime = null;
                                            } catch (\Exception $e) {
                                                log_message('error', "Failed to auto-move patient back: " . $e->getMessage());
                                                $isInORRoom = false;
                                                $surgeryEndDateTime = null;
                                            }
                                        } else {
                                            // Countdown still active - show as in OR
                                            $isInORRoom = true;
                                        }
                                    } else {
                                        // No surgery date/time - don't show as in OR
                                        $isInORRoom = false;
                                        $surgeryEndDateTime = null;
                                    }
                                } else {
                                    log_message('info', "Patient #{$patientId} has scheduled surgery but not in OR room");
                                    $isInORRoom = false;
                                }
                            }
                        }
                    }
                    
                    // Also check if patient's room_id points to an OR room AND surgery is still scheduled
                    if (!$isInORRoom) {
                        $patientRoomId = $patient['room_id'] ?? null;
                        if ($patientRoomId) {
                            $patientRoom = $db->table('rooms')
                                ->where('id', $patientRoomId)
                                ->where('room_type', 'OR')
                                ->where('current_patient_id', $patientId)
                                ->get()
                                ->getRowArray();
                            
                            if ($patientRoom) {
                                // Check for any surgery (scheduled or completed) - if completed but still in OR, need to move back
                                $activeSurgery = $db->table('surgeries')
                                    ->where('patient_id', $patientId)
                                    ->where('or_room_id', $patientRoomId)
                                    ->whereIn('status', ['scheduled', 'completed']) // Check both scheduled and completed
                                    ->where('deleted_at', null)
                                    ->orderBy('created_at', 'DESC')
                                    ->get()
                                    ->getRowArray();
                                
                                if ($activeSurgery) {
                                    // Only show as in OR if status is scheduled (not completed) AND countdown hasn't finished
                                    if ($activeSurgery['status'] === 'scheduled') {
                                        // Check if countdown has finished
                                        $countdownFinished = false;
                                        if (!empty($activeSurgery['surgery_date']) && !empty($activeSurgery['surgery_time'])) {
                                            $surgeryDateTime = $activeSurgery['surgery_date'] . ' ' . $activeSurgery['surgery_time'];
                                            $surgeryStart = strtotime($surgeryDateTime);
                                            $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                                            $countdownFinished = (time() >= $surgeryEnd);
                                        }
                                        
                                        if ($countdownFinished) {
                                            // Countdown finished - move patient back
                                            log_message('info', "Patient #{$patientId} countdown finished (fallback check) - auto-moving back from OR");
                                            try {
                                                $surgeryController = new \App\Controllers\Doctor\SurgeryController();
                                                $surgeryController->movePatientBackFromOR($activeSurgery);
                                                $isInORRoom = false; // Don't show as in OR
                                            } catch (\Exception $e) {
                                                log_message('error', "Failed to auto-move patient back: " . $e->getMessage());
                                                $isInORRoom = false; // Don't show as in OR if countdown finished
                                            }
                                        } else {
                                            // Countdown not finished yet - check if countdown actually hasn't finished
                                            $countdownStillActive = true;
                                            if (!empty($activeSurgery['surgery_date']) && !empty($activeSurgery['surgery_time'])) {
                                                $surgeryDateTime = $activeSurgery['surgery_date'] . ' ' . $activeSurgery['surgery_time'];
                                                $surgeryStart = strtotime($surgeryDateTime);
                                                $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                                                $countdownStillActive = (time() < $surgeryEnd);
                                            }
                                            
                                            if ($countdownStillActive) {
                                                $isInORRoom = true;
                                                // Try to get surgery info if available
                                                if (!empty($activeSurgery['surgery_date']) && !empty($activeSurgery['surgery_time'])) {
                                                    $surgeryDateTime = $activeSurgery['surgery_date'] . ' ' . $activeSurgery['surgery_time'];
                                                    $surgeryStart = strtotime($surgeryDateTime);
                                                    $surgeryEndDateTime = date('Y-m-d H:i:s', $surgeryStart + (2 * 60 * 60)); // Add 2 hours
                                                }
                                            } else {
                                                // Countdown finished - don't show as in OR
                                                $isInORRoom = false;
                                                $surgeryEndDateTime = null;
                                            }
                                        }
                                    } else if ($activeSurgery['status'] === 'completed') {
                                        // Surgery is completed but patient still in OR - auto-move back
                                        log_message('warning', "Patient #{$patientId} has completed surgery but still in OR room - auto-moving back");
                                        try {
                                            $surgeryController = new \App\Controllers\Doctor\SurgeryController();
                                            $moveResult = $surgeryController->movePatientBackFromOR($activeSurgery);
                                            $isInORRoom = false; // Don't show as in OR if surgery is completed
                                        } catch (\Exception $e) {
                                            log_message('error', "Failed to auto-move patient back: " . $e->getMessage());
                                            $isInORRoom = false; // Don't show as in OR if surgery is completed
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                // FINAL CHECK: If countdown finished, always set to false (regardless of any previous checks)
                if ($surgeryEndDateTime) {
                    $endTime = strtotime($surgeryEndDateTime);
                    $now = time();
                    if ($now >= $endTime) {
                        // Countdown finished - force to false
                        $isInORRoom = false;
                        $surgeryEndDateTime = null;
                        log_message('info', "Patient #{$patientId} FINAL CHECK: Countdown finished - forcing isInORRoom to false");
                    }
                }
                
                $patient['is_in_or_room'] = $isInORRoom;
                $patient['surgery_end_datetime'] = $surgeryEndDateTime;
                
                // Check if patient has active continuous monitoring
                $isMonitoring = false;
                if ($db->tableExists('patient_monitoring')) {
                    // Get the correct patient ID for monitoring check
                    $monitoringPatientId = null;
                    if ($patientSource === 'admin' || $patientSource === 'admin_patients') {
                        $monitoringPatientId = $patientId;
                    } else {
                        // For patients table, find corresponding admin_patients.id
                        $nameParts = [];
                        if (!empty($patient['firstname'])) $nameParts[] = $patient['firstname'];
                        if (!empty($patient['lastname'])) $nameParts[] = $patient['lastname'];
                        
                        if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                            $adminPatient = $db->table('admin_patients')
                                ->where('firstname', $nameParts[0])
                                ->where('lastname', $nameParts[1])
                                ->where('doctor_id', $doctorId)
                                ->where('deleted_at IS NULL', null, false)
                                ->get()
                                ->getRowArray();
                            
                            if ($adminPatient) {
                                $monitoringPatientId = $adminPatient['id'];
                            }
                        }
                    }
                    
                    if ($monitoringPatientId) {
                        $monitoring = $db->table('patient_monitoring')
                            ->where('patient_id', $monitoringPatientId)
                            ->where('status', 'active')
                            ->orderBy('started_at', 'DESC')
                            ->get()
                            ->getRowArray();
                        
                        $isMonitoring = !empty($monitoring);
                    }
                }
                
                $patient['is_monitoring'] = $isMonitoring;
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
                $scheduleInfo = null;

                // Check if nurse has an active schedule for the selected date
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

                        // Include nurse if they have a schedule (regardless of current time)
                        $scheduleInfo = [
                            'shift_type' => $schedule['shift_type'] ?? 'N/A',
                            'start_time' => substr($startTime, 0, 5),
                            'end_time' => substr($endTime, 0, 5),
                        ];
                    }
                }

                // Include nurses who have schedules for the selected date
                if ($scheduleInfo) {
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
                    // Nurse has a schedule for this date - allow assignment
                    $isAvailable = true;
                }
            }

            if (!$isAvailable) {
                throw new \Exception('Nurse does not have an active schedule for this date. Please select a nurse with an active schedule.');
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

        // Get active admission for this patient (for discharge button)
        $activeAdmission = null;
        $admissionId = null;
        if ($db->tableExists('admissions')) {
            $activeAdmission = $db->table('admissions')
                ->where('patient_id', $adminPatientIdForQueries)
                ->where('attending_physician_id', $doctorId)
                ->where('status', 'admitted')
                ->where('discharge_status', 'admitted')
                ->where('deleted_at', null)
                ->orderBy('admission_date', 'DESC')
                ->get()
                ->getRowArray();
            
            if ($activeAdmission) {
                $admissionId = $activeAdmission['id'];
            }
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
                
                // Check if doctor has created an order after the latest vitals were recorded
                $hasOrderForLatestVitals = false;
                if ($latestVitals && $db->tableExists('doctor_orders')) {
                    $latestVitalsTime = $latestVitals['recorded_at'] ?? $latestVitals['created_at'];
                    $orderAfterVitals = $db->table('doctor_orders')
                        ->where('patient_id', $vitalsPatientId)
                        ->where('doctor_id', $doctorId)
                        ->where('created_at >=', $latestVitalsTime)
                        ->limit(1)
                        ->get()
                        ->getRowArray();
                    
                    $hasOrderForLatestVitals = !empty($orderAfterVitals);
                }
                $latestVitals['has_order'] = $hasOrderForLatestVitals;
                
                // Add comparison status for each vital sign and check if order exists
                foreach ($vitalSigns as $index => &$vital) {
                    // Get previous vital sign (next in array since ordered DESC)
                    $previousVital = null;
                    if ($index < count($vitalSigns) - 1) {
                        $previousVital = $vitalSigns[$index + 1];
                    }
                    
                    // Calculate status for each vital sign
                    $vital['status'] = $this->calculateVitalStatus($vital, $previousVital);
                    
                    // Check if doctor has created an order after this vital was recorded
                    $hasOrderForThisVital = false;
                    if ($db->tableExists('doctor_orders')) {
                        $vitalTime = $vital['recorded_at'] ?? $vital['created_at'];
                        $orderAfterVital = $db->table('doctor_orders')
                            ->where('patient_id', $vitalsPatientId)
                            ->where('doctor_id', $doctorId)
                            ->where('created_at >=', $vitalTime)
                            ->limit(1)
                            ->get()
                            ->getRowArray();
                        
                        $hasOrderForThisVital = !empty($orderAfterVital);
                    }
                    $vital['has_order'] = $hasOrderForThisVital;
                    
                    // Add admission_id for discharge button (only for latest vital)
                    if ($index === 0 && $admissionId) {
                        $vital['admission_id'] = $admissionId;
                    }
                }
                unset($vital); // Break reference
            }
        }

        // Get completed lab test results for this patient
        $labResults = [];
        if ($db->tableExists('lab_requests') && $db->tableExists('lab_results')) {
            $labResults = $db->table('lab_requests lr')
                ->select('lr.*, lr_result.result, lr_result.result_file, lr_result.completed_at, 
                         lr_result.completed_by, users.username as completed_by_name')
                ->join('lab_results lr_result', 'lr_result.lab_request_id = lr.id', 'inner')
                ->join('users', 'users.id = lr_result.completed_by', 'left')
                ->where('lr.patient_id', $adminPatientIdForQueries ?? $patientId)
                ->where('lr.doctor_id', $doctorId)
                ->where('lr.status', 'completed')
                ->orderBy('lr_result.completed_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        // Get all orders for this patient (including completed ones)
        $allPatientOrders = [];
        if ($db->tableExists('doctor_orders')) {
            $allPatientOrders = $db->table('doctor_orders do')
                ->select('do.*, users.username as completed_by_name, nurse_users.username as nurse_name')
                ->join('users', 'users.id = do.completed_by', 'left')
                ->join('users as nurse_users', 'nurse_users.id = do.nurse_id', 'left')
                ->where('do.patient_id', $adminPatientIdForQueries)
                ->where('do.doctor_id', $doctorId)
                ->orderBy('do.created_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        // Check if patient is in OR room (surgery room)
        $isInORRoom = false;
        $surgeryEndDateTime = null; // Initialize variable - MUST be initialized before use
        if ($db->tableExists('rooms') && $db->tableExists('surgeries')) {
            $patientIdForSurgery = $adminPatientIdForQueries ?? $id;
            
            // Check if patient has a scheduled surgery with OR room
            $surgery = $db->table('surgeries')
                ->where('patient_id', $patientIdForSurgery)
                ->whereIn('status', ['scheduled', 'completed']) // Check both scheduled and completed
                ->where('deleted_at', null)
                ->orderBy('surgery_date', 'DESC')
                ->orderBy('surgery_time', 'DESC')
                ->get()
                ->getRowArray();
            
            if ($surgery && !empty($surgery['or_room_id'])) {
                // If surgery is completed, don't show as in OR
                if ($surgery['status'] === 'completed') {
                    log_message('info', "Patient #{$patientIdForSurgery} surgery is completed (view) - not showing as in OR");
                    $isInORRoom = false;
                } else if ($surgery['status'] === 'scheduled') {
                    // Check if countdown has finished
                    $countdownFinished = false;
                    if (!empty($surgery['surgery_date']) && !empty($surgery['surgery_time'])) {
                        $surgeryDateTime = $surgery['surgery_date'] . ' ' . $surgery['surgery_time'];
                        $surgeryStart = strtotime($surgeryDateTime);
                        $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                        $countdownFinished = (time() >= $surgeryEnd);
                        
                        log_message('info', "Patient #{$patientIdForSurgery} surgery check (view) - start: {$surgeryDateTime}, end: " . date('Y-m-d H:i:s', $surgeryEnd) . ", now: " . date('Y-m-d H:i:s') . ", finished: " . ($countdownFinished ? 'YES' : 'NO'));
                    }
                    
                    // If countdown finished, move patient back immediately
                    if ($countdownFinished) {
                        log_message('info', "Patient #{$patientIdForSurgery} countdown finished (view page) - auto-moving back from OR");
                        try {
                            $surgeryController = new \App\Controllers\Doctor\SurgeryController();
                            $moveResult = $surgeryController->movePatientBackFromOR($surgery);
                            
                            if ($moveResult) {
                                log_message('info', "Patient #{$patientIdForSurgery} successfully moved back from OR (view)");
                                // Re-check patient room after moving back
                                $adminPatient = $this->adminPatientModel->find($adminPatientIdForQueries ?? $id);
                                if ($adminPatient) {
                                    $patient['room_id'] = $adminPatient['room_id'] ?? null;
                                    $patient['room_number'] = $adminPatient['room_number'] ?? null;
                                }
                            } else {
                                log_message('warning', "Patient #{$patientIdForSurgery} move back returned false (view)");
                            }
                            // Always set to false if countdown finished, regardless of move result
                            $isInORRoom = false;
                        } catch (\Exception $e) {
                            log_message('error', "Failed to auto-move patient back (view): " . $e->getMessage());
                            // Still set to false if countdown finished - don't show as in OR
                            $isInORRoom = false;
                        }
                    } else {
                        // Check if OR room is occupied by this patient
                        $orRoom = $db->table('rooms')
                            ->where('id', $surgery['or_room_id'])
                            ->where('room_type', 'OR')
                            ->where('current_patient_id', $patientIdForSurgery)
                            ->get()
                            ->getRowArray();
                        
                        if ($orRoom) {
                            // Get surgery datetime for countdown FIRST, before setting isInORRoom
                            $countdownStillActive = true;
                            if (!empty($surgery['surgery_date']) && !empty($surgery['surgery_time'])) {
                                $surgeryDateTime = $surgery['surgery_date'] . ' ' . $surgery['surgery_time'];
                                // Calculate end time (2 hours from surgery start)
                                $surgeryStart = strtotime($surgeryDateTime);
                                $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                                $surgeryEndDateTime = date('Y-m-d H:i:s', $surgeryEnd);
                                
                                // Check if countdown has finished
                                $countdownStillActive = (time() < $surgeryEnd);
                                
                                // If countdown finished, move patient back and don't show as in OR
                                if (!$countdownStillActive) {
                                    log_message('info', "Patient #{$patientIdForSurgery} countdown finished (view OR room check) - auto-moving back from OR");
                                    try {
                                        $surgeryController = new \App\Controllers\Doctor\SurgeryController();
                                        $moveResult = $surgeryController->movePatientBackFromOR($surgery);
                                        $isInORRoom = false;
                                        $surgeryEndDateTime = null;
                                    } catch (\Exception $e) {
                                        log_message('error', "Failed to auto-move patient back: " . $e->getMessage());
                                        $isInORRoom = false;
                                        $surgeryEndDateTime = null;
                                    }
                                } else {
                                    // Countdown still active - show as in OR
                                    $isInORRoom = true;
                                }
                            } else {
                                // No surgery date/time - don't show as in OR
                                $isInORRoom = false;
                                $surgeryEndDateTime = null;
                            }
                        } else {
                            log_message('info', "Patient #{$patientIdForSurgery} has scheduled surgery but not in OR room (view)");
                            $isInORRoom = false;
                        }
                    }
                }
            }
            
            // Also check if patient's room_id points to an OR room AND surgery is still scheduled
            if (!$isInORRoom) {
                $patientRoomId = $patient['room_id'] ?? null;
                if ($patientRoomId) {
                    $patientRoom = $db->table('rooms')
                        ->where('id', $patientRoomId)
                        ->where('room_type', 'OR')
                        ->where('current_patient_id', $patientIdForSurgery)
                        ->get()
                        ->getRowArray();
                    
                    if ($patientRoom) {
                        // Check for any surgery (scheduled or completed) - if completed but still in OR, need to move back
                        $activeSurgery = $db->table('surgeries')
                            ->where('patient_id', $patientIdForSurgery)
                            ->where('or_room_id', $patientRoomId)
                            ->whereIn('status', ['scheduled', 'completed']) // Check both scheduled and completed
                            ->where('deleted_at', null)
                            ->orderBy('created_at', 'DESC')
                            ->get()
                            ->getRowArray();
                        
                        if ($activeSurgery) {
                            // Only show as in OR if status is scheduled (not completed) AND countdown hasn't finished
                            if ($activeSurgery['status'] === 'scheduled') {
                                // Check if countdown has finished
                                $countdownFinished = false;
                                if (!empty($activeSurgery['surgery_date']) && !empty($activeSurgery['surgery_time'])) {
                                    $surgeryDateTime = $activeSurgery['surgery_date'] . ' ' . $activeSurgery['surgery_time'];
                                    $surgeryStart = strtotime($surgeryDateTime);
                                    $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                                    $countdownFinished = (time() >= $surgeryEnd);
                                }
                                
                                if ($countdownFinished) {
                                    // Countdown finished - move patient back
                                    log_message('info', "Patient #{$patientIdForSurgery} countdown finished (fallback check view) - auto-moving back from OR");
                                    try {
                                        $surgeryController = new \App\Controllers\Doctor\SurgeryController();
                                        $surgeryController->movePatientBackFromOR($activeSurgery);
                                        $isInORRoom = false; // Don't show as in OR
                                        $surgeryEndDateTime = null;
                                    } catch (\Exception $e) {
                                        log_message('error', "Failed to auto-move patient back: " . $e->getMessage());
                                        $isInORRoom = false; // Don't show as in OR if countdown finished
                                        $surgeryEndDateTime = null;
                                    }
                                } else {
                                    // Countdown still active - show as in OR
                                    $isInORRoom = true;
                                    // Try to get surgery info if available
                                    if (!empty($activeSurgery['surgery_date']) && !empty($activeSurgery['surgery_time'])) {
                                        $surgeryDateTime = $activeSurgery['surgery_date'] . ' ' . $activeSurgery['surgery_time'];
                                        $surgeryStart = strtotime($surgeryDateTime);
                                        $surgeryEndDateTime = date('Y-m-d H:i:s', $surgeryStart + (2 * 60 * 60)); // Add 2 hours
                                    }
                                }
                            } else if ($activeSurgery['status'] === 'completed') {
                                // Surgery is completed but patient still in OR - auto-move back
                                log_message('warning', "Patient #{$patientIdForSurgery} has completed surgery but still in OR room - auto-moving back");
                                try {
                                    $surgeryController = new \App\Controllers\Doctor\SurgeryController();
                                    $moveResult = $surgeryController->movePatientBackFromOR($activeSurgery);
                                    $isInORRoom = false; // Don't show as in OR if surgery is completed
                                } catch (\Exception $e) {
                                    log_message('error', "Failed to auto-move patient back: " . $e->getMessage());
                                    $isInORRoom = false; // Don't show as in OR if surgery is completed
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // FINAL CHECK for view page: If countdown finished, always set to false
        if (isset($surgeryEndDateTime) && $surgeryEndDateTime) {
            $endTime = strtotime($surgeryEndDateTime);
            $now = time();
            if ($now >= $endTime) {
                // Countdown finished - force to false
                $isInORRoom = false;
                $surgeryEndDateTime = null;
                log_message('info', "Patient #{$id} FINAL CHECK (view): Countdown finished - forcing isInORRoom to false");
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
            'vitalsPatientId' => $vitalsPatientId ?? $adminPatientIdForQueries ?? null, // Patient ID used for vitals queries (admin_patients.id)
            'labResults' => $labResults, // Completed lab test results
            'allPatientOrders' => $allPatientOrders, // All orders (pending, in_progress, completed)
            'isInORRoom' => $isInORRoom,
            'surgeryEndDateTime' => $surgeryEndDateTime,
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

    public function requestVitalsCheck($patientId)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        $patientModel = new AdminPatientModel();

        // Get patient
        $patient = $patientModel->find($patientId);
        $patientSource = 'admin_patients';
        
        // If not found in admin_patients, check patients table
        if (!$patient && $db->tableExists('patients')) {
            $patient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->get()
                ->getRowArray();
            $patientSource = 'patients';
        }

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        // Verify patient is assigned to this doctor
        $patientDoctorId = null;
        if ($patientSource === 'admin_patients') {
            $patientDoctorId = $patient['doctor_id'] ?? null;
        } else {
            $patientDoctorId = $patient['doctor_id'] ?? null;
        }

        if ($patientDoctorId != $doctorId) {
            return redirect()->back()->with('error', 'Patient is not assigned to you.');
        }

        // BACKEND VALIDATION: Prevent doctor from clicking Check multiple times
        // Check if doctor_check_status is already 'pending_nurse' or 'pending_order'
        $currentStatus = $patient['doctor_check_status'] ?? 'available';
        if ($currentStatus === 'pending_nurse') {
            return redirect()->back()->with('error', 'Vitals check is already in progress. Please wait for the nurse to complete the vital signs check.');
        }
        if ($currentStatus === 'pending_order') {
            return redirect()->back()->with('error', 'Please create and complete a medical order from the Vital Signs History before checking this patient again.');
        }

        // Set status to 'pending_nurse' to lock the workflow
        // This disables the Check button and enables nurse to check vitals
        $updateData = [
            'is_doctor_checked' => 1,
            'doctor_check_status' => 'pending_nurse',
            'nurse_vital_status' => 'pending',
        ];

        // Update both admin_patients and patients tables if needed
        if ($patientSource === 'admin_patients') {
            $patientModel->update($patientId, $updateData);
            
            // Also update patients table if corresponding record exists
            if ($db->tableExists('patients')) {
                $nameParts = [
                    $patient['firstname'] ?? '',
                    $patient['lastname'] ?? ''
                ];
                
                if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                    $hmsPatient = $db->table('patients')
                        ->where('first_name', $nameParts[0])
                        ->where('last_name', $nameParts[1])
                        ->where('doctor_id', $doctorId)
                        ->get()
                        ->getRowArray();
                    
                    if ($hmsPatient) {
                        $db->table('patients')
                            ->where('patient_id', $hmsPatient['patient_id'])
                            ->update($updateData);
                    }
                }
            }
        } else {
            // Update patients table
            $db->table('patients')
                ->where('patient_id', $patientId)
                ->update($updateData);
            
            // Also update admin_patients if corresponding record exists
            $nameParts = [];
            if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
            if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
            if (empty($nameParts) && !empty($patient['full_name'])) {
                $parts = explode(' ', $patient['full_name'], 2);
                $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
            }
            
            if (!empty($nameParts[0]) && !empty($nameParts[1]) && $db->tableExists('admin_patients')) {
                $adminPatient = $db->table('admin_patients')
                    ->where('firstname', $nameParts[0])
                    ->where('lastname', $nameParts[1])
                    ->where('doctor_id', $doctorId)
                    ->where('deleted_at IS NULL', null, false)
                    ->get()
                    ->getRowArray();
                
                if ($adminPatient) {
                    $patientModel->update($adminPatient['id'], $updateData);
                }
            }
        }

        // Get assigned nurse for notification
        $assignedNurseId = null;
        if ($patientSource === 'admin_patients') {
            $assignedNurseId = $patient['assigned_nurse_id'] ?? null;
        } else {
            $assignedNurseId = $patient['assigned_nurse_id'] ?? null;
        }

        // Get patient name
        $patientName = '';
        $notificationPatientId = $patientId;
        if ($patientSource === 'admin_patients') {
            $patientName = trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''));
            $notificationPatientId = $patientId;
        } else {
            $patientName = trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
            if (empty($patientName) && !empty($patient['full_name'])) {
                $patientName = $patient['full_name'];
            }
            // Find admin_patients ID for notification
            if ($db->tableExists('admin_patients') && !empty($nameParts[0]) && !empty($nameParts[1])) {
                $adminPatient = $db->table('admin_patients')
                    ->where('firstname', $nameParts[0])
                    ->where('lastname', $nameParts[1])
                    ->where('doctor_id', $doctorId)
                    ->where('deleted_at IS NULL', null, false)
                    ->get()
                    ->getRowArray();
                
                if ($adminPatient) {
                    $notificationPatientId = $adminPatient['id'];
                }
            }
        }

        // Create notification for nurse if assigned
        if ($assignedNurseId && $db->tableExists('nurse_notifications')) {
            $doctorName = session()->get('name') ?? session()->get('username') ?? 'Doctor';
            
            $db->table('nurse_notifications')->insert([
                'nurse_id' => $assignedNurseId,
                'type' => 'vitals_check_requested',
                'title' => 'Doctor Checked Patient - Vitals Check Enabled',
                'message' => 'Dr. ' . $doctorName . ' has checked ' . $patientName . '. You can now check and record vital signs for this patient.',
                'related_id' => $notificationPatientId,
                'related_type' => 'patient_vitals',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->back()->with('success', 'Patient checked successfully. Nurse can now check vital signs for this patient.');
    }

    /**
     * Calculate vital sign status (Improving/Worsening/Stable) compared to previous reading
     */
    private function calculateVitalStatus($current, $previous)
    {
        if (!$previous) {
            return [
                'overall' => 'new',
                'bp' => null,
                'hr' => null,
                'temp' => null,
                'o2' => null,
                'rr' => null
            ];
        }

        $status = [
            'overall' => 'stable',
            'bp' => null,
            'hr' => null,
            'temp' => null,
            'o2' => null,
            'rr' => null
        ];

        // Blood Pressure (Systolic)
        if (!empty($current['blood_pressure_systolic']) && !empty($previous['blood_pressure_systolic'])) {
            $diff = $current['blood_pressure_systolic'] - $previous['blood_pressure_systolic'];
            if ($diff > 10) {
                $status['bp'] = 'worsening';
            } elseif ($diff < -10 && $current['blood_pressure_systolic'] > 90) {
                $status['bp'] = 'improving';
            } else {
                $status['bp'] = 'stable';
            }
        }

        // Heart Rate
        if (!empty($current['heart_rate']) && !empty($previous['heart_rate'])) {
            $currentHR = $current['heart_rate'];
            $previousHR = $previous['heart_rate'];
            
            if ($currentHR > 100 || ($previousHR <= 100 && $currentHR > $previousHR + 15)) {
                $status['hr'] = 'worsening';
            } elseif ($currentHR < 60 || ($previousHR >= 60 && $currentHR < $previousHR - 15)) {
                $status['hr'] = 'worsening';
            } elseif ($previousHR > 100 && $currentHR <= 100) {
                $status['hr'] = 'improving';
            } elseif ($previousHR < 60 && $currentHR >= 60) {
                $status['hr'] = 'improving';
            } else {
                $status['hr'] = 'stable';
            }
        }

        // Temperature
        if (!empty($current['temperature']) && !empty($previous['temperature'])) {
            $currentTemp = $current['temperature'];
            $previousTemp = $previous['temperature'];
            
            if ($currentTemp > 37.2 || ($previousTemp <= 37.2 && $currentTemp > $previousTemp + 0.5)) {
                $status['temp'] = 'worsening';
            } elseif ($previousTemp > 37.2 && $currentTemp <= 37.2) {
                $status['temp'] = 'improving';
            } else {
                $status['temp'] = 'stable';
            }
        }

        // Oxygen Saturation
        if (!empty($current['oxygen_saturation']) && !empty($previous['oxygen_saturation'])) {
            $currentO2 = $current['oxygen_saturation'];
            $previousO2 = $previous['oxygen_saturation'];
            
            if ($currentO2 < 95 || ($previousO2 >= 95 && $currentO2 < $previousO2 - 3)) {
                $status['o2'] = 'worsening';
            } elseif ($previousO2 < 95 && $currentO2 >= 95) {
                $status['o2'] = 'improving';
            } else {
                $status['o2'] = 'stable';
            }
        }

        // Respiratory Rate
        if (!empty($current['respiratory_rate']) && !empty($previous['respiratory_rate'])) {
            $currentRR = $current['respiratory_rate'];
            $previousRR = $previous['respiratory_rate'];
            
            if ($currentRR > 20 || ($previousRR <= 20 && $currentRR > $previousRR + 5)) {
                $status['rr'] = 'worsening';
            } elseif ($currentRR < 12 || ($previousRR >= 12 && $currentRR < $previousRR - 5)) {
                $status['rr'] = 'worsening';
            } elseif ($previousRR > 20 && $currentRR <= 20) {
                $status['rr'] = 'improving';
            } elseif ($previousRR < 12 && $currentRR >= 12) {
                $status['rr'] = 'improving';
            } else {
                $status['rr'] = 'stable';
            }
        }

        // Calculate overall status (if any vital is worsening, overall is worsening)
        $worseningCount = 0;
        $improvingCount = 0;
        foreach (['bp', 'hr', 'temp', 'o2', 'rr'] as $vital) {
            if ($status[$vital] === 'worsening') $worseningCount++;
            if ($status[$vital] === 'improving') $improvingCount++;
        }

        if ($worseningCount > 0) {
            $status['overall'] = 'worsening';
        } elseif ($improvingCount > 0) {
            $status['overall'] = 'improving';
        } else {
            $status['overall'] = 'stable';
        }

        return $status;
    }
    
    /**
     * Create consultation charge when consultation is completed
     * @param int $consultationId
     * @param int $patientId (admin_patients.id)
     * @return bool
     */
    private function createConsultationCharge($consultationId, $patientId)
    {
        $db = \Config\Database::connect();
        
        // Check if charge already exists for this consultation to avoid duplicates
        $existingCharge = $db->table('charges')
            ->where('consultation_id', $consultationId)
            ->where('patient_id', $patientId)
            ->get()
            ->getRowArray();
        
        if ($existingCharge) {
            log_message('info', "Consultation charge already exists for Consultation #{$consultationId} - Charge ID: {$existingCharge['id']}");
            return true; // Charge already exists
        }
        
        if (!$db->tableExists('charges') || !$db->tableExists('billing_items')) {
            log_message('error', "Cannot create consultation charge - charges or billing_items table does not exist");
            return false;
        }
        
        try {
            $chargeModel = new \App\Models\ChargeModel();
            $billingItemModel = new \App\Models\BillingItemModel();
            
            // Consultation fee (default: 500 PHP, can be configured)
            $consultationFee = 500.00; // Default consultation fee
            
            // Generate charge number
            $chargeNumber = $chargeModel->generateChargeNumber();
            
            // Create charge record
            $chargeData = [
                'consultation_id' => $consultationId,
                'patient_id' => $patientId, // Use admin_patients.id
                'charge_number' => $chargeNumber,
                'total_amount' => $consultationFee,
                'status' => 'pending',
                'notes' => 'Consultation Fee - Consultation #' . $consultationId,
            ];
            
            if ($chargeModel->insert($chargeData)) {
                $chargeId = $chargeModel->getInsertID();
                
                // Create billing item for consultation fee
                $billingItemData = [
                    'charge_id' => $chargeId,
                    'item_type' => 'consultation',
                    'item_name' => 'Consultation Fee',
                    'description' => 'Doctor Consultation - Consultation #' . $consultationId,
                    'quantity' => 1.00,
                    'unit_price' => $consultationFee,
                    'total_price' => $consultationFee,
                    'related_id' => $consultationId,
                    'related_type' => 'consultation',
                ];
                
                if ($billingItemModel->insert($billingItemData)) {
                    log_message('info', "✅✅✅ Consultation charge created successfully - Charge ID: {$chargeId}, Consultation ID: {$consultationId}, Patient ID: {$patientId}, Amount: {$consultationFee}");
                    return true;
                } else {
                    log_message('error', "❌ Failed to create billing item for consultation charge - Charge ID: {$chargeId}");
                    return false;
                }
            } else {
                log_message('error', "❌ Failed to create consultation charge - Consultation ID: {$consultationId}, Patient ID: {$patientId}");
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "❌ Exception creating consultation charge: " . $e->getMessage());
            log_message('error', "Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
}
