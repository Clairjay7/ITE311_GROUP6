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
        'generic_name',
        'category',
        'description',
        'strength',
        'dosage_form',
        'quantity',
        'batch_number',
        'expiration_date',
        'reorder_level',
        'price',
        'unit_price',
        'selling_price',
        'markup_percent',
        'supplier_name',
        'supplier_contact',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'item_name' => 'permit_empty|max_length[255]',
        'description' => 'permit_empty|max_length[500]',
        'quantity' => 'permit_empty|integer|greater_than_equal_to[0]',
        'price' => 'permit_empty|decimal|greater_than_equal_to[0]',
    ];
    
    // Skip validation when inserting/updating - validation is handled in controller
    protected $skipValidation = false;
}

