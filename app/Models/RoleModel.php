<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'role_name', 'role_description', 'permissions', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'permissions' => 'json',
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
        'role_name' => 'required|max_length[50]|is_unique[roles.role_name,id,{id}]',
        'role_description' => 'permit_empty|max_length[500]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'role_name' => [
            'required' => 'Role name is required',
            'is_unique' => 'Role name must be unique'
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
     * Get active roles
     */
    public function getActiveRoles()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Get role by name
     */
    public function getRoleByName($roleName)
    {
        return $this->where('role_name', $roleName)->first();
    }

    /**
     * Check if role has permission
     */
    public function hasPermission($roleId, $permission)
    {
        $role = $this->find($roleId);
        if (!$role || !$role['permissions']) {
            return false;
        }

        $permissions = is_string($role['permissions']) ? 
            json_decode($role['permissions'], true) : $role['permissions'];
        
        return in_array($permission, $permissions ?? []);
    }

    /**
     * Add permission to role
     */
    public function addPermission($roleId, $permission)
    {
        $role = $this->find($roleId);
        if (!$role) {
            return false;
        }

        $permissions = is_string($role['permissions']) ? 
            json_decode($role['permissions'], true) : $role['permissions'];
        $permissions = $permissions ?? [];

        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            return $this->update($roleId, ['permissions' => $permissions]);
        }

        return true;
    }

    /**
     * Remove permission from role
     */
    public function removePermission($roleId, $permission)
    {
        $role = $this->find($roleId);
        if (!$role) {
            return false;
        }

        $permissions = is_string($role['permissions']) ? 
            json_decode($role['permissions'], true) : $role['permissions'];
        $permissions = $permissions ?? [];

        $key = array_search($permission, $permissions);
        if ($key !== false) {
            unset($permissions[$key]);
            $permissions = array_values($permissions); // Re-index array
            return $this->update($roleId, ['permissions' => $permissions]);
        }

        return true;
    }
}
