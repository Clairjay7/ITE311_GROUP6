<?php

namespace App\Controllers\Accountant;

use App\Controllers\BaseController;
use App\Models\FinanceOverviewModel;

class FinanceOverviewController extends BaseController
{
    protected $financeOverviewModel;

    public function __construct()
    {
        $this->financeOverviewModel = new FinanceOverviewModel();
    }

    public function index()
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        // Get cross-role financial data
        $crossRoleData = [
            // Receptionist → Patient Payments
            'receptionist_payments' => $db->tableExists('payment_reports') ? 
                $db->table('payment_reports')
                    ->select('payment_reports.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = payment_reports.patient_id', 'left')
                    ->where('payment_reports.status', 'completed')
                    ->orderBy('payment_reports.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray() : [],
            
            // Doctor/Nurse → Treatment Charges
            'consultation_charges' => $db->tableExists('consultations') ?
                $db->table('consultations')
                    ->select('consultations.*, admin_patients.firstname, admin_patients.lastname, users.username as doctor_name')
                    ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                    ->join('users', 'users.id = consultations.doctor_id', 'left')
                    ->where('consultations.status', 'completed')
                    ->orderBy('consultations.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray() : [],
            
            // Lab Staff → Lab Test Charges
            'lab_test_charges' => $db->tableExists('lab_requests') ?
                $db->table('lab_requests')
                    ->select('lab_requests.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = lab_requests.patient_id', 'left')
                    ->where('lab_requests.status', 'completed')
                    ->orderBy('lab_requests.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray() : [],
            
            // Pharmacy → Medication Expenses
            'pharmacy_expenses' => $db->tableExists('pharmacy') ?
                $db->table('pharmacy')
                    ->select('pharmacy.*')
                    ->orderBy('pharmacy.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray() : [],
        ];

        $data = [
            'title' => 'Finance Overview',
            'name' => session()->get('name'),
            'finance_overviews' => $this->financeOverviewModel
                ->select('finance_overview.*, users.username as created_by_name')
                ->join('users', 'users.id = finance_overview.created_by', 'left')
                ->orderBy('finance_overview.created_at', 'DESC')
                ->findAll(),
            'cross_role_data' => $crossRoleData,
        ];

        return view('Accountant/finance_overview/index', $data);
    }

    public function create()
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $data = [
            'title' => 'Create Finance Overview',
            'name' => session()->get('name'),
        ];

        return view('Accountant/finance_overview/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
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
            return redirect()->to('/accounting/finance')->with('success', 'Finance overview created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create finance overview.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $financeOverview = $this->financeOverviewModel->find($id);

        if (!$financeOverview) {
            return redirect()->to('/accounting/finance')->with('error', 'Finance overview not found.');
        }

        $data = [
            'title' => 'Edit Finance Overview',
            'name' => session()->get('name'),
            'finance_overview' => $financeOverview,
        ];

        return view('Accountant/finance_overview/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $financeOverview = $this->financeOverviewModel->find($id);

        if (!$financeOverview) {
            return redirect()->to('/accounting/finance')->with('error', 'Finance overview not found.');
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
            return redirect()->to('/accounting/finance')->with('success', 'Finance overview updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update finance overview.');
        }
    }

    public function delete($id)
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $financeOverview = $this->financeOverviewModel->find($id);

        if (!$financeOverview) {
            return redirect()->to('/accounting/finance')->with('error', 'Finance overview not found.');
        }

        if ($this->financeOverviewModel->delete($id)) {
            return redirect()->to('/accounting/finance')->with('success', 'Finance overview deleted successfully.');
        } else {
            return redirect()->to('/accounting/finance')->with('error', 'Failed to delete finance overview.');
        }
    }
}

