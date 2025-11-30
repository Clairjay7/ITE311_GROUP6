<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'patient_id',
        'date',
        'time',
        'doctor',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer',
        'date' => 'required|valid_date',
        'time' => 'required',
        'doctor' => 'required|max_length[255]',
        'status' => 'required|in_list[pending,confirmed,completed,cancelled]',
    ];
}

