<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentReportModel extends Model
{
    protected $table = 'payment_reports';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'report_date',
        'patient_id',
        'billing_id',
        'payment_method',
        'amount',
        'reference_number',
        'status',
        'payment_date',
        'processed_by',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'report_date' => 'required|valid_date',
        'payment_method' => 'required|in_list[cash,credit_card,debit_card,bank_transfer,check,insurance,other]',
        'amount' => 'required|decimal|greater_than[0]',
        'status' => 'required|in_list[pending,completed,failed,refunded]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}

