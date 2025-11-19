<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorScheduleModel extends Model
{
    protected $table            = 'doctor_schedules';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'doctor_id',
        'shift_date',
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
