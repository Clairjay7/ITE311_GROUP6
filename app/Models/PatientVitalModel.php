<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientVitalModel extends Model
{
    protected $table = 'patient_vitals';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'patient_id',
        'nurse_id',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'oxygen_saturation',
        'respiratory_rate',
        'weight',
        'height',
        'notes',
        'recorded_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer',
        'nurse_id' => 'required|integer',
    ];
}

