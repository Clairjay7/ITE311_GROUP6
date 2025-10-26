<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;

    protected $allowedFields = [
        'item_name',
        'description',
        'quantity',
        'supplier',
        'expiration_date',
        'status',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'item_name' => 'required|max_length[150]',
        'quantity' => 'permit_empty|integer',
        'expiration_date' => 'permit_empty|valid_date',
        'status' => 'permit_empty|in_list[available,low_stock,out_of_stock,expired,inactive]',
    ];

    public function getAllWithHighlights(): array
    {
        $items = $this->orderBy('item_name', 'ASC')->findAll();
        $today = strtotime(date('Y-m-d'));
        $warningDate = strtotime('+7 days', $today);

        return array_map(static function (array $item) use ($today, $warningDate) {
            $item['is_low_stock'] = isset($item['quantity']) && (int) $item['quantity'] <= 5;

            $item['is_expiring_soon'] = false;
            if (!empty($item['expiration_date'])) {
                $expiry = strtotime($item['expiration_date']);
                $item['is_expiring_soon'] = $expiry !== false && $expiry >= $today && $expiry <= $warningDate;
            }

            return $item;
        }, $items);
    }
}
