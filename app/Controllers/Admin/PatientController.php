<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\UserModel;

class PatientController extends BaseController
{
    protected $patientModel;
    protected $userModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->patientModel = new AdminPatientModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $patients = $this->patientModel
            ->select('admin_patients.*, users.username as doctor_name')
            ->join('users', 'users.id = admin_patients.doctor_id', 'left')
            ->orderBy('admin_patients.created_at', 'DESC')
            ->findAll();
        
        $data = [
            'title' => 'Patient Records',
            'patients' => $patients,
        ];

        return view('admin/patients/index', $data);
    }

    public function create()
    {
        // Get all doctors from users table
        $db = \Config\Database::connect();
        $doctors = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $doctors = $db->table('users u')
                ->select('u.id, u.username, u.email')
                ->join('roles r', 'r.id = u.role_id', 'left')
                ->where('LOWER(r.name)', 'doctor')
                ->where('u.status', 'active')
                ->get()->getResultArray();
        }
        
        $data = [
            'title' => 'Add New Patient',
            'doctors' => $doctors,
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
            'doctor_id' => 'permit_empty|integer',
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
            'doctor_id' => $this->request->getPost('doctor_id') ?: null,
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

        // Get all doctors from users table
        $db = \Config\Database::connect();
        $doctors = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $doctors = $db->table('users u')
                ->select('u.id, u.username, u.email')
                ->join('roles r', 'r.id = u.role_id', 'left')
                ->where('LOWER(r.name)', 'doctor')
                ->where('u.status', 'active')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Edit Patient',
            'patient' => $patient,
            'doctors' => $doctors,
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
            'doctor_id' => 'permit_empty|integer',
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
            'doctor_id' => $this->request->getPost('doctor_id') ?: null,
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

