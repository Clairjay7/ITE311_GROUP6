<?php

namespace App\Controllers\LabStaff;

use App\Controllers\BaseController;
use App\Models\LabRequestModel;
use App\Models\LabResultModel;
use App\Models\AdminPatientModel;
use App\Models\DoctorNotificationModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;
use App\Models\NurseNotificationModel;
use App\Models\LabTestModel;

class LabStaffController extends BaseController
{
    protected $labRequestModel;
    protected $labResultModel;
    protected $patientModel;
    protected $chargeModel;
    protected $billingItemModel;
    protected $labTestModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->labRequestModel = new LabRequestModel();
        $this->labResultModel = new LabResultModel();
        $this->patientModel = new AdminPatientModel();
        $this->chargeModel = new ChargeModel();
        $this->billingItemModel = new BillingItemModel();
        $this->labTestModel = new LabTestModel();
    }

    /**
     * Lab Staff Dashboard - Summary statistics
     */
    public function dashboard()
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        // Get pending test requests (only after payment is paid - accountant must process payment first)
        $pendingTests = $this->labRequestModel
            ->where('status', 'pending')
            ->where('payment_status', 'paid') // ONLY count after payment is PAID
            ->countAllResults();

        // Get completed tests today
        $completedToday = $this->labRequestModel
            ->where('status', 'completed')
            ->where('DATE(updated_at)', $today)
            ->countAllResults();

        // Get total tests this month
        $monthlyTests = $this->labRequestModel
            ->where('status', 'completed')
            ->where('DATE(updated_at) >=', $monthStart)
            ->countAllResults();

        // Get pending specimens (lab requests with status pending or in_progress, only after payment is paid)
        $pendingSpecimens = $this->labRequestModel
            ->whereIn('status', ['specimen_collected', 'in_progress'])
            ->where('payment_status', 'paid') // ONLY count after payment is PAID
            ->countAllResults();

        $data = [
            'title' => 'Lab Staff Dashboard',
            'pendingTests' => $pendingTests,
            'completedToday' => $completedToday,
            'monthlyTests' => $monthlyTests,
            'pendingSpecimens' => $pendingSpecimens,
        ];

        return view('labstaff/dashboard', $data);
    }

    /**
     * Test Requests - All test requests from doctors/nurses
     */
    public function testRequests()
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $db = \Config\Database::connect();

        // Get all test requests with patient and doctor/nurse info
        // Lab staff can ONLY see lab requests AFTER payment is processed (paid)
        // Payment must be approved and processed by accountant first
        $testRequests = $db->table('lab_requests')
            ->select('lab_requests.*, 
                admin_patients.firstname as patient_firstname, 
                admin_patients.lastname as patient_lastname,
                admin_patients.contact as patient_contact,
                doctor.username as doctor_name,
                nurse.username as nurse_name,
                charges.charge_number,
                charges.total_amount as charge_amount,
                charges.status as charge_status')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->join('charges', 'charges.id = lab_requests.charge_id', 'inner') // INNER JOIN - must have charge
            ->where('lab_requests.status !=', 'cancelled')
            ->where('lab_requests.payment_status', 'paid') // ONLY show after payment is PAID (accountant must process payment first)
            ->where('charges.status', 'paid') // ALSO verify charge status is paid (double check)
            ->where('lab_requests.charge_id IS NOT NULL') // Must have charge_id
            ->groupStart()
                // Include requests that are ready for testing:
                // 1. Status = 'pending' with payment paid (without_specimen tests - no specimen needed)
                // 2. Status = 'specimen_collected' or 'in_progress' (with_specimen tests that have been collected)
                ->where('lab_requests.status', 'pending')
                ->groupEnd()
            ->orGroupStart()
                ->whereIn('lab_requests.status', ['specimen_collected', 'in_progress'])
                ->groupEnd()
            ->orderBy('lab_requests.priority', 'ASC') // Urgent/Stat first
            ->orderBy('lab_requests.requested_date', 'ASC')
            ->orderBy('lab_requests.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Test Requests',
            'testRequests' => $testRequests,
        ];

        return view('labstaff/test_requests', $data);
    }

    /**
     * Pending Specimens - Specimens waiting to be processed
     */
    public function pendingSpecimens()
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $db = \Config\Database::connect();

        // Get pending and in_progress specimens
        $pendingSpecimens = $db->table('lab_requests')
            ->select('lab_requests.*, 
                admin_patients.firstname as patient_firstname, 
                admin_patients.lastname as patient_lastname,
                admin_patients.contact as patient_contact,
                doctor.username as doctor_name,
                nurse.username as nurse_name,
                charges.charge_number,
                charges.total_amount as charge_amount,
                charges.status as charge_status')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->join('charges', 'charges.id = lab_requests.charge_id', 'inner') // INNER JOIN - must have charge
            ->whereIn('lab_requests.status', ['specimen_collected', 'in_progress'])
            ->where('lab_requests.payment_status', 'paid') // ONLY show after payment is PAID (accountant must process payment first)
            ->where('charges.status', 'paid') // ALSO verify charge status is paid (double check)
            ->where('lab_requests.charge_id IS NOT NULL') // Must have charge_id
            ->orderBy('lab_requests.priority', 'ASC') // Urgent/Stat first
            ->orderBy('lab_requests.requested_date', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Pending Specimens',
            'pendingSpecimens' => $pendingSpecimens,
        ];

        return view('labstaff/pending_specimens', $data);
    }

    /**
     * Completed Tests - All completed test results
     */
    public function completedTests()
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $db = \Config\Database::connect();

        // Get completed tests with results
        $completedTests = $db->table('lab_requests')
            ->select('lab_requests.*, 
                admin_patients.firstname as patient_firstname, 
                admin_patients.lastname as patient_lastname,
                admin_patients.contact as patient_contact,
                doctor.username as doctor_name,
                nurse.username as nurse_name,
                lab_results.result as test_result,
                lab_results.result_file,
                lab_results.interpretation,
                lab_results.completed_at,
                completed_by.username as completed_by_name')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->join('lab_results', 'lab_results.lab_request_id = lab_requests.id', 'left')
            ->join('users as completed_by', 'completed_by.id = lab_results.completed_by', 'left')
            ->where('lab_requests.status', 'completed')
            ->orderBy('lab_requests.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Completed Tests',
            'completedTests' => $completedTests,
        ];

        return view('labstaff/completed_tests', $data);
    }

    /**
     * Mark Specimen as Collected - Update status to in_progress
     */
    public function markCollected($requestId)
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized. Please log in as lab staff.'
            ])->setStatusCode(401);
        }

        $request = $this->labRequestModel->find($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test request not found'
            ])->setStatusCode(404);
        }

        // Check if status is 'specimen_collected' (nurse has already collected specimen)
        if (($request['status'] ?? 'pending') !== 'specimen_collected') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Specimen must be collected by nurse first. Current status: ' . ucfirst(str_replace('_', ' ', $request['status'] ?? 'pending'))
            ])->setStatusCode(400);
        }

        // Check payment status - Payment must be paid before processing
        $paymentStatus = $request['payment_status'] ?? 'unpaid';
        if ($paymentStatus !== 'paid') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment required before processing test. Please ensure payment is completed first.'
            ])->setStatusCode(400);
        }

        // Update status to in_progress (lab staff is now testing)
        $this->labRequestModel->update($requestId, [
            'status' => 'in_progress',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Log status change
        $db = \Config\Database::connect();
        if ($db->tableExists('lab_status_history')) {
            $db->table('lab_status_history')->insert([
                'lab_request_id' => $requestId,
                'status' => 'in_progress',
                'changed_by' => session()->get('user_id'),
                'notes' => 'Specimen collected by lab staff',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Notify assigned nurse that specimen has been received by lab
        if (!empty($request['nurse_id']) && $db->tableExists('nurse_notifications')) {
            $patient = $this->patientModel->find($request['patient_id']);
            $patientName = ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'Patient');
            
            $nurseNotificationModel = new NurseNotificationModel();
            $nurseNotificationModel->insert([
                'nurse_id' => $request['nurse_id'],
                'type' => 'lab_result_ready',
                'title' => 'Lab Specimen Received',
                'message' => 'Lab specimen for ' . $patientName . ' (' . ($request['test_name'] ?? 'Lab Test') . ') has been received by the laboratory and is now being processed. Payment is required before testing.',
                'related_id' => $requestId,
                'related_type' => 'lab_request',
                'is_read' => 0,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Specimen marked as collected'
        ]);
    }

    /**
     * Mark Test as Completed - Update status and create result record
     */
    public function markCompleted($requestId)
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized. Please log in as lab staff.'
            ])->setStatusCode(401);
        }

        $request = $this->labRequestModel->find($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test request not found'
            ])->setStatusCode(404);
        }

        // Check payment status - Payment must be PAID (processed by accountant) before completing test
        $paymentStatus = $request['payment_status'] ?? 'unpaid';
        if ($paymentStatus !== 'paid') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment must be processed by accountant first before completing test. Current payment status: ' . ucfirst($paymentStatus)
            ])->setStatusCode(400);
        }
        
        // For "without_specimen" tests, if status is 'pending', update to 'in_progress' first
        $currentStatus = $request['status'] ?? 'pending';
        if ($currentStatus === 'pending') {
            // Check if this is a without_specimen test (no nurse_id)
            $isWithoutSpecimen = empty($request['nurse_id']);
            if ($isWithoutSpecimen) {
                // Update status to in_progress before completing
                $this->labRequestModel->update($requestId, [
                    'status' => 'in_progress',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $result = $this->request->getPost('result');
        $interpretation = $this->request->getPost('interpretation');
        $resultFile = $this->request->getPost('result_file');

        // Update lab request status
        $this->labRequestModel->update($requestId, [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Initialize database connection
        $db = \Config\Database::connect();
        
        // Update corresponding doctor_order status to 'completed'
        // Find doctor_order by matching patient_id, doctor_id, order_type='lab_test', and test_name
        if ($db->tableExists('doctor_orders')) {
            // Try to find doctor_order by extracting link info from instructions
            $instructions = $request['instructions'] ?? '';
            $doctorOrderId = null;
            
            // Check if linking info is stored in instructions
            if (preg_match('/\| LINK:(.+)$/', $instructions, $matches)) {
                $linkingInfo = json_decode($matches[1], true);
                if ($linkingInfo && isset($linkingInfo['doctor_order_id'])) {
                    $doctorOrderId = $linkingInfo['doctor_order_id'];
                }
            }
            
            // If no link found, try to find by matching criteria
            if (!$doctorOrderId) {
                $matchingOrder = $db->table('doctor_orders')
                    ->where('patient_id', $request['patient_id'])
                    ->where('doctor_id', $request['doctor_id'])
                    ->where('order_type', 'lab_test')
                    ->where('order_description', $request['test_name'])
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
                
                if ($matchingOrder) {
                    $doctorOrderId = $matchingOrder['id'];
                }
            }
            
            // Update doctor_order status if found
            if ($doctorOrderId) {
                $db->table('doctor_orders')
                    ->where('id', $doctorOrderId)
                    ->update([
                        'status' => 'completed',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            }
        }

        // Create or update lab result
        $existingResult = $this->labResultModel
            ->where('lab_request_id', $requestId)
            ->first();

        $resultData = [
            'lab_request_id' => $requestId,
            'result' => $result,
            'interpretation' => $interpretation,
            'result_file' => $resultFile,
            'completed_by' => session()->get('user_id'),
            'completed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existingResult) {
            $this->labResultModel->update($existingResult['id'], $resultData);
        } else {
            $resultData['created_at'] = date('Y-m-d H:i:s');
            $this->labResultModel->insert($resultData);
        }

        // Log status change
        $db = \Config\Database::connect();
        if ($db->tableExists('lab_status_history')) {
            $db->table('lab_status_history')->insert([
                'lab_request_id' => $requestId,
                'status' => 'completed',
                'changed_by' => session()->get('user_id'),
                'notes' => 'Test completed by lab staff',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Get patient and test information
        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($request['patient_id']);
        $patientName = ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient');
        
        // Note: Billing is handled during payment, not after completion
        
        // Notify doctor when lab result is uploaded
        if ($request['doctor_id']) {
            if ($db->tableExists('doctor_notifications')) {
                $doctorNotificationModel = new DoctorNotificationModel();
                $doctorNotificationModel->insert([
                    'doctor_id' => $request['doctor_id'],
                    'type' => 'lab_result_ready',
                    'title' => 'Lab Result Ready',
                    'message' => 'Lab result for ' . $patientName . ' (' . $request['test_name'] . ') is now available for review.',
                    'related_id' => $requestId,
                    'related_type' => 'lab_result',
                    'is_read' => 0,
                ]);
            }
        }
        
        // Notify nurse if action is needed (e.g., specimen follow-up, medication administration)
        // Check if there are any pending orders that might need nurse action based on lab results
        if ($request['nurse_id']) {
            $hasPendingOrders = false;
            if ($db->tableExists('doctor_orders')) {
                $pendingOrders = $db->table('doctor_orders')
                    ->where('patient_id', $request['patient_id'])
                    ->where('nurse_id', $request['nurse_id'])
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->countAllResults();
                $hasPendingOrders = $pendingOrders > 0;
            }
            
            if ($hasPendingOrders || $db->tableExists('nurse_notifications')) {
                $nurseNotificationModel = new NurseNotificationModel();
                $nurseNotificationModel->insert([
                    'nurse_id' => $request['nurse_id'],
                    'type' => 'lab_result_ready',
                    'title' => 'Lab Result Completed',
                    'message' => 'Lab result for ' . $patientName . ' (' . $request['test_name'] . ') has been completed. Please check if any action is needed.',
                    'related_id' => $requestId,
                    'related_type' => 'lab_result',
                    'is_read' => 0,
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Test marked as completed. Billing charge created and notifications sent.'
        ]);
    }

    /**
     * Process Payment for Lab Test
     */
    public function processPayment($requestId)
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin', 'finance'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized. Please log in as lab staff or accountant.'
            ])->setStatusCode(401);
        }

        $request = $this->labRequestModel->find($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test request not found'
            ])->setStatusCode(404);
        }

        // Check if already paid
        if (($request['payment_status'] ?? 'unpaid') === 'paid') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment already processed for this test.'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Get lab test price from lab_tests table
        $testPrice = 0.00;
        if ($db->tableExists('lab_tests')) {
            $labTest = $db->table('lab_tests')
                ->where('test_name', $request['test_name'])
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            
            if ($labTest && isset($labTest['price'])) {
                $testPrice = (float) $labTest['price'];
            } else {
                // Default price if not found
                $testPrice = 300.00;
            }
        } else {
            $testPrice = 300.00; // Default price
        }

        $db->transStart();

        try {
            // Generate charge number
            $chargeNumber = $this->chargeModel->generateChargeNumber();

            // Create charge record - status is 'pending' until accountant approves
            $chargeData = [
                'patient_id' => $request['patient_id'],
                'charge_number' => $chargeNumber,
                'total_amount' => $testPrice,
                'status' => 'pending', // Pending until accountant approves and processes payment
                'notes' => 'Lab test payment: ' . $request['test_name'] . ' - Requires accountant approval',
            ];
            
            // Add optional fields only if columns exist in the table
            $db = \Config\Database::connect();
            if ($db->fieldExists('processed_by', 'charges') && $userId) {
                $chargeData['processed_by'] = $userId;
            }
            if ($db->fieldExists('paid_at', 'charges')) {
                $chargeData['paid_at'] = date('Y-m-d H:i:s');
            }

            // Insert charge
            if (!$this->chargeModel->insert($chargeData)) {
                $errors = $this->chargeModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Database insert failed';
                log_message('error', 'Charge insert failed: ' . $errorMsg . ' | Data: ' . json_encode($chargeData));
                throw new \Exception('Failed to create charge: ' . $errorMsg);
            }
            $chargeId = $this->chargeModel->getInsertID();
            
            if (!$chargeId) {
                throw new \Exception('Failed to get charge ID after insert');
            }

                // Create billing item
                $billingItemData = [
                    'charge_id' => $chargeId,
                    'item_type' => 'lab_test',
                    'item_name' => $request['test_name'],
                    'description' => 'Lab Test: ' . ($request['test_type'] ?? 'Laboratory') . ' - ' . $request['test_name'],
                    'quantity' => 1.00,
                    'unit_price' => $testPrice,
                    'total_price' => $testPrice,
                    'related_id' => $requestId,
                    'related_type' => 'lab_request',
                ];

                if (!$this->billingItemModel->insert($billingItemData)) {
                    $errors = $this->billingItemModel->errors();
                    $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Billing item insert failed';
                    log_message('error', 'Billing item insert failed: ' . $errorMsg);
                    throw new \Exception('Failed to create billing item: ' . $errorMsg);
                }

                // Update lab request with charge_id (payment_status stays 'pending' until accountant processes)
                if (!$this->labRequestModel->update($requestId, [
                    'payment_status' => 'pending', // Will be updated to 'paid' by accountant
                    'charge_id' => $chargeId,
                    'updated_at' => date('Y-m-d H:i:s')
                ])) {
                    $errors = $this->labRequestModel->errors();
                    $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Lab request update failed';
                    log_message('error', 'Lab request update failed: ' . $errorMsg);
                    throw new \Exception('Failed to update lab request: ' . $errorMsg);
                }

                // Notify Accountant about new payment request (needs approval)
                if ($db->tableExists('accountant_notifications')) {
                    $patient = $this->patientModel->find($request['patient_id']);
                    $patientName = ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'Patient');
                    
                    $db->table('accountant_notifications')->insert([
                        'type' => 'lab_payment',
                        'title' => 'Lab Test Payment Pending Approval',
                        'message' => 'Payment request for lab test: ' . $request['test_name'] . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve and process payment.',
                        'related_id' => $chargeId,
                        'related_type' => 'charge',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Transaction failed');
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'charge_id' => $chargeId,
                    'charge_number' => $chargeNumber,
                    'amount' => $testPrice
                ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Print Receipt for Lab Test Payment
     */
    public function printReceipt($requestId)
    {
        // Check if user is logged in
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        if (!$isLoggedIn) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $request = $this->labRequestModel->find($requestId);
        if (!$request) {
            return redirect()->back()->with('error', 'Test request not found.');
        }

        // Check if payment is processed
        if (($request['payment_status'] ?? 'unpaid') !== 'paid' || empty($request['charge_id'])) {
            return redirect()->back()->with('error', 'Payment not yet processed for this test.');
        }

        $db = \Config\Database::connect();

        // Get charge and billing items
        $charge = $this->chargeModel->find($request['charge_id']);
        if (!$charge) {
            return redirect()->back()->with('error', 'Charge not found.');
        }

        $billingItems = $this->billingItemModel
            ->where('charge_id', $request['charge_id'])
            ->findAll();

        // Get patient info
        $patient = $this->patientModel->find($request['patient_id']);

        $data = [
            'title' => 'Receipt - ' . $charge['charge_number'],
            'charge' => $charge,
            'billingItems' => $billingItems,
            'patient' => $patient,
            'labRequest' => $request,
        ];

        return view('labstaff/receipt', $data);
    }

    /**
     * Print Lab Result
     */
    public function printResult($requestId)
    {
        // Check if user is logged in and is lab staff
        $isLoggedIn = session()->get('isLoggedIn') || session()->get('logged_in');
        $userRole = session()->get('role');
        if (!$isLoggedIn || !in_array($userRole, ['labstaff', 'lab_staff', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as lab staff to access this page.');
        }

        $request = $this->labRequestModel->find($requestId);
        if (!$request) {
            return redirect()->back()->with('error', 'Test request not found.');
        }

        // Check if test is completed
        if ($request['status'] !== 'completed') {
            return redirect()->back()->with('error', 'Test is not yet completed.');
        }

        $db = \Config\Database::connect();

        // Get lab result
        $labResult = $this->labResultModel
            ->where('lab_request_id', $requestId)
            ->first();

        if (!$labResult) {
            return redirect()->back()->with('error', 'Lab result not found.');
        }

        // Get patient info
        $patient = $this->patientModel->find($request['patient_id']);

        // Get completed by user
        $completedBy = null;
        if (!empty($labResult['completed_by'])) {
            $completedBy = $db->table('users')
                ->where('id', $labResult['completed_by'])
                ->get()
                ->getRowArray();
        }

        // Get test info from lab_tests table
        $testInfo = null;
        if ($db->tableExists('lab_tests')) {
            $testInfo = $db->table('lab_tests')
                ->where('test_name', $request['test_name'])
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
        }

        $data = [
            'title' => 'Lab Result - ' . $request['test_name'],
            'labRequest' => $request,
            'labResult' => $labResult,
            'patient' => $patient,
            'completedBy' => $completedBy,
            'testInfo' => $testInfo,
        ];

        return view('labstaff/print_result', $data);
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }
}

