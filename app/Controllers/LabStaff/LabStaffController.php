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

        // Get pending test requests (only without_specimen tests that go directly to lab)
        $pendingTests = $this->labRequestModel
            ->where('status', 'pending')
            ->where('nurse_id', null) // Only without_specimen tests (no nurse assigned)
            ->where('status !=', 'cancelled')
            ->countAllResults();
        
        // Also count specimen_collected and in_progress tests
        $pendingTests += $this->labRequestModel
            ->whereIn('status', ['specimen_collected', 'in_progress'])
            ->where('status !=', 'cancelled')
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
            ->whereIn('status', ['specimen_collected', 'in_progress'])
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
        // Show ALL requests (pending payment and paid) so lab staff can see them immediately
        // Lab staff can see requests even before payment is processed
        // Handle both admin_patients and patients tables
        $testRequests = $db->table('lab_requests')
            ->select('lab_requests.*, 
                COALESCE(admin_patients.firstname, patients.first_name) as patient_firstname, 
                COALESCE(admin_patients.lastname, patients.last_name) as patient_lastname,
                COALESCE(admin_patients.contact, patients.contact) as patient_contact,
                doctor.username as doctor_name,
                nurse.username as nurse_name,
                charges.charge_number,
                charges.total_amount as charge_amount,
                charges.status as charge_status')
            ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
            ->join('patients', 'patients.patient_id = lab_requests.patient_id', 'left')
            ->join('users as doctor', 'doctor.id = lab_requests.doctor_id', 'left')
            ->join('users as nurse', 'nurse.id = lab_requests.nurse_id', 'left')
            ->join('charges', 'charges.id = lab_requests.charge_id', 'left') // LEFT JOIN - show even without charge
            ->where('lab_requests.status !=', 'cancelled')
            ->groupStart()
                // Include requests that are ready for testing:
                // 1. Status = 'pending' (all pending requests - both with and without specimen)
                //    - without_specimen tests (nurse_id IS NULL) go directly to lab
                //    - with_specimen tests (nurse_id IS NOT NULL) wait for nurse to collect specimen first
                // 2. Status = 'specimen_collected' or 'in_progress' (with_specimen tests that have been collected by nurse)
                ->where('lab_requests.status', 'pending')
            ->orGroupStart()
                ->whereIn('lab_requests.status', ['specimen_collected', 'in_progress'])
                ->groupEnd()
            ->groupEnd()
            ->orderBy('lab_requests.priority', 'ASC') // Urgent/Stat first
            ->orderBy('lab_requests.requested_date', 'ASC')
            ->orderBy('lab_requests.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Check admission status for each request
        foreach ($testRequests as &$request) {
            $request['is_admitted'] = $this->isPatientAdmitted($request['patient_id']);
        }
        unset($request);

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

        // Get pending and in_progress specimens (show all, including pending payment)
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
            ->join('charges', 'charges.id = lab_requests.charge_id', 'left') // LEFT JOIN - show even without charge
            ->whereIn('lab_requests.status', ['specimen_collected', 'in_progress'])
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

        // Check if patient is admitted - if admitted, allow proceeding without upfront payment (will be billed)
        $isAdmitted = $this->isPatientAdmitted($request['patient_id']);
        
        // Check payment status - Payment must be paid before processing UNLESS patient is admitted
        $paymentStatus = $request['payment_status'] ?? 'unpaid';
        if ($paymentStatus !== 'paid' && !$isAdmitted) {
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
            
            $isAdmitted = $this->isPatientAdmitted($request['patient_id']);
            $paymentMessage = $isAdmitted 
                ? 'Lab specimen for ' . $patientName . ' (' . ($request['test_name'] ?? 'Lab Test') . ') has been received by the laboratory and is now being processed. Will be billed to patient.'
                : 'Lab specimen for ' . $patientName . ' (' . ($request['test_name'] ?? 'Lab Test') . ') has been received by the laboratory and is now being processed. Payment is required before testing.';
            
            $nurseNotificationModel = new NurseNotificationModel();
            $nurseNotificationModel->insert([
                'nurse_id' => $request['nurse_id'],
                'type' => 'lab_result_ready',
                'title' => 'Lab Specimen Received',
                'message' => $paymentMessage,
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

        // Check if patient is admitted - if admitted, allow completing test without upfront payment (will be billed)
        $isAdmitted = $this->isPatientAdmitted($request['patient_id']);
        
        // Check payment status - Payment must be PAID (processed by accountant) before completing test UNLESS patient is admitted
        $paymentStatus = $request['payment_status'] ?? 'unpaid';
        if ($paymentStatus !== 'paid' && !$isAdmitted) {
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
            // Format: "Doctor Order #123 | LINK:{"doctor_order_id":123} | ..."
            if (preg_match('/Doctor Order #(\d+)/', $instructions, $orderMatches)) {
                $doctorOrderId = (int)$orderMatches[1];
            } elseif (preg_match('/\| LINK:(.+?)(?:\s*\|)/', $instructions, $matches)) {
                $linkingInfo = json_decode(trim($matches[1]), true);
                if ($linkingInfo && isset($linkingInfo['doctor_order_id'])) {
                    $doctorOrderId = (int)$linkingInfo['doctor_order_id'];
                }
            }
            
            // If no link found, try to find by matching criteria
            // Match by patient_id, doctor_id, order_type='lab_test', and order_description containing test_name
            if (!$doctorOrderId) {
                $testName = $request['test_name'];
                $matchingOrder = $db->table('doctor_orders')
                    ->where('patient_id', $request['patient_id'])
                    ->where('doctor_id', $request['doctor_id'])
                    ->where('order_type', 'lab_test')
                    ->like('order_description', $testName, 'both') // Match if description contains test_name (case-insensitive)
                    ->whereIn('status', ['pending', 'in_progress']) // Also check in_progress orders
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
                
                if ($matchingOrder) {
                    $doctorOrderId = $matchingOrder['id'];
                    log_message('info', "Found doctor_order #{$doctorOrderId} by matching test_name '{$testName}' in order_description");
                } else {
                    log_message('warning', "Could not find doctor_order for lab_request #{$requestId}, test_name: {$testName}, patient_id: {$request['patient_id']}, doctor_id: {$request['doctor_id']}");
                }
            } else {
                log_message('info', "Found doctor_order #{$doctorOrderId} from instructions link for lab_request #{$requestId}");
            }
            
            // Update doctor_order status if found
            if ($doctorOrderId) {
                $userId = session()->get('user_id');
                $db->table('doctor_orders')
                    ->where('id', $doctorOrderId)
                    ->update([
                        'status' => 'completed',
                        'completed_by' => $userId, // Set who completed it (lab staff)
                        'completed_at' => date('Y-m-d H:i:s'), // Set completion time
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                
                log_message('info', "Doctor order #{$doctorOrderId} marked as completed by lab staff (user_id: {$userId}) for lab test: {$request['test_name']}");
                
                // WORKFLOW UNLOCK: If ANY order type is completed and patient status is 'pending_order', unlock Check button
                // This applies to ALL order types, not just lab tests
                $completedOrder = $db->table('doctor_orders')
                    ->where('id', $doctorOrderId)
                    ->get()
                    ->getRowArray();
                
                // Check if we need to unlock the workflow
                if ($completedOrder) {
                    $patientModel = new \App\Models\AdminPatientModel();
                    $patient = $patientModel->find($completedOrder['patient_id']);
                    
                    if ($patient && ($patient['doctor_check_status'] ?? 'available') === 'pending_order') {
                        // Check if there are any other pending orders for this patient (not just vital-linked)
                        // If no other pending orders exist, unlock the Check button
                        $hasOtherPendingOrders = false;
                        $otherPendingOrders = $db->table('doctor_orders')
                            ->where('patient_id', $completedOrder['patient_id'])
                            ->where('id !=', $doctorOrderId)
                            ->where('status !=', 'completed')
                            ->where('status !=', 'cancelled')
                            ->countAllResults();
                        
                        $hasOtherPendingOrders = $otherPendingOrders > 0;
                        
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
                            
                            $patientModel->update($completedOrder['patient_id'], $unlockData);
                            
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

        // Update lab_services table with the result (so it appears in admin lab services)
        if ($db->tableExists('lab_services')) {
            // Find lab_service by lab_request_id
            $labService = $db->table('lab_services')
                ->where('lab_request_id', $requestId)
                ->get()
                ->getRowArray();
            
            if ($labService) {
                // Update lab_service with result and remarks
                $labServiceUpdateData = [
                    'result' => $result, // Save the test result
                    'remarks' => $interpretation, // Save interpretation as remarks
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $db->table('lab_services')
                    ->where('id', $labService['id'])
                    ->update($labServiceUpdateData);
                
                log_message('debug', 'Lab Staff - Updated lab_service ID: ' . $labService['id'] . ' with result and remarks');
            } else {
                log_message('warning', 'Lab Staff - No lab_service found for lab_request_id: ' . $requestId);
            }
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
        
        // Auto-complete consultation if all lab tests from consultation are completed
        $instructions = $request['instructions'] ?? '';
        $consultationId = null;
        
        // Extract consultation ID from instructions (format: "From Consultation #123 | ...")
        if (preg_match('/From Consultation #(\d+)/', $instructions, $matches)) {
            $consultationId = (int)$matches[1];
            
            // Check if all lab requests from this consultation are completed
            if ($consultationId && $db->tableExists('lab_requests') && $db->tableExists('consultations')) {
                // Get all lab requests from this consultation
                $allLabRequests = $db->table('lab_requests')
                    ->where('patient_id', $request['patient_id'])
                    ->where('doctor_id', $request['doctor_id'])
                    ->like('instructions', 'From Consultation #' . $consultationId, 'after')
                    ->get()
                    ->getResultArray();
                
                // Only proceed if there are lab requests from this consultation
                if (!empty($allLabRequests)) {
                    // Check if all lab requests have results (completed)
                    $allCompleted = true;
                    foreach ($allLabRequests as $labReq) {
                        $hasResult = $this->labResultModel
                            ->where('lab_request_id', $labReq['id'])
                            ->first();
                        
                        if (!$hasResult) {
                            $allCompleted = false;
                            break;
                        }
                    }
                    
                    // If all lab tests are completed, auto-complete the consultation
                    if ($allCompleted) {
                        $consultation = $db->table('consultations')
                            ->where('id', $consultationId)
                            ->get()
                            ->getRowArray();
                        
                        // Only update if consultation is still pending/upcoming
                        if ($consultation && ($consultation['status'] === 'pending' || $consultation['type'] === 'upcoming')) {
                            $db->table('consultations')
                                ->where('id', $consultationId)
                                ->update([
                                    'status' => 'completed',
                                    'type' => 'completed',
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            
                            log_message('info', "Auto-completed consultation #{$consultationId} - all lab results are ready");
                            
                            // Create consultation charge when consultation is auto-completed
                            $this->createConsultationCharge($consultationId, $consultation['patient_id']);
                        }
                    }
                }
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
     * Check if patient is admitted
     * @param int $patientId Admin patient ID
     * @return bool
     */
    private function isPatientAdmitted($patientId)
    {
        if (empty($patientId)) {
            return false;
        }
        
        $db = \Config\Database::connect();
        
        // Check if patient has active admission
        if ($db->tableExists('admissions')) {
            $activeAdmission = $db->table('admissions')
                ->where('patient_id', $patientId)
                ->where('status', 'admitted')
                ->where('discharge_status', 'admitted')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            
            if ($activeAdmission) {
                return true;
            }
        }
        
        // Also check admin_patients table for visit_type = 'Admission' or 'ADMISSION'
        $patient = $this->patientModel->find($patientId);
        if ($patient) {
            $visitType = strtoupper(trim($patient['visit_type'] ?? ''));
            if ($visitType === 'ADMISSION') {
                return true;
            }
        }
        
        // Check patients table (HMS patients) for type = 'In-Patient' and visit_type = 'Admission'
        if ($db->tableExists('patients')) {
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                $patientType = $hmsPatient['type'] ?? '';
                $visitType = strtoupper(trim($hmsPatient['visit_type'] ?? ''));
                if ($patientType === 'In-Patient' && $visitType === 'ADMISSION') {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Create consultation charge when consultation is completed
     * @param int $consultationId
     * @param int $patientId (admin_patients.id)
     * @return bool
     */
    private function createConsultationCharge($consultationId, $patientId)
    {
        $db = \Config\Database::connect();
        
        // Check if charge already exists for this consultation to avoid duplicates
        $existingCharge = $db->table('charges')
            ->where('consultation_id', $consultationId)
            ->where('patient_id', $patientId)
            ->get()
            ->getRowArray();
        
        if ($existingCharge) {
            log_message('info', "Consultation charge already exists for Consultation #{$consultationId} - Charge ID: {$existingCharge['id']}");
            return true; // Charge already exists
        }
        
        if (!$db->tableExists('charges') || !$db->tableExists('billing_items')) {
            log_message('error', "Cannot create consultation charge - charges or billing_items table does not exist");
            return false;
        }
        
        try {
            // Consultation fee (default: 500 PHP, can be configured)
            $consultationFee = 500.00; // Default consultation fee
            
            // Generate charge number
            $chargeNumber = $this->chargeModel->generateChargeNumber();
            
            // Create charge record
            $chargeData = [
                'consultation_id' => $consultationId,
                'patient_id' => $patientId, // Use admin_patients.id
                'charge_number' => $chargeNumber,
                'total_amount' => $consultationFee,
                'status' => 'pending',
                'notes' => 'Consultation Fee - Consultation #' . $consultationId,
            ];
            
            if ($this->chargeModel->insert($chargeData)) {
                $chargeId = $this->chargeModel->getInsertID();
                
                // Create billing item for consultation fee
                $billingItemData = [
                    'charge_id' => $chargeId,
                    'item_type' => 'consultation',
                    'item_name' => 'Consultation Fee',
                    'description' => 'Doctor Consultation - Consultation #' . $consultationId,
                    'quantity' => 1.00,
                    'unit_price' => $consultationFee,
                    'total_price' => $consultationFee,
                    'related_id' => $consultationId,
                    'related_type' => 'consultation',
                ];
                
                if ($this->billingItemModel->insert($billingItemData)) {
                    log_message('info', "✅✅✅ Consultation charge created successfully - Charge ID: {$chargeId}, Consultation ID: {$consultationId}, Patient ID: {$patientId}, Amount: {$consultationFee}");
                    return true;
                } else {
                    log_message('error', "❌ Failed to create billing item for consultation charge - Charge ID: {$chargeId}");
                    return false;
                }
            } else {
                log_message('error', "❌ Failed to create consultation charge - Consultation ID: {$consultationId}, Patient ID: {$patientId}");
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "❌ Exception creating consultation charge: " . $e->getMessage());
            log_message('error', "Stack trace: " . $e->getTraceAsString());
            return false;
        }
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

