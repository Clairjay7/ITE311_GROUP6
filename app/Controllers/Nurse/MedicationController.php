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

        // Get medication and IV fluids orders that this nurse can administer:
        // 1. Orders assigned to this nurse (nurse_id = $nurseId)
        // 2. Orders with pharmacy_status = 'dispensed' (WAITING FOR NURSE) where patient's assigned_nurse_id = $nurseId
        $medicationOrders = $db->table('doctor_orders do')
            ->select('do.*, ap.firstname, ap.lastname, ap.birthdate, ap.gender, 
                      u.username as doctor_name, do.created_at as order_date, ap.assigned_nurse_id')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->whereIn('do.order_type', ['medication', 'iv_fluids_order'])
            ->where('do.status !=', 'completed')
            ->where('(do.nurse_id = ' . (int)$nurseId . ' OR (do.pharmacy_status = "dispensed" AND ap.assigned_nurse_id = ' . (int)$nurseId . ' AND do.nurse_id IS NULL))', null, false)
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
                      u.username as doctor_name, do.created_at as order_date, ap.assigned_nurse_id')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->where('do.id', $orderId)
            ->whereIn('do.order_type', ['medication', 'iv_fluids_order'])
            ->where('(do.nurse_id = ' . (int)$nurseId . ' OR (do.pharmacy_status = "dispensed" AND ap.assigned_nurse_id = ' . (int)$nurseId . ' AND do.nurse_id IS NULL))', null, false)
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('/nurse/medications')->with('error', 'Order not found or not assigned to you.');
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

        // Get patient's assigned nurse
        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($order['patient_id']);
        $patientAssignedNurseId = $patient['assigned_nurse_id'] ?? null;

        // Check if order is assigned to this nurse OR if patient is assigned to this nurse and order is dispensed
        $orderNurseId = $order['nurse_id'] ?? null;
        $pharmacyStatus = $order['pharmacy_status'] ?? 'pending';
        
        $isAuthorized = false;
        if ($orderNurseId == $nurseId) {
            // Order is directly assigned to this nurse
            $isAuthorized = true;
        } elseif ($pharmacyStatus === 'dispensed' && $patientAssignedNurseId == $nurseId && $orderNurseId === null) {
            // Order is dispensed, patient is assigned to this nurse, and order has no specific nurse assignment
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'This order is not assigned to you']);
        }

        // Check if order type is medication or IV fluids
        if (!in_array($order['order_type'], ['medication', 'iv_fluids_order'])) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'This order type cannot be administered through this interface']);
        }

        // Check if order has been dispensed by Pharmacy
        $pharmacyStatus = $order['pharmacy_status'] ?? 'pending';
        if ($pharmacyStatus !== 'dispensed') {
            $orderTypeLabel = $order['order_type'] === 'medication' ? 'Medication' : 'IV Fluid';
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => $orderTypeLabel . ' must be dispensed by Pharmacy before administration. Current status: ' . ucfirst($pharmacyStatus)]);
        }

        // Check if already administered
        if ($order['status'] === 'completed') {
            $orderTypeLabel = $order['order_type'] === 'medication' ? 'medication' : 'IV fluid';
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'This ' . $orderTypeLabel . ' has already been administered']);
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
        
        // If order doesn't have a nurse_id assigned, assign it to this nurse now
        if (empty($order['nurse_id'])) {
            $updateData['nurse_id'] = $nurseId;
        }

        if ($orderModel->update($orderId, $updateData)) {
            // Log administration
            $orderTypeLabel = $order['order_type'] === 'medication' ? 'Medication' : 'IV Fluid';
            $actionLabel = $order['order_type'] === 'medication' ? 'administered' : 'injected';
            $logModel->insert([
                'order_id' => $orderId,
                'status' => 'completed',
                'changed_by' => $nurseId,
                'notes' => $orderTypeLabel . ' ' . $actionLabel . ' to patient. Time: ' . $administeredTime . 
                          ($dosageConfirmation ? '. Dosage confirmed: ' . $dosageConfirmation : '') .
                          ($remarks ? '. Remarks: ' . $remarks : ''),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // WORKFLOW UNLOCK: If ANY order type is completed and patient status is 'pending_order', unlock Check button
            // This applies to ALL order types, not just medication
            if ($updateData['status'] === 'completed') {
                $patientModel = new \App\Models\AdminPatientModel();
                $patient = $patientModel->find($order['patient_id']);
                
                if ($patient && ($patient['doctor_check_status'] ?? 'available') === 'pending_order') {
                    // Check if there are any other pending orders for this patient (not just vital-linked)
                    // If no other pending orders exist, unlock the Check button
                    $hasOtherPendingOrders = false;
                    if ($db->tableExists('doctor_orders')) {
                        $otherPendingOrders = $db->table('doctor_orders')
                            ->where('patient_id', $order['patient_id'])
                            ->where('id !=', $orderId)
                            ->where('status !=', 'completed')
                            ->where('status !=', 'cancelled')
                            ->countAllResults();
                        
                        $hasOtherPendingOrders = $otherPendingOrders > 0;
                    }
                    
                    // Unlock if there are no other pending orders
                    // This works for ANY order type - once completed, unlock the Check button
                    if (!$hasOtherPendingOrders) {
                        $unlockData = [
                            'is_doctor_checked' => 0,
                            'doctor_check_status' => 'available', // Unlock Check button
                            'nurse_vital_status' => 'completed',
                        ];
                        
                        // Add doctor_order_status only if column exists
                        if ($db->fieldExists('doctor_order_status', 'admin_patients')) {
                            $unlockData['doctor_order_status'] = 'not_required';
                        }
                        
                        $patientModel->update($order['patient_id'], $unlockData);
                    
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
                                    ->where('doctor_id', $patient['doctor_id'] ?? null)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($hmsPatient) {
                                    $db->table('patients')
                                        ->where('patient_id', $hmsPatient['patient_id'])
                                        ->update($unlockData);
                                }
                            }
                        }
                    }
                }
            }

            // Store administration details (if table exists) - for medication orders only
            if ($order['order_type'] === 'medication' && $db->tableExists('medication_administrations')) {
                $db->table('medication_administrations')->insert([
                    'order_id' => $orderId,
                    'nurse_id' => $nurseId,
                    'administered_time' => $administeredTime,
                    'dosage_confirmation' => $dosageConfirmation,
                    'remarks' => $remarks,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Update patient_medication_records with administration details (for medication orders only)
            if ($order['order_type'] === 'medication' && $db->tableExists('patient_medication_records')) {
                $db->table('patient_medication_records')
                    ->where('order_id', $orderId)
                    ->update([
                        'administered_at' => $administeredTime,
                        'notes' => ($remarks ? $remarks . ' | ' : '') . 'Administered by nurse. Dosage confirmed: ' . ($dosageConfirmation ?: 'N/A'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            // AUTOMATIC BILLING: Create billing record for Accountant (for both medication and IV fluids)
            if (in_array($order['order_type'], ['medication', 'iv_fluids_order']) && 
                $db->tableExists('billing') && $db->tableExists('pharmacy')) {
                $medicinePrice = 0.00;
                $itemName = '';
                
                if ($order['order_type'] === 'medication') {
                    // Get medicine price from pharmacy
                    $itemName = $order['medicine_name'] ?? '';
                    $medicine = $db->table('pharmacy')
                        ->where('item_name', $itemName)
                        ->get()
                        ->getRowArray();
                    
                    if ($medicine) {
                        $medicinePrice = (float)($medicine['price'] ?? 0.00);
                    }
                } elseif ($order['order_type'] === 'iv_fluids_order') {
                    // Get IV Fluid price from pharmacy
                    // Extract IV fluid name from order_description (format: "IV Fluid: [Name] - Volume: ...")
                    $orderDesc = $order['order_description'] ?? '';
                    if (preg_match('/IV Fluid:\s*([^-]+)/', $orderDesc, $matches)) {
                        $itemName = trim($matches[1]);
                    } else {
                        $itemName = $orderDesc;
                    }
                    
                    $ivFluid = $db->table('pharmacy')
                        ->where('item_name', $itemName)
                        ->where('category', 'IV Fluids / Electrolytes')
                        ->get()
                        ->getRowArray();
                    
                    if ($ivFluid) {
                        $medicinePrice = (float)($ivFluid['price'] ?? 0.00);
                    }
                }

                // Calculate administration fee (default: 50.00 PHP, can be configured)
                $administrationFee = 50.00; // Can be made configurable later
                
                // Calculate total amount
                $quantity = 1; // Default quantity, can be adjusted based on dosage
                $totalAmount = ($medicinePrice * $quantity) + $administrationFee;

                // Generate invoice number
                $invoicePrefix = $order['order_type'] === 'iv_fluids_order' ? 'IVF' : 'MED';
                $invoiceNumber = $invoicePrefix . '-' . date('Ymd') . '-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);

                // Determine service type based on order_type
                $serviceType = 'Medication Administration';
                if ($order['order_type'] === 'iv_fluids_order') {
                    $serviceType = 'IV Fluid Administration';
                }
                
                // Create billing record
                $billingData = [
                    'patient_id' => $order['patient_id'],
                    'order_id' => $orderId,
                    'service' => $serviceType,
                    'medicine_name' => $order['order_type'] === 'iv_fluids_order' ? ($order['order_description'] ?? 'N/A') : ($order['medicine_name'] ?? 'N/A'),
                    'dosage' => $order['dosage'] ?? 'N/A',
                    'order_description' => $order['order_description'] ?? null, // Store for IV Fluids
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
                    
                    $orderTypeLabel = $order['order_type'] === 'iv_fluids_order' ? 'IV Fluid' : 'Medication';
                    $itemDisplayName = $order['order_type'] === 'iv_fluids_order' 
                        ? ($order['order_description'] ?? $itemName ?? 'N/A')
                        : ($order['medicine_name'] ?? 'N/A');
                    
                    $db->table('accountant_notifications')->insert([
                        'type' => $order['order_type'] === 'iv_fluids_order' ? 'new_iv_fluids_bill' : 'new_medication_bill',
                        'title' => 'New ' . $orderTypeLabel . ' Bill Generated',
                        'message' => $orderTypeLabel . ' "' . $itemDisplayName . '" was ' . 
                                   ($order['order_type'] === 'iv_fluids_order' ? 'injected' : 'administered') . ' to ' . 
                                   ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . 
                                   ' by Nurse. Amount: ₱' . number_format($totalAmount, 2) . '. Please review and process payment.',
                        'related_id' => $orderId,
                        'related_type' => 'billing',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $orderTypeLabel = $order['order_type'] === 'iv_fluids_order' ? 'IV Fluid' : 'Medication';
            $actionLabel = $order['order_type'] === 'iv_fluids_order' ? 'injected' : 'administered';
            
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => ucfirst($orderTypeLabel) . ' ' . $actionLabel . ' successfully. Billing record created and sent to Accountant for review.'
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

