<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminPatientModel;

class BillingController extends BaseController
{
    protected $billingModel;
    protected $patientModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->billingModel = new BillingModel();
        $this->patientModel = new AdminPatientModel();
    }

    public function index()
    {
        $billings = $this->billingModel
            ->select('billing.*, admin_patients.firstname, admin_patients.lastname')
            ->join('admin_patients', 'admin_patients.id = billing.patient_id', 'left')
            ->orderBy('billing.created_at', 'DESC')
            ->findAll();
        
        $data = [
            'title' => 'Billing Services',
            'billings' => $billings,
        ];

        return view('admin/billing/index', $data);
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
}

