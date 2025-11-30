<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\ConsultationModel;

class PatientController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new PatientModel();
        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        // Get patients assigned to this doctor
        $assignedPatientIds = $consultationModel
            ->select('patient_id')
            ->where('doctor_id', $doctorId)
            ->distinct()
            ->findAll();

        $patientIds = array_column($assignedPatientIds, 'patient_id');

        $patients = [];
        if (!empty($patientIds)) {
            $patients = $patientModel
                ->whereIn('patient_id', $patientIds)
                ->orderBy('last_name', 'ASC')
                ->orderBy('first_name', 'ASC')
                ->findAll();
        }

        $data = [
            'title' => 'Patient List',
            'patients' => $patients
        ];

        return view('doctor/patients/index', $data);
    }

    public function view($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new PatientModel();
        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $patient = $patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor
        $hasAccess = $consultationModel
            ->where('doctor_id', $doctorId)
            ->where('patient_id', $id)
            ->first();

        if (!$hasAccess) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        // Get patient's consultation history
        $consultations = $consultationModel
            ->where('patient_id', $id)
            ->where('doctor_id', $doctorId)
            ->orderBy('consultation_date', 'DESC')
            ->orderBy('consultation_time', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Patient Details',
            'patient' => $patient,
            'consultations' => $consultations
        ];

        return view('doctor/patients/view', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $data = [
            'title' => 'Create New Patient',
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/patients/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new PatientModel();

        $validation = $this->validate([
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
            'contact' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birthdate' => $this->request->getPost('birthdate'),
            'gender' => $this->request->getPost('gender'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address')
        ];

        if ($patientModel->insert($data)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create patient.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new PatientModel();
        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $patient = $patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor
        $hasAccess = $consultationModel
            ->where('doctor_id', $doctorId)
            ->where('patient_id', $id)
            ->first();

        if (!$hasAccess) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        $data = [
            'title' => 'Edit Patient',
            'patient' => $patient,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/patients/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new PatientModel();
        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $patient = $patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor
        $hasAccess = $consultationModel
            ->where('doctor_id', $doctorId)
            ->where('patient_id', $id)
            ->first();

        if (!$hasAccess) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        $validation = $this->validate([
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
            'contact' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birthdate' => $this->request->getPost('birthdate'),
            'gender' => $this->request->getPost('gender'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address')
        ];

        if ($patientModel->update($id, $data)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update patient.');
        }
    }

    public function delete($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new PatientModel();
        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $patient = $patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor
        $hasAccess = $consultationModel
            ->where('doctor_id', $doctorId)
            ->where('patient_id', $id)
            ->first();

        if (!$hasAccess) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        if ($patientModel->delete($id)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete patient.');
        }
    }
}
