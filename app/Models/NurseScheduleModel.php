<?php

namespace App\Models;

use CodeIgniter\Model;

class NurseScheduleModel extends Model
{
    protected $table            = 'nurse_schedules';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nurse_id',
        'shift_date',
        'shift_type',
        'start_time',
        'end_time',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

