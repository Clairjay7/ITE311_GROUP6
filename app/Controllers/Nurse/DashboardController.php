<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\PatientVitalModel;
use App\Models\NurseNoteModel;
use App\Models\DoctorOrderModel;
use App\Models\LabRequestModel;
use App\Models\LabResultModel;
use App\Models\NurseNotificationModel;
use App\Models\AppointmentModel;

class DashboardController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $nurseId = session()->get('user_id');
        $patientModel = new AdminPatientModel();
        $vitalModel = new PatientVitalModel();
        $noteModel = new NurseNoteModel();
        $orderModel = new DoctorOrderModel();
        $labRequestModel = new LabRequestModel();
        $notificationModel = new NurseNotificationModel();
        $appointmentModel = new AppointmentModel();

        $today = date('Y-m-d');

        // Get critical patients (patients with urgent vitals or high priority notes)
        $criticalPatients = $this->getCriticalPatientsCount();

        // Get patients under care (patients with recent vitals or notes)
        $patientsUnderCare = $patientModel
            ->select('admin_patients.*')
            ->join('patient_vitals', 'patient_vitals.patient_id = admin_patients.id', 'left')
            ->join('nurse_notes', 'nurse_notes.patient_id = admin_patients.id', 'left')
            ->where('DATE(patient_vitals.created_at)', $today)
            ->orWhere('DATE(nurse_notes.created_at)', $today)
            ->groupBy('admin_patients.id')
            ->countAllResults();

        $db = \Config\Database::connect();
        
        // Get assigned patients from medication orders
        $assignedPatientsFromOrders = $db->table('doctor_orders do')
            ->select('ap.*, "medication_order" as assignment_type, "admin_patients" as source')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->where('do.nurse_id', $nurseId)
            ->where('do.order_type', 'medication')
            ->where('ap.id IS NOT NULL', null, false)
            ->groupBy('ap.id')
            ->get()
            ->getResultArray();
        
        // Get directly assigned patients from admin_patients table (via assigned_nurse_id)
        $directlyAssignedAdminPatients = [];
        if ($db->tableExists('admin_patients')) {
            $directlyAssignedAdminPatients = $db->table('admin_patients')
                ->select('admin_patients.*, "direct_assignment" as assignment_type, "admin_patients" as source')
                ->where('admin_patients.assigned_nurse_id', $nurseId)
                ->where('admin_patients.deleted_at IS NULL', null, false)
                ->get()
                ->getResultArray();
        }
        
        // Get directly assigned patients from patients table (via assigned_nurse_id)
        $directlyAssignedHmsPatients = [];
        if ($db->tableExists('patients')) {
            $directlyAssignedHmsPatientsRaw = $db->table('patients')
                ->select('patients.*, "direct_assignment" as assignment_type')
                ->where('patients.assigned_nurse_id', $nurseId)
                ->get()
                ->getResultArray();
            
            // Format HMS patients to match admin_patients structure
            foreach ($directlyAssignedHmsPatientsRaw as $patient) {
                $nameParts = [];
                $fullName = '';
                
                // Prioritize first_name and last_name if available
                if (!empty($patient['first_name']) && !empty($patient['last_name'])) {
                    $nameParts = [$patient['first_name'], $patient['last_name']];
                    $fullName = trim($patient['first_name'] . ' ' . $patient['last_name']);
                } elseif (!empty($patient['full_name'])) {
                    // If full_name exists, use it and try to parse
                    $fullName = trim($patient['full_name']);
                    $parts = explode(' ', $fullName, 3); // Handle names like "Clair Jay Galorpot"
                    if (count($parts) >= 2) {
                        // First part is firstname, last part is lastname
                        $nameParts = [$parts[0], $parts[count($parts) - 1]];
                    } else {
                        $nameParts = [$parts[0] ?? '', ''];
                    }
                }
                
                $directlyAssignedHmsPatients[] = [
                    'id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                    'patient_id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                    'firstname' => $nameParts[0] ?? '',
                    'lastname' => $nameParts[1] ?? '',
                    'full_name' => $fullName ?: implode(' ', $nameParts),
                    'birthdate' => $patient['date_of_birth'] ?? $patient['birthdate'] ?? null,
                    'gender' => strtolower($patient['gender'] ?? ''),
                    'contact' => $patient['contact'] ?? null,
                    'address' => $patient['address'] ?? null,
                    'visit_type' => $patient['visit_type'] ?? null,
                    'type' => $patient['type'] ?? 'Out-Patient',
                    'assignment_type' => 'direct_assignment',
                    'source' => 'patients', // Mark as from patients table
                ];
            }
        }
        
        // Merge all assigned patients and remove duplicates
        $allAssignedPatients = array_merge($assignedPatientsFromOrders, $directlyAssignedAdminPatients, $directlyAssignedHmsPatients);
        
        // Remove duplicates based on name + birthdate (same patient in both tables should only appear once)
        // Prefer admin_patients version if duplicate exists
        $uniqueAssignedPatients = [];
        $seenKeys = [];
        
        foreach ($allAssignedPatients as $patient) {
            $patientId = $patient['id'] ?? $patient['patient_id'] ?? null;
            $source = $patient['source'] ?? 'admin_patients';
            
            if (!$patientId) {
                continue; // Skip if no ID
            }
            
            // Create unique key using name (case-insensitive) + birthdate to identify same patient across tables
            $firstName = strtolower(trim($patient['firstname'] ?? $patient['first_name'] ?? ''));
            $lastName = strtolower(trim($patient['lastname'] ?? $patient['last_name'] ?? ''));
            $fullName = strtolower(trim($patient['full_name'] ?? ''));
            $birthdate = $patient['birthdate'] ?? $patient['date_of_birth'] ?? '';
            
            // Use full name if available, otherwise combine first and last
            $nameKey = !empty($fullName) ? $fullName : trim($firstName . ' ' . $lastName);
            $uniqueKey = md5($nameKey . '|' . $birthdate);
            
            // If we've seen this patient before (same name + birthdate), prefer admin_patients version
            if (isset($seenKeys[$uniqueKey])) {
                $existingIndex = $seenKeys[$uniqueKey]['index'];
                $existingSource = $seenKeys[$uniqueKey]['source'];
                
                // If current is from admin_patients and existing is from patients, replace it
                if ($source === 'admin_patients' && $existingSource === 'patients') {
                    $uniqueAssignedPatients[$existingIndex] = $patient;
                    $seenKeys[$uniqueKey] = ['index' => $existingIndex, 'source' => $source];
                }
                // Otherwise, skip this duplicate (keep the existing one)
                continue;
            }
            
            // Add to unique list
            if (!isset($patient['source'])) {
                $patient['source'] = $source;
            }
            $index = count($uniqueAssignedPatients);
            $uniqueAssignedPatients[] = $patient;
            $seenKeys[$uniqueKey] = ['index' => $index, 'source' => $source];
        }
        
        // Re-index array
        $assignedPatients = array_values($uniqueAssignedPatients);

        // Get medication orders assigned to this nurse with pharmacy status
        $medicationOrders = $db->table('doctor_orders do')
            ->select('do.*, ap.firstname, ap.lastname, u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->where('do.order_type', 'medication')
            ->where('do.nurse_id', $nurseId)
            ->orderBy('do.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Group medication orders by status
        $waitingForPharmacy = array_filter($medicationOrders, function($order) {
            $pharmacyStatus = $order['pharmacy_status'] ?? 'pending';
            return in_array($pharmacyStatus, ['pending', 'approved', 'prepared']);
        });

        $readyToAdminister = array_filter($medicationOrders, function($order) {
            return ($order['pharmacy_status'] ?? 'pending') === 'dispensed' && 
                   ($order['status'] ?? 'pending') !== 'completed';
        });

        $administered = array_filter($medicationOrders, function($order) {
            return ($order['status'] ?? 'pending') === 'completed';
        });

        // Get medications due count (ready to administer)
        $medicationsDue = count($readyToAdminister);

        // Get vitals pending (patients without vitals today)
        $vitalsPending = $this->getVitalsPendingCount($today);

        // Get pending doctor orders
        $pendingOrders = $orderModel
            ->select('doctor_orders.*, admin_patients.firstname, admin_patients.lastname, users.username as doctor_name')
            ->join('admin_patients', 'admin_patients.id = doctor_orders.patient_id', 'left')
            ->join('users', 'users.id = doctor_orders.doctor_id', 'left')
            ->where('doctor_orders.status', 'pending')
            ->orderBy('doctor_orders.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get pending lab requests
        $pendingLabRequests = $labRequestModel
            ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->where('lab_requests.nurse_id', $nurseId)
            ->where('lab_requests.status', 'pending')
            ->orderBy('lab_requests.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get approved lab requests (in progress)
        $approvedLabRequests = $labRequestModel
            ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->where('lab_requests.nurse_id', $nurseId)
            ->where('lab_requests.status', 'in_progress')
            ->orderBy('lab_requests.updated_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get completed lab results
        $completedLabResults = $labRequestModel
            ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, lab_results.result, lab_results.completed_at')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('lab_results', 'lab_results.lab_request_id = lab_requests.id', 'left')
            ->where('lab_requests.nurse_id', $nurseId)
            ->where('lab_requests.status', 'completed')
            ->orderBy('lab_results.completed_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get unread notifications
        $unreadNotifications = $notificationModel->getUnreadNotifications($nurseId, 10);
        $unreadNotificationsCount = $notificationModel
            ->where('nurse_id', $nurseId)
            ->where('is_read', 0)
            ->countAllResults();

        // Get today's appointments
        $todaysAppointments = $appointmentModel
            ->select('appointments.*, patients.full_name as patient_name')
            ->join('patients', 'patients.patient_id = appointments.patient_id', 'left')
            ->where('appointments.appointment_date', $today)
            ->whereNotIn('appointments.status', ['cancelled', 'no_show'])
            ->orderBy('appointments.appointment_time', 'ASC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Nurse Dashboard',
            'criticalPatients' => $criticalPatients,
            'patientsUnderCare' => $patientsUnderCare,
            'medicationsDue' => $medicationsDue,
            'vitalsPending' => $vitalsPending,
            'pendingOrders' => $pendingOrders,
            'pendingLabRequests' => $pendingLabRequests,
            'approvedLabRequests' => $approvedLabRequests,
            'completedLabResults' => $completedLabResults,
            'unreadNotifications' => $unreadNotifications,
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'todaysAppointments' => $todaysAppointments,
            // Medication administration data
            'assignedPatients' => $assignedPatients,
            'waitingForPharmacy' => $waitingForPharmacy,
            'readyToAdminister' => $readyToAdminister,
            'administered' => $administered,
            'waitingCount' => count($waitingForPharmacy),
            'readyCount' => count($readyToAdminister),
            'administeredCount' => count($administered),
        ];

        return view('nurse/dashboard', $data);
    }

    private function getCriticalPatientsCount()
    {
        $vitalModel = new PatientVitalModel();
        $noteModel = new NurseNoteModel();
        $today = date('Y-m-d');

        // Patients with abnormal vitals today
        $criticalVitals = $vitalModel
            ->select('patient_id')
            ->where('DATE(created_at)', $today)
            ->groupStart()
                ->where('heart_rate >', 100)
                ->orWhere('heart_rate <', 60)
                ->orWhere('blood_pressure_systolic >', 140)
                ->orWhere('blood_pressure_systolic <', 90)
                ->orWhere('temperature >', 38)
                ->orWhere('oxygen_saturation <', 95)
            ->groupEnd()
            ->groupBy('patient_id')
            ->findAll();

        // Patients with urgent notes today
        $urgentNotes = $noteModel
            ->select('patient_id')
            ->where('DATE(created_at)', $today)
            ->where('priority', 'urgent')
            ->groupBy('patient_id')
            ->findAll();

        $criticalPatientIds = array_unique(array_merge(
            array_column($criticalVitals, 'patient_id'),
            array_column($urgentNotes, 'patient_id')
        ));

        return count($criticalPatientIds);
    }

    private function getVitalsPendingCount($today)
    {
        $patientModel = new AdminPatientModel();
        $vitalModel = new PatientVitalModel();

        // Get all patients
        $allPatients = $patientModel->findAll();
        $allPatientIds = array_column($allPatients, 'id');

        if (empty($allPatientIds)) {
            return 0;
        }

        // Get patients with vitals today
        $patientsWithVitals = $vitalModel
            ->select('patient_id')
            ->where('DATE(created_at)', $today)
            ->groupBy('patient_id')
            ->findAll();
        $patientsWithVitalsIds = array_column($patientsWithVitals, 'patient_id');

        // Patients without vitals today
        return count(array_diff($allPatientIds, $patientsWithVitalsIds));
    }
}

