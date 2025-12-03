<?php

namespace App\Models;

use CodeIgniter\Model;

class LabRequestModel extends Model
{
    protected $table = 'lab_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'patient_id',
        'doctor_id',
        'nurse_id',
        'test_type',
        'test_name',
        'requested_by',
        'priority',
        'instructions',
        'status',
        'requested_date',
        'payment_status',
        'charge_id',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer',
        'test_type' => 'required|max_length[255]',
        'test_name' => 'required|max_length[255]',
        'requested_by' => 'required|in_list[doctor,nurse,admin]',
        'priority' => 'required|in_list[routine,urgent,stat]',
        'status' => 'required|in_list[pending,specimen_collected,in_progress,completed,cancelled]',
        'requested_date' => 'required|valid_date',
    ];
}

