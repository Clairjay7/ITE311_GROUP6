<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model
{
    protected $table = 'rooms';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'room_number', 'department_id', 'room_type', 'capacity', 'current_occupancy',
        'floor', 'equipment', 'status', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'equipment' => 'json',
        'capacity' => 'int',
        'current_occupancy' => 'int',
        'floor' => 'int'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'room_number' => 'required|max_length[20]|is_unique[rooms.room_number,id,{id}]',
        'department_id' => 'required|is_natural_no_zero',
        'room_type' => 'required|in_list[consultation,surgery,ward,icu,emergency,lab,pharmacy,admin]',
        'capacity' => 'permit_empty|is_natural',
        'current_occupancy' => 'permit_empty|is_natural',
        'floor' => 'permit_empty|is_natural',
        'status' => 'permit_empty|in_list[available,occupied,maintenance,reserved]'
    ];

    protected $validationMessages = [
        'room_number' => [
            'required' => 'Room number is required',
            'is_unique' => 'Room number must be unique'
        ],
        'department_id' => [
            'required' => 'Department is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
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
     * Get available rooms
     */
    public function getAvailableRooms()
    {
        return $this->where('status', 'available')->findAll();
    }

    /**
     * Get rooms by department
     */
    public function getRoomsByDepartment($departmentId)
    {
        return $this->where('department_id', $departmentId)->findAll();
    }

    /**
     * Get rooms with department info
     */
    public function getRoomsWithDepartment()
    {
        return $this->select('rooms.*, departments.department_name')
                    ->join('departments', 'departments.id = rooms.department_id')
                    ->findAll();
    }

    /**
     * Get rooms by type
     */
    public function getRoomsByType($roomType)
    {
        return $this->where('room_type', $roomType)->findAll();
    }

    /**
     * Get available rooms by type
     */
    public function getAvailableRoomsByType($roomType)
    {
        return $this->where('room_type', $roomType)
                    ->where('status', 'available')
                    ->findAll();
    }

    /**
     * Update room occupancy
     */
    public function updateOccupancy($roomId, $occupancy)
    {
        $room = $this->find($roomId);
        if (!$room) {
            return false;
        }

        $status = 'available';
        if ($occupancy > 0) {
            $status = ($occupancy >= $room['capacity']) ? 'occupied' : 'available';
        }

        return $this->update($roomId, [
            'current_occupancy' => $occupancy,
            'status' => $status
        ]);
    }

    /**
     * Search rooms
     */
    public function searchRooms($searchTerm)
    {
        return $this->select('rooms.*, departments.department_name')
                    ->join('departments', 'departments.id = rooms.department_id')
                    ->like('rooms.room_number', $searchTerm)
                    ->orLike('departments.department_name', $searchTerm)
                    ->orLike('rooms.room_type', $searchTerm)
                    ->findAll();
    }

    /**
     * Get room utilization stats
     */
    public function getRoomUtilizationStats()
    {
        return $this->select('room_type, status, COUNT(*) as count')
                    ->groupBy(['room_type', 'status'])
                    ->findAll();
    }
}
