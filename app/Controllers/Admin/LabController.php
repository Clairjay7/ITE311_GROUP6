<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LabServiceModel;
use App\Models\AdminPatientModel;

class LabController extends BaseController
{
    protected $labServiceModel;
    protected $patientModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->labServiceModel = new LabServiceModel();
        $this->patientModel = new AdminPatientModel();
    }

    public function index()
    {
        $labServices = $this->labServiceModel
            ->select('lab_services.*, admin_patients.firstname, admin_patients.lastname')
            ->join('admin_patients', 'admin_patients.id = lab_services.patient_id', 'left')
            ->orderBy('lab_services.created_at', 'DESC')
            ->findAll();
        
        $data = [
            'title' => 'Lab Services',
            'labServices' => $labServices,
        ];

        return view('admin/lab/index', $data);
    }

    public function create()
    {
        $patients = $this->patientModel->findAll();
        
        $data = [
            'title' => 'Create Lab Service',
            'patients' => $patients,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/lab/create', $data);
    }

    public function store()
    {
        $rules = [
            'patient_id' => 'required|integer',
            'test_type' => 'required|max_length[255]',
            'result' => 'permit_empty|max_length[500]',
            'remarks' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'test_type' => $this->request->getPost('test_type'),
            'result' => $this->request->getPost('result'),
            'remarks' => $this->request->getPost('remarks'),
        ];

        $this->labServiceModel->insert($data);

        return redirect()->to('/admin/lab')->with('success', 'Lab service created successfully.');
    }

    public function edit($id)
    {
        $labService = $this->labServiceModel->find($id);
        
        if (!$labService) {
            return redirect()->to('/admin/lab')->with('error', 'Lab service not found.');
        }

        $patients = $this->patientModel->findAll();

        $data = [
            'title' => 'Edit Lab Service',
            'labService' => $labService,
            'patients' => $patients,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/lab/edit', $data);
    }

    public function update($id)
    {
        $labService = $this->labServiceModel->find($id);
        
        if (!$labService) {
            return redirect()->to('/admin/lab')->with('error', 'Lab service not found.');
        }

        $rules = [
            'patient_id' => 'required|integer',
            'test_type' => 'required|max_length[255]',
            'result' => 'permit_empty|max_length[500]',
            'remarks' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'test_type' => $this->request->getPost('test_type'),
            'result' => $this->request->getPost('result'),
            'remarks' => $this->request->getPost('remarks'),
        ];

        $this->labServiceModel->update($id, $data);

        return redirect()->to('/admin/lab')->with('success', 'Lab service updated successfully.');
    }

    public function delete($id)
    {
        $labService = $this->labServiceModel->find($id);
        
        if (!$labService) {
            return redirect()->to('/admin/lab')->with('error', 'Lab service not found.');
        }

        $this->labServiceModel->delete($id);

        return redirect()->to('/admin/lab')->with('success', 'Lab service deleted successfully.');
    }
}

