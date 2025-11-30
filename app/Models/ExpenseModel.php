<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'expense_date',
        'category',
        'description',
        'amount',
        'vendor',
        'invoice_number',
        'payment_method',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'expense_date' => 'required|valid_date',
        'category' => 'required|in_list[medical_supplies,equipment,utilities,salaries,maintenance,office_supplies,insurance,rent,other]',
        'description' => 'required|min_length[3]|max_length[255]',
        'amount' => 'required|decimal|greater_than[0]',
        'payment_method' => 'required|in_list[cash,check,bank_transfer,credit_card,other]',
        'status' => 'required|in_list[pending,approved,paid,rejected]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}

