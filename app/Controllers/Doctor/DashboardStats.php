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

            // Assigned patients count (from admin_patients table)
            $assignedPatientsCount = $patientModel
                ->where('doctor_id', $doctorId)
                ->countAllResults();

            // Get recent assigned patients (latest 5)
            $assignedPatients = $patientModel
                ->where('doctor_id', $doctorId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();

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

            $data = [
                'appointments_count' => $appointmentsCount,
                'patients_seen_today' => $patientsSeenToday,
                'assigned_patients_count' => $assignedPatientsCount,
                'pending_consultations' => $pendingConsultations,
                'upcoming_consultations' => $upcomingConsultations,
                'pending_lab_requests_count' => $pendingLabRequestsCount,
                'pending_lab_requests' => $pendingLabRequests,
                'assigned_patients' => $assignedPatients,
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'in_progress_orders' => $inProgressOrders,
                'completed_orders' => $completedOrders,
                'recent_orders' => $recentOrders,
                'unread_notifications' => $unreadNotifications,
                'total_unread_notifications' => $totalUnreadNotifications,
                'last_updated' => date('Y-m-d H:i:s')
            ];

            return $this->response->setJSON($data);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching Doctor Dashboard Stats: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to fetch stats'])->setStatusCode(500);
        }
    }
}

