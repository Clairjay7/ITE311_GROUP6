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

class LabStaffController extends BaseController
{
    protected $labRequestModel;
    protected $labResultModel;
    protected $patientModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->labRequestModel = new LabRequestModel();
        $this->labResultModel = new LabResultModel();
        $this->patientModel = new AdminPatientModel();
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

        // Get pending test requests
        $pendingTests = $this->labRequestModel
            ->where('status', 'pending')
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

        // Get pending specimens (lab requests with status pending or in_progress)
        $pendingSpecimens = $this->labRequestModel
            ->whereIn('status', ['pending', 'in_progress'])
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
        // Lab staff can see ALL lab requests (from doctors and nurses)
        $testRequests = $db->table('lab_requests')
            ->select('lab_requests.*, 
                admin_patients.firstname as patient_firstname, 
                admin_patients.lastname as patient_lastname,
                admin_patients.contact as patient_contact,
                doctor.username as doctor_name,
                nurse.username as nurse_name')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->where('lab_requests.status !=', 'cancelled')
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
                nurse.username as nurse_name')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->whereIn('lab_requests.status', ['pending', 'in_progress'])
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

        // Update status to in_progress
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
        
        // Get lab test price from lab_tests table
        $labTestPrice = 300.00; // Default price
        if ($db->tableExists('lab_tests')) {
            $labTestInfo = $db->table('lab_tests')
                ->where('test_name', $request['test_name'])
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            if ($labTestInfo && isset($labTestInfo['price'])) {
                $labTestPrice = floatval($labTestInfo['price']);
            }
        }
        
        // AUTO-GENERATE BILLING: Create charge and billing item for completed lab test
        if ($db->tableExists('charges') && $db->tableExists('billing_items')) {
            $chargeModel = new ChargeModel();
            $billingItemModel = new BillingItemModel();
            
            // Check if charge already exists for this lab request
            $existingCharge = $db->table('billing_items')
                ->where('related_type', 'lab_request')
                ->where('related_id', $requestId)
                ->get()
                ->getRowArray();
            
            if (!$existingCharge) {
                // Generate charge number
                $chargeNumber = $chargeModel->generateChargeNumber();
                
                // Create charge record
                $chargeData = [
                    'patient_id' => $request['patient_id'],
                    'charge_number' => $chargeNumber,
                    'total_amount' => $labTestPrice,
                    'status' => 'pending',
                    'notes' => 'Auto-generated charge for completed lab test: ' . $request['test_name'],
                ];
                
                // Add consultation_id if available (for outpatient consultations)
                if ($db->tableExists('consultations')) {
                    $consultation = $db->table('consultations')
                        ->where('patient_id', $request['patient_id'])
                        ->where('doctor_id', $request['doctor_id'])
                        ->orderBy('consultation_date', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRowArray();
                    if ($consultation) {
                        $chargeData['consultation_id'] = $consultation['id'];
                    }
                }
                
                if ($chargeModel->insert($chargeData)) {
                    $chargeId = $chargeModel->getInsertID();
                    
                    // Create billing item
                    $billingItemData = [
                        'charge_id' => $chargeId,
                        'item_type' => 'lab_test',
                        'item_name' => $request['test_name'],
                        'description' => 'Lab Test: ' . ($request['test_type'] ?? 'Laboratory') . ' - Completed on ' . date('Y-m-d'),
                        'quantity' => 1.00,
                        'unit_price' => $labTestPrice,
                        'total_price' => $labTestPrice,
                        'related_id' => $requestId,
                        'related_type' => 'lab_request',
                    ];
                    
                    $billingItemModel->insert($billingItemData);
                    
                    // Notify Accountant about new charge
                    if ($db->tableExists('accountant_notifications')) {
                        $db->table('accountant_notifications')->insert([
                            'type' => 'new_charge',
                            'title' => 'New Lab Test Charge',
                            'message' => 'Lab test charge ' . $chargeNumber . ' generated for patient: ' . $patientName . '. Test: ' . $request['test_name'] . '. Amount: ₱' . number_format($labTestPrice, 2),
                            'related_id' => $chargeId,
                            'related_type' => 'charge',
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }
        
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
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }
}

