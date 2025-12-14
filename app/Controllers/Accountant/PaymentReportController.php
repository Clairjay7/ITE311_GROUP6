<?php

namespace App\Controllers\Accountant;

use App\Controllers\BaseController;
use App\Models\PaymentReportModel;
use App\Models\AdminPatientModel;

class PaymentReportController extends BaseController
{
    protected $paymentReportModel;
    protected $patientModel;

    public function __construct()
    {
        $this->paymentReportModel = new PaymentReportModel();
        $this->patientModel = new AdminPatientModel();
    }

    public function index()
    {
        // Check if user is logged in - Allow Admin and Accountant
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Admin and Accountant can access payment reports.');
        }

        $db = \Config\Database::connect();

        // Get billing data from Receptionist (patient payments)
        $billingPayments = [];
        if ($db->tableExists('billing')) {
            $billingPayments = $db->table('billing')
                ->select('billing.*, admin_patients.firstname, admin_patients.lastname')
                ->join('admin_patients', 'admin_patients.id = billing.patient_id', 'left')
                ->where('billing.status', 'paid')
                ->where('billing.service !=', 'Medication Administration') // Exclude medication bills (they're in payment_reports)
                ->orderBy('billing.updated_at', 'DESC')
                ->limit(20)
                ->get()->getResultArray();
        }

        // Get payment reports including medication billing payments
        $paymentReports = $this->paymentReportModel
            ->select('payment_reports.*, admin_patients.firstname, admin_patients.lastname, 
                     users.username as processed_by_name, billing.service as billing_service,
                     billing.medicine_name, billing.invoice_number as billing_invoice')
            ->join('admin_patients', 'admin_patients.id = payment_reports.patient_id', 'left')
            ->join('users', 'users.id = payment_reports.processed_by', 'left')
            ->join('billing', 'billing.id = payment_reports.billing_id', 'left')
            ->orderBy('payment_reports.created_at', 'DESC')
            ->findAll();

        // Separate medication payments from other payments
        $medicationPayments = array_filter($paymentReports, function($report) {
            return !empty($report['billing_service']) && $report['billing_service'] === 'Medication Administration';
        });
        $otherPayments = array_filter($paymentReports, function($report) {
            return empty($report['billing_service']) || $report['billing_service'] !== 'Medication Administration';
        });

        $data = [
            'title' => 'Payment Reports',
            'name' => session()->get('name'),
            'payment_reports' => $paymentReports,
            'medication_payments' => $medicationPayments,
            'other_payments' => $otherPayments,
            'billing_payments' => $billingPayments, // Receptionist → Patient Payments (non-medication)
            'userRole' => $role,
        ];

        return view('Accountant/payment_reports/index', $data);
    }

    public function create()
    {
        // Check if user is logged in - Allow Admin and Accountant
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $data = [
            'title' => 'Create Payment Report',
            'name' => session()->get('name'),
            'patients' => $this->patientModel->findAll(),
        ];

        return view('Accountant/payment_reports/create', $data);
    }

    public function store()
    {
        // Check if user is logged in - Allow Admin and Accountant
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $rules = [
            'report_date' => 'required|valid_date',
            'payment_method' => 'required|in_list[cash,credit_card,debit_card,bank_transfer,check,insurance,other]',
            'amount' => 'required|decimal|greater_than[0]',
            'status' => 'required|in_list[pending,completed,failed,refunded]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'report_date' => $this->request->getPost('report_date'),
            'patient_id' => $this->request->getPost('patient_id') ?: null,
            'billing_id' => $this->request->getPost('billing_id') ?: null,
            'payment_method' => $this->request->getPost('payment_method'),
            'amount' => $this->request->getPost('amount'),
            'reference_number' => $this->request->getPost('reference_number'),
            'status' => $this->request->getPost('status'),
            'payment_date' => $this->request->getPost('payment_date') ?: date('Y-m-d H:i:s'),
            'processed_by' => session()->get('user_id'),
            'notes' => $this->request->getPost('notes'),
        ];

        if ($this->paymentReportModel->insert($data)) {
            return redirect()->to('/accounting/payments')->with('success', 'Payment report created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create payment report.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in - Allow Admin and Accountant
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $paymentReport = $this->paymentReportModel->find($id);

        if (!$paymentReport) {
            return redirect()->to('/accounting/payments')->with('error', 'Payment report not found.');
        }

        $data = [
            'title' => 'Edit Payment Report',
            'name' => session()->get('name'),
            'payment_report' => $paymentReport,
            'patients' => $this->patientModel->findAll(),
        ];

        return view('Accountant/payment_reports/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in - Allow Admin and Accountant
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $paymentReport = $this->paymentReportModel->find($id);

        if (!$paymentReport) {
            return redirect()->to('/accounting/payments')->with('error', 'Payment report not found.');
        }

        $rules = [
            'report_date' => 'required|valid_date',
            'payment_method' => 'required|in_list[cash,credit_card,debit_card,bank_transfer,check,insurance,other]',
            'amount' => 'required|decimal|greater_than[0]',
            'status' => 'required|in_list[pending,completed,failed,refunded]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'report_date' => $this->request->getPost('report_date'),
            'patient_id' => $this->request->getPost('patient_id') ?: null,
            'billing_id' => $this->request->getPost('billing_id') ?: null,
            'payment_method' => $this->request->getPost('payment_method'),
            'amount' => $this->request->getPost('amount'),
            'reference_number' => $this->request->getPost('reference_number'),
            'status' => $this->request->getPost('status'),
            'payment_date' => $this->request->getPost('payment_date') ?: date('Y-m-d H:i:s'),
            'notes' => $this->request->getPost('notes'),
        ];

        if ($this->paymentReportModel->update($id, $data)) {
            return redirect()->to('/accounting/payments')->with('success', 'Payment report updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update payment report.');
        }
    }

    public function delete($id)
    {
        // Check if user is logged in - Allow Admin and Accountant
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $paymentReport = $this->paymentReportModel->find($id);

        if (!$paymentReport) {
            return redirect()->to('/accounting/payments')->with('error', 'Payment report not found.');
        }

        if ($this->paymentReportModel->delete($id)) {
            return redirect()->to('/accounting/payments')->with('success', 'Payment report deleted successfully.');
        } else {
            return redirect()->to('/accounting/payments')->with('error', 'Failed to delete payment report.');
        }
    }

    /**
     * Patient Billing - View all bills for a specific patient
     */
    public function patientBilling()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $db = \Config\Database::connect();
        $patientId = $this->request->getGet('patient_id');

        $patients = $this->patientModel->findAll();
        $patientBills = [];
        $selectedPatient = null;
        $totalAmount = 0.0;
        $paidAmount = 0.0;
        $pendingAmount = 0.0;
        $patientInsuranceInfo = null; // Initialize insurance info variable

        if ($patientId) {
            $selectedPatient = $this->patientModel->find($patientId);
            
            // Get patient insurance information from patients table
            if ($selectedPatient && $db->tableExists('patients')) {
                // Try to match by name (firstname + lastname)
                $patientInsuranceInfo = $db->table('patients')
                    ->where('first_name', $selectedPatient['firstname'] ?? '')
                    ->where('last_name', $selectedPatient['lastname'] ?? '')
                    ->where('type', 'In-Patient')
                    ->orderBy('patient_id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
                
                // If not found, try to match by patient_id if available
                if (!$patientInsuranceInfo && !empty($selectedPatient['patient_id'])) {
                    $patientInsuranceInfo = $db->table('patients')
                        ->where('patient_id', $selectedPatient['patient_id'])
                        ->where('type', 'In-Patient')
                        ->orderBy('patient_id', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRowArray();
                }
            }
            
            if ($selectedPatient) {
                // Get all bills for this patient from billing table (including IV Fluid Administration)
                $patientBillsRaw = $db->table('billing')
                    ->where('patient_id', $patientId)
                    ->where('deleted_at', null) // Exclude soft-deleted records
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getResultArray();

                // Format bills to include medicine name in service description
                $patientBills = [];
                foreach ($patientBillsRaw as $bill) {
                    // Build service description with medicine name if available
                    $serviceDescription = $bill['service'] ?? 'Service';
                    if ($bill['service'] === 'Medication Administration' && !empty($bill['medicine_name'])) {
                        $serviceDescription = 'Medication Administration: ' . $bill['medicine_name'];
                        // Add dosage if available
                        if (!empty($bill['dosage'])) {
                            $serviceDescription .= ' (' . $bill['dosage'] . ')';
                        }
                    } elseif ($bill['service'] === 'IV Fluid Administration' && !empty($bill['order_description'])) {
                        // For IV Fluids, use order_description which contains fluid name
                        $serviceDescription = 'IV Fluid Administration: ' . $bill['order_description'];
                    } elseif ($bill['service'] === 'IV Fluid Administration' && !empty($bill['medicine_name'])) {
                        // Fallback if order_description is not available
                        $serviceDescription = 'IV Fluid Administration: ' . $bill['medicine_name'];
                    }
                    
                    $formattedBill = $bill;
                    $formattedBill['service'] = $serviceDescription;
                    $patientBills[] = $formattedBill;
                    
                    // Calculate totals
                    $totalAmount += (float)($bill['amount'] ?? 0);
                    if ($bill['status'] === 'paid') {
                        $paidAmount += (float)($bill['amount'] ?? 0);
                    } else {
                        $pendingAmount += (float)($bill['amount'] ?? 0);
                    }
                }
                
                // Get charges for this patient FIRST
                // Try to find charges by patient_id (admin_patients.id)
                // Also check if there are charges linked via surgeries table
                $patientCharges = $db->table('charges')
                    ->where('patient_id', $patientId)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                // Debug: Log all charges found
                log_message('info', "Patient Billing (Accountant): Found " . count($patientCharges) . " charges for patient_id={$patientId}");
                foreach ($patientCharges as $charge) {
                    log_message('info', "Charge ID: {$charge['id']}, Notes: " . ($charge['notes'] ?? 'N/A') . ", Amount: " . ($charge['total_amount'] ?? 0));
                }
                
                // Check if there are any room charges in the charges
                $hasRoomChargeInCharges = false;
                if (!empty($patientCharges) && $db->tableExists('billing_items')) {
                    foreach ($patientCharges as $charge) {
                        $roomChargeItems = $db->table('billing_items')
                            ->where('charge_id', $charge['id'])
                            ->where('item_type', 'room_charge')
                            ->get()
                            ->getResultArray();
                        if (!empty($roomChargeItems)) {
                            $hasRoomChargeInCharges = true;
                            break;
                        }
                    }
                }
                
                // Get room charges for admitted patients (current or discharged) - only if no room charge exists in charges
                if (!$hasRoomChargeInCharges && $db->tableExists('admissions') && $db->tableExists('rooms')) {
                    // Get any admission record for this patient (prioritize current admissions)
                    // First try to get admitted patients
                    $admission = $db->table('admissions')
                        ->where('patient_id', $patientId)
                        ->where('status', 'admitted')
                        ->orderBy('admission_date', 'DESC')
                        ->get()
                        ->getRowArray();
                    
                    // If no admitted, get any admission record
                    if (!$admission) {
                        $admission = $db->table('admissions')
                            ->where('patient_id', $patientId)
                            ->orderBy('admission_date', 'DESC')
                            ->get()
                            ->getRowArray();
                    }
                    
                    if ($admission && !empty($admission['room_id'])) {
                        $room = $db->table('rooms')
                            ->where('id', $admission['room_id'])
                            ->get()
                            ->getRowArray();
                        
                        if ($room && !empty($room['price'])) {
                            // Calculate days from admission date to today (or discharge date if discharged)
                            $admissionDate = new \DateTime($admission['admission_date']);
                            $endDate = !empty($admission['discharge_date']) 
                                ? new \DateTime($admission['discharge_date']) 
                                : new \DateTime();
                            
                            // Calculate number of days (including the day of admission)
                            $days = $admissionDate->diff($endDate)->days + 1;
                            
                            $roomPricePerDay = (float)($room['price'] ?? 0);
                            $totalRoomCharge = $roomPricePerDay * $days;
                            
                            // Check if room charge already exists in patientBills array (by checking service name or charge notes)
                            $roomChargeExists = false;
                            foreach ($patientBills as $key => $bill) {
                                $billService = $bill['service'] ?? '';
                                // Check if this bill is a room charge (from service name or charge notes)
                                if (stripos($billService, 'Room Charge') !== false || 
                                    stripos($billService, 'Room:') !== false) {
                                    $roomChargeExists = true;
                                    break;
                                }
                            }
                            
                            if (!$roomChargeExists && $totalRoomCharge > 0) {
                                // Add room charge to bills (fallback for legacy data)
                                $roomChargeBill = [
                                    'id' => 'ROOM-' . ($admission['id'] ?? $patientId),
                                    'type' => 'bill',
                                    'service' => 'Room Charge: ' . ($room['room_number'] ?? 'N/A') . ' (' . $days . ' day' . ($days > 1 ? 's' : '') . ')',
                                    'amount' => $totalRoomCharge,
                                    'status' => 'pending',
                                    'created_at' => $admission['admission_date'],
                                    'charge_number' => null,
                                ];
                                
                                $patientBills[] = $roomChargeBill;
                                $totalAmount += $totalRoomCharge;
                                $pendingAmount += $totalRoomCharge;
                            }
                        }
                    }
                }
                
                
                // Fallback: Also check charges by patient name (in case patient_id mismatch)
                // This helps find charges created with different patient_id but same patient
                $patientFirstName = $selectedPatient['firstname'] ?? '';
                $patientLastName = $selectedPatient['lastname'] ?? '';
                if (!empty($patientFirstName) && !empty($patientLastName)) {
                    // Get all charges with OR Room in notes or billing items
                    $allORCharges = $db->table('charges c')
                        ->select('c.*')
                        ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
                        ->where("(c.notes LIKE '%OR Room%' OR c.notes LIKE '%Operating Room%')")
                        ->where('ap.firstname', $patientFirstName)
                        ->where('ap.lastname', $patientLastName)
                        ->where('c.deleted_at', null)
                        ->get()
                        ->getResultArray();
                    
                    // Add any OR charges found by name that aren't already in the list
                    foreach ($allORCharges as $orCharge) {
                        $alreadyInList = false;
                        foreach ($patientCharges as $existingCharge) {
                            if ($existingCharge['id'] == $orCharge['id']) {
                                $alreadyInList = true;
                                break;
                            }
                        }
                        if (!$alreadyInList) {
                            log_message('info', "Patient Billing (Accountant): Found OR charge by name match - charge_id={$orCharge['id']}, patient_id in charge={$orCharge['patient_id']}, selected patient_id={$patientId}");
                            $patientCharges[] = $orCharge;
                        }
                    }
                }
                
                // Also check for charges linked via surgeries (in case patient_id mismatch)
                // Get surgeries for this patient and find related charges
                if ($db->tableExists('surgeries')) {
                    // First, try to find surgeries by patient_id
                    $surgeries = $db->table('surgeries')
                        ->where('patient_id', $patientId)
                        ->where('deleted_at', null)
                        ->get()
                        ->getResultArray();
                    
                    // Also try to find surgeries by patient name (in case patient_id is from patients table)
                    if (empty($surgeries) && !empty($patientFirstName) && !empty($patientLastName)) {
                        // Check if patient exists in patients table and find surgeries there
                        if ($db->tableExists('patients')) {
                            $hmsPatient = $db->table('patients')
                                ->where('first_name', $patientFirstName)
                                ->where('last_name', $patientLastName)
                                ->get()
                                ->getRowArray();
                            
                            if ($hmsPatient) {
                                $surgeries = $db->table('surgeries')
                                    ->where('patient_id', $hmsPatient['patient_id'])
                                    ->where('deleted_at', null)
                                    ->get()
                                    ->getResultArray();
                                log_message('info', "Patient Billing (Accountant): Found " . count($surgeries) . " surgeries for patient from patients table");
                            }
                        }
                    }
                    
                    log_message('info', "Patient Billing (Accountant): Found " . count($surgeries) . " surgeries for patient_id={$patientId}");
                    
                    foreach ($surgeries as $surgery) {
                        // Find charges linked to this surgery via billing_items
                        if ($db->tableExists('billing_items')) {
                            $surgeryBillingItems = $db->table('billing_items')
                                ->where('related_type', 'surgery')
                                ->where('related_id', $surgery['id'])
                                ->get()
                                ->getResultArray();
                            
                            log_message('info', "Patient Billing (Accountant): Found " . count($surgeryBillingItems) . " billing items for surgery_id={$surgery['id']}");
                            
                            foreach ($surgeryBillingItems as $item) {
                                // Get the charge for this billing item
                                $surgeryCharge = $db->table('charges')
                                    ->where('id', $item['charge_id'])
                                    ->where('deleted_at', null)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($surgeryCharge) {
                                    // Check if this charge is already in the list
                                    $alreadyInList = false;
                                    foreach ($patientCharges as $existingCharge) {
                                        if ($existingCharge['id'] == $surgeryCharge['id']) {
                                            $alreadyInList = true;
                                            break;
                                        }
                                    }
                                    
                                    if (!$alreadyInList) {
                                        log_message('info', "Patient Billing (Accountant): Found OR charge via surgery - charge_id={$surgeryCharge['id']}, patient_id in charge={$surgeryCharge['patient_id']}, selected patient_id={$patientId}, amount={$surgeryCharge['total_amount']}");
                                        $patientCharges[] = $surgeryCharge;
                                    }
                                } else {
                                    log_message('warning', "Patient Billing (Accountant): Billing item found but charge not found - charge_id={$item['charge_id']}");
                                }
                            }
                        }
                    }
                    
                    // Also directly query for OR charges that might be linked to any surgery for this patient
                    // This is a catch-all to find any OR charges
                    $allORChargesDirect = $db->table('charges')
                        ->where("(notes LIKE '%OR Room%' OR notes LIKE '%Operating Room%')")
                        ->where('deleted_at', null)
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    foreach ($allORChargesDirect as $orCharge) {
                        // Check if this charge has a billing item linked to a surgery for this patient
                        if ($db->tableExists('billing_items')) {
                            $chargeBillingItems = $db->table('billing_items')
                                ->where('charge_id', $orCharge['id'])
                                ->where('related_type', 'surgery')
                                ->get()
                                ->getResultArray();
                            
                            foreach ($chargeBillingItems as $bi) {
                                $relatedSurgery = $db->table('surgeries')
                                    ->where('id', $bi['related_id'])
                                    ->where('deleted_at', null)
                                    ->get()
                                    ->getRowArray();
                                
                                if ($relatedSurgery) {
                                    // Check if this surgery belongs to the selected patient
                                    $surgeryBelongsToPatient = false;
                                    
                                    // Check by patient_id
                                    if ($relatedSurgery['patient_id'] == $patientId) {
                                        $surgeryBelongsToPatient = true;
                                    }
                                    
                                    // Also check by patient name if patient_id doesn't match
                                    if (!$surgeryBelongsToPatient && !empty($patientFirstName) && !empty($patientLastName)) {
                                        // Check if surgery patient matches selected patient name
                                        if ($db->tableExists('admin_patients')) {
                                            $surgeryPatient = $db->table('admin_patients')
                                                ->where('id', $relatedSurgery['patient_id'])
                                                ->where('firstname', $patientFirstName)
                                                ->where('lastname', $patientLastName)
                                                ->where('deleted_at', null)
                                                ->get()
                                                ->getRowArray();
                                            if ($surgeryPatient) {
                                                $surgeryBelongsToPatient = true;
                                            }
                                        }
                                    }
                                    
                                    if ($surgeryBelongsToPatient) {
                                        // Check if already in list
                                        $alreadyInList = false;
                                        foreach ($patientCharges as $existingCharge) {
                                            if ($existingCharge['id'] == $orCharge['id']) {
                                                $alreadyInList = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$alreadyInList) {
                                            log_message('info', "Patient Billing (Accountant): Found OR charge via direct query - charge_id={$orCharge['id']}, patient_id in charge={$orCharge['patient_id']}, selected patient_id={$patientId}, amount={$orCharge['total_amount']}");
                                            $patientCharges[] = $orCharge;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Add charges to bills array with billing items details
                foreach ($patientCharges as $charge) {
                    // Get billing items for this charge to show detailed breakdown
                    $billingItems = [];
                    if ($db->tableExists('billing_items')) {
                        $billingItems = $db->table('billing_items')
                            ->where('charge_id', $charge['id'])
                            ->get()
                            ->getResultArray();
                        
                        // Debug: Log room charges
                        foreach ($billingItems as $item) {
                            if (($item['item_type'] ?? '') === 'room_charge') {
                                log_message('info', "Patient Billing (Accountant): Found room_charge item - charge_id={$charge['id']}, item_name={$item['item_name']}, amount={$charge['total_amount']}");
                            }
                        }
                    }
                    
                    // Build service description from billing items or use charge notes
                    $serviceDescription = $charge['notes'] ?? 'Charge';
                    
                    // Check if this is an OR charge (from notes or billing items)
                    $isORCharge = false;
                    if (stripos($charge['notes'] ?? '', 'OR Room') !== false || 
                        stripos($charge['notes'] ?? '', 'Operating Room') !== false) {
                        $isORCharge = true;
                    }
                    
                    if (!empty($billingItems)) {
                        $itemDescriptions = [];
                        foreach ($billingItems as $item) {
                            $itemName = $item['item_name'] ?? '';
                            $itemDesc = $item['description'] ?? $itemName;
                            $itemType = $item['item_type'] ?? '';
                            
                            if ($itemType === 'lab_test') {
                                $itemDescriptions[] = 'Lab Test: ' . $itemName;
                            } elseif ($itemType === 'room_charge') {
                                // Room charge items - display prominently
                                $itemDescriptions[] = $itemDesc;
                            } elseif ($itemType === 'procedure') {
                                // Check if it's OR Room charge
                                if (stripos($itemDesc, 'OR Room') !== false || 
                                    stripos($itemDesc, 'Operating Room') !== false ||
                                    stripos($itemName, 'OR Room') !== false) {
                                    $itemDescriptions[] = $itemDesc; // OR Room charges
                                    $isORCharge = true;
                                } else {
                                    $itemDescriptions[] = $itemDesc;
                                }
                            } else {
                                $itemDescriptions[] = $itemDesc;
                            }
                        }
                        if (!empty($itemDescriptions)) {
                            $serviceDescription = implode('; ', $itemDescriptions);
                        }
                    }
                    
                    // If charge notes contain "Room charge" but no billing items, use notes
                    if (stripos($charge['notes'] ?? '', 'Room charge') !== false && empty($billingItems)) {
                        $serviceDescription = $charge['notes'];
                    }
                    
                    // If it's an OR charge but no billing items, use the notes
                    if ($isORCharge && empty($billingItems)) {
                        $serviceDescription = $charge['notes'] ?? 'OR Room Charge';
                    }
                    
                    $billEntry = [
                        'id' => 'CHG-' . $charge['id'],
                        'type' => 'charge',
                        'service' => $serviceDescription,
                        'amount' => $charge['total_amount'] ?? 0,
                        'status' => $charge['status'] ?? 'pending',
                        'created_at' => $charge['created_at'] ?? date('Y-m-d H:i:s'),
                        'charge_number' => $charge['charge_number'] ?? null,
                        'billing_items' => $billingItems, // Include for detailed view if needed
                    ];
                    
                    $patientBills[] = $billEntry;
                    
                    // Debug: Log every charge being added
                    log_message('info', "Patient Billing (Accountant): Adding charge to bills - charge_id={$charge['id']}, service={$serviceDescription}, amount={$billEntry['amount']}, has_room_charge=" . (in_array('room_charge', array_column($billingItems, 'item_type')) ? 'YES' : 'NO'));
                    
                    $totalAmount += (float)($charge['total_amount'] ?? 0);
                    if ($charge['status'] === 'paid') {
                        $paidAmount += (float)($charge['total_amount'] ?? 0);
                    } else {
                        $pendingAmount += (float)($charge['total_amount'] ?? 0);
                    }
                }

                // Sort by date
                usort($patientBills, function($a, $b) {
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                });
            }
        }

        // Parse insurance information if available
        $availableInsurances = [];
        if ($patientInsuranceInfo) {
            $insuranceProviders = !empty($patientInsuranceInfo['insurance_provider']) 
                ? explode(', ', $patientInsuranceInfo['insurance_provider']) 
                : [];
            $insuranceNumbers = !empty($patientInsuranceInfo['insurance_number']) 
                ? $patientInsuranceInfo['insurance_number'] 
                : '';
            
            // Parse insurance numbers (format: "Provider1: Number1 | Provider2: Number2")
            $insuranceNumberPairs = [];
            if (!empty($insuranceNumbers)) {
                $pairs = explode(' | ', $insuranceNumbers);
                foreach ($pairs as $pair) {
                    if (strpos($pair, ':') !== false) {
                        list($provider, $number) = explode(':', $pair, 2);
                        $insuranceNumberPairs[trim($provider)] = trim($number);
                    }
                }
            }
            
            // Build available insurances array
            foreach ($insuranceProviders as $provider) {
                $provider = trim($provider);
                if (!empty($provider)) {
                    // Calculate used insurance amount for this provider
                    $usedAmount = 0;
                    if ($patientId && $db->tableExists('payment_reports')) {
                        $insurancePayments = $db->table('payment_reports')
                            ->where('patient_id', $patientId)
                            ->where('payment_method', 'insurance')
                            ->where('status', 'completed')
                            ->like('notes', '%Insurance: ' . $provider . '%')
                            ->get()
                            ->getResultArray();
                        
                        // Extract insurance amounts from notes
                        foreach ($insurancePayments as $payment) {
                            if (preg_match('/Insurance: ' . preg_quote($provider, '/') . ' \(Covered: ₱([\d,]+\.?\d*)\)/', $payment['notes'] ?? '', $matches)) {
                                $coveredAmount = str_replace(',', '', $matches[1]);
                                $usedAmount += floatval($coveredAmount);
                            }
                        }
                    }
                    
                    $availableInsurances[] = [
                        'provider' => $provider,
                        'number' => $insuranceNumberPairs[$provider] ?? '',
                        'used_amount' => $usedAmount
                    ];
                }
            }
        }
        
        $data = [
            'title' => 'Patient Billing',
            'patients' => $patients,
            'selectedPatient' => $selectedPatient,
            'patientBills' => $patientBills,
            'totalAmount' => $totalAmount,
            'paidAmount' => $paidAmount,
            'pendingAmount' => $pendingAmount,
            'patientId' => $patientId,
            'availableInsurances' => $availableInsurances,
            'patientInsuranceInfo' => $patientInsuranceInfo,
        ];

        return view('Accountant/patient_billing', $data);
    }

    /**
     * Process bill payment with insurance support
     */
    public function processBillPayment()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        $requestData = $this->request->getJSON(true);
        
        // Support both single bill and multiple bills
        $billIds = $requestData['bill_ids'] ?? (isset($requestData['bill_id']) ? [$requestData['bill_id']] : []);
        $billTypes = $requestData['bill_types'] ?? (isset($requestData['bill_type']) ? [$requestData['bill_type']] : ['billing']);
        $paymentMethod = $requestData['payment_method'] ?? 'cash';
        $insuranceProvider = $requestData['insurance_provider'] ?? null;
        $patientPaymentMethod = $requestData['patient_payment_method'] ?? null;
        $amount = $requestData['amount'] ?? 0;
        $totalAmount = $requestData['total_amount'] ?? 0;

        if (empty($billIds)) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Bill ID(s) is required']);
        }

        // Ensure billTypes array matches billIds array length
        while (count($billTypes) < count($billIds)) {
            $billTypes[] = 'billing';
        }

        // Insurance coverage percentages
        $insuranceCoverageRates = [
            'PhilHealth' => 80,
            'Maxicare' => 90,
            'Medicard' => 85,
            'Intellicare' => 90,
            'Pacific Cross' => 85,
            'Cocolife' => 85,
            'AXA' => 80,
            'Sun Life' => 85,
            'Pru Life UK' => 85,
            'Other' => 70,
        ];

        // Calculate insurance coverage if applicable
        $insuranceAmount = 0;
        $patientPays = $amount;
        if ($paymentMethod === 'insurance' && $insuranceProvider) {
            $coveragePercent = $insuranceCoverageRates[$insuranceProvider] ?? $insuranceCoverageRates['Other'];
            $insuranceAmount = $totalAmount * $coveragePercent / 100;
            $patientPays = $totalAmount - $insuranceAmount;
        }

        // Process all bills
        $processedCount = 0;
        $failedBills = [];
        $patientId = null;
        
        foreach ($billIds as $index => $billId) {
            $billType = $billTypes[$index] ?? 'billing';
            
            // Get bill from appropriate table
            if ($billType === 'charge') {
                $bill = $db->table('charges')->where('id', str_replace('CHG-', '', $billId))->get()->getRowArray();
                $tableName = 'charges';
            } else {
                $bill = $db->table('billing')->where('id', $billId)->get()->getRowArray();
                $tableName = 'billing';
            }
            
            if (!$bill) {
                $failedBills[] = $billId;
                continue;
            }
            
            if (($bill['status'] ?? 'pending') === 'paid') {
                continue; // Skip already paid bills
            }
            
            if (!$patientId) {
                $patientId = $bill['patient_id'] ?? null;
            }
            
            // Update bill status
            $updateData = [
                'status' => 'paid',
                'processed_by' => session()->get('user_id'),
                'paid_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            if ($db->table($tableName)->where('id', $billId)->update($updateData)) {
                $processedCount++;
            } else {
                $failedBills[] = $billId;
            }
        }
        
        // Create payment record for all bills
        if ($processedCount > 0 && $db->tableExists('payment_reports') && $patientId) {
            $paymentData = [
                'report_date' => date('Y-m-d'),
                'patient_id' => $patientId,
                'billing_id' => implode(',', $billIds), // Store all bill IDs
                'payment_method' => $paymentMethod,
                'amount' => $patientPays,
                'reference_number' => 'BILLS-' . implode('-', $billIds),
                'status' => 'completed',
                'payment_date' => date('Y-m-d H:i:s'),
                'processed_by' => session()->get('user_id'),
                'notes' => 'Payment for ' . count($billIds) . ' bill(s)' . 
                          ($insuranceProvider ? ' | Insurance: ' . $insuranceProvider . ' (Covered: ₱' . number_format($insuranceAmount, 2) . ')' : ''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $this->paymentReportModel->skipValidation(true);
            $this->paymentReportModel->insert($paymentData);
            $this->paymentReportModel->skipValidation(false);
        }
        
        if ($processedCount > 0) {
            $message = "Payment processed successfully for {$processedCount} bill(s).";
            if (!empty($failedBills)) {
                $message .= " Failed to process " . count($failedBills) . " bill(s).";
            }
            
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => $message,
                    'insurance_amount' => $insuranceAmount,
                    'patient_pays' => $patientPays,
                    'processed_count' => $processedCount,
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Failed to process any bills']);
        }
    }
}

