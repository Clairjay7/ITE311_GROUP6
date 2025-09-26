<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'patient_id',
        'bill_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'reference_number',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer',
        'amount' => 'required|decimal',
        'payment_method' => 'required|in_list[cash,card,bank_transfer,insurance]',
        'status' => 'required|in_list[pending,completed,failed,refunded]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient ID is required',
            'integer' => 'Patient ID must be a valid number'
        ],
        'amount' => [
            'required' => 'Payment amount is required',
            'decimal' => 'Payment amount must be a valid decimal number'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get payments with patient details
     */
    public function getPaymentsWithDetails($limit = null)
    {
        $builder = $this->select('payments.*, 
                                 patients.first_name as patient_first_name,
                                 patients.last_name as patient_last_name,
                                 patients.email as patient_email')
                        ->join('patients', 'patients.id = payments.patient_id', 'left')
                        ->orderBy('payments.created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get total payments for a date range
     */
    public function getTotalPayments($startDate = null, $endDate = null)
    {
        $builder = $this->selectSum('amount')
                        ->where('status', 'completed');
        
        if ($startDate) {
            $builder->where('payment_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('payment_date <=', $endDate);
        }
        
        $result = $builder->get()->getRow();
        return $result->amount ?? 0;
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats()
    {
        return [
            'total_today' => $this->getTotalPayments(date('Y-m-d'), date('Y-m-d')),
            'total_month' => $this->getTotalPayments(date('Y-m-01'), date('Y-m-t')),
            'pending_count' => $this->where('status', 'pending')->countAllResults(),
            'completed_count' => $this->where('status', 'completed')->countAllResults()
        ];
    }
}
