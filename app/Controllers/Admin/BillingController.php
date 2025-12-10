<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminPatientModel;
use App\Models\FinanceOverviewModel;
use App\Models\PaymentReportModel;
use App\Models\ExpenseModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;
use App\Models\DischargeOrderModel;

class BillingController extends BaseController
{
    protected $billingModel;
    protected $patientModel;
    protected $financeOverviewModel;
    protected $paymentReportModel;
    protected $expenseModel;
    protected $chargeModel;
    protected $billingItemModel;
    protected $dischargeOrderModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->billingModel = new BillingModel();
        $this->patientModel = new AdminPatientModel();
        $this->financeOverviewModel = new FinanceOverviewModel();
        $this->paymentReportModel = new PaymentReportModel();
        $this->expenseModel = new ExpenseModel();
        $this->chargeModel = new ChargeModel();
        $this->billingItemModel = new BillingItemModel();
        $this->dischargeOrderModel = new DischargeOrderModel();
    }

    public function index()
    {
        // Redirect to dashboard instead of simple billing list
        return redirect()->to('/admin/billing/dashboard');
    }

    /**
     * Billing Dashboard
     */
    public function dashboard()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Get basic stats for initial display
        $todayRevenue = 0.0;
        $pendingBills = 0;
        $outstandingBalance = 0.0;

        if ($db->tableExists('billing')) {
            $revenue = $db->table('billing')
                ->selectSum('amount', 'sum')
                ->where('status', 'paid')
                ->where('DATE(created_at)', $today)
                ->get()->getRow();
            $todayRevenue = (float) ($revenue->sum ?? 0);

            $pendingBills = $db->table('billing')
                ->where('status', 'pending')
                ->countAllResults();

            $outstanding = $db->table('billing')
                ->selectSum('amount', 'sum')
                ->where('status', 'pending')
                ->get()->getRow();
            $outstandingBalance = (float) ($outstanding->sum ?? 0);
        }

        $data = [
            'title' => 'Billing Service Dashboard',
            'todayRevenue' => $todayRevenue,
            'pendingBills' => $pendingBills,
            'outstandingBalance' => $outstandingBalance,
        ];

        return view('admin/billing/dashboard', $data);
    }

    /**
     * Dashboard Stats API Endpoint
     */
    public function dashboardStats()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        // Use the same logic as Accountant DashboardStats
        $accountantStats = new \App\Controllers\Accountant\DashboardStats();
        return $accountantStats->stats();
    }

    public function create()
    {
        $patients = $this->patientModel->findAll();
        
        $data = [
            'title' => 'Create Bill',
            'patients' => $patients,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/billing/create', $data);
    }

    public function store()
    {
        $rules = [
            'patient_id' => 'required|integer',
            'service' => 'required|max_length[255]',
            'amount' => 'required|decimal',
            'status' => 'required|in_list[pending,paid,cancelled]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'service' => $this->request->getPost('service'),
            'amount' => $this->request->getPost('amount'),
            'status' => $this->request->getPost('status'),
        ];

        $this->billingModel->insert($data);

        return redirect()->to('/admin/billing')->with('success', 'Bill created successfully.');
    }

    public function edit($id)
    {
        $billing = $this->billingModel->find($id);
        
        if (!$billing) {
            return redirect()->to('/admin/billing')->with('error', 'Bill not found.');
        }

        $patients = $this->patientModel->findAll();

        $data = [
            'title' => 'Edit Bill',
            'billing' => $billing,
            'patients' => $patients,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/billing/edit', $data);
    }

    public function update($id)
    {
        $billing = $this->billingModel->find($id);
        
        if (!$billing) {
            return redirect()->to('/admin/billing')->with('error', 'Bill not found.');
        }

        $rules = [
            'patient_id' => 'required|integer',
            'service' => 'required|max_length[255]',
            'amount' => 'required|decimal',
            'status' => 'required|in_list[pending,paid,cancelled]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'service' => $this->request->getPost('service'),
            'amount' => $this->request->getPost('amount'),
            'status' => $this->request->getPost('status'),
        ];

        $this->billingModel->update($id, $data);

        return redirect()->to('/admin/billing')->with('success', 'Bill updated successfully.');
    }

    public function delete($id)
    {
        $billing = $this->billingModel->find($id);
        
        if (!$billing) {
            return redirect()->to('/admin/billing')->with('error', 'Bill not found.');
        }

        $this->billingModel->delete($id);

        return redirect()->to('/admin/billing')->with('success', 'Bill deleted successfully.');
    }

    // ========== FINANCE OVERVIEW METHODS ==========
    
    public function finance()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        // Get cross-role financial data
        $crossRoleData = [
            'receptionist_payments' => $db->tableExists('payment_reports') ? 
                $db->table('payment_reports')
                    ->select('payment_reports.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = payment_reports.patient_id', 'left')
                    ->where('payment_reports.status', 'completed')
                    ->orderBy('payment_reports.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray() : [],
            
            'consultation_charges' => $db->tableExists('consultations') ?
                $db->table('consultations')
                    ->select('consultations.*, admin_patients.firstname, admin_patients.lastname, users.username as doctor_name')
                    ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                    ->join('users', 'users.id = consultations.doctor_id', 'left')
                    ->where('consultations.status', 'completed')
                    ->orderBy('consultations.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray() : [],
            
            'lab_test_charges' => $db->tableExists('lab_requests') ?
                $db->table('lab_requests')
                    ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                    ->where('lab_requests.status', 'completed')
                    ->orderBy('lab_requests.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray() : [],
            
            'pharmacy_expenses' => $db->tableExists('pharmacy') ?
                $db->table('pharmacy')
                    ->select('pharmacy.*')
                    ->orderBy('pharmacy.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray() : [],
        ];

        $data = [
            'title' => 'Finance Overview',
            'finance_overviews' => $this->financeOverviewModel
                ->select('finance_overview.*, users.username as created_by_name')
                ->join('users', 'users.id = finance_overview.created_by', 'left')
                ->orderBy('finance_overview.created_at', 'DESC')
                ->findAll(),
            'cross_role_data' => $crossRoleData,
        ];

        return view('admin/billing/finance/index', $data);
    }

    public function financeCreate()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $data = [
            'title' => 'Create Finance Overview',
        ];

        return view('admin/billing/finance/create', $data);
    }

    public function financeStore()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $rules = [
            'period_type' => 'required|in_list[daily,weekly,monthly,yearly]',
            'period_start' => 'required|valid_date',
            'period_end' => 'required|valid_date',
            'total_revenue' => 'permit_empty|decimal',
            'total_expenses' => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'period_type' => $this->request->getPost('period_type'),
            'period_start' => $this->request->getPost('period_start'),
            'period_end' => $this->request->getPost('period_end'),
            'total_revenue' => $this->request->getPost('total_revenue') ?? 0.00,
            'total_expenses' => $this->request->getPost('total_expenses') ?? 0.00,
            'net_profit' => ($this->request->getPost('total_revenue') ?? 0.00) - ($this->request->getPost('total_expenses') ?? 0.00),
            'total_bills' => $this->request->getPost('total_bills') ?? 0,
            'paid_bills' => $this->request->getPost('paid_bills') ?? 0,
            'pending_bills' => $this->request->getPost('pending_bills') ?? 0,
            'insurance_claims_total' => $this->request->getPost('insurance_claims_total') ?? 0.00,
            'notes' => $this->request->getPost('notes'),
            'created_by' => session()->get('user_id'),
        ];

        if ($this->financeOverviewModel->insert($data)) {
            return redirect()->to('/admin/billing/finance')->with('success', 'Finance overview created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create finance overview.');
        }
    }

    public function financeEdit($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $financeOverview = $this->financeOverviewModel->find($id);

        if (!$financeOverview) {
            return redirect()->to('/admin/billing/finance')->with('error', 'Finance overview not found.');
        }

        $data = [
            'title' => 'Edit Finance Overview',
            'finance_overview' => $financeOverview,
        ];

        return view('admin/billing/finance/edit', $data);
    }

    public function financeUpdate($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $financeOverview = $this->financeOverviewModel->find($id);

        if (!$financeOverview) {
            return redirect()->to('/admin/billing/finance')->with('error', 'Finance overview not found.');
        }

        $rules = [
            'period_type' => 'required|in_list[daily,weekly,monthly,yearly]',
            'period_start' => 'required|valid_date',
            'period_end' => 'required|valid_date',
            'total_revenue' => 'permit_empty|decimal',
            'total_expenses' => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'period_type' => $this->request->getPost('period_type'),
            'period_start' => $this->request->getPost('period_start'),
            'period_end' => $this->request->getPost('period_end'),
            'total_revenue' => $this->request->getPost('total_revenue') ?? 0.00,
            'total_expenses' => $this->request->getPost('total_expenses') ?? 0.00,
            'net_profit' => ($this->request->getPost('total_revenue') ?? 0.00) - ($this->request->getPost('total_expenses') ?? 0.00),
            'total_bills' => $this->request->getPost('total_bills') ?? 0,
            'paid_bills' => $this->request->getPost('paid_bills') ?? 0,
            'pending_bills' => $this->request->getPost('pending_bills') ?? 0,
            'insurance_claims_total' => $this->request->getPost('insurance_claims_total') ?? 0.00,
            'notes' => $this->request->getPost('notes'),
        ];

        if ($this->financeOverviewModel->update($id, $data)) {
            return redirect()->to('/admin/billing/finance')->with('success', 'Finance overview updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update finance overview.');
        }
    }

    public function financeDelete($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $financeOverview = $this->financeOverviewModel->find($id);

        if (!$financeOverview) {
            return redirect()->to('/admin/billing/finance')->with('error', 'Finance overview not found.');
        }

        if ($this->financeOverviewModel->delete($id)) {
            return redirect()->to('/admin/billing/finance')->with('success', 'Finance overview deleted successfully.');
        } else {
            return redirect()->to('/admin/billing/finance')->with('error', 'Failed to delete finance overview.');
        }
    }

    // ========== PAYMENT REPORTS METHODS ==========

    public function paymentReports()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        // Get billing data from Receptionist (patient payments)
        $billingPayments = [];
        if ($db->tableExists('billing')) {
            $billingPayments = $db->table('billing')
                ->select('billing.*, admin_patients.firstname, admin_patients.lastname')
                ->join('admin_patients', 'admin_patients.id = billing.patient_id', 'left')
                ->where('billing.status', 'paid')
                ->where('billing.service !=', 'Medication Administration')
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
            'payment_reports' => $paymentReports,
            'medication_payments' => $medicationPayments,
            'other_payments' => $otherPayments,
            'billing_payments' => $billingPayments,
            'userRole' => 'admin',
        ];

        return view('admin/billing/payment_reports/index', $data);
    }

    public function paymentReportsCreate()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $data = [
            'title' => 'Create Payment Report',
            'patients' => $this->patientModel->findAll(),
        ];

        return view('admin/billing/payment_reports/create', $data);
    }

    public function paymentReportsStore()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
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
            return redirect()->to('/admin/billing/payment_reports')->with('success', 'Payment report created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create payment report.');
        }
    }

    public function paymentReportsEdit($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $paymentReport = $this->paymentReportModel->find($id);

        if (!$paymentReport) {
            return redirect()->to('/admin/billing/payment_reports')->with('error', 'Payment report not found.');
        }

        $data = [
            'title' => 'Edit Payment Report',
            'payment_report' => $paymentReport,
            'patients' => $this->patientModel->findAll(),
        ];

        return view('admin/billing/payment_reports/edit', $data);
    }

    public function paymentReportsUpdate($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $paymentReport = $this->paymentReportModel->find($id);

        if (!$paymentReport) {
            return redirect()->to('/admin/billing/payment_reports')->with('error', 'Payment report not found.');
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
            return redirect()->to('/admin/billing/payment_reports')->with('success', 'Payment report updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update payment report.');
        }
    }

    public function paymentReportsDelete($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $paymentReport = $this->paymentReportModel->find($id);

        if (!$paymentReport) {
            return redirect()->to('/admin/billing/payment_reports')->with('error', 'Payment report not found.');
        }

        if ($this->paymentReportModel->delete($id)) {
            return redirect()->to('/admin/billing/payment_reports')->with('success', 'Payment report deleted successfully.');
        } else {
            return redirect()->to('/admin/billing/payment_reports')->with('error', 'Failed to delete payment report.');
        }
    }

    // ========== EXPENSES METHODS ==========

    public function expenses()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        // Get Pharmacy → Medication Expenses
        $pharmacyExpenses = [];
        if ($db->tableExists('pharmacy')) {
            $pharmacyExpenses = $db->table('pharmacy')
                ->select('pharmacy.*')
                ->where('pharmacy.quantity >', 0)
                ->orderBy('pharmacy.created_at', 'DESC')
                ->limit(20)
                ->get()->getResultArray();
        }

        // Get Lab Staff → Lab Test Expenses
        $labTestExpenses = [];
        if ($db->tableExists('lab_requests')) {
            $labTestExpenses = $db->table('lab_requests')
                ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
                ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                ->where('lab_requests.status', 'completed')
                ->orderBy('lab_requests.created_at', 'DESC')
                ->limit(20)
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Expense Tracking',
            'expenses' => $this->expenseModel
                ->select('expenses.*, users.username as created_by_name, approver.username as approved_by_name')
                ->join('users', 'users.id = expenses.created_by', 'left')
                ->join('users as approver', 'approver.id = expenses.approved_by', 'left')
                ->orderBy('expenses.created_at', 'DESC')
                ->findAll(),
            'pharmacy_expenses' => $pharmacyExpenses,
            'lab_test_expenses' => $labTestExpenses,
        ];

        return view('admin/billing/expenses/index', $data);
    }

    public function expensesCreate()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $data = [
            'title' => 'Create Expense',
        ];

        return view('admin/billing/expenses/create', $data);
    }

    public function expensesStore()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $rules = [
            'expense_date' => 'required|valid_date',
            'category' => 'required|in_list[medical_supplies,equipment,utilities,salaries,maintenance,office_supplies,insurance,rent,other]',
            'description' => 'required|min_length[3]|max_length[255]',
            'amount' => 'required|decimal|greater_than[0]',
            'payment_method' => 'required|in_list[cash,check,bank_transfer,credit_card,other]',
            'status' => 'required|in_list[pending,approved,paid,rejected]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'expense_date' => $this->request->getPost('expense_date'),
            'category' => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'amount' => $this->request->getPost('amount'),
            'vendor' => $this->request->getPost('vendor'),
            'invoice_number' => $this->request->getPost('invoice_number'),
            'payment_method' => $this->request->getPost('payment_method'),
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id'),
            'notes' => $this->request->getPost('notes'),
        ];

        // Auto-approve if status is approved
        if ($this->request->getPost('status') === 'approved') {
            $data['approved_by'] = session()->get('user_id');
            $data['approved_at'] = date('Y-m-d H:i:s');
        }

        if ($this->expenseModel->insert($data)) {
            return redirect()->to('/admin/billing/expenses')->with('success', 'Expense created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create expense.');
        }
    }

    public function expensesEdit($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return redirect()->to('/admin/billing/expenses')->with('error', 'Expense not found.');
        }

        $data = [
            'title' => 'Edit Expense',
            'expense' => $expense,
        ];

        return view('admin/billing/expenses/edit', $data);
    }

    public function expensesUpdate($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return redirect()->to('/admin/billing/expenses')->with('error', 'Expense not found.');
        }

        $rules = [
            'expense_date' => 'required|valid_date',
            'category' => 'required|in_list[medical_supplies,equipment,utilities,salaries,maintenance,office_supplies,insurance,rent,other]',
            'description' => 'required|min_length[3]|max_length[255]',
            'amount' => 'required|decimal|greater_than[0]',
            'payment_method' => 'required|in_list[cash,check,bank_transfer,credit_card,other]',
            'status' => 'required|in_list[pending,approved,paid,rejected]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'expense_date' => $this->request->getPost('expense_date'),
            'category' => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'amount' => $this->request->getPost('amount'),
            'vendor' => $this->request->getPost('vendor'),
            'invoice_number' => $this->request->getPost('invoice_number'),
            'payment_method' => $this->request->getPost('payment_method'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes'),
        ];

        // Update approval if status changed to approved
        if ($this->request->getPost('status') === 'approved' && $expense['status'] !== 'approved') {
            $data['approved_by'] = session()->get('user_id');
            $data['approved_at'] = date('Y-m-d H:i:s');
        }

        if ($this->expenseModel->update($id, $data)) {
            return redirect()->to('/admin/billing/expenses')->with('success', 'Expense updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update expense.');
        }
    }

    public function expensesDelete($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return redirect()->to('/admin/billing/expenses')->with('error', 'Expense not found.');
        }

        if ($this->expenseModel->delete($id)) {
            return redirect()->to('/admin/billing/expenses')->with('success', 'Expense deleted successfully.');
        } else {
            return redirect()->to('/admin/billing/expenses')->with('error', 'Failed to delete expense.');
        }
    }

    // ========== DISCHARGE METHODS ==========

    public function discharge()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        // Get all discharge pending admissions
        $dischargePending = [];
        if ($db->tableExists('admissions') && $db->tableExists('discharge_orders')) {
            $dischargePending = $db->table('admissions a')
                ->select('a.*, ap.firstname, ap.lastname, ap.contact,
                         r.room_number, r.ward,
                         do.id as discharge_order_id, do.discharge_date as planned_discharge_date,
                         u.username as doctor_name,
                         (SELECT SUM(total_amount) FROM charges WHERE patient_id = a.patient_id AND status = "pending" AND deleted_at IS NULL) as total_charges')
                ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
                ->join('rooms r', 'r.id = a.room_id', 'left')
                ->join('discharge_orders do', 'do.admission_id = a.id', 'left')
                ->join('users u', 'u.id = do.doctor_id', 'left')
                ->where('a.discharge_status', 'discharge_pending')
                ->where('a.status', 'admitted')
                ->where('do.status', 'pending')
                ->where('a.deleted_at', null)
                ->orderBy('do.discharge_date', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Discharge Billing',
            'dischargePending' => $dischargePending,
        ];

        return view('admin/billing/discharge/index', $data);
    }

    public function dischargeFinalize($admissionId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        // Get admission with discharge order
        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname,
                     do.id as discharge_order_id')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('discharge_orders do', 'do.admission_id = a.id', 'left')
            ->where('a.id', $admissionId)
            ->where('a.discharge_status', 'discharge_pending')
            ->where('a.status', 'admitted')
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->to('/admin/billing/dashboard')->with('error', 'Admission not found or not pending discharge.');
        }

        // Get all pending charges for this patient
        $pendingCharges = $db->table('charges')
            ->where('patient_id', $admission['patient_id'])
            ->where('status', 'pending')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Finalize Billing for Discharge',
            'admission' => $admission,
            'pendingCharges' => $pendingCharges,
        ];

        return view('admin/billing/discharge/finalize', $data);
    }

    public function dischargeProcess($admissionId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Get admission using direct database query
        $admission = $db->table('admissions')
            ->where('id', $admissionId)
            ->where('discharge_status', 'discharge_pending')
            ->where('status', 'admitted')
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->back()->with('error', 'Admission not found or not pending discharge.');
        }

        // Get all charges for this patient
        $charges = $db->table('charges')
            ->where('patient_id', $admission['patient_id'])
            ->where('status !=', 'cancelled')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $db->transStart();

        try {
            // Mark all pending charges as paid
            foreach ($charges as $charge) {
                if ($charge['status'] === 'pending') {
                    $this->chargeModel->update($charge['id'], [
                        'status' => 'paid',
                        'processed_by' => $userId,
                        'paid_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // Update admission status to discharged
            $db->table('admissions')
                ->where('id', $admissionId)
                ->update([
                    'discharge_status' => 'discharged',
                    'status' => 'discharged',
                    'discharge_date' => date('Y-m-d H:i:s'),
                ]);

            // Update discharge order status
            $dischargeOrder = $this->dischargeOrderModel
                ->where('admission_id', $admissionId)
                ->first();
            
            if ($dischargeOrder) {
                $this->dischargeOrderModel->update($dischargeOrder['id'], [
                    'status' => 'completed',
                ]);
            }

            // Free up room and bed
            if ($admission['room_id']) {
                $db->table('rooms')
                    ->where('id', $admission['room_id'])
                    ->update([
                        'status' => 'Available',
                        'current_patient_id' => null,
                    ]);
            }

            // Free up bed if exists
            if (!empty($admission['bed_number'])) {
                $bed = $db->table('beds')
                    ->where('room_id', $admission['room_id'])
                    ->where('bed_number', $admission['bed_number'])
                    ->get()
                    ->getRowArray();
                
                if ($bed) {
                    $db->table('beds')
                        ->where('id', $bed['id'])
                        ->update([
                            'status' => 'available',
                            'current_patient_id' => null,
                        ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/admin/billing/dashboard')->with('success', 'Patient discharged successfully. All charges have been paid and room/bed are now available.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to process discharge: ' . $e->getMessage());
        }
    }

    // ========== CHARGES METHODS ==========

    public function charges()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as Admin to access this page.');
        }

        $db = \Config\Database::connect();

        // Get charges with patient and doctor information
        // Include lab test charges (which don't have consultation_id)
        $charges = $db->table('charges c')
            ->select('c.*, ap.firstname, ap.lastname, ap.contact, 
                     u.username as doctor_name,
                     COUNT(bi.id) as item_count,
                     MAX(CASE WHEN bi.item_type = "lab_test" THEN 1 ELSE 0 END) as has_lab_test,
                     MAX(CASE WHEN bi.item_type = "lab_test" THEN bi.item_name ELSE NULL END) as test_name,
                     MAX(CASE WHEN bi.item_type = "lab_test" THEN bi.description ELSE NULL END) as test_description,
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

        $db = \Config\Database::connect();
        
        if ($this->chargeModel->update($id, ['status' => 'approved'])) {
            // Update lab_request payment_status to 'approved' if this charge is linked to a lab request
            if ($db->tableExists('lab_requests')) {
                $labRequest = $db->table('lab_requests')
                    ->where('charge_id', $id)
                    ->get()
                    ->getRowArray();
                
                if ($labRequest) {
                    // Check if test requires specimen by checking instructions or lab_tests table
                    $requiresSpecimen = true; // Default to true
                    $instructions = $labRequest['instructions'] ?? '';
                    
                    // Check if specimen_category is stored in instructions
                    if (preg_match('/SPECIMEN_CATEGORY:(without_specimen|with_specimen)/', $instructions, $matches)) {
                        $requiresSpecimen = ($matches[1] === 'with_specimen');
                    } else {
                        // Fallback: Check lab_tests table
                        if ($db->tableExists('lab_tests') && !empty($labRequest['test_name'])) {
                            $labTest = $db->table('lab_tests')
                                ->where('test_name', $labRequest['test_name'])
                                ->where('is_active', 1)
                                ->get()
                                ->getRowArray();
                            if ($labTest) {
                                $requiresSpecimen = ($labTest['specimen_category'] ?? 'with_specimen') === 'with_specimen';
                            }
                        }
                    }
                    
                    $patientModel = new \App\Models\AdminPatientModel();
                    $patient = $patientModel->find($labRequest['patient_id']);
                    $patientName = ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'Patient');
                    
                    if ($requiresSpecimen) {
                        // WITH SPECIMEN: Update payment status and notify nurse
                        $db->table('lab_requests')
                            ->where('charge_id', $id)
                            ->update([
                                'payment_status' => 'approved', // Approved - nurse can proceed
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        
                        // Notify assigned nurse that payment is approved and they can collect specimen
                        if (!empty($labRequest['nurse_id']) && $db->tableExists('nurse_notifications')) {
                            $db->table('nurse_notifications')->insert([
                                'nurse_id' => $labRequest['nurse_id'],
                                'type' => 'lab_specimen_collection',
                                'title' => 'Lab Test Payment Approved - Collect Specimen',
                                'message' => 'Payment for lab test (' . ($labRequest['test_name'] ?? 'Lab Test') . ') for patient ' . $patientName . ' has been approved. Please collect the specimen.',
                                'related_id' => $labRequest['id'],
                                'related_type' => 'lab_request',
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        $message = 'Charge approved successfully. Nurse has been notified to collect specimen.';
                    } else {
                        // WITHOUT SPECIMEN: Update payment status to 'approved' (NOT ready for lab yet - must process payment first)
                        $db->table('lab_requests')
                            ->where('charge_id', $id)
                            ->update([
                                'payment_status' => 'approved', // Approved - but NOT paid yet, so NOT ready for lab
                                'updated_at' => date('Y-m-d H:i:s')
                                // Status stays 'pending' - will be updated to ready when payment is processed
                            ]);
                        
                        $message = 'Charge approved successfully. Please process payment to proceed to laboratory (no specimen required).';
                    }
                }
            }
            
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => $message ?? 'Charge approved successfully.'
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

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Update charge status to paid
        $updateData = [
            'status' => 'paid',
            'processed_by' => $userId,
            'paid_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->chargeModel->update($id, $updateData)) {
            // Update lab_request payment_status to 'paid' if this charge is linked to a lab request
            if ($db->tableExists('lab_requests')) {
                $labRequest = $db->table('lab_requests')
                    ->where('charge_id', $id)
                    ->get()
                    ->getRowArray();
                
                if ($labRequest) {
                    // Check if test requires specimen
                    $requiresSpecimen = true; // Default to true
                    $instructions = $labRequest['instructions'] ?? '';
                    
                    // Check if specimen_category is stored in instructions
                    if (preg_match('/SPECIMEN_CATEGORY:(without_specimen|with_specimen)/', $instructions, $matches)) {
                        $requiresSpecimen = ($matches[1] === 'with_specimen');
                    } else {
                        // Fallback: Check lab_tests table
                        if ($db->tableExists('lab_tests') && !empty($labRequest['test_name'])) {
                            $labTest = $db->table('lab_tests')
                                ->where('test_name', $labRequest['test_name'])
                                ->where('is_active', 1)
                                ->get()
                                ->getRowArray();
                            if ($labTest) {
                                $requiresSpecimen = ($labTest['specimen_category'] ?? 'with_specimen') === 'with_specimen';
                            }
                        }
                    }
                    
                    $patientModel = new \App\Models\AdminPatientModel();
                    $patient = $patientModel->find($labRequest['patient_id']);
                    $patientName = ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'Patient');
                    
                    if ($requiresSpecimen) {
                        // WITH SPECIMEN: Update payment status and notify nurse
                        $db->table('lab_requests')
                            ->where('charge_id', $id)
                            ->update([
                                'payment_status' => 'paid',
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        
                        // Notify assigned nurse that payment is approved and they can collect specimen
                        if (!empty($labRequest['nurse_id']) && $db->tableExists('nurse_notifications')) {
                            $db->table('nurse_notifications')->insert([
                                'nurse_id' => $labRequest['nurse_id'],
                                'type' => 'lab_specimen_collection',
                                'title' => 'Lab Test Payment Approved - Collect Specimen',
                                'message' => 'Payment for lab test (' . ($labRequest['test_name'] ?? 'Lab Test') . ') for patient ' . $patientName . ' has been approved. Please collect the specimen.',
                                'related_id' => $labRequest['id'],
                                'related_type' => 'lab_request',
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    } else {
                        // WITHOUT SPECIMEN: Update payment status and status to 'pending' (ready for lab testing)
                        $db->table('lab_requests')
                            ->where('charge_id', $id)
                            ->update([
                                'payment_status' => 'paid',
                                'status' => 'pending', // Ready for lab testing (no specimen collection needed)
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        
                        // Notify lab staff that a test is ready (no specimen required)
                        if ($db->tableExists('lab_notifications')) {
                            $db->table('lab_notifications')->insert([
                                'type' => 'test_ready',
                                'title' => 'Lab Test Ready - No Specimen Required',
                                'message' => 'Payment processed for lab test (' . ($labRequest['test_name'] ?? 'Lab Test') . ') for patient ' . $patientName . '. Test is ready for processing (no specimen collection required).',
                                'related_id' => $labRequest['id'],
                                'related_type' => 'lab_request',
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            }
            
            // Create payment record
            if ($db->tableExists('payment_reports')) {
                $paymentData = [
                    'report_date' => date('Y-m-d'),
                    'patient_id' => $charge['patient_id'],
                    'billing_id' => null, // Not linked to old billing table
                    'payment_method' => $this->request->getPost('payment_method') ?? 'cash',
                    'amount' => $charge['total_amount'],
                    'reference_number' => $charge['charge_number'],
                    'status' => 'completed',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'processed_by' => $userId,
                    'notes' => 'Payment for charge ' . $charge['charge_number'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $this->paymentReportModel->skipValidation(true);
                $this->paymentReportModel->insert($paymentData);
                $this->paymentReportModel->skipValidation(false);
            }

            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Payment processed successfully. Receipt can be printed.',
                    'charge_id' => $id // Return charge_id for receipt printing
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

    // ========== PATIENT BILLING METHOD ==========

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

        return view('admin/billing/patient_billing', $data);
    }
}

