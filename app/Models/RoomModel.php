<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model
{
    protected $table            = 'rooms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'ward',
        'room_number',
        'room_type',
        'bed_count',
        'price',
        'status',
        'current_patient_id',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAvailableByWard(string $ward): array
    {
        return $this->where('ward', $ward)
            ->where('status', 'Available')
            ->orderBy('room_number', 'ASC')
            ->findAll();
    }

    public function getAvailableByType(string $roomType): array
    {
        return $this->where('room_type', $roomType)
            ->where('status', 'Available')
            ->orderBy('room_number', 'ASC')
            ->findAll();
    }
}
