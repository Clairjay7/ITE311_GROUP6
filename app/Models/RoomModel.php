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
        $db = \Config\Database::connect();
        return $db->table('rooms')
            ->where('ward', $ward)
            ->where('(LOWER(status) = "available" OR status IS NULL)', null, false)
            ->where('(LOWER(status) != "occupied" OR status IS NULL)', null, false)
            ->where('current_patient_id IS NULL', null, false)
            ->orderBy('room_number', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getAvailableByType(string $roomType): array
    {
        $db = \Config\Database::connect();
        return $db->table('rooms')
            ->where('room_type', $roomType)
            ->where('(LOWER(status) = "available" OR status IS NULL)', null, false)
            ->where('(LOWER(status) != "occupied" OR status IS NULL)', null, false)
            ->where('current_patient_id IS NULL', null, false)
            ->orderBy('room_number', 'ASC')
            ->get()
            ->getResultArray();
    }
}
