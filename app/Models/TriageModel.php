<?php

namespace App\Models;

use CodeIgniter\Model;

class TriageModel extends Model
{
    protected $table            = 'triage';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'patient_id',
        'nurse_id',
        'triage_level',
        'vital_signs',
        'chief_complaint',
        'notes',
        'status',
        'sent_to_doctor',
        'doctor_id',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'patient_id' => 'required|integer',
        'nurse_id' => 'required|integer',
        'triage_level' => 'required|in_list[Critical,Moderate,Minor]',
        'status' => 'permit_empty|in_list[pending,completed,sent_to_doctor]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}

