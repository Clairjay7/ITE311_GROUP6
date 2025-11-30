<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table = 'stock_monitoring';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'item_name',
        'category',
        'quantity',
        'threshold',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'item_name' => 'required|max_length[255]',
        'category' => 'required|max_length[100]',
        'quantity' => 'required|integer|greater_than_equal_to[0]',
        'threshold' => 'required|integer|greater_than_equal_to[0]',
    ];
}

