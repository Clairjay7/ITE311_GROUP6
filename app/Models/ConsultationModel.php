<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsultationModel extends Model
{
    protected $table = 'consultations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'doctor_id',
        'patient_id',
        'consultation_date',
        'consultation_time',
        'type',
        'notes',
        'observations',
        'diagnosis',
        'status',
        'for_admission',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $skipValidation = false;

    protected $validationRules = [
        'doctor_id' => 'required|integer|greater_than[0]',
        'patient_id' => 'required|integer|greater_than[0]',
        'consultation_date' => 'required|valid_date',
        'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9](:00)?$/]',
        'type' => 'required|in_list[upcoming,completed]',
        'status' => 'required|in_list[pending,approved,cancelled]',
        'notes' => 'permit_empty|max_length[2000]',
        'observations' => 'permit_empty|max_length[5000]',
        'diagnosis' => 'permit_empty|max_length[2000]'
    ];

    protected $validationMessages = [
        'doctor_id' => [
            'required' => 'Doctor is required.',
            'greater_than' => 'Please select a valid doctor.'
        ],
        'patient_id' => [
            'required' => 'Patient is required.',
            'greater_than' => 'Please select a valid patient.'
        ],
        'consultation_date' => [
            'required' => 'Consultation date is required.',
            'valid_date' => 'Please enter a valid consultation date.'
        ],
        'consultation_time' => [
            'required' => 'Consultation time is required.',
            'regex_match' => 'Please enter a valid time in HH:MM format.'
        ],
        'type' => [
            'required' => 'Consultation type is required.',
            'in_list' => 'Please select a valid consultation type.'
        ],
        'status' => [
            'required' => 'Status is required.',
            'in_list' => 'Please select a valid status.'
        ]
    ];

    protected $beforeInsert = ['setCreatedAt'];
    protected $beforeUpdate = ['setUpdatedAt'];

    protected function setCreatedAt(array $data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function setUpdatedAt(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Get upcoming consultations for a specific doctor
     * Only shows consultations where the date and time have arrived
     */
    public function getUpcomingConsultations($doctorId)
    {
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        // Get consultations where:
        // 1. Date is in the future, OR
        // 2. Date is today AND time has arrived (consultation_time <= current_time)
        $builder = $this->select('consultations.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                    ->where('consultations.doctor_id', $doctorId)
                    ->where('consultations.type', 'upcoming')
                    ->where('consultations.status', 'approved')
                    ->groupStart()
                        ->where('consultations.consultation_date >', $today) // Future dates
                        ->orGroupStart()
                            ->where('consultations.consultation_date', $today) // Today's date
                            ->where('consultations.consultation_time <=', $currentTime) // Time has arrived
                        ->groupEnd()
                    ->groupEnd()
                    ->orderBy('consultations.consultation_date', 'ASC')
                    ->orderBy('consultations.consultation_time', 'ASC');
        
        return $builder->findAll();
    }

    /**
     * Get all consultations for a specific doctor
     */
    public function getDoctorSchedule($doctorId)
    {
        return $this->select('consultations.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                    ->where('consultations.doctor_id', $doctorId)
                    ->orderBy('consultations.consultation_date', 'DESC')
                    ->orderBy('consultations.consultation_time', 'DESC')
                    ->findAll();
    }

    /**
     * Get consultations by date range for a doctor
     */
    public function getConsultationsByDateRange($doctorId, $startDate, $endDate)
    {
        return $this->select('consultations.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                    ->where('consultations.doctor_id', $doctorId)
                    ->where('consultations.consultation_date >=', $startDate)
                    ->where('consultations.consultation_date <=', $endDate)
                    ->orderBy('consultations.consultation_date', 'ASC')
                    ->orderBy('consultations.consultation_time', 'ASC')
                    ->findAll();
    }
}
