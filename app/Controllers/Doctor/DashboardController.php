<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\DoctorOrderModel;
use App\Models\DoctorNotificationModel;

class DashboardController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $patientModel = new AdminPatientModel();
        $db = \Config\Database::connect();

        // Get dashboard statistics
        $today = date('Y-m-d');
        $appointmentsCount = 0;
        $patientsSeenToday = 0;
        
        if ($db->tableExists('consultations')) {
            $appointmentsCount = $db->table('consultations')
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->where('status', 'approved')
                ->countAllResults();

            $patientsSeenToday = $db->table('consultations')
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->where('type', 'completed')
                ->countAllResults();
        }

        // Get assigned patients from admin_patients table
        $assignedPatientsCount = $patientModel
            ->where('doctor_id', $doctorId)
            ->countAllResults();

        // Get recent assigned patients (including newly assigned from waiting list)
        // Increased limit to show more patients
        $assignedPatients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->limit(50) // Increased to show more patients
            ->findAll();

        // Get patients from patients table (HMSPatientModel) assigned to this doctor
        // This includes both In-Patients and Out-Patients registered by receptionist
        $db = \Config\Database::connect();
        $hmsPatients = [];
        if ($db->tableExists('patients')) {
            // Query directly using doctor_id (which is users.id from session)
            // Both In-Patients and Out-Patients are saved to patients table with doctor_id = users.id
            // Make sure to select all necessary fields including In-Patients
            $hmsPatientsRaw = $db->table('patients')
                ->select('patients.*')
                ->where('patients.doctor_id', $doctorId)
                ->where('patients.doctor_id IS NOT NULL')
                ->where('patients.doctor_id !=', 0)
                ->where('patients.doctor_id !=', '')
                ->orderBy('patients.updated_at', 'DESC')
                ->orderBy('patients.created_at', 'DESC')
                ->limit(100) // Increased limit to show more patients including In-Patients
                ->get()
                ->getResultArray();
            
            // Debug logging
            log_message('info', "Doctor Dashboard - Querying patients for doctor_id: {$doctorId}, Found: " . count($hmsPatientsRaw) . " patients");
            if (!empty($hmsPatientsRaw)) {
                $inPatientCount = 0;
                $outPatientCount = 0;
                foreach ($hmsPatientsRaw as $p) {
                    if (($p['type'] ?? '') === 'In-Patient') $inPatientCount++;
                    else $outPatientCount++;
                }
                log_message('info', "Doctor Dashboard - In-Patients: {$inPatientCount}, Out-Patients: {$outPatientCount}");
                log_message('debug', "Sample patient: " . json_encode($hmsPatientsRaw[0]));
            }
            
            // Format hmsPatients to match admin_patients structure for consistent display
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
                
                $hmsPatients[] = [
                    'id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                    'patient_id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                    'firstname' => $nameParts[0] ?? '',
                    'lastname' => $nameParts[1] ?? '',
                    'first_name' => $patient['first_name'] ?? $nameParts[0] ?? '',
                    'last_name' => $patient['last_name'] ?? $nameParts[1] ?? '',
                    'full_name' => $patient['full_name'] ?? implode(' ', $nameParts),
                    'birthdate' => $patient['date_of_birth'] ?? $patient['birthdate'] ?? null,
                    'gender' => strtolower($patient['gender'] ?? ''),
                    'contact' => $patient['contact'] ?? null,
                    'address' => $patient['address'] ?? null,
                    'type' => $patient['type'] ?? 'Out-Patient',
                    'visit_type' => $patient['visit_type'] ?? null,
                    'room_number' => $patient['room_number'] ?? null,
                    'room_id' => $patient['room_id'] ?? null,
                    'admission_date' => $patient['admission_date'] ?? null,
                    'source' => 'receptionist',
                ];
            }
            
            // Log for debugging (remove in production)
            if (empty($hmsPatients)) {
                log_message('debug', "No patients found for doctor_id: {$doctorId} in patients table");
                // Check if there are any patients with doctor_id at all
                $testQuery = $db->table('patients')
                    ->select('patient_id, full_name, doctor_id, type, visit_type')
                    ->where('doctor_id IS NOT NULL')
                    ->where('doctor_id !=', 0)
                    ->limit(5)
                    ->get()
                    ->getResultArray();
                log_message('debug', "Sample patients with doctor_id: " . json_encode($testQuery));
            } else {
                log_message('debug', "Found " . count($hmsPatients) . " patients for doctor_id: {$doctorId} in patients table");
            }
        }

        // Get awaiting consultation patients (assigned but consultation not completed)
        // Consultations table references patients.patient_id, so we need to join with patients table
        $awaitingConsultation = [];
        
        if ($db->tableExists('consultations') && $db->tableExists('patients')) {
            // Query consultations and join with patients table (for out-patients)
            // Only show consultations where date and time have arrived
            $currentTime = date('H:i:s');
            $consultations = $db->table('consultations')
                ->select('consultations.*, patients.full_name, patients.date_of_birth as birthdate, patients.gender, patients.patient_id, patients.type as patient_type')
                ->join('patients', 'patients.patient_id = consultations.patient_id', 'left')
                ->where('consultations.doctor_id', $doctorId)
                ->where('consultations.type', 'upcoming')
                ->whereIn('consultations.status', ['approved', 'pending']) // Include both approved and pending
                ->groupStart()
                    ->where('consultations.consultation_date >', $today) // Future dates
                    ->orGroupStart()
                        ->where('consultations.consultation_date', $today) // Today's date
                        ->where('consultations.consultation_time <=', $currentTime) // Time has arrived
                    ->groupEnd()
                ->groupEnd()
                ->orderBy('consultations.consultation_date', 'ASC')
                ->orderBy('consultations.consultation_time', 'ASC')
                ->get()
                ->getResultArray();
            
            foreach ($consultations as $consult) {
                // Format patient name
                $nameParts = explode(' ', $consult['full_name'] ?? '');
                $consult['firstname'] = $nameParts[0] ?? '';
                $consult['lastname'] = implode(' ', array_slice($nameParts, 1)) ?? '';
                $awaitingConsultation[] = $consult;
            }
        }
        
        // Also check admin_patients if they have consultations (though consultations table references patients.patient_id)
        // This is for backward compatibility if there are any old records
        if ($db->tableExists('admin_patients')) {
            // Only show consultations where date and time have arrived
            $currentTime = date('H:i:s');
            $adminConsultations = $db->table('consultations')
                ->select('consultations.*, admin_patients.firstname, admin_patients.lastname, admin_patients.birthdate, admin_patients.gender, admin_patients.id as patient_id')
                ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                ->where('consultations.doctor_id', $doctorId)
                ->where('consultations.type', 'upcoming')
                ->whereIn('consultations.status', ['approved', 'pending']) // Include both approved and pending
                ->groupStart()
                    ->where('consultations.consultation_date >', $today) // Future dates
                    ->orGroupStart()
                        ->where('consultations.consultation_date', $today) // Today's date
                        ->where('consultations.consultation_time <=', $currentTime) // Time has arrived
                    ->groupEnd()
                ->groupEnd()
                ->where('admin_patients.id IS NOT NULL') // Only get records that actually have admin_patient
                ->orderBy('consultations.consultation_date', 'ASC')
                ->orderBy('consultations.consultation_time', 'ASC')
                ->get()
                ->getResultArray();
            
            // Add to awaiting consultation, avoiding duplicates
            foreach ($adminConsultations as $consult) {
                // Check if this consultation is already in the list
                $exists = false;
                foreach ($awaitingConsultation as $existing) {
                    if ($existing['id'] == $consult['id']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $awaitingConsultation[] = $consult;
                }
            }
        }
        
        // Also add patients from triage that were sent to this doctor but may not have consultation record
        // This handles cases where FK constraint prevents consultation creation
        if ($db->tableExists('triage')) {
            $triageForDoctor = $triageModel
                ->where('doctor_id', $doctorId)
                ->where('status', 'completed')
                ->where('sent_to_doctor', 1)
                ->where('triage_level !=', 'Critical') // Critical cases are in emergencyCases
                ->orderBy('created_at', 'DESC')
                ->findAll();
            
            foreach ($triageForDoctor as $triage) {
                // Check if this patient already has a consultation in the list
                $hasConsultation = false;
                foreach ($awaitingConsultation as $consult) {
                    if (($consult['patient_id'] ?? null) == $triage['patient_id']) {
                        $hasConsultation = true;
                        break;
                    }
                }
                
                // If no consultation exists, add triage info to awaiting consultation
                if (!$hasConsultation) {
                    // Get patient info
                    $patientName = 'Unknown Patient';
                    $patientId = $triage['patient_id'];
                    
                    if ($db->tableExists('patients')) {
                        $patient = $db->table('patients')
                            ->where('patient_id', $triage['patient_id'])
                            ->get()
                            ->getRowArray();
                        if ($patient) {
                            $patientName = $patient['full_name'] ?? ($patient['first_name'] . ' ' . $patient['last_name']);
                        }
                    }
                    
                    if ($db->tableExists('admin_patients') && empty($patient)) {
                        $patient = $db->table('admin_patients')
                            ->where('id', $triage['patient_id'])
                            ->get()
                            ->getRowArray();
                        if ($patient) {
                            $patientName = ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '');
                        }
                    }
                    
                    // Add to awaiting consultation
                    $nameParts = explode(' ', $patientName, 2);
                    $awaitingConsultation[] = [
                        'id' => 'triage_' . $triage['id'],
                        'patient_id' => $patientId,
                        'full_name' => $patientName,
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'consultation_date' => date('Y-m-d'),
                        'consultation_time' => date('H:i:s'),
                        'type' => 'upcoming',
                        'status' => 'pending',
                        'notes' => "From Triage - Level: {$triage['triage_level']}. " . ($triage['chief_complaint'] ?? ''),
                        'triage_id' => $triage['id'],
                        'triage_level' => $triage['triage_level'],
                        'from_triage' => true,
                    ];
                }
            }
        }
        
        // Sort by consultation date and time
        usort($awaitingConsultation, function($a, $b) {
            $dateA = strtotime($a['consultation_date'] . ' ' . ($a['consultation_time'] ?? '00:00:00'));
            $dateB = strtotime($b['consultation_date'] . ' ' . ($b['consultation_time'] ?? '00:00:00'));
            return $dateA <=> $dateB;
        });

        // Get pending lab requests from nurses AND doctors for assigned patients
        $labRequestModel = new \App\Models\LabRequestModel();
        $assignedPatientIds = array_column($assignedPatients, 'id');
        $pendingLabRequestsCount = 0;
        $pendingLabRequests = [];
        
        // Also get patient IDs from patients table (receptionist-registered)
        $db = \Config\Database::connect();
        $hmsPatientIds = [];
        if ($db->tableExists('patients')) {
            $hmsPatientsRaw = $db->table('patients')
                ->select('patients.patient_id')
                ->where('patients.doctor_id', $doctorId)
                ->where('patients.doctor_id IS NOT NULL')
                ->where('patients.doctor_id !=', 0)
                ->get()
                ->getResultArray();
            $hmsPatientIds = array_column($hmsPatientsRaw, 'patient_id');
        }
        
        // Find corresponding admin_patients IDs for hms patients
        $allPatientIds = $assignedPatientIds;
        if (!empty($hmsPatientIds)) {
            foreach ($hmsPatientIds as $hmsPatientId) {
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $hmsPatientId)
                    ->get()
                    ->getRowArray();
                
                if ($hmsPatient) {
                    $nameParts = [];
                    if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                    if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                    if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                        $parts = explode(' ', $hmsPatient['full_name'], 2);
                        $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                    }
                    
                    if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                        $adminPatient = $db->table('admin_patients')
                            ->where('firstname', $nameParts[0])
                            ->where('lastname', $nameParts[1])
                            ->where('doctor_id', $doctorId)
                            ->get()
                            ->getRowArray();
                        
                        if ($adminPatient && !in_array($adminPatient['id'], $allPatientIds)) {
                            $allPatientIds[] = $adminPatient['id'];
                        }
                    }
                }
            }
        }
        
        if (!empty($allPatientIds)) {
            // Count all pending lab requests (from both nurses and doctors)
            $pendingLabRequestsCount = $labRequestModel
                ->whereIn('patient_id', $allPatientIds)
                ->where('status', 'pending')
                ->whereIn('requested_by', ['nurse', 'doctor'])
                ->countAllResults();
            
            // Get pending lab requests (from both nurses and doctors)
            $pendingLabRequests = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as nurse_name, doctor_users.username as doctor_name')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->join('users', 'users.id = lab_requests.nurse_id', 'left')
                ->join('users as doctor_users', 'doctor_users.id = lab_requests.doctor_id', 'left')
                ->whereIn('lab_requests.patient_id', $allPatientIds)
                ->where('lab_requests.status', 'pending')
                ->whereIn('lab_requests.requested_by', ['nurse', 'doctor'])
                ->orderBy('lab_requests.created_at', 'DESC')
                ->limit(10)
                ->findAll();
        }

        $pendingLabResults = 0; // This would require additional models/tables

        // Get doctor orders statistics
        $orderModel = new DoctorOrderModel();
        $totalOrders = $orderModel->where('doctor_id', $doctorId)->countAllResults();
        $pendingOrders = $orderModel->where('doctor_id', $doctorId)->where('status', 'pending')->countAllResults();
        $inProgressOrders = $orderModel->where('doctor_id', $doctorId)->where('status', 'in_progress')->countAllResults();
        $completedOrders = $orderModel->where('doctor_id', $doctorId)->where('status', 'completed')->countAllResults();
        
        // Get recent orders
        $recentOrders = $orderModel
            ->select('doctor_orders.*, admin_patients.firstname, admin_patients.lastname')
            ->join('admin_patients', 'admin_patients.id = doctor_orders.patient_id', 'left')
            ->where('doctor_orders.doctor_id', $doctorId)
            ->orderBy('doctor_orders.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get unread notifications
        $notificationModel = new DoctorNotificationModel();
        $unreadNotifications = $notificationModel->getUnreadNotifications($doctorId);
        $totalUnreadNotifications = count($unreadNotifications);

        // Get emergency cases assigned to this doctor (Critical triage)
        // Also get non-critical triage cases that were sent to this doctor
        $triageModel = new \App\Models\TriageModel();
        $emergencyCases = [];
        if ($db->tableExists('triage')) {
            // Get all triage cases assigned to this doctor (Critical, Moderate, Minor)
            $triageRecords = $triageModel
                ->where('doctor_id', $doctorId)
                ->where('status', 'completed')
                ->where('sent_to_doctor', 1)
                ->orderBy('created_at', 'DESC')
                ->findAll();
            
            foreach ($triageRecords as $triage) {
                $patientInfo = null;
                $patientName = 'Unknown Patient';
                
                // Try to get patient from patients table first
                if ($db->tableExists('patients')) {
                    $patient = $db->table('patients')
                        ->where('patient_id', $triage['patient_id'])
                        ->get()
                        ->getRowArray();
                    
                    if ($patient) {
                        $patientName = $patient['full_name'] ?? ($patient['first_name'] . ' ' . $patient['last_name']);
                        $patientInfo = [
                            'patient_id' => $patient['patient_id'],
                            'patient_name' => $patientName,
                            'visit_type' => $patient['visit_type'] ?? 'Emergency',
                            'source' => 'patients'
                        ];
                    }
                }
                
                // If not found in patients table, try admin_patients
                if (!$patientInfo && $db->tableExists('admin_patients')) {
                    $patient = $db->table('admin_patients')
                        ->where('id', $triage['patient_id'])
                        ->get()
                        ->getRowArray();
                    
                    if ($patient) {
                        $patientName = ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '');
                        $patientInfo = [
                            'patient_id' => $patient['id'],
                            'patient_name' => $patientName,
                            'visit_type' => $patient['visit_type'] ?? 'Emergency',
                            'source' => 'admin_patients'
                        ];
                    }
                }
                
                if ($patientInfo) {
                    // Include all triage data (disposition, opd_queue_number, etc.)
                    $emergencyCases[] = array_merge($triage, $patientInfo);
                }
            }
        }
        
        // Also get triage patients that were sent to this doctor but not critical
        // These should appear in awaiting consultation but with triage info
        if ($db->tableExists('triage')) {
            $nonCriticalTriage = $triageModel
                ->where('doctor_id', $doctorId)
                ->where('status', 'completed')
                ->where('sent_to_doctor', 1)
                ->whereIn('triage_level', ['Moderate', 'Minor'])
                ->orderBy('created_at', 'DESC')
                ->findAll();
            
            foreach ($nonCriticalTriage as $triage) {
                // Check if already in awaitingConsultation
                $alreadyInList = false;
                foreach ($awaitingConsultation as $consult) {
                    if (($consult['patient_id'] ?? null) == $triage['patient_id'] && 
                        !empty($consult['from_triage'])) {
                        // Update with triage info
                        $consult['disposition'] = $triage['disposition'] ?? null;
                        $consult['triage_level'] = $triage['triage_level'] ?? null;
                        $consult['opd_queue_number'] = $triage['opd_queue_number'] ?? null;
                        $alreadyInList = true;
                        break;
                    }
                }
                
                if (!$alreadyInList) {
                    // Get patient info
                    $patientName = 'Unknown Patient';
                    $patientId = $triage['patient_id'];
                    
                    if ($db->tableExists('patients')) {
                        $patient = $db->table('patients')
                            ->where('patient_id', $triage['patient_id'])
                            ->get()
                            ->getRowArray();
                        if ($patient) {
                            $patientName = $patient['full_name'] ?? ($patient['first_name'] . ' ' . $patient['last_name']);
                        }
                    }
                    
                    if ($db->tableExists('admin_patients') && empty($patient)) {
                        $patient = $db->table('admin_patients')
                            ->where('id', $triage['patient_id'])
                            ->get()
                            ->getRowArray();
                        if ($patient) {
                            $patientName = ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '');
                        }
                    }
                    
                    // Add to awaiting consultation with triage info
                    $nameParts = explode(' ', $patientName, 2);
                    $awaitingConsultation[] = [
                        'id' => 'triage_' . $triage['id'],
                        'patient_id' => $patientId,
                        'full_name' => $patientName,
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'consultation_date' => date('Y-m-d'),
                        'consultation_time' => date('H:i:s'),
                        'type' => 'upcoming',
                        'status' => 'pending',
                        'notes' => "From Triage - Level: {$triage['triage_level']}. " . ($triage['chief_complaint'] ?? ''),
                        'triage_id' => $triage['id'],
                        'triage_level' => $triage['triage_level'],
                        'disposition' => $triage['disposition'] ?? null,
                        'opd_queue_number' => $triage['opd_queue_number'] ?? null,
                        'from_triage' => true,
                    ];
                }
            }
        }

        // Merge assignedPatients and hmsPatients for unified display
        $merged = array_merge($assignedPatients, $hmsPatients);
        
        // Deduplicate: If same patient exists in both tables (same name + doctor_id), keep only admin_patients version
        $allAssignedPatients = [];
        $seenKeys = [];
        
        foreach ($merged as $patient) {
            // Create a unique key based on name (case-insensitive) and doctor_id
            $nameKey = strtolower(trim(($patient['firstname'] ?? $patient['first_name'] ?? '') . ' ' . ($patient['lastname'] ?? $patient['last_name'] ?? '')));
            $key = md5($nameKey . '|' . $doctorId);
            
            // If we've seen this patient before, prefer admin_patients version (source = 'admin' or no source)
            if (isset($seenKeys[$key])) {
                // If current patient is from admin_patients and previous was from receptionist, replace it
                $currentSource = $patient['source'] ?? 'admin';
                $prevSource = $seenKeys[$key]['source'] ?? 'admin';
                if ($currentSource === 'admin' && $prevSource === 'receptionist') {
                    $allAssignedPatients[$seenKeys[$key]['index']] = $patient;
                    $seenKeys[$key] = ['index' => $seenKeys[$key]['index'], 'source' => $currentSource];
                }
                // Otherwise, skip this duplicate
                continue;
            }
            
            // Add to deduplicated list
            $index = count($allAssignedPatients);
            $allAssignedPatients[] = $patient;
            $seenKeys[$key] = ['index' => $index, 'source' => $patient['source'] ?? 'admin'];
        }
        
        // Re-index array
        $allAssignedPatients = array_values($allAssignedPatients);
        
        // Sort by updated_at (most recently updated first) to show newly assigned patients
        usort($allAssignedPatients, function($a, $b) {
            $updatedA = strtotime($a['updated_at'] ?? $a['created_at'] ?? '1970-01-01');
            $updatedB = strtotime($b['updated_at'] ?? $b['created_at'] ?? '1970-01-01');
            return $updatedB <=> $updatedA; // Descending order
        });
        
        // Get admitted patients (including In-Patients from receptionist)
        $admittedPatients = [];
        if ($db->tableExists('admissions')) {
            // Get from admissions table
            $admittedFromAdmin = $db->table('admissions a')
                ->select('a.*, ap.firstname, ap.lastname, ap.contact, ap.birthdate, ap.gender,
                         r.room_number, r.ward, r.room_type,
                         (SELECT COUNT(*) FROM doctor_orders WHERE admission_id = a.id AND status != "completed" AND status != "cancelled") as pending_orders_count')
                ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
                ->join('rooms r', 'r.id = a.room_id', 'left')
                ->where('a.attending_physician_id', $doctorId)
                ->where('a.status', 'admitted')
                ->where('a.discharge_status', 'admitted')
                ->where('a.deleted_at', null)
                ->orderBy('a.admission_date', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();
            
            foreach ($admittedFromAdmin as $adm) {
                // Get visit_type from admin_patients table
                $visitType = null;
                if (!empty($adm['patient_id'])) {
                    $adminPatient = $db->table('admin_patients')
                        ->select('visit_type, type')
                        ->where('id', $adm['patient_id'])
                        ->get()
                        ->getRowArray();
                    $visitType = $adminPatient['visit_type'] ?? null;
                    $patientType = $adminPatient['type'] ?? null;
                }
                
                $admittedPatients[] = [
                    'id' => $adm['id'],
                    'admission_id' => $adm['id'],
                    'patient_id' => $adm['patient_id'],
                    'firstname' => $adm['firstname'] ?? '',
                    'lastname' => $adm['lastname'] ?? '',
                    'room_number' => $adm['room_number'] ?? null,
                    'ward' => $adm['ward'] ?? null,
                    'admission_date' => $adm['admission_date'] ?? null,
                    'admission_reason' => $adm['admission_reason'] ?? null,
                    'pending_orders_count' => $adm['pending_orders_count'] ?? 0,
                    'source' => 'admin',
                    'type' => $patientType ?? 'In-Patient',
                    'visit_type' => $visitType ?? 'Admission',
                ];
            }
        }
        
        // Get In-Patients from patients table (direct admissions)
        if ($db->tableExists('patients')) {
            $inPatientsRaw = $db->table('patients p')
                ->select('p.*, r.room_number, r.ward, r.room_type')
                ->join('rooms r', 'r.id = p.room_id', 'left')
                ->where('p.doctor_id', $doctorId)
                ->where('p.type', 'In-Patient')
                ->where('p.doctor_id IS NOT NULL')
                ->where('p.doctor_id !=', 0)
                ->orderBy('p.admission_date', 'DESC')
                ->orderBy('p.created_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();
            
            foreach ($inPatientsRaw as $patient) {
                $nameParts = [];
                if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                if (empty($nameParts) && !empty($patient['full_name'])) {
                    $parts = explode(' ', $patient['full_name'], 2);
                    $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                }
                
                $admittedPatients[] = [
                    'id' => 'patient_' . $patient['patient_id'],
                    'admission_id' => null,
                    'patient_id' => $patient['patient_id'],
                    'firstname' => $nameParts[0] ?? '',
                    'lastname' => $nameParts[1] ?? '',
                    'room_number' => $patient['room_number'] ?? null,
                    'ward' => $patient['ward'] ?? $patient['room_type'] ?? null,
                    'admission_date' => $patient['admission_date'] ?? $patient['created_at'] ?? date('Y-m-d'),
                    'admission_reason' => $patient['purpose'] ?? 'In-Patient Admission',
                    'pending_orders_count' => 0,
                    'source' => 'receptionist',
                    'type' => $patient['type'] ?? 'In-Patient',
                    'visit_type' => $patient['visit_type'] ?? 'Admission',
                ];
            }
        }
        
        // Sort by admission date
        usort($admittedPatients, function($a, $b) {
            $dateA = strtotime($a['admission_date'] ?? '1970-01-01');
            $dateB = strtotime($b['admission_date'] ?? '1970-01-01');
            return $dateB <=> $dateA;
        });
        
        // Check if doctor is a pediatrician
        $isPediatricsDoctor = false;
        $doctorSpecialization = null;
        if ($db->tableExists('doctors')) {
            $doctor = $db->table('doctors')
                ->where('user_id', $doctorId)
                ->get()
                ->getRowArray();
            if ($doctor && !empty($doctor['specialization'])) {
                $doctorSpecialization = $doctor['specialization'];
                $isPediatricsDoctor = (strtolower(trim($doctor['specialization'])) === 'pediatrics');
            }
        }
        
        // Get patients with recent vital signs from assigned nurses (for "Check Nurse Assessment" button)
        // Note: patient_vitals.patient_id references admin_patients.id
        $patientsWithNurseAssessment = [];
        $patientsWithRecentVitals = []; // Map of admin_patients.id => has_recent_vitals
        
        if ($db->tableExists('patient_vitals') && $db->tableExists('admin_patients')) {
            $today = date('Y-m-d');
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            
            // Get all recent vital signs from assigned nurses for patients assigned to this doctor
            // patient_vitals.patient_id = admin_patients.id
            $recentVitals = $db->table('patient_vitals pv')
                ->select('pv.*, ap.id as admin_patient_id, ap.firstname, ap.lastname, ap.assigned_nurse_id, ap.doctor_id,
                         users.first_name as nurse_first_name, users.last_name as nurse_last_name, users.username as nurse_username')
                ->join('admin_patients ap', 'ap.id = pv.patient_id', 'inner')
                ->join('users', 'users.id = pv.nurse_id', 'left')
                ->where('ap.doctor_id', $doctorId)
                ->where('ap.assigned_nurse_id IS NOT NULL', null, false)
                ->where('ap.assigned_nurse_id = pv.nurse_id', null, false) // Only vitals from assigned nurse
                ->where('DATE(pv.created_at) >=', $sevenDaysAgo) // Last 7 days
                ->where('ap.deleted_at IS NULL', null, false)
                ->orderBy('pv.created_at', 'DESC')
                ->get()
                ->getResultArray();
            
            // Group by patient and get the most recent vital signs
            $patientVitalsMap = [];
            foreach ($recentVitals as $vital) {
                $adminPatientId = $vital['admin_patient_id'];
                $isToday = date('Y-m-d', strtotime($vital['created_at'])) === $today;
                
                if (!isset($patientVitalsMap[$adminPatientId]) || 
                    strtotime($vital['created_at']) > strtotime($patientVitalsMap[$adminPatientId]['created_at'])) {
                    $patientVitalsMap[$adminPatientId] = $vital;
                    $patientVitalsMap[$adminPatientId]['is_today'] = $isToday;
                    $patientsWithRecentVitals[$adminPatientId] = true;
                }
            }
            
            // Also map vitals to patients table IDs (for patients from patients table)
            // This helps match vitals when displaying patients from patients table
            if ($db->tableExists('patients')) {
                foreach ($patientVitalsMap as $adminPatientId => $vital) {
                    // Find corresponding patients table record
                    $adminPatient = $db->table('admin_patients')
                        ->where('id', $adminPatientId)
                        ->get()
                        ->getRowArray();
                    
                    if ($adminPatient) {
                        $nameParts = [
                            $adminPatient['firstname'] ?? '',
                            $adminPatient['lastname'] ?? ''
                        ];
                        
                        if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                            $hmsPatient = $db->table('patients')
                                ->where('first_name', $nameParts[0])
                                ->where('last_name', $nameParts[1])
                                ->where('doctor_id', $doctorId)
                                ->get()
                                ->getRowArray();
                            
                            if ($hmsPatient) {
                                // Map hms patient_id to the vital
                                $patientVitalsMap[$adminPatientId]['hms_patient_id'] = $hmsPatient['patient_id'];
                            }
                        }
                    }
                }
            }
            
            $patientsWithNurseAssessment = array_values($patientVitalsMap);
        }
        
        // Debug: Log patient counts
        log_message('debug', "Doctor Dashboard - doctor_id: {$doctorId}, assignedPatientsCount: {$assignedPatientsCount}, hmsPatientsCount: " . count($hmsPatients) . ", total: " . count($allAssignedPatients) . ", admittedPatients: " . count($admittedPatients));
        if (!empty($hmsPatients)) {
            log_message('debug', "Sample hmsPatient: " . json_encode($hmsPatients[0] ?? []));
        }
        
        $data = [
            'title' => 'Doctor Dashboard',
            'name' => session()->get('name'),
            'appointmentsCount' => $appointmentsCount,
            'patientsSeenToday' => $patientsSeenToday,
            'pendingLabResults' => $pendingLabResults,
            'pendingLabRequestsCount' => $pendingLabRequestsCount,
            'pendingLabRequests' => $pendingLabRequests,
            'assignedPatientsCount' => count($allAssignedPatients), // Use merged count
            'assignedPatients' => $assignedPatients,
            'hmsPatients' => $hmsPatients,
            'allAssignedPatients' => $allAssignedPatients, // Pass merged list to view
            'admittedPatients' => $admittedPatients, // Add admitted patients
            'awaitingConsultation' => $awaitingConsultation,
            'emergencyCases' => $emergencyCases,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'inProgressOrders' => $inProgressOrders,
            'completedOrders' => $completedOrders,
            'recentOrders' => $recentOrders,
            'unreadNotifications' => $unreadNotifications,
            'totalUnreadNotifications' => $totalUnreadNotifications,
            'isPediatricsDoctor' => $isPediatricsDoctor,
            'doctorSpecialization' => $doctorSpecialization,
            'patientsWithNurseAssessment' => $patientsWithNurseAssessment, // Patients with recent vital signs from assigned nurses
            'patientsWithRecentVitals' => $patientsWithRecentVitals ?? [], // Map of patient_id => has_recent_vitals
        ];

        return view('doctor/dashboard/index', $data);
    }
}
