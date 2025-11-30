<?php

namespace App\Models;

use CodeIgniter\Model;

class LabStatusHistoryModel extends Model
{
    protected $table = 'lab_status_history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'lab_request_id',
        'status',
        'changed_by',
        'notes',
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;
}

