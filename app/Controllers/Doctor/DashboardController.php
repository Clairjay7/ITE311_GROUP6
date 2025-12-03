<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\ConsultationModel;
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
        $consultationModel = new ConsultationModel();

        // Get dashboard statistics
        $today = date('Y-m-d');
        $appointmentsCount = $consultationModel
            ->where('doctor_id', $doctorId)
            ->where('consultation_date', $today)
            ->where('status', 'approved')
            ->countAllResults();

        $patientsSeenToday = $consultationModel
            ->where('doctor_id', $doctorId)
            ->where('consultation_date', $today)
            ->where('type', 'completed')
            ->countAllResults();

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
            // Out-patients are saved to patients table with doctor_id = users.id
            // Make sure to select all necessary fields
            $hmsPatientsRaw = $db->table('patients')
                ->select('patients.*')
                ->where('patients.doctor_id', $doctorId)
                ->where('patients.doctor_id IS NOT NULL')
                ->where('patients.doctor_id !=', 0)
                ->orderBy('patients.updated_at', 'DESC')
                ->orderBy('patients.created_at', 'DESC')
                ->limit(50) // Increased limit to show more patients
                ->get()
                ->getResultArray();
            
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
            $consultations = $db->table('consultations')
                ->select('consultations.*, patients.full_name, patients.date_of_birth as birthdate, patients.gender, patients.patient_id, patients.type as patient_type')
                ->join('patients', 'patients.patient_id = consultations.patient_id', 'left')
                ->where('consultations.doctor_id', $doctorId)
                ->where('consultations.type', 'upcoming')
                ->whereIn('consultations.status', ['approved', 'pending']) // Include both approved and pending
                ->where('consultations.consultation_date >=', $today)
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
            $adminConsultations = $consultationModel
                ->select('consultations.*, admin_patients.firstname, admin_patients.lastname, admin_patients.birthdate, admin_patients.gender, admin_patients.id as patient_id')
                ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                ->where('consultations.doctor_id', $doctorId)
                ->where('consultations.type', 'upcoming')
                ->whereIn('consultations.status', ['approved', 'pending']) // Include both approved and pending
                ->where('consultations.consultation_date >=', $today)
                ->where('admin_patients.id IS NOT NULL') // Only get records that actually have admin_patient
                ->orderBy('consultations.consultation_date', 'ASC')
                ->orderBy('consultations.consultation_time', 'ASC')
                ->findAll();
            
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

        // Get pending lab requests from nurses for assigned patients
        $labRequestModel = new \App\Models\LabRequestModel();
        $assignedPatientIds = array_column($assignedPatients, 'id');
        $pendingLabRequestsCount = 0;
        $pendingLabRequests = [];
        
        if (!empty($assignedPatientIds)) {
            $pendingLabRequestsCount = $labRequestModel
                ->whereIn('patient_id', $assignedPatientIds)
                ->where('status', 'pending')
                ->where('requested_by', 'nurse')
                ->countAllResults();
            
            $pendingLabRequests = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as nurse_name')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->join('users', 'users.id = lab_requests.nurse_id', 'left')
                ->whereIn('lab_requests.patient_id', $assignedPatientIds)
                ->where('lab_requests.status', 'pending')
                ->where('lab_requests.requested_by', 'nurse')
                ->orderBy('lab_requests.created_at', 'DESC')
                ->limit(5)
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
        
        // Debug: Log patient counts
        log_message('debug', "Doctor Dashboard - doctor_id: {$doctorId}, assignedPatientsCount: {$assignedPatientsCount}, hmsPatientsCount: " . count($hmsPatients) . ", total: " . count($allAssignedPatients));
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
            'awaitingConsultation' => $awaitingConsultation,
            'emergencyCases' => $emergencyCases,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'inProgressOrders' => $inProgressOrders,
            'completedOrders' => $completedOrders,
            'recentOrders' => $recentOrders,
            'unreadNotifications' => $unreadNotifications,
            'totalUnreadNotifications' => $totalUnreadNotifications,
        ];

        return view('doctor/dashboard/index', $data);
    }
}
