<?php

namespace App\Models;

use CodeIgniter\Model;

class BedModel extends Model
{
    protected $table = 'beds';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'room_id',
        'bed_number',
        'status',
        'current_patient_id',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'room_id' => 'required|integer|greater_than[0]',
        'bed_number' => 'required|max_length[50]',
        'status' => 'required|in_list[available,occupied,maintenance]',
    ];

    /**
     * Get available beds for a room
     */
    public function getAvailableBedsByRoom($roomId)
    {
        $db = \Config\Database::connect();
        
        // Get all beds for this room
        $allBeds = $this->where('room_id', $roomId)
            ->orderBy('bed_number', 'ASC')
            ->findAll();
        
        // Get occupied bed IDs from patients table
        $occupiedBedIds = [];
        if ($db->tableExists('patients') && $db->fieldExists('bed_id', 'patients')) {
            $occupiedBeds = $db->table('patients')
                ->select('bed_id')
                ->where('bed_id IS NOT NULL', null, false)
                ->where('bed_id !=', '')
                ->get()
                ->getResultArray();
            $occupiedBedIds = array_column($occupiedBeds, 'bed_id');
        }
        
        // Filter available beds
        $availableBeds = [];
        foreach ($allBeds as $bed) {
            $bedStatus = strtolower(trim($bed['status'] ?? ''));
            $isOccupied = ($bedStatus === 'occupied')
                || !empty($bed['current_patient_id'])
                || in_array($bed['id'], $occupiedBedIds);
            
            if (!$isOccupied) {
                $availableBeds[] = $bed;
            }
        }
        
        return $availableBeds;
    }
}

