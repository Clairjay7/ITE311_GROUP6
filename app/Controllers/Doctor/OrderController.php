<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\DoctorOrderModel;
use App\Models\OrderStatusLogModel;
use App\Models\AdminPatientModel;
use App\Models\DoctorNotificationModel;
use App\Models\NurseNotificationModel;

class OrderController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();

        // Get all orders by this doctor
        $allOrders = $orderModel
            ->select('doctor_orders.*, admin_patients.firstname, admin_patients.lastname, users.username as completed_by_name')
            ->join('admin_patients', 'admin_patients.id = doctor_orders.patient_id', 'left')
            ->join('users', 'users.id = doctor_orders.completed_by', 'left')
            ->where('doctor_orders.doctor_id', $doctorId)
            ->orderBy('doctor_orders.created_at', 'DESC')
            ->findAll();

        // Get orders by status
        $pendingOrders = array_filter($allOrders, fn($order) => $order['status'] === 'pending');
        $inProgressOrders = array_filter($allOrders, fn($order) => $order['status'] === 'in_progress');
        $completedOrders = array_filter($allOrders, fn($order) => $order['status'] === 'completed');
        $cancelledOrders = array_filter($allOrders, fn($order) => $order['status'] === 'cancelled');

        $data = [
            'title' => 'Doctor Orders',
            'allOrders' => $allOrders,
            'pendingOrders' => $pendingOrders,
            'inProgressOrders' => $inProgressOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
        ];

        return view('doctor/orders/index', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $patientModel = new AdminPatientModel();
        $db = \Config\Database::connect();

        // Get assigned patients
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        // Get all active nurses
        $nurses = $db->table('users')
            ->select('users.id, users.username, users.email')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('LOWER(roles.name)', 'nurse')
            ->where('users.status', 'active')
            ->orderBy('users.username', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Create Medical Order',
            'patients' => $patients,
            'nurses' => $nurses,
        ];

        return view('doctor/orders/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();
        $nurseNotificationModel = new NurseNotificationModel();

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'nurse_id' => 'required|integer|greater_than[0]',
            'order_type' => 'required|in_list[medication,lab_test,procedure,diet,activity,other]',
            'order_description' => 'required',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'doctor_id' => $doctorId,
            'nurse_id' => $this->request->getPost('nurse_id'),
            'order_type' => $this->request->getPost('order_type'),
            'order_description' => $this->request->getPost('order_description'),
            'instructions' => $this->request->getPost('instructions'),
            'frequency' => $this->request->getPost('frequency'),
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'status' => 'pending',
        ];

        if ($orderModel->insert($data)) {
            $orderId = $orderModel->getInsertID();

            // Log initial status
            $logModel->insert([
                'order_id' => $orderId,
                'status' => 'pending',
                'changed_by' => $doctorId,
                'notes' => 'Order created by doctor',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Create notification for the selected nurse
            $patientModel = new AdminPatientModel();
            $patient = $patientModel->find($data['patient_id']);

            if ($data['nurse_id']) {
                $nurseNotificationModel->insert([
                    'nurse_id' => $data['nurse_id'],
                    'type' => 'new_doctor_order',
                    'title' => 'New Doctor Order',
                    'message' => 'Dr. ' . session()->get('name') . ' has created a new ' . $data['order_type'] . ' order for ' . ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . '. Please execute this order.',
                    'related_id' => $orderId,
                    'related_type' => 'doctor_order',
                    'is_read' => 0,
                ]);
            }

            return redirect()->to('/doctor/orders')->with('success', 'Medical order created successfully. The assigned nurse has been notified.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create medical order.');
        }
    }

    public function view($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();

        $order = $orderModel
            ->select('doctor_orders.*, admin_patients.firstname, admin_patients.lastname, admin_patients.birthdate, admin_patients.gender, users.username as completed_by_name, nurse_users.username as nurse_name')
            ->join('admin_patients', 'admin_patients.id = doctor_orders.patient_id', 'left')
            ->join('users', 'users.id = doctor_orders.completed_by', 'left')
            ->join('users as nurse_users', 'nurse_users.id = doctor_orders.nurse_id', 'left')
            ->where('doctor_orders.id', $id)
            ->where('doctor_orders.doctor_id', $doctorId)
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Order not found.');
        }

        // Get audit trail (status logs)
        $auditTrail = $logModel
            ->select('order_status_logs.*, users.username as changed_by_name')
            ->join('users', 'users.id = order_status_logs.changed_by', 'left')
            ->where('order_status_logs.order_id', $id)
            ->orderBy('order_status_logs.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Order Details',
            'order' => $order,
            'auditTrail' => $auditTrail
        ];

        return view('doctor/orders/view', $data);
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $patientModel = new AdminPatientModel();

        $order = $orderModel
            ->where('id', $id)
            ->where('doctor_id', $doctorId)
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Order not found.');
        }

        // Check if order can be edited (only pending or in_progress orders)
        if (!in_array($order['status'], ['pending', 'in_progress'])) {
            return redirect()->to('/doctor/orders')->with('error', 'This order cannot be edited as it has been completed or cancelled.');
        }

        // Get assigned patients
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Edit Medical Order',
            'order' => $order,
            'patients' => $patients,
        ];

        return view('doctor/orders/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();

        $order = $orderModel
            ->where('id', $id)
            ->where('doctor_id', $doctorId)
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Order not found.');
        }

        // Check if order can be edited
        if (!in_array($order['status'], ['pending', 'in_progress'])) {
            return redirect()->to('/doctor/orders')->with('error', 'This order cannot be edited as it has been completed or cancelled.');
        }

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'nurse_id' => 'required|integer|greater_than[0]',
            'order_type' => 'required|in_list[medication,lab_test,procedure,diet,activity,other]',
            'order_description' => 'required',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'patient_id' => $this->request->getPost('patient_id'),
            'nurse_id' => $this->request->getPost('nurse_id'),
            'order_type' => $this->request->getPost('order_type'),
            'order_description' => $this->request->getPost('order_description'),
            'instructions' => $this->request->getPost('instructions'),
            'frequency' => $this->request->getPost('frequency'),
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
        ];

        if ($orderModel->update($id, $updateData)) {
            // Log update
            $logModel->insert([
                'order_id' => $id,
                'status' => $order['status'], // Keep current status
                'changed_by' => $doctorId,
                'notes' => 'Order updated by doctor',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/doctor/orders')->with('success', 'Medical order updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update medical order.');
        }
    }

    public function cancel($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();

        $order = $orderModel
            ->where('id', $id)
            ->where('doctor_id', $doctorId)
            ->first();

        if (!$order) {
            return redirect()->to('/doctor/orders')->with('error', 'Order not found.');
        }

        if ($order['status'] === 'completed') {
            return redirect()->to('/doctor/orders')->with('error', 'Cannot cancel a completed order.');
        }

        if ($orderModel->update($id, ['status' => 'cancelled'])) {
            // Log cancellation
            $logModel->insert([
                'order_id' => $id,
                'status' => 'cancelled',
                'changed_by' => $doctorId,
                'notes' => $this->request->getPost('cancellation_reason') ?? 'Order cancelled by doctor',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/doctor/orders')->with('success', 'Medical order cancelled successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to cancel medical order.');
        }
    }
}

