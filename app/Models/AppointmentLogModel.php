<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentLogModel extends Model
{
    protected $table = 'appointment_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'appointment_id',
        'status',
        'changed_by',
        'notes',
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;
}

