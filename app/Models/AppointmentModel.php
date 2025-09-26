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
}
