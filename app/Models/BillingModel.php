<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingModel extends Model
{
    protected $table = 'billing';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'patient_id',
        'order_id',
        'service',
        'medicine_name',
        'dosage',
        'order_description',
        'quantity',
        'unit_price',
        'administration_fee',
        'amount',
        'status',
        'nurse_id',
        'administered_at',
        'invoice_number',
        'processed_by',
        'paid_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer',
        'service' => 'required|max_length[255]',
        'amount' => 'required|decimal',
        'status' => 'required|in_list[pending,paid,cancelled]',
    ];
}

