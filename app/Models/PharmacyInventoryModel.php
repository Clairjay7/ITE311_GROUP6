<?php

namespace App\Models;

use CodeIgniter\Model;

class PharmacyInventoryModel extends Model
{
    protected $table = 'pharmacy_inventory';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'medicine_code', 'medicine_name', 'generic_name', 'brand_name', 'category',
        'strength', 'manufacturer', 'batch_number', 'manufacturing_date', 'expiry_date',
        'current_stock', 'minimum_stock', 'maximum_stock', 'unit', 'unit_price',
        'selling_price', 'storage_location', 'storage_conditions', 'supplier',
        'prescription_required', 'status', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'manufacturing_date' => 'datetime',
        'expiry_date' => 'datetime',
        'current_stock' => 'int',
        'minimum_stock' => 'int',
        'maximum_stock' => 'int',
        'unit_price' => 'float',
        'selling_price' => 'float',
        'prescription_required' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'medicine_code' => 'required|max_length[20]|is_unique[pharmacy_inventory.medicine_code,id,{id}]',
        'medicine_name' => 'required|max_length[150]',
        'category' => 'required|in_list[tablet,capsule,syrup,injection,cream,drops,inhaler,other]',
        'expiry_date' => 'required|valid_date',
        'current_stock' => 'permit_empty|is_natural',
        'minimum_stock' => 'permit_empty|is_natural',
        'maximum_stock' => 'permit_empty|is_natural',
        'unit_price' => 'permit_empty|decimal',
        'selling_price' => 'permit_empty|decimal',
        'status' => 'permit_empty|in_list[active,inactive,expired,recalled,out_of_stock]'
    ];

    protected $validationMessages = [
        'medicine_code' => [
            'required' => 'Medicine code is required',
            'is_unique' => 'Medicine code must be unique'
        ],
        'medicine_name' => [
            'required' => 'Medicine name is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateMedicineCode'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['checkStockStatus'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function generateMedicineCode(array $data)
    {
        if (!isset($data['data']['medicine_code'])) {
            $data['data']['medicine_code'] = 'MED' . str_pad($this->countAll() + 1, 6, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    protected function checkStockStatus(array $data)
    {
        if (isset($data['data']['current_stock'])) {
            if ($data['data']['current_stock'] <= 0) {
                $data['data']['status'] = 'out_of_stock';
            } elseif (isset($data['data']['minimum_stock']) && 
                     $data['data']['current_stock'] <= $data['data']['minimum_stock']) {
                // Keep current status but could trigger low stock alert
            }
        }
        return $data;
    }

    /**
     * Get active medicines
     */
    public function getActiveMedicines()
    {
        return $this->where('status', 'active')
                    ->orderBy('medicine_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get low stock medicines
     */
    public function getLowStockMedicines()
    {
        return $this->where('current_stock <=', 'minimum_stock', false)
                    ->where('status', 'active')
                    ->orderBy('current_stock', 'ASC')
                    ->findAll();
    }

    /**
     * Get expired medicines
     */
    public function getExpiredMedicines()
    {
        return $this->where('expiry_date <', date('Y-m-d'))
                    ->whereIn('status', ['active', 'expired'])
                    ->orderBy('expiry_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get medicines expiring soon
     */
    public function getMedicinesExpiringSoon($days = 30)
    {
        $expiryDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->where('expiry_date <=', $expiryDate)
                    ->where('expiry_date >=', date('Y-m-d'))
                    ->where('status', 'active')
                    ->orderBy('expiry_date', 'ASC')
                    ->findAll();
    }

    /**
     * Search medicines
     */
    public function searchMedicines($searchTerm)
    {
        return $this->like('medicine_name', $searchTerm)
                    ->orLike('generic_name', $searchTerm)
                    ->orLike('brand_name', $searchTerm)
                    ->orLike('medicine_code', $searchTerm)
                    ->where('status', 'active')
                    ->orderBy('medicine_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get medicines by category
     */
    public function getMedicinesByCategory($category)
    {
        return $this->where('category', $category)
                    ->where('status', 'active')
                    ->orderBy('medicine_name', 'ASC')
                    ->findAll();
    }

    /**
     * Update stock
     */
    public function updateStock($medicineId, $quantity, $operation = 'subtract')
    {
        $medicine = $this->find($medicineId);
        if (!$medicine) {
            return false;
        }

        $newStock = $medicine['current_stock'];
        if ($operation === 'add') {
            $newStock += $quantity;
        } else {
            $newStock -= $quantity;
        }

        $newStock = max(0, $newStock); // Ensure stock doesn't go negative

        $updateData = ['current_stock' => $newStock];
        
        // Update status based on stock level
        if ($newStock <= 0) {
            $updateData['status'] = 'out_of_stock';
        } elseif ($newStock <= $medicine['minimum_stock'] && $medicine['status'] === 'out_of_stock') {
            $updateData['status'] = 'active'; // Reactivate if restocked
        }

        return $this->update($medicineId, $updateData);
    }

    /**
     * Check medicine availability
     */
    public function checkAvailability($medicineId, $requiredQuantity)
    {
        $medicine = $this->find($medicineId);
        
        if (!$medicine || $medicine['status'] !== 'active') {
            return false;
        }

        return $medicine['current_stock'] >= $requiredQuantity;
    }

    /**
     * Get medicine by code
     */
    public function getMedicineByCode($medicineCode)
    {
        return $this->where('medicine_code', $medicineCode)->first();
    }

    /**
     * Get inventory alerts
     */
    public function getInventoryAlerts()
    {
        $alerts = [];

        // Low stock alerts
        $lowStock = $this->getLowStockMedicines();
        foreach ($lowStock as $medicine) {
            $alerts[] = [
                'type' => 'low_stock',
                'medicine' => $medicine,
                'message' => "Low stock: {$medicine['medicine_name']} ({$medicine['current_stock']} remaining)"
            ];
        }

        // Expiry alerts
        $expiringSoon = $this->getMedicinesExpiringSoon(30);
        foreach ($expiringSoon as $medicine) {
            $alerts[] = [
                'type' => 'expiring_soon',
                'medicine' => $medicine,
                'message' => "Expiring soon: {$medicine['medicine_name']} (expires on {$medicine['expiry_date']})"
            ];
        }

        // Expired alerts
        $expired = $this->getExpiredMedicines();
        foreach ($expired as $medicine) {
            $alerts[] = [
                'type' => 'expired',
                'medicine' => $medicine,
                'message' => "Expired: {$medicine['medicine_name']} (expired on {$medicine['expiry_date']})"
            ];
        }

        return $alerts;
    }

    /**
     * Get inventory statistics
     */
    public function getInventoryStats()
    {
        return [
            'total_medicines' => $this->countAll(),
            'active_medicines' => $this->where('status', 'active')->countAllResults(),
            'low_stock_count' => count($this->getLowStockMedicines()),
            'expired_count' => count($this->getExpiredMedicines()),
            'expiring_soon_count' => count($this->getMedicinesExpiringSoon(30)),
            'out_of_stock_count' => $this->where('status', 'out_of_stock')->countAllResults(),
            'total_value' => $this->selectSum('current_stock * unit_price', 'total_value')->first()['total_value'] ?? 0
        ];
    }
}
