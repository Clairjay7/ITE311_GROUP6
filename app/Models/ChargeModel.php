<?php

namespace App\Models;

use CodeIgniter\Model;

class ChargeModel extends Model
{
    protected $table = 'charges';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'consultation_id',
        'patient_id',
        'charge_number',
        'total_amount',
        'status',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer|greater_than[0]',
        'charge_number' => 'required|max_length[50]',
        'total_amount' => 'required|decimal',
        'status' => 'required|in_list[pending,approved,paid,cancelled]',
    ];


    /**
     * Generate unique charge number
     */
    public function generateChargeNumber()
    {
        $prefix = 'CHG-' . date('Ymd');
        $lastCharge = $this->where('charge_number LIKE', $prefix . '%')
            ->orderBy('charge_number', 'DESC')
            ->first();
        
        if ($lastCharge) {
            $lastNumber = (int) substr($lastCharge['charge_number'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}

