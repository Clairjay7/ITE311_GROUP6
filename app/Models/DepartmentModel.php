<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'department_name', 'department_code', 'head_doctor_id', 'description', 
        'location', 'phone', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_active' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'department_name' => 'required|max_length[100]',
        'department_code' => 'required|max_length[10]|is_unique[departments.department_code,id,{id}]',
        'head_doctor_id' => 'permit_empty|is_natural_no_zero',
        'phone' => 'permit_empty|max_length[20]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'department_name' => [
            'required' => 'Department name is required'
        ],
        'department_code' => [
            'required' => 'Department code is required',
            'is_unique' => 'Department code must be unique'
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
     * Get active departments
     */
    public function getActiveDepartments()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Get departments with head doctor info
     */
    public function getDepartmentsWithHeadDoctor()
    {
        return $this->select('departments.*, users.first_name, users.last_name')
                    ->join('users', 'users.id = departments.head_doctor_id', 'left')
                    ->where('departments.is_active', true)
                    ->findAll();
    }

    /**
     * Get department by code
     */
    public function getDepartmentByCode($code)
    {
        return $this->where('department_code', $code)->first();
    }

    /**
     * Get departments with room count
     */
    public function getDepartmentsWithRoomCount()
    {
        return $this->select('departments.*, COUNT(rooms.id) as room_count')
                    ->join('rooms', 'rooms.department_id = departments.id', 'left')
                    ->where('departments.is_active', true)
                    ->groupBy('departments.id')
                    ->findAll();
    }

    /**
     * Search departments
     */
    public function searchDepartments($searchTerm)
    {
        return $this->like('department_name', $searchTerm)
                    ->orLike('department_code', $searchTerm)
                    ->orLike('location', $searchTerm)
                    ->where('is_active', true)
                    ->findAll();
    }
}
