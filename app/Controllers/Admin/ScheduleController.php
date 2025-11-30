<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ScheduleModel;
use App\Models\AdminPatientModel;

class ScheduleController extends BaseController
{
    protected $scheduleModel;
    protected $patientModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->scheduleModel = new ScheduleModel();
        $this->patientModel = new AdminPatientModel();
    }

    public function index()
    {
        $schedules = $this->scheduleModel
            ->select('schedules.*, admin_patients.firstname, admin_patients.lastname')
            ->join('admin_patients', 'admin_patients.id = schedules.patient_id', 'left')
            ->orderBy('schedules.date', 'DESC')
            ->orderBy('schedules.time', 'ASC')
            ->findAll();
        
        $data = [
            'title' => 'Scheduling',
            'schedules' => $schedules,
        ];

        return view('admin/schedule/index', $data);
    }

    public function create()
    {
        $patients = $this->patientModel->findAll();
        
        $data = [
            'title' => 'Create Schedule',
            'patients' => $patients,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/schedule/create', $data);
    }

    public function store()
    {
        $rules = [
            'patient_id' => 'required|integer',
            'date' => 'required|valid_date',
            'time' => 'required',
            'doctor' => 'required|max_length[255]',
            'status' => 'required|in_list[pending,confirmed,completed,cancelled]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'date' => $this->request->getPost('date'),
            'time' => $this->request->getPost('time'),
            'doctor' => $this->request->getPost('doctor'),
            'status' => $this->request->getPost('status'),
        ];

        $this->scheduleModel->insert($data);

        return redirect()->to('/admin/schedule')->with('success', 'Schedule created successfully.');
    }

    public function edit($id)
    {
        $schedule = $this->scheduleModel->find($id);
        
        if (!$schedule) {
            return redirect()->to('/admin/schedule')->with('error', 'Schedule not found.');
        }

        $patients = $this->patientModel->findAll();

        $data = [
            'title' => 'Edit Schedule',
            'schedule' => $schedule,
            'patients' => $patients,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/schedule/edit', $data);
    }

    public function update($id)
    {
        $schedule = $this->scheduleModel->find($id);
        
        if (!$schedule) {
            return redirect()->to('/admin/schedule')->with('error', 'Schedule not found.');
        }

        $rules = [
            'patient_id' => 'required|integer',
            'date' => 'required|valid_date',
            'time' => 'required',
            'doctor' => 'required|max_length[255]',
            'status' => 'required|in_list[pending,confirmed,completed,cancelled]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'date' => $this->request->getPost('date'),
            'time' => $this->request->getPost('time'),
            'doctor' => $this->request->getPost('doctor'),
            'status' => $this->request->getPost('status'),
        ];

        $this->scheduleModel->update($id, $data);

        return redirect()->to('/admin/schedule')->with('success', 'Schedule updated successfully.');
    }

    public function delete($id)
    {
        $schedule = $this->scheduleModel->find($id);
        
        if (!$schedule) {
            return redirect()->to('/admin/schedule')->with('error', 'Schedule not found.');
        }

        $this->scheduleModel->delete($id);

        return redirect()->to('/admin/schedule')->with('success', 'Schedule deleted successfully.');
    }
}

