<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\ConsultationModel;
use App\Models\LabRequestModel;
use App\Models\DoctorOrderModel;
use App\Models\DoctorNotificationModel;

class DashboardStats extends BaseController
{
    public function stats()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $doctorId = session()->get('user_id');
        $patientModel = new AdminPatientModel();
        $consultationModel = new ConsultationModel();

        try {
            // Get dashboard statistics
            $today = date('Y-m-d');
            
            // Today's appointments
            $appointmentsCount = $consultationModel
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->where('status', 'approved')
                ->countAllResults();

            // Patients seen today
            $patientsSeenToday = $consultationModel
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->where('type', 'completed')
                ->countAllResults();

            // Initialize database connection
            $db = \Config\Database::connect();
            
            // Assigned patients count (from admin_patients table)
            $adminPatientsCount = $patientModel
                ->where('doctor_id', $doctorId)
                ->countAllResults();
            
            // Count patients from patients table (receptionist-registered)
            $hmsPatientsCount = 0;
            if ($db->tableExists('patients')) {
                $hmsPatientsCount = $db->table('patients')
                    ->where('patients.doctor_id', $doctorId)
                    ->where('patients.doctor_id IS NOT NULL')
                    ->where('patients.doctor_id !=', 0)
                    ->countAllResults();
            }
            
            // Total assigned patients count
            $assignedPatientsCount = $adminPatientsCount + $hmsPatientsCount;

            // Get recent assigned patients (latest 10, ordered by update time to show newly assigned)
            $assignedPatientsRaw = $patientModel
                ->where('doctor_id', $doctorId)
                ->orderBy('updated_at', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->findAll();
            
            // Format admin patients to include visit_type if available
            $assignedPatients = [];
            foreach ($assignedPatientsRaw as $patient) {
                $patient['visit_type'] = $patient['visit_type'] ?? null;
                $assignedPatients[] = $patient;
            }

            // Get patients from patients table assigned to this doctor
            // This includes both In-Patients and Out-Patients registered by receptionist
            $hmsPatients = [];
            if ($db->tableExists('patients')) {
                // Query directly using doctor_id (which is users.id from session)
                // Out-patients are saved to patients table with doctor_id = users.id
                $hmsPatientsRaw = $db->table('patients')
                    ->select('patients.*')
                    ->where('patients.doctor_id', $doctorId)
                    ->where('patients.doctor_id IS NOT NULL')
                    ->where('patients.doctor_id !=', 0)
                    ->orderBy('patients.updated_at', 'DESC')
                    ->orderBy('patients.created_at', 'DESC')
                    ->limit(10)
                    ->get()
                    ->getResultArray();
                
                // Format hmsPatients to match admin_patients structure
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
            }

            // Get awaiting consultation patients (assigned but consultation not completed)
            // Consultations table references patients.patient_id, so we need to join with patients table
            $awaitingConsultation = [];
            
            if ($db->tableExists('consultations') && $db->tableExists('patients')) {
                // Query consultations and join with patients table (for out-patients)
                $consultations = $db->table('consultations')
                    ->select('consultations.*, patients.full_name, patients.date_of_birth as birthdate, patients.gender, patients.patient_id, patients.type as patient_type, patients.visit_type')
                    ->join('patients', 'patients.patient_id = consultations.patient_id', 'left')
                    ->where('consultations.doctor_id', $doctorId)
                    ->where('consultations.type', 'upcoming')
                    ->where('consultations.status', 'approved')
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
            
            // Also check admin_patients if they have consultations (backward compatibility)
            if ($db->tableExists('admin_patients')) {
                $adminConsultations = $consultationModel
                    ->select('consultations.*, admin_patients.firstname, admin_patients.lastname, admin_patients.birthdate, admin_patients.gender, admin_patients.id as patient_id, admin_patients.visit_type')
                    ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                    ->where('consultations.doctor_id', $doctorId)
                    ->where('consultations.type', 'upcoming')
                    ->where('consultations.status', 'approved')
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
            
            // Sort by consultation date and time
            usort($awaitingConsultation, function($a, $b) {
                $dateA = strtotime($a['consultation_date'] . ' ' . ($a['consultation_time'] ?? '00:00:00'));
                $dateB = strtotime($b['consultation_date'] . ' ' . ($b['consultation_time'] ?? '00:00:00'));
                return $dateA <=> $dateB;
            });

            // Pending consultations
            $pendingConsultations = $consultationModel
                ->where('doctor_id', $doctorId)
                ->where('status', 'pending')
                ->countAllResults();

            // Upcoming consultations (next 7 days)
            $upcomingConsultations = $consultationModel
                ->where('doctor_id', $doctorId)
                ->where('consultation_date >=', $today)
                ->where('consultation_date <=', date('Y-m-d', strtotime('+7 days')))
                ->where('status', 'approved')
                ->where('type', 'upcoming')
                ->countAllResults();

            // Get assigned patient IDs
            $assignedPatientIds = $patientModel
                ->select('id')
                ->where('doctor_id', $doctorId)
                ->findAll();
            $patientIds = array_column($assignedPatientIds, 'id');

            // Get pending lab requests from nurses for assigned patients
            $labRequestModel = new LabRequestModel();
            $pendingLabRequestsCount = 0;
            $pendingLabRequests = [];
            
            if (!empty($patientIds)) {
                $pendingLabRequestsCount = $labRequestModel
                    ->whereIn('patient_id', $patientIds)
                    ->where('status', 'pending')
                    ->where('requested_by', 'nurse')
                    ->countAllResults();
                
                $pendingLabRequests = $labRequestModel
                    ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, users.username as nurse_name')
                    ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                    ->join('users', 'users.id = lab_requests.nurse_id', 'left')
                    ->whereIn('lab_requests.patient_id', $patientIds)
                    ->where('lab_requests.status', 'pending')
                    ->where('lab_requests.requested_by', 'nurse')
                    ->orderBy('lab_requests.created_at', 'DESC')
                    ->limit(5)
                    ->findAll();
            }

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
            $triageModel = new \App\Models\TriageModel();
            $emergencyCases = [];
            $db = \Config\Database::connect();
            if ($db->tableExists('triage')) {
                // Get critical triage cases assigned to this doctor
                // Note: triage table uses 'assigned_doctor_id' not 'doctor_id'
                $triageRecords = $db->table('triage')
                    ->where('assigned_doctor_id', $doctorId)
                    ->where('triage_level', 'Critical')
                    ->where('status', 'triaged')
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
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
                        $emergencyCases[] = array_merge($triage, $patientInfo);
                    }
                }
            }

            // Merge assigned_patients and hms_patients for unified display
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

            $data = [
                'appointments_count' => $appointmentsCount,
                'patients_seen_today' => $patientsSeenToday,
                'assigned_patients_count' => count($allAssignedPatients), // Use merged count
                'pending_consultations' => $pendingConsultations,
                'upcoming_consultations' => $upcomingConsultations,
                'awaiting_consultation' => $awaitingConsultation,
                'awaiting_consultation_count' => count($awaitingConsultation),
                'pending_lab_requests_count' => $pendingLabRequestsCount,
                'pending_lab_requests' => $pendingLabRequests,
                'assigned_patients' => $assignedPatients,
                'hms_patients' => $hmsPatients,
                'all_assigned_patients' => $allAssignedPatients, // Pass merged list
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'in_progress_orders' => $inProgressOrders,
                'completed_orders' => $completedOrders,
                'recent_orders' => $recentOrders,
                'unread_notifications' => $unreadNotifications,
                'total_unread_notifications' => $totalUnreadNotifications,
                'emergency_cases' => $emergencyCases,
                'emergency_cases_count' => count($emergencyCases),
                'last_updated' => date('Y-m-d H:i:s')
            ];

            return $this->response->setJSON($data);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching Doctor Dashboard Stats: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to fetch stats'])->setStatusCode(500);
        }
    }
}

