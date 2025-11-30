<?php

namespace App\Models;

use CodeIgniter\Model;

class PharmacyModel extends Model
{
    protected $table = 'pharmacy';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'item_name',
        'description',
        'quantity',
        'price',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'item_name' => 'required|max_length[255]',
        'description' => 'permit_empty|max_length[500]',
        'quantity' => 'required|integer|greater_than_equal_to[0]',
        'price' => 'required|decimal|greater_than_equal_to[0]',
    ];
}

