<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use App\Models\DoctorOrderModel;
use App\Models\AdminPatientModel;
use App\Models\PatientVitalModel;
use App\Models\LabRequestModel;
use App\Models\LabStatusHistoryModel;
use App\Models\DoctorNotificationModel;

class AdmissionOrdersController extends BaseController
{
    /**
     * List all admitted patients for the doctor
     */
    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get all active admissions where this doctor is the attending physician
        $admittedPatients = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.contact, ap.birthdate, ap.gender,
                     r.room_number, r.ward, r.room_type,
                     c.consultation_date, c.diagnosis, c.observations,
                     (SELECT COUNT(*) FROM doctor_orders WHERE admission_id = a.id AND status != "completed" AND status != "cancelled") as pending_orders_count')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('consultations c', 'c.id = a.consultation_id', 'left')
            ->where('a.attending_physician_id', $doctorId)
            ->where('a.status', 'admitted')
            ->where('a.discharge_status', 'admitted')
            ->where('a.deleted_at', null)
            ->orderBy('a.admission_date', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Admitted Patients',
            'admittedPatients' => $admittedPatients,
        ];

        return view('doctor/admission_orders/index', $data);
    }

    /**
     * View admission details and create orders
     */
    public function view($admissionId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get admission details
        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.contact, ap.birthdate, ap.gender,
                     r.room_number, r.ward, r.room_type,
                     c.consultation_date, c.diagnosis, c.observations, c.notes as consultation_notes')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('consultations c', 'c.id = a.consultation_id', 'left')
            ->where('a.id', $admissionId)
            ->where('a.attending_physician_id', $doctorId)
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->to('/doctor/admission-orders')->with('error', 'Admission not found.');
        }

        // Get latest vitals
        $latestVitals = $db->table('patient_vitals')
            ->where('patient_id', $admission['patient_id'])
            ->orderBy('recorded_at', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // Get existing orders for this admission
        $existingOrders = $db->table('doctor_orders')
            ->where('admission_id', $admissionId)
            ->where('status !=', 'cancelled')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Group orders by type
        $ordersByType = [
            'medication' => [],
            'lab_test' => [],
            'procedure' => [],
            'diet' => [],
            'activity' => [],
            'other' => [],
        ];

        foreach ($existingOrders as $order) {
            $ordersByType[$order['order_type']][] = $order;
        }
        
        // Get completed lab results for this patient
        $labResults = [];
        if ($db->tableExists('lab_requests') && $db->tableExists('lab_results')) {
            $labResults = $db->table('lab_requests lr')
                ->select('lr.*, lr_result.result, lr_result.interpretation, lr_result.result_file, 
                         lr_result.completed_at, lr_result.completed_by,
                         completed_by_user.username as completed_by_name')
                ->join('lab_results lr_result', 'lr_result.lab_request_id = lr.id', 'left')
                ->join('users completed_by_user', 'completed_by_user.id = lr_result.completed_by', 'left')
                ->where('lr.patient_id', $admission['patient_id'])
                ->where('lr.doctor_id', $doctorId)
                ->where('lr.status', 'completed')
                ->orderBy('lr_result.completed_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Admission Orders - ' . $admission['firstname'] . ' ' . $admission['lastname'],
            'admission' => $admission,
            'latestVitals' => $latestVitals,
            'existingOrders' => $existingOrders,
            'ordersByType' => $ordersByType,
            'labResults' => $labResults,
        ];

        return view('doctor/admission_orders/view', $data);
    }

    /**
     * Create admission orders (comprehensive form)
     */
    public function create($admissionId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get admission details
        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.contact, ap.birthdate, ap.gender,
                     r.room_number, r.ward,
                     c.diagnosis, c.observations')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('consultations c', 'c.id = a.consultation_id', 'left')
            ->where('a.id', $admissionId)
            ->where('a.attending_physician_id', $doctorId)
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->to('/doctor/admission-orders')->with('error', 'Admission not found.');
        }

        // Get all available medicines from pharmacy (quantity > 0)
        $medicines = [];
        if ($db->tableExists('pharmacy')) {
            $medicines = $db->table('pharmacy')
                ->where('quantity >', 0)
                ->where('deleted_at', null)
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get all active lab tests grouped by category
        $labTests = [];
        if ($db->tableExists('lab_tests')) {
            $labTestModel = new \App\Models\LabTestModel();
            $labTests = $labTestModel->getActiveTestsGroupedByCategory();
        }

        // Get all active nurses
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Create Admission Orders',
            'admission' => $admission,
            'medicines' => $medicines,
            'labTests' => $labTests,
            'nurses' => $nurses,
        ];

        return view('doctor/admission_orders/create', $data);
    }

    /**
     * Store admission orders
     */
    public function store()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        $orderModel = new DoctorOrderModel();

        $admissionId = $this->request->getPost('admission_id');
        $patientId = $this->request->getPost('patient_id');

        // Verify admission belongs to this doctor
        $admission = $db->table('admissions')
            ->where('id', $admissionId)
            ->where('attending_physician_id', $doctorId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->back()->with('error', 'Admission not found or access denied.');
        }

        // Get assigned nurse (required only if there are orders to assign)
        $nurseId = $this->request->getPost('nurse_id');
        
        // Check if there's at least one order to create
        $hasOrders = false;
        $hasOrders = $hasOrders || !empty($this->request->getPost('treatment_plan'));
        $hasOrders = $hasOrders || !empty($this->request->getPost('medications'));
        $hasOrders = $hasOrders || !empty($this->request->getPost('lab_tests'));
        $hasOrders = $hasOrders || !empty($this->request->getPost('procedures'));
        $hasOrders = $hasOrders || !empty($this->request->getPost('nursing_care'));
        $hasOrders = $hasOrders || !empty($this->request->getPost('diet'));
        $hasOrders = $hasOrders || !empty($this->request->getPost('activity'));
        $hasOrders = $hasOrders || !empty($this->request->getPost('admitting_notes'));
        
        if ($hasOrders && empty($nurseId)) {
            return redirect()->back()->withInput()->with('error', 'Please select an assigned nurse when creating orders.');
        }
        
        // Verify nurse exists and is active (only if nurse is provided)
        if (!empty($nurseId)) {
            $nurse = $db->table('users')
                ->select('users.id, users.username, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.id', $nurseId)
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->get()
                ->getRowArray();

            if (!$nurse) {
                return redirect()->back()->withInput()->with('error', 'Invalid nurse selected. Please select a valid active nurse.');
            }
        }
        
        // If no orders are provided, return error
        if (!$hasOrders) {
            return redirect()->back()->withInput()->with('error', 'Please fill at least one order section (Treatment Plan, Medications, Lab Tests, Procedures, Nursing Care, Diet, Activity, or Admitting Notes).');
        }

        $db->transStart();

        try {
            // Process Treatment Plan (stored as 'other' order type)
            $treatmentPlan = $this->request->getPost('treatment_plan');
            if (!empty($treatmentPlan) && trim($treatmentPlan) !== '') {
                $orderModel->insert([
                    'patient_id' => $patientId,
                    'admission_id' => $admissionId,
                    'doctor_id' => $doctorId,
                    'nurse_id' => $nurseId ?? null,
                    'order_type' => 'other',
                    'order_description' => 'Admission Treatment Plan',
                    'instructions' => $treatmentPlan,
                    'status' => 'pending',
                ]);
            }

            // Process Medications
            $medications = $this->request->getPost('medications');
            if (!empty($medications) && is_array($medications)) {
                foreach ($medications as $med) {
                    // Only insert if medicine_name is provided
                    if (!empty($med['medicine_name']) && trim($med['medicine_name']) !== '') {
                        $orderModel->insert([
                            'patient_id' => $patientId,
                            'admission_id' => $admissionId,
                            'doctor_id' => $doctorId,
                            'nurse_id' => $nurseId ?? null,
                            'order_type' => 'medication',
                            'medicine_name' => $med['medicine_name'],
                            'dosage' => $med['dosage'] ?? '',
                            'frequency' => $med['frequency'] ?? '',
                            'duration' => $med['duration'] ?? '',
                            'instructions' => $med['instructions'] ?? '',
                            'order_description' => $med['medicine_name'] . ' - ' . ($med['dosage'] ?? ''),
                            'status' => 'pending',
                            'pharmacy_status' => 'pending',
                        ]);
                    }
                }
            }

            // Process Lab Requests - Create both doctor_order AND lab_request
            $labTests = $this->request->getPost('lab_tests');
            $labRequestModel = new LabRequestModel();
            $labStatusHistoryModel = new LabStatusHistoryModel();
            
            if (!empty($labTests) && is_array($labTests)) {
                foreach ($labTests as $lab) {
                    // Only insert if test_name is provided
                    if (!empty($lab['test_name']) && trim($lab['test_name']) !== '') {
                        // Get test_type from lab_tests table
                        $testType = '';
                        if ($db->tableExists('lab_tests')) {
                            $labTestInfo = $db->table('lab_tests')
                                ->where('test_name', $lab['test_name'])
                                ->where('is_active', 1)
                                ->get()
                                ->getRowArray();
                            if ($labTestInfo) {
                                $testType = $labTestInfo['test_type'] ?? '';
                            }
                        }
                        
                        // If test_type not found, try to get from form or use default
                        if (empty($testType)) {
                            $testType = $lab['test_type'] ?? 'Laboratory';
                        }
                        
                        // Create doctor_order (for tracking in orders list)
                        $orderModel->insert([
                            'patient_id' => $patientId,
                            'admission_id' => $admissionId,
                            'doctor_id' => $doctorId,
                            'nurse_id' => $nurseId ?? null,
                            'order_type' => 'lab_test',
                            'order_description' => $lab['test_name'],
                            'instructions' => $lab['instructions'] ?? '',
                            'remarks' => $lab['remarks'] ?? '',
                            'status' => 'pending',
                        ]);
                        $doctorOrderId = $orderModel->getInsertID();
                        
                        // Create lab_request directly (bypasses nurse approval - goes straight to lab staff)
                        $labRequestData = [
                            'patient_id' => $patientId,
                            'doctor_id' => $doctorId,
                            'nurse_id' => $nurseId ?? null, // Nurse can prepare patient but doesn't approve
                            'test_type' => $testType,
                            'test_name' => $lab['test_name'],
                            'requested_by' => 'doctor',
                            'priority' => $lab['priority'] ?? 'routine',
                            'instructions' => $lab['instructions'] ?? '',
                            'status' => 'pending', // Directly visible to lab staff
                            'requested_date' => date('Y-m-d'),
                        ];
                        
                        if ($labRequestModel->insert($labRequestData)) {
                            $labRequestId = $labRequestModel->getInsertID();
                            
                            // Store doctor_order_id in lab_request for linking (if column exists)
                            // If not, we'll use the test_name and patient_id to find it later
                            // Store in instructions or remarks field as JSON for linking
                            $linkingInfo = json_encode([
                                'doctor_order_id' => $doctorOrderId,
                                'admission_id' => $admissionId
                            ]);
                            $labRequestModel->update($labRequestId, [
                                'instructions' => ($lab['instructions'] ?? '') . ' | LINK:' . $linkingInfo
                            ]);
                            
                            // Log status change
                            $labStatusHistoryModel->insert([
                                'lab_request_id' => $labRequestId,
                                'status' => 'pending',
                                'changed_by' => $doctorId,
                                'notes' => 'Lab request created by doctor - direct to lab staff',
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                            
                            // Notify all lab staff (if notification system exists)
                            if ($db->tableExists('users') && $db->tableExists('roles')) {
                                $labStaff = $db->table('users')
                                    ->select('users.id')
                                    ->join('roles', 'roles.id = users.role_id', 'left')
                                    ->where('LOWER(roles.name)', 'labstaff')
                                    ->where('users.status', 'active')
                                    ->get()
                                    ->getResultArray();
                                
                                $patientModel = new AdminPatientModel();
                                $patient = $patientModel->find($patientId);
                                $patientName = ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient');
                                
                                // Create notification for each lab staff member
                                // Note: If there's a lab_staff_notifications table, use it. Otherwise, we'll add it to dashboard.
                                foreach ($labStaff as $staff) {
                                    // For now, lab staff will see it in their dashboard
                                    // You can create a lab_staff_notifications table later if needed
                                }
                            }
                        }
                    }
                }
            }

            // Process Procedures/Imaging
            $procedures = $this->request->getPost('procedures');
            if (!empty($procedures) && is_array($procedures)) {
                foreach ($procedures as $proc) {
                    // Only insert if procedure_name is provided
                    if (!empty($proc['procedure_name']) && trim($proc['procedure_name']) !== '') {
                        $orderModel->insert([
                            'patient_id' => $patientId,
                            'admission_id' => $admissionId,
                            'doctor_id' => $doctorId,
                            'nurse_id' => $nurseId ?? null,
                            'order_type' => 'procedure',
                            'order_description' => $proc['procedure_name'],
                            'instructions' => $proc['instructions'] ?? '',
                            'remarks' => $proc['remarks'] ?? '',
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            // Process Nursing Care Instructions
            $nursingCare = $this->request->getPost('nursing_care');
            if (!empty($nursingCare) && trim($nursingCare) !== '') {
                $orderModel->insert([
                    'patient_id' => $patientId,
                    'admission_id' => $admissionId,
                    'doctor_id' => $doctorId,
                    'nurse_id' => $nurseId ?? null,
                    'order_type' => 'nursing_care',
                    'order_description' => 'Nursing Care Instructions',
                    'instructions' => $nursingCare,
                    'status' => 'pending',
                ]);
            }

            // Process Diet Orders
            $diet = $this->request->getPost('diet');
            if (!empty($diet) && trim($diet) !== '') {
                $orderModel->insert([
                    'patient_id' => $patientId,
                    'admission_id' => $admissionId,
                    'doctor_id' => $doctorId,
                    'nurse_id' => $nurseId ?? null,
                    'order_type' => 'diet',
                    'order_description' => 'Diet Order',
                    'instructions' => $diet,
                    'status' => 'pending',
                ]);
            }

            // Process Activity Orders
            $activity = $this->request->getPost('activity');
            if (!empty($activity) && trim($activity) !== '') {
                $orderModel->insert([
                    'patient_id' => $patientId,
                    'admission_id' => $admissionId,
                    'doctor_id' => $doctorId,
                    'nurse_id' => $nurseId ?? null,
                    'order_type' => 'activity',
                    'order_description' => 'Activity Order',
                    'instructions' => $activity,
                    'status' => 'pending',
                ]);
            }

            // Process Admitting Notes
            $admittingNotes = $this->request->getPost('admitting_notes');
            if (!empty($admittingNotes) && trim($admittingNotes) !== '') {
                $orderModel->insert([
                    'patient_id' => $patientId,
                    'admission_id' => $admissionId,
                    'doctor_id' => $doctorId,
                    'nurse_id' => $nurseId ?? null,
                    'order_type' => 'other',
                    'order_description' => 'Admitting Notes',
                    'instructions' => $admittingNotes,
                    'status' => 'pending',
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Failed to save admission orders.');
            }

            // Success message
            $successMessage = 'Admission orders created successfully.';
            if (!empty($labTests) && is_array($labTests)) {
                $labCount = 0;
                foreach ($labTests as $lab) {
                    if (!empty($lab['test_name']) && trim($lab['test_name']) !== '') {
                        $labCount++;
                    }
                }
                if ($labCount > 0) {
                    $successMessage .= ' ' . $labCount . ' lab test(s) sent directly to laboratory staff.';
                }
            }

            return redirect()->to('/doctor/admission-orders/view/' . $admissionId)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to create admission orders: ' . $e->getMessage());
        }
    }
}

