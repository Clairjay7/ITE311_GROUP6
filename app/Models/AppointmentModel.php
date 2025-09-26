<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'patient_id', 'doctor_id', 'appointment_date', 'status', 'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function upcomingForDateRange($start, $end)
    {
        return $this->where('appointment_date >=', $start)
                    ->where('appointment_date <=', $end)
                    ->orderBy('appointment_date', 'ASC')
                    ->findAll();
    }

    public function withPatientDoctor()
    {
        return $this->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name, doctors.name as doctor_name')
                    ->join('patients', 'patients.id = appointments.patient_id')
                    ->join('doctors', 'doctors.id = appointments.doctor_id');
    }

    public function getByDoctor($doctorId)
    {
        return $this->select('appointments.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = appointments.patient_id')
                    ->where('appointments.doctor_id', $doctorId)
                    ->orderBy('appointments.appointment_date', 'ASC')
                    ->findAll();
    }

    public function getByPatient($patientId)
    {
        return $this->select('appointments.*, 
                             users.first_name as doctor_first_name,
                             users.last_name as doctor_last_name')
                    ->join('users', 'users.id = appointments.doctor_id')
                    ->where('appointments.patient_id', $patientId)
                    ->orderBy('appointments.appointment_date', 'DESC')
                    ->findAll();
    }
}
