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

class DashboardStats extends BaseController
{
    public function stats()
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
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

        try {
            // Get critical patients
            $criticalPatients = $this->getCriticalPatientsCount();

            // Get patients under care
            $patientsUnderCare = $patientModel
                ->select('admin_patients.*')
                ->join('patient_vitals', 'patient_vitals.patient_id = admin_patients.id', 'left')
                ->join('nurse_notes', 'nurse_notes.patient_id = admin_patients.id', 'left')
                ->where('DATE(patient_vitals.created_at)', $today)
                ->orWhere('DATE(nurse_notes.created_at)', $today)
                ->groupBy('admin_patients.id')
                ->countAllResults();

            $db = \Config\Database::connect();
            
            // Get medication orders assigned to this nurse with pharmacy status
            $medicationOrders = $db->table('doctor_orders do')
                ->where('do.order_type', 'medication')
                ->where('do.nurse_id', $nurseId)
                ->get()
                ->getResultArray();

            // Count by status
            $waitingForPharmacy = array_filter($medicationOrders, function($order) {
                $pharmacyStatus = $order['pharmacy_status'] ?? 'pending';
                return in_array($pharmacyStatus, ['pending', 'approved', 'prepared']);
            });

            $readyToAdminister = array_filter($medicationOrders, function($order) {
                return ($order['pharmacy_status'] ?? 'pending') === 'dispensed' && 
                       ($order['status'] ?? 'pending') !== 'completed';
            });

            // Get medications due (ready to administer)
            $medicationsDue = count($readyToAdminister);

            // Get vitals pending
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

            // Get lab orders from doctor orders (nurse can see but cannot approve)
            // Lab orders bypass nurse approval - they go directly to lab staff
            $labOrdersFromDoctor = $orderModel
                ->select('doctor_orders.*, admin_patients.firstname, admin_patients.lastname, users.username as doctor_name')
                ->join('admin_patients', 'admin_patients.id = doctor_orders.patient_id', 'left')
                ->join('users', 'users.id = doctor_orders.doctor_id', 'left')
                ->where('doctor_orders.order_type', 'lab_test')
                ->where('doctor_orders.nurse_id', $nurseId)
                ->where('doctor_orders.status', 'pending')
                ->orderBy('doctor_orders.created_at', 'DESC')
                ->limit(5)
                ->findAll();

            // Get pending lab requests (nurse-created requests that need doctor approval)
            $pendingLabRequests = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->where('lab_requests.nurse_id', $nurseId)
                ->where('lab_requests.requested_by', 'nurse')
                ->where('lab_requests.status', 'pending')
                ->orderBy('lab_requests.created_at', 'DESC')
                ->limit(5)
                ->findAll();

            // Get lab requests ready for specimen collection (payment approved, status pending, assigned to this nurse)
            $approvedLabRequests = $labRequestModel
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname, charges.charge_number, charges.total_amount, charges.status as charge_status')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->join('charges', 'charges.id = lab_requests.charge_id', 'left')
                ->where('lab_requests.nurse_id', $nurseId)
                ->whereIn('lab_requests.payment_status', ['approved', 'paid']) // Payment approved or paid - nurse can proceed
                ->where('lab_requests.status', 'pending') // Ready for nurse to collect specimen
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

            // Get pending discharges (patients with discharge orders)
            $pendingDischarges = [];
            if ($db->tableExists('admissions') && $db->tableExists('discharge_orders')) {
                $pendingDischarges = $db->table('admissions a')
                    ->select('a.*, ap.firstname, ap.lastname, ap.contact,
                             r.room_number, r.ward,
                             do.id as discharge_order_id, do.discharge_date as planned_discharge_date,
                             u.username as doctor_name')
                    ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
                    ->join('rooms r', 'r.id = a.room_id', 'left')
                    ->join('discharge_orders do', 'do.admission_id = a.id', 'left')
                    ->join('users u', 'u.id = do.doctor_id', 'left')
                    ->where('a.discharge_status', 'discharge_pending')
                    ->where('a.status', 'admitted')
                    ->where('do.status', 'pending')
                    ->where('a.deleted_at', null)
                    ->orderBy('do.discharge_date', 'ASC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();
            }

            // Get pending admissions (consultations marked for admission but not yet admitted)
            $pendingAdmissions = $db->table('consultations c')
                ->select('c.*, ap.firstname, ap.lastname, ap.contact, u.username as doctor_name')
                ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
                ->join('users u', 'u.id = c.doctor_id', 'left')
                ->where('c.for_admission', 1)
                ->where('c.type', 'completed')
                ->where('c.status', 'approved')
                ->where('c.deleted_at', null)
                ->orderBy('c.consultation_date', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            // Filter out already admitted
            $filteredAdmissions = [];
            foreach ($pendingAdmissions as $consultation) {
                $existingAdmission = $db->table('admissions')
                    ->where('consultation_id', $consultation['id'])
                    ->where('status !=', 'discharged')
                    ->where('status !=', 'cancelled')
                    ->where('deleted_at', null)
                    ->get()
                    ->getRowArray();
                
                if (!$existingAdmission) {
                    $filteredAdmissions[] = $consultation;
                }
            }

            $data = [
                'criticalPatients' => $criticalPatients,
                'patientsUnderCare' => $patientsUnderCare,
                'medicationsDue' => $medicationsDue,
                'vitalsPending' => $vitalsPending,
                'pendingOrders' => $pendingOrders,
                'labOrdersFromDoctor' => $labOrdersFromDoctor,
                'pendingLabRequests' => $pendingLabRequests,
                'approvedLabRequests' => $approvedLabRequests,
                'completedLabResults' => $completedLabResults,
                'unreadNotifications' => $unreadNotifications,
                'unreadNotificationsCount' => $unreadNotificationsCount,
                'todaysAppointments' => $todaysAppointments,
                'pendingAdmissions' => $filteredAdmissions,
                'pendingDischarges' => $pendingDischarges,
                // Medication administration stats
                'waitingForPharmacy' => count($waitingForPharmacy ?? []),
                'readyToAdminister' => count($readyToAdminister ?? []),
                'last_updated' => date('Y-m-d H:i:s')
            ];

            return $this->response->setJSON($data);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching Nurse Dashboard Stats: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to fetch stats'])->setStatusCode(500);
        }
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

