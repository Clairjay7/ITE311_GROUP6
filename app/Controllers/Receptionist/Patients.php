<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\HMSPatientModel;
use App\Models\DepartmentModel;
use App\Models\DoctorDirectoryModel;

class Patients extends BaseController
{
    protected $patientModel;
    protected $departmentModel;
    protected $doctorModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->patientModel = new HMSPatientModel();
        $this->departmentModel = new DepartmentModel();
        $this->doctorModel = new DoctorDirectoryModel();
    }

    public function index()
    {
        $type = $this->request->getGet('type'); // In-Patient | Out-Patient
        $search = trim((string)$this->request->getGet('q'));

        $builder = $this->patientModel->builder();
        $builder->select('patients.*, doctors.doctor_name, departments.department_name')
                ->join('doctors', 'doctors.id = patients.doctor_id', 'left')
                ->join('departments', 'departments.id = patients.department_id', 'left');

        if ($type && in_array($type, ['In-Patient', 'Out-Patient'])) {
            $builder->where('patients.type', $type);
        }
        if ($search !== '') {
            $builder->groupStart()
                    ->like('patients.full_name', $search)
                    ->orLike('patients.patient_id', $search)
                    ->orLike('doctors.doctor_name', $search)
                    ->groupEnd();
        }
        $builder->orderBy('patients.patient_id', 'DESC');
        $patients = $builder->get()->getResultArray();

        return view('Reception/patients/index', [
            'title' => 'Patient Records',
            'patients' => $patients,
            'filterType' => $type,
            'query' => $search,
        ]);
    }

    public function create()
    {
        return view('Reception/patients/register', [
            'title' => 'Register Patient',
            'departments' => $this->departmentModel->findAll(),
            'doctors' => $this->doctorModel->findAll(),
            'validation' => \Config\Services::validation()
        ]);
    }

    public function store()
    {
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
            'age' => 'required|integer',
            'type' => 'required|in_list[In-Patient,Out-Patient]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prevent duplicate: same full_name + age + contact
        $exists = $this->patientModel->where([
            'full_name' => $this->request->getPost('full_name'),
            'age' => (int)$this->request->getPost('age'),
            'contact' => $this->request->getPost('contact') ?: null,
        ])->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Duplicate patient detected.');
        }

        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'gender' => $this->request->getPost('gender'),
            'age' => (int)$this->request->getPost('age'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address'),
            'type' => $this->request->getPost('type'),
            'doctor_id' => $this->request->getPost('doctor_id') ?: null,
            'department_id' => $this->request->getPost('department_id') ?: null,
            'purpose' => $this->request->getPost('purpose'),
            'admission_date' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('admission_date') : null,
            'room_number' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('room_number') : null,
        ];

        $this->patientModel->save($data);
        return redirect()->to('/receptionist/patients')->with('success', 'Patient registered successfully.');
    }

    public function show($id)
    {
        $patient = $this->patientModel
            ->select('patients.*, doctors.doctor_name, departments.department_name')
            ->join('doctors', 'doctors.id = patients.doctor_id', 'left')
            ->join('departments', 'departments.id = patients.department_id', 'left')
            ->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        return view('Reception/patients/view', [
            'title' => 'Patient Details',
            'patient' => $patient,
        ]);
    }

    public function edit($id)
    {
        $patient = $this->patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        return view('Reception/patients/edit', [
            'title' => 'Edit Patient',
            'patient' => $patient,
            'departments' => $this->departmentModel->findAll(),
            'doctors' => $this->doctorModel->findAll(),
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
            'age' => 'required|integer',
            'type' => 'required|in_list[In-Patient,Out-Patient]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $data = [
            'patient_id' => $id,
            'full_name' => $this->request->getPost('full_name'),
            'gender' => $this->request->getPost('gender'),
            'age' => (int)$this->request->getPost('age'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address'),
            'type' => $this->request->getPost('type'),
            'doctor_id' => $this->request->getPost('doctor_id') ?: null,
            'department_id' => $this->request->getPost('department_id') ?: null,
            'purpose' => $this->request->getPost('purpose'),
            'admission_date' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('admission_date') : null,
            'room_number' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('room_number') : null,
        ];
        $this->patientModel->save($data);
        return redirect()->to('/receptionist/patients')->with('success', 'Patient updated successfully.');
    }

    public function delete($id)
    {
        $patient = $this->patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        $this->patientModel->delete($id);
        return redirect()->to('/receptionist/patients')->with('success', 'Patient deleted successfully.');
    }
}
