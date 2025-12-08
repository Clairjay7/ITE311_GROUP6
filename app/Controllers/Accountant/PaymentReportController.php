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

        if ($patientId) {
            $selectedPatient = $this->patientModel->find($patientId);
            
            if ($selectedPatient) {
                // Get all bills for this patient from billing table
                $patientBills = $db->table('billing')
                    ->where('patient_id', $patientId)
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getResultArray();

                // Calculate totals
                foreach ($patientBills as $bill) {
                    $totalAmount += (float)($bill['amount'] ?? 0);
                    if ($bill['status'] === 'paid') {
                        $paidAmount += (float)($bill['amount'] ?? 0);
                    } else {
                        $pendingAmount += (float)($bill['amount'] ?? 0);
                    }
                }

                // Also get charges for this patient
                $patientCharges = $db->table('charges')
                    ->where('patient_id', $patientId)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getResultArray();

                // Add charges to bills array
                foreach ($patientCharges as $charge) {
                    $patientBills[] = [
                        'id' => 'CHG-' . $charge['id'],
                        'type' => 'charge',
                        'service' => $charge['notes'] ?? 'Charge',
                        'amount' => $charge['total_amount'] ?? 0,
                        'status' => $charge['status'] ?? 'pending',
                        'created_at' => $charge['created_at'] ?? date('Y-m-d H:i:s'),
                        'charge_number' => $charge['charge_number'] ?? null,
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

        $data = [
            'title' => 'Patient Billing',
            'patients' => $patients,
            'selectedPatient' => $selectedPatient,
            'patientBills' => $patientBills,
            'totalAmount' => $totalAmount,
            'paidAmount' => $paidAmount,
            'pendingAmount' => $pendingAmount,
            'patientId' => $patientId,
        ];

        return view('Accountant/patient_billing', $data);
    }
}

