<?php

namespace App\Models;

use CodeIgniter\Model;

class PrescriptionModel extends Model
{
    protected $table = 'prescriptions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'prescription_id',
        'patient_id',
        'doctor_id',
        'medical_record_id',
        'medication_name',
        'medicine_name',
        'dosage',
        'frequency',
        'duration',
        'instructions',
        'quantity',
        'status',
        'dispensed_at',
        'dispensed_by',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'dispensed_at' => 'datetime'
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
        'medication_name' => 'required_without[medicine_name]|max_length[200]',
        'medicine_name' => 'required_without[medication_name]|max_length[150]',
        'quantity' => 'permit_empty|integer',
        'status' => 'permit_empty|in_list[pending,dispensed,completed,cancelled]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient is required'
        ],
        'doctor_id' => [
            'required' => 'Doctor is required'
        ],
        'medication_name' => [
            'required_without' => 'Medication name or medicine name is required'
        ],
        'medicine_name' => [
            'required_without' => 'Medicine name or medication name is required'
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
     * Get prescriptions by patient
     */
    public function getByPatient($patientId)
    {
        return $this->where('patient_id', $patientId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get prescriptions by doctor
     */
    public function getByDoctor($doctorId)
    {
        return $this->select('prescriptions.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = prescriptions.patient_id')
                    ->where('prescriptions.doctor_id', $doctorId)
                    ->orderBy('prescriptions.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get pending prescriptions
     */
    public function getPendingPrescriptions()
    {
        return $this->select('prescriptions.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number,
                             doctors.first_name as doctor_first_name,
                             doctors.last_name as doctor_last_name')
                    ->join('patients', 'patients.id = prescriptions.patient_id')
                    ->join('users as doctors', 'doctors.id = prescriptions.doctor_id')
                    ->where('prescriptions.status', 'pending')
                    ->orderBy('prescriptions.created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Mark as dispensed
     */
    public function markAsDispensed($prescriptionId, $dispensedBy)
    {
        return $this->update($prescriptionId, [
            'status' => 'dispensed',
            'dispensed_at' => date('Y-m-d H:i:s'),
            'dispensed_by' => $dispensedBy
        ]);
    }

    /**
     * Search prescriptions
     */
    public function searchPrescriptions($searchTerm)
    {
        return $this->select('prescriptions.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = prescriptions.patient_id')
                    ->like('patients.first_name', $searchTerm)
                    ->orLike('patients.last_name', $searchTerm)
                    ->orLike('patients.patient_id', $searchTerm)
                    ->orLike('prescriptions.medicine_name', $searchTerm)
                    ->orderBy('prescriptions.created_at', 'DESC')
                    ->findAll();
    }
}
