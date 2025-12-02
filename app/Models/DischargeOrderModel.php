<?php

namespace App\Models;

use CodeIgniter\Model;

class DischargeOrderModel extends Model
{
    protected $table = 'discharge_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'admission_id',
        'patient_id',
        'doctor_id',
        'final_diagnosis',
        'treatment_summary',
        'recommendations',
        'follow_up_instructions',
        'medications_prescribed',
        'discharge_date',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'admission_id' => 'required|integer|greater_than[0]',
        'patient_id' => 'required|integer|greater_than[0]',
        'doctor_id' => 'required|integer|greater_than[0]',
        'status' => 'required|in_list[pending,approved,completed,cancelled]',
    ];
}

