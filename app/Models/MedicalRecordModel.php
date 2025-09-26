<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicalRecordModel extends Model
{
    protected $table = 'medical_records';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'patient_id', 'doctor_id', 'appointment_id', 'visit_date', 'chief_complaint',
        'history_present_illness', 'physical_examination', 'diagnosis', 'treatment_plan',
        'notes', 'follow_up_date', 'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'visit_date' => 'datetime',
        'follow_up_date' => 'datetime'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'patient_id' => 'required|is_natural_no_zero',
        'doctor_id' => 'required|is_natural_no_zero',
        'visit_date' => 'required|valid_date',
        'status' => 'permit_empty|in_list[active,completed,cancelled]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient is required'
        ],
        'doctor_id' => [
            'required' => 'Doctor is required'
        ],
        'visit_date' => [
            'required' => 'Visit date is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get records by patient
     */
    public function getRecordsByPatient($patientId)
    {
        return $this->where('patient_id', $patientId)
                    ->orderBy('visit_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get records by doctor
     */
    public function getRecordsByDoctor($doctorId)
    {
        return $this->where('doctor_id', $doctorId)
                    ->orderBy('visit_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get records with patient and doctor info
     */
    public function getRecordsWithDetails($limit = null)
    {
        $builder = $this->select('medical_records.*, 
                                 patients.first_name as patient_first_name, 
                                 patients.last_name as patient_last_name,
                                 patients.patient_id as patient_number,
                                 doctors.first_name as doctor_first_name,
                                 doctors.last_name as doctor_last_name')
                        ->join('patients', 'patients.id = medical_records.patient_id')
                        ->join('users as doctors', 'doctors.id = medical_records.doctor_id')
                        ->orderBy('medical_records.visit_date', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get patient medical history
     */
    public function getPatientHistory($patientId)
    {
        return $this->select('medical_records.*, 
                             users.first_name as doctor_first_name,
                             users.last_name as doctor_last_name')
                    ->join('users', 'users.id = medical_records.doctor_id')
                    ->where('medical_records.patient_id', $patientId)
                    ->orderBy('medical_records.visit_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get recent records for follow-up
     */
    public function getRecordsForFollowUp($days = 7)
    {
        $followUpDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->select('medical_records.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.phone as patient_phone')
                    ->join('patients', 'patients.id = medical_records.patient_id')
                    ->where('medical_records.follow_up_date <=', $followUpDate)
                    ->where('medical_records.status', 'active')
                    ->orderBy('medical_records.follow_up_date', 'ASC')
                    ->findAll();
    }

    /**
     * Search medical records
     */
    public function searchRecords($searchTerm)
    {
        return $this->select('medical_records.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = medical_records.patient_id')
                    ->like('patients.first_name', $searchTerm)
                    ->orLike('patients.last_name', $searchTerm)
                    ->orLike('patients.patient_id', $searchTerm)
                    ->orLike('medical_records.diagnosis', $searchTerm)
                    ->orLike('medical_records.chief_complaint', $searchTerm)
                    ->orderBy('medical_records.visit_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get records by date range
     */
    public function getRecordsByDateRange($startDate, $endDate)
    {
        return $this->where('visit_date >=', $startDate)
                    ->where('visit_date <=', $endDate)
                    ->orderBy('visit_date', 'DESC')
                    ->findAll();
    }
}
