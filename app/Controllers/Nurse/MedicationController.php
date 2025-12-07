<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\DoctorOrderModel;
use App\Models\OrderStatusLogModel;
use App\Models\AdminPatientModel;

class MedicationController extends BaseController
{
    /**
     * View medication orders assigned to nurse
     */
    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $nurseId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get medication orders assigned to this nurse
        $medicationOrders = $db->table('doctor_orders do')
            ->select('do.*, ap.firstname, ap.lastname, ap.birthdate, ap.gender, 
                      u.username as doctor_name, do.created_at as order_date')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->where('do.order_type', 'medication')
            ->where('do.nurse_id', $nurseId)
            ->orderBy('do.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Group by pharmacy status
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

        // Get assigned patients
        $assignedPatients = $db->table('doctor_orders do')
            ->select('ap.*')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->where('do.nurse_id', $nurseId)
            ->where('do.order_type', 'medication')
            ->groupBy('ap.id')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Medication Administration',
            'medicationOrders' => $medicationOrders,
            'waitingForPharmacy' => $waitingForPharmacy,
            'readyToAdminister' => $readyToAdminister,
            'administered' => $administered,
            'assignedPatients' => $assignedPatients,
        ];

        return view('nurse/medications/index', $data);
    }

    /**
     * View medication order details
     */
    public function view($orderId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $nurseId = session()->get('user_id');
        $db = \Config\Database::connect();

        $order = $db->table('doctor_orders do')
            ->select('do.*, ap.firstname, ap.lastname, ap.birthdate, ap.gender, 
                      u.username as doctor_name, do.created_at as order_date')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->where('do.id', $orderId)
            ->where('do.nurse_id', $nurseId)
            ->where('do.order_type', 'medication')
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('/nurse/medications')->with('error', 'Medication order not found.');
        }

        // Get audit trail
        $auditTrail = [];
        if ($db->tableExists('order_status_logs')) {
            $auditTrail = $db->table('order_status_logs')
                ->select('order_status_logs.*, users.username as changed_by_name')
                ->join('users', 'users.id = order_status_logs.changed_by', 'left')
                ->where('order_status_logs.order_id', $orderId)
                ->orderBy('order_status_logs.created_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Medication Order Details',
            'order' => $order,
            'auditTrail' => $auditTrail,
        ];

        return view('nurse/medications/view', $data);
    }

    /**
     * Administer medication (mark as given to patient)
     */
    public function administer($orderId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $nurseId = session()->get('user_id');
        $orderModel = new DoctorOrderModel();
        $logModel = new OrderStatusLogModel();
        $db = \Config\Database::connect();

        $order = $orderModel->find($orderId);

        if (!$order) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Order not found']);
        }

        // Check if order is assigned to this nurse
        if ($order['nurse_id'] != $nurseId) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'This order is not assigned to you']);
        }

        // Check if medication has been dispensed by Pharmacy
        $pharmacyStatus = $order['pharmacy_status'] ?? 'pending';
        if ($pharmacyStatus !== 'dispensed') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Medication must be dispensed by Pharmacy before administration. Current status: ' . ucfirst($pharmacyStatus)]);
        }

        // Check if already administered
        if ($order['status'] === 'completed') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'This medication has already been administered']);
        }

        $administeredTime = $this->request->getPost('administered_time') ?: date('Y-m-d H:i:s');
        $dosageConfirmation = $this->request->getPost('dosage_confirmation') ?? '';
        $remarks = $this->request->getPost('remarks') ?? '';

        // Update order status
        $updateData = [
            'status' => 'completed',
            'completed_by' => $nurseId,
            'completed_at' => $administeredTime,
        ];

        if ($orderModel->update($orderId, $updateData)) {
            // Log administration
            $logModel->insert([
                'order_id' => $orderId,
                'status' => 'completed',
                'changed_by' => $nurseId,
                'notes' => 'Medication administered to patient. Time: ' . $administeredTime . 
                          ($dosageConfirmation ? '. Dosage confirmed: ' . $dosageConfirmation : '') .
                          ($remarks ? '. Remarks: ' . $remarks : ''),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Store administration details (if table exists)
            if ($db->tableExists('medication_administrations')) {
                $db->table('medication_administrations')->insert([
                    'order_id' => $orderId,
                    'nurse_id' => $nurseId,
                    'administered_time' => $administeredTime,
                    'dosage_confirmation' => $dosageConfirmation,
                    'remarks' => $remarks,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Update patient_medication_records with administration details
            if ($db->tableExists('patient_medication_records')) {
                $db->table('patient_medication_records')
                    ->where('order_id', $orderId)
                    ->update([
                        'administered_at' => $administeredTime,
                        'notes' => ($remarks ? $remarks . ' | ' : '') . 'Administered by nurse. Dosage confirmed: ' . ($dosageConfirmation ?: 'N/A'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            // AUTOMATIC BILLING: Create billing record for Accountant
            if ($db->tableExists('billing') && $db->tableExists('pharmacy')) {
                // Get medicine price from pharmacy
                $medicinePrice = 0.00;
                $medicine = $db->table('pharmacy')
                    ->where('item_name', $order['medicine_name'])
                    ->get()
                    ->getRowArray();
                
                if ($medicine) {
                    $medicinePrice = (float)($medicine['price'] ?? 0.00);
                }

                // Calculate administration fee (default: 50.00 PHP, can be configured)
                $administrationFee = 50.00; // Can be made configurable later
                
                // Calculate total amount
                $quantity = 1; // Default quantity, can be adjusted based on dosage
                $totalAmount = ($medicinePrice * $quantity) + $administrationFee;

                // Generate invoice number
                $invoiceNumber = 'MED-' . date('Ymd') . '-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);

                // Create billing record
                $billingData = [
                    'patient_id' => $order['patient_id'],
                    'order_id' => $orderId,
                    'service' => 'Medication Administration',
                    'medicine_name' => $order['medicine_name'] ?? 'N/A',
                    'dosage' => $order['dosage'] ?? 'N/A',
                    'quantity' => $quantity,
                    'unit_price' => $medicinePrice,
                    'administration_fee' => $administrationFee,
                    'amount' => $totalAmount,
                    'status' => 'pending',
                    'nurse_id' => $nurseId,
                    'administered_at' => $administeredTime,
                    'invoice_number' => $invoiceNumber,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $db->table('billing')->insert($billingData);

                // Notify Accountant about new billable item
                if ($db->tableExists('accountant_notifications')) {
                    $patientModel = new AdminPatientModel();
                    $patient = $patientModel->find($order['patient_id']);
                    
                    $db->table('accountant_notifications')->insert([
                        'type' => 'new_medication_bill',
                        'title' => 'New Medication Bill Generated',
                        'message' => 'Medication "' . ($order['medicine_name'] ?? 'N/A') . '" was administered to ' . 
                                   ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . 
                                   ' by Nurse. Amount: ₱' . number_format($totalAmount, 2) . '. Please review and process payment.',
                        'related_id' => $orderId,
                        'related_type' => 'billing',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Medication administered successfully. Billing record created and sent to Accountant for review.'
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to update order status'
                ]);
        }
    }
}

