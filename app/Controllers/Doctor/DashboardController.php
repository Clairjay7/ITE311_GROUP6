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

        // Get recent assigned patients
        $assignedPatients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

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

        $data = [
            'title' => 'Doctor Dashboard',
            'name' => session()->get('name'),
            'appointmentsCount' => $appointmentsCount,
            'patientsSeenToday' => $patientsSeenToday,
            'pendingLabResults' => $pendingLabResults,
            'pendingLabRequestsCount' => $pendingLabRequestsCount,
            'pendingLabRequests' => $pendingLabRequests,
            'assignedPatientsCount' => $assignedPatientsCount,
            'assignedPatients' => $assignedPatients,
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
