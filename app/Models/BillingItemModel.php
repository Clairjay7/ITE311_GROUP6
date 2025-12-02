<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingItemModel extends Model
{
    protected $table = 'billing_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'charge_id',
        'item_type',
        'item_name',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'related_id',
        'related_type',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'charge_id' => 'required|integer|greater_than[0]',
        'item_type' => 'required|in_list[consultation,lab_test,medication,procedure,other]',
        'item_name' => 'required|max_length[255]',
        'quantity' => 'required|decimal|greater_than[0]',
        'unit_price' => 'required|decimal|greater_than_equal_to[0]',
        'total_price' => 'required|decimal|greater_than_equal_to[0]',
    ];
}

