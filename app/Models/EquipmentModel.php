<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentModel extends Model
{
    protected $table = 'equipment';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'equipment_name',
        'equipment_type',
        'serial_number',
        'status',
        'last_maintenance_date',
        'next_maintenance_date',
        'last_calibration_date',
        'next_calibration_date',
        'usage_hours',
        'condition',
    ];

    protected array $casts = [
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'last_calibration_date' => 'date',
        'next_calibration_date' => 'date',
        'usage_hours' => 'float',
    ];

    public function getEquipment(array $filters = []): array
    {
        $builder = $this->builder()->orderBy('updated_at', 'DESC');

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        if (!empty($filters['condition'])) {
            $builder->where('condition', $filters['condition']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('equipment_name', $filters['search'])
                ->orLike('equipment_type', $filters['search'])
                ->orLike('serial_number', $filters['search'])
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}
