<?php

namespace App\Models;

use CodeIgniter\Model;

class AdmissionModel extends Model
{
    protected $table = 'admissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'consultation_id',
        'patient_id',
        'doctor_id',
        'room_id',
        'bed_number',
        'room_type',
        'admission_reason',
        'attending_physician_id',
        'initial_notes',
        'admission_date',
        'status',
        'discharge_status',
        'requested_by',
        'processed_by',
        'discharge_date',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer|greater_than[0]',
        'attending_physician_id' => 'required|integer|greater_than[0]',
        'admission_date' => 'required|valid_date',
        'status' => 'required|in_list[pending,admitted,discharged,cancelled]',
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient is required.',
        ],
        'attending_physician_id' => [
            'required' => 'Attending physician is required.',
        ],
    ];
}

