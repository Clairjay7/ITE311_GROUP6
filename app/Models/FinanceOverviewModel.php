<?php

namespace App\Models;

use CodeIgniter\Model;

class FinanceOverviewModel extends Model
{
    protected $table = 'finance_overview';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'period_type',
        'period_start',
        'period_end',
        'total_revenue',
        'total_expenses',
        'net_profit',
        'total_bills',
        'paid_bills',
        'pending_bills',
        'insurance_claims_total',
        'notes',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'period_type' => 'required|in_list[daily,weekly,monthly,yearly]',
        'period_start' => 'required|valid_date',
        'period_end' => 'required|valid_date',
        'total_revenue' => 'permit_empty|decimal',
        'total_expenses' => 'permit_empty|decimal',
        'net_profit' => 'permit_empty|decimal',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}

