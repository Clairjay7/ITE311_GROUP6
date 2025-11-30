<?php

namespace App\Controllers\Accountant;

use App\Controllers\BaseController;
use App\Models\ExpenseModel;

class ExpenseController extends BaseController
{
    protected $expenseModel;

    public function __construct()
    {
        $this->expenseModel = new ExpenseModel();
    }

    public function index()
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
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

        // Get Lab Staff → Lab Test Expenses (if there's a cost field)
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
            'name' => session()->get('name'),
            'expenses' => $this->expenseModel
                ->select('expenses.*, users.username as created_by_name, approver.username as approved_by_name')
                ->join('users', 'users.id = expenses.created_by', 'left')
                ->join('users as approver', 'approver.id = expenses.approved_by', 'left')
                ->orderBy('expenses.created_at', 'DESC')
                ->findAll(),
            'pharmacy_expenses' => $pharmacyExpenses, // Pharmacy → Medication Expenses
            'lab_test_expenses' => $labTestExpenses, // Lab Staff → Lab Test Expenses
        ];

        return view('Accountant/expenses/index', $data);
    }

    public function create()
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $data = [
            'title' => 'Create Expense',
            'name' => session()->get('name'),
        ];

        return view('Accountant/expenses/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
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
            return redirect()->to('/accounting/expenses')->with('success', 'Expense created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create expense.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return redirect()->to('/accounting/expenses')->with('error', 'Expense not found.');
        }

        $data = [
            'title' => 'Edit Expense',
            'name' => session()->get('name'),
            'expense' => $expense,
        ];

        return view('Accountant/expenses/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return redirect()->to('/accounting/expenses')->with('error', 'Expense not found.');
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
            return redirect()->to('/accounting/expenses')->with('success', 'Expense updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update expense.');
        }
    }

    public function delete($id)
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as accountant to access this page.');
        }

        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return redirect()->to('/accounting/expenses')->with('error', 'Expense not found.');
        }

        if ($this->expenseModel->delete($id)) {
            return redirect()->to('/accounting/expenses')->with('success', 'Expense deleted successfully.');
        } else {
            return redirect()->to('/accounting/expenses')->with('error', 'Failed to delete expense.');
        }
    }
}

