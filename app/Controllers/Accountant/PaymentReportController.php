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
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $db = \Config\Database::connect();

        // Get billing data from Receptionist (patient payments)
        $billingPayments = [];
        if ($db->tableExists('billing')) {
            $billingPayments = $db->table('billing')
                ->select('billing.*, admin_patients.firstname, admin_patients.lastname')
                ->join('admin_patients', 'admin_patients.id = billing.patient_id', 'left')
                ->where('billing.status', 'paid')
                ->orderBy('billing.updated_at', 'DESC')
                ->limit(20)
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Payment Reports',
            'name' => session()->get('name'),
            'payment_reports' => $this->paymentReportModel
                ->select('payment_reports.*, admin_patients.firstname, admin_patients.lastname, users.username as processed_by_name')
                ->join('admin_patients', 'admin_patients.id = payment_reports.patient_id', 'left')
                ->join('users', 'users.id = payment_reports.processed_by', 'left')
                ->orderBy('payment_reports.created_at', 'DESC')
                ->findAll(),
            'billing_payments' => $billingPayments, // Receptionist → Patient Payments
        ];

        return view('Accountant/payment_reports/index', $data);
    }

    public function create()
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
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
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
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
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
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
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
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
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
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
}

