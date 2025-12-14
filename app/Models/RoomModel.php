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
        
        // Get all rooms for this ward
        $allRooms = $db->table('rooms')
            ->where('ward', $ward)
            ->orderBy('room_number', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get occupied room IDs from patients table
        $occupiedRoomIds = [];
        if ($db->tableExists('patients')) {
            $occupiedRooms = $db->table('patients')
                ->select('room_id')
                ->where('room_id IS NOT NULL', null, false)
                ->where('room_id !=', '')
                ->get()
                ->getResultArray();
            $occupiedRoomIds = array_column($occupiedRooms, 'room_id');
        }
        
        // Filter available rooms
        $availableRooms = [];
        foreach ($allRooms as $room) {
            $roomStatus = strtolower(trim($room['status'] ?? ''));
            $isOccupied = ($roomStatus === 'occupied') 
                || !empty($room['current_patient_id'])
                || in_array($room['id'], $occupiedRoomIds);
            
            if (!$isOccupied) {
                $availableRooms[] = $room;
            }
        }
        
        return $availableRooms;
    }

    public function getAvailableByType(string $roomType): array
    {
        $db = \Config\Database::connect();
        
        // Get all rooms for this type
        $allRooms = $db->table('rooms')
            ->where('room_type', $roomType)
            ->orderBy('room_number', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get occupied room IDs from patients table
        $occupiedRoomIds = [];
        if ($db->tableExists('patients')) {
            $occupiedRooms = $db->table('patients')
                ->select('room_id')
                ->where('room_id IS NOT NULL', null, false)
                ->where('room_id !=', '')
                ->get()
                ->getResultArray();
            $occupiedRoomIds = array_column($occupiedRooms, 'room_id');
        }
        
        // Filter available rooms
        $availableRooms = [];
        foreach ($allRooms as $room) {
            $roomStatus = strtolower(trim($room['status'] ?? ''));
            $isOccupied = ($roomStatus === 'occupied') 
                || !empty($room['current_patient_id'])
                || in_array($room['id'], $occupiedRoomIds);
            
            if (!$isOccupied) {
                $availableRooms[] = $room;
            }
        }
        
        return $availableRooms;
    }
}
