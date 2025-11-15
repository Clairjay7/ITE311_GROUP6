<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\HMSPatientModel;

class Inpatients extends BaseController
{
    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new HMSPatientModel();
    }

    public function rooms()
    {
        $builder = $this->patientModel->builder();
        $builder->select('patients.*, doctors.doctor_name, departments.department_name')
            ->join('doctors', 'doctors.id = patients.doctor_id', 'left')
            ->join('departments', 'departments.id = patients.department_id', 'left')
            ->where('patients.type', 'In-Patient')
            ->orderBy('patients.admission_date', 'DESC');

        $patients = $builder->get()->getResultArray();

        return view('Reception/inpatients/rooms', [
            'title' => 'In-Patient Rooms',
            'patients' => $patients,
        ]);
    }
}
