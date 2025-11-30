<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;

class PatientController extends BaseController
{
    protected $patientModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->patientModel = new AdminPatientModel();
    }

    public function index()
    {
        $patients = $this->patientModel->orderBy('created_at', 'DESC')->findAll();
        
        $data = [
            'title' => 'Patient Records',
            'patients' => $patients,
        ];

        return view('admin/patients/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Patient',
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/patients/create', $data);
    }

    public function store()
    {
        $rules = [
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
            'contact' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birthdate' => $this->request->getPost('birthdate'),
            'gender' => $this->request->getPost('gender'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address'),
        ];

        $this->patientModel->insert($data);

        return redirect()->to('/admin/patients')->with('success', 'Patient created successfully.');
    }

    public function edit($id)
    {
        $patient = $this->patientModel->find($id);
        
        if (!$patient) {
            return redirect()->to('/admin/patients')->with('error', 'Patient not found.');
        }

        $data = [
            'title' => 'Edit Patient',
            'patient' => $patient,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/patients/edit', $data);
    }

    public function update($id)
    {
        $patient = $this->patientModel->find($id);
        
        if (!$patient) {
            return redirect()->to('/admin/patients')->with('error', 'Patient not found.');
        }

        $rules = [
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
            'contact' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birthdate' => $this->request->getPost('birthdate'),
            'gender' => $this->request->getPost('gender'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address'),
        ];

        $this->patientModel->update($id, $data);

        return redirect()->to('/admin/patients')->with('success', 'Patient updated successfully.');
    }

    public function delete($id)
    {
        $patient = $this->patientModel->find($id);
        
        if (!$patient) {
            return redirect()->to('/admin/patients')->with('error', 'Patient not found.');
        }

        $this->patientModel->delete($id);

        return redirect()->to('/admin/patients')->with('success', 'Patient deleted successfully.');
    }
}

