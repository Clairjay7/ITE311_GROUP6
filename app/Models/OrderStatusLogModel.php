<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderStatusLogModel extends Model
{
    protected $table = 'order_status_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'order_id',
        'status',
        'changed_by',
        'notes',
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;
}

