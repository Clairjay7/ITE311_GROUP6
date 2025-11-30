<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\ConsultationModel;
use App\Models\AdminPatientModel;

class ConsultationController extends BaseController
{
    public function upcoming()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $upcomingConsultations = $consultationModel->getUpcomingConsultations($doctorId);

        $data = [
            'title' => 'Upcoming Consultations',
            'consultations' => $upcomingConsultations
        ];

        return view('doctor/consultations/upcoming', $data);
    }

    public function mySchedule()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $mySchedule = $consultationModel->getDoctorSchedule($doctorId);

        $data = [
            'title' => 'My Schedule',
            'consultations' => $mySchedule
        ];

        return view('doctor/consultations/my_schedule', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');

        // Get patients assigned to this doctor from admin_patients table
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Create Consultation',
            'patients' => $patients,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'consultation_date' => 'required|valid_date',
            'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'type' => 'required|in_list[upcoming,completed]',
            'status' => 'required|in_list[pending,approved,cancelled]',
            'notes' => 'permit_empty|max_length[2000]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'doctor_id' => $doctorId,
            'patient_id' => $this->request->getPost('patient_id'),
            'consultation_date' => $this->request->getPost('consultation_date'),
            'consultation_time' => $this->request->getPost('consultation_time'),
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($consultationModel->insert($data)) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', 'Consultation created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create consultation.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');

        $consultation = $consultationModel->find($id);
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        // Get patients assigned to this doctor from admin_patients table
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Edit Consultation',
            'consultation' => $consultation,
            'patients' => $patients,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $consultation = $consultationModel->find($id);
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'consultation_date' => 'required|valid_date',
            'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'type' => 'required|in_list[upcoming,completed]',
            'status' => 'required|in_list[pending,approved,cancelled]',
            'notes' => 'permit_empty|max_length[2000]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'consultation_date' => $this->request->getPost('consultation_date'),
            'consultation_time' => $this->request->getPost('consultation_time'),
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($consultationModel->update($id, $data)) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', 'Consultation updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update consultation.');
        }
    }

    public function delete($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $consultation = $consultationModel->find($id);
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        if ($consultationModel->delete($id)) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', 'Consultation deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete consultation.');
        }
    }
}
