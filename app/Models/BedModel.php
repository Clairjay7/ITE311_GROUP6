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
        return $this->where('room_id', $roomId)
            ->where('status', 'available')
            ->orderBy('bed_number', 'ASC')
            ->findAll();
    }
}

