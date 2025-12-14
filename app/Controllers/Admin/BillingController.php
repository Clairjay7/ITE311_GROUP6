<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;

class BillingController extends BaseController
{
    protected $patientModel;
    protected $chargeModel;
    protected $billingItemModel;

    public function __construct()
    {
        $this->patientModel = new AdminPatientModel();
        $this->chargeModel = new ChargeModel();
        $this->billingItemModel = new BillingItemModel();
    }

    /**
     * Index method - redirects to patient billing page
     */
    public function index()
    {
        return redirect()->to('/admin/billing/patient_billing');
    }

    public function patientBilling()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
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

                // Also get charges for this patient
                // Try to find charges by patient_id (admin_patients.id)
                // Also check if there are charges linked via surgeries table
                $patientCharges = $db->table('charges')
                    ->where('patient_id', $patientId)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                // Debug: Log charge query
                log_message('info', "Patient Billing: Querying charges for patient_id={$patientId}, found " . count($patientCharges) . " charges");
                
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
                            log_message('info', "Patient Billing: Found OR charge by name match - charge_id={$orCharge['id']}, patient_id in charge={$orCharge['patient_id']}, selected patient_id={$patientId}");
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
                                log_message('info', "Patient Billing: Found " . count($surgeries) . " surgeries for patient from patients table");
                            }
                        }
                    }
                    
                    log_message('info', "Patient Billing: Found " . count($surgeries) . " surgeries for patient_id={$patientId}");
                    
                    foreach ($surgeries as $surgery) {
                        // Find charges linked to this surgery via billing_items
                        if ($db->tableExists('billing_items')) {
                            $surgeryBillingItems = $db->table('billing_items')
                                ->where('related_type', 'surgery')
                                ->where('related_id', $surgery['id'])
                                ->get()
                                ->getResultArray();
                            
                            log_message('info', "Patient Billing: Found " . count($surgeryBillingItems) . " billing items for surgery_id={$surgery['id']}");
                            
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
                                        log_message('info', "Patient Billing: Found OR charge via surgery - charge_id={$surgeryCharge['id']}, patient_id in charge={$surgeryCharge['patient_id']}, selected patient_id={$patientId}, amount={$surgeryCharge['total_amount']}");
                                        $patientCharges[] = $surgeryCharge;
                                    }
                                } else {
                                    log_message('warning', "Patient Billing: Billing item found but charge not found - charge_id={$item['charge_id']}");
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
                                            log_message('info', "Patient Billing: Found OR charge via direct query - charge_id={$orCharge['id']}, patient_id in charge={$orCharge['patient_id']}, selected patient_id={$patientId}, amount={$orCharge['total_amount']}");
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
                    
                    $patientBills[] = [
                        'id' => 'CHG-' . $charge['id'],
                        'type' => 'charge',
                        'service' => $serviceDescription,
                        'amount' => $charge['total_amount'] ?? 0,
                        'status' => $charge['status'] ?? 'pending',
                        'created_at' => $charge['created_at'] ?? date('Y-m-d H:i:s'),
                        'charge_number' => $charge['charge_number'] ?? null,
                        'billing_items' => $billingItems, // Include for detailed view if needed
                    ];
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

        return view('admin/billing/patient_billing', $data);
    }

    /**
     * Process bill payment with insurance support
     */
    public function processBillPayment()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
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
            $paymentReportModel = new \App\Models\PaymentReportModel();
            $paymentNotes = 'Payment for ' . count($billIds) . ' bill(s)';
            if ($insuranceProvider) {
                $paymentNotes .= ' | Insurance: ' . $insuranceProvider . ' (Covered: ₱' . number_format($insuranceAmount, 2) . ')';
            }
            if ($patientPaymentMethod && $patientPays > 0) {
                $paymentNotes .= ' | Patient Payment: ' . ucfirst(str_replace('_', ' ', $patientPaymentMethod)) . ' (₱' . number_format($patientPays, 2) . ')';
            }
            
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
                'notes' => $paymentNotes,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $paymentReportModel->skipValidation(true);
            $paymentReportModel->insert($paymentData);
            $paymentReportModel->skipValidation(false);
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
    
    /**
     * Debug method to check OR charges for a patient
     * This helps verify if charges exist and why they might not be showing
     */
    public function debugORCharges()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }
        
        $patientId = $this->request->getGet('patient_id');
        if (!$patientId) {
            return $this->response->setJSON(['error' => 'patient_id required']);
        }
        
        $db = \Config\Database::connect();
        $patient = $this->patientModel->find($patientId);
        
        if (!$patient) {
            return $this->response->setJSON(['error' => 'Patient not found']);
        }
        
        $debug = [
            'patient' => [
                'id' => $patient['id'],
                'name' => ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''),
            ],
            'direct_charges' => [],
            'charges_by_name' => [],
            'surgeries' => [],
            'charges_via_surgeries' => [],
        ];
        
        // 1. Direct charges by patient_id
        $directCharges = $db->table('charges')
            ->where('patient_id', $patientId)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();
        $debug['direct_charges'] = $directCharges;
        
        // 2. Charges by patient name
        $chargesByName = $db->table('charges c')
            ->select('c.*')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->where("(c.notes LIKE '%OR Room%' OR c.notes LIKE '%Operating Room%')")
            ->where('ap.firstname', $patient['firstname'] ?? '')
            ->where('ap.lastname', $patient['lastname'] ?? '')
            ->where('c.deleted_at', null)
            ->get()
            ->getResultArray();
        $debug['charges_by_name'] = $chargesByName;
        
        // 3. Surgeries for this patient
        if ($db->tableExists('surgeries')) {
            $surgeries = $db->table('surgeries')
                ->where('patient_id', $patientId)
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();
            $debug['surgeries'] = $surgeries;
            
            // 4. Charges via surgeries
            foreach ($surgeries as $surgery) {
                if ($db->tableExists('billing_items')) {
                    $billingItems = $db->table('billing_items')
                        ->where('related_type', 'surgery')
                        ->where('related_id', $surgery['id'])
                        ->get()
                        ->getResultArray();
                    
                    foreach ($billingItems as $item) {
                        $charge = $db->table('charges')
                            ->where('id', $item['charge_id'])
                            ->where('deleted_at', null)
                            ->get()
                            ->getRowArray();
                        if ($charge) {
                            $debug['charges_via_surgeries'][] = [
                                'surgery_id' => $surgery['id'],
                                'billing_item' => $item,
                                'charge' => $charge,
                            ];
                        }
                    }
                }
            }
        }
        
        return $this->response->setJSON($debug);
    }

    // ========== CHARGES METHODS ==========

    public function charges()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        // Get charges with patient and doctor information
        // Include lab test charges and OR charges (which don't have consultation_id)
        $charges = $db->table('charges c')
            ->select('c.*, ap.firstname, ap.lastname, ap.contact, 
                     u.username as doctor_name,
                     COUNT(bi.id) as item_count,
                     MAX(CASE WHEN bi.item_type = "lab_test" THEN 1 ELSE 0 END) as has_lab_test,
                     MAX(CASE WHEN bi.item_type = "procedure" THEN 1 ELSE 0 END) as has_procedure,
                     MAX(CASE WHEN bi.item_type = "lab_test" THEN bi.item_name ELSE NULL END) as test_name,
                     MAX(CASE WHEN bi.item_type = "lab_test" THEN bi.description ELSE NULL END) as test_description,
                     MAX(CASE WHEN bi.item_type = "procedure" THEN bi.description ELSE NULL END) as procedure_description,
                     lr.nurse_id as lab_nurse_id,
                     lr.instructions as lab_instructions,
                     users_nurse.username as nurse_name')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->join('consultations con', 'con.id = c.consultation_id', 'left')
            ->join('users u', 'u.id = con.doctor_id', 'left')
            ->join('billing_items bi', 'bi.charge_id = c.id', 'left')
            ->join('lab_requests lr', 'lr.charge_id = c.id', 'left')
            ->join('users as users_nurse', 'users_nurse.id = lr.nurse_id', 'left')
            ->where('c.deleted_at', null)
            ->groupBy('c.id')
            ->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Filter by status if requested
        $status = $this->request->getGet('status');
        if ($status && in_array($status, ['pending', 'approved', 'paid', 'cancelled'])) {
            $charges = array_filter($charges, fn($charge) => $charge['status'] === $status);
        }

        $data = [
            'title' => 'Pending Charges',
            'charges' => $charges,
            'currentStatus' => $status ?? 'all',
        ];

        return view('admin/billing/charges/index', $data);
    }

    public function chargesView($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        $charge = $db->table('charges c')
            ->select('c.*, ap.*, u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->join('consultations con', 'con.id = c.consultation_id', 'left')
            ->join('users u', 'u.id = con.doctor_id', 'left')
            ->where('c.id', $id)
            ->where('c.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$charge) {
            return redirect()->to('/admin/billing/charges')->with('error', 'Charge not found.');
        }

        // Get billing items
        $billingItems = $this->billingItemModel
            ->where('charge_id', $id)
            ->findAll();

        $data = [
            'title' => 'Charge Details - ' . $charge['charge_number'],
            'charge' => $charge,
            'billingItems' => $billingItems,
        ];

        return view('admin/billing/charges/view', $data);
    }

    public function chargesInvoice($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        $charge = $db->table('charges c')
            ->select('c.*, ap.*, u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->join('consultations con', 'con.id = c.consultation_id', 'left')
            ->join('users u', 'u.id = con.doctor_id', 'left')
            ->where('c.id', $id)
            ->where('c.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$charge) {
            return redirect()->to('/admin/billing/charges')->with('error', 'Charge not found.');
        }

        // Get billing items
        $billingItems = $this->billingItemModel
            ->where('charge_id', $id)
            ->findAll();

        $data = [
            'title' => 'Invoice - ' . $charge['charge_number'],
            'charge' => $charge,
            'billingItems' => $billingItems,
        ];

        return view('admin/billing/charges/invoice', $data);
    }

    public function chargesApprove($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $charge = $this->chargeModel->find($id);

        if (!$charge) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Charge not found']);
        }

        if ($charge['status'] !== 'pending') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Only pending charges can be approved']);
        }

        if ($this->chargeModel->update($id, ['status' => 'approved'])) {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Charge approved successfully.'
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to approve charge'
                ]);
        }
    }

    public function chargesProcessPayment($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $charge = $this->chargeModel->find($id);

        if (!$charge) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Charge not found']);
        }

        if ($charge['status'] === 'paid') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'This charge has already been paid']);
        }

        $userId = session()->get('user_id');

        // Update charge status to paid
        $updateData = [
            'status' => 'paid',
            'processed_by' => $userId,
            'paid_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->chargeModel->update($id, $updateData)) {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Payment processed successfully. Receipt can be printed.',
                    'charge_id' => $id
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to process payment'
                ]);
        }
    }

    public function chargesCancel($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $charge = $this->chargeModel->find($id);

        if (!$charge) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Charge not found']);
        }

        if ($charge['status'] === 'paid') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Cannot cancel a paid charge']);
        }

        if ($this->chargeModel->update($id, ['status' => 'cancelled'])) {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Charge cancelled successfully'
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to cancel charge'
                ]);
        }
    }
}
