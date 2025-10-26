<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'username', 
        'email', 
        'password_hash',
        'first_name',
        'last_name',
        'profile_pic',
        'status',
        'role',
        'last_login'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Not using soft deletes

    // Validation rules
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password_hash' => 'permit_empty|min_length[6]',
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'role' => 'required|in_list[superadmin,doctor,nurse,receptionist,laboratory,pharmacist,accountant,it_staff]',
        'status' => 'permit_empty|in_list[active,inactive,suspended]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username is required',
            'min_length' => 'Username must be at least 3 characters',
            'is_unique' => 'Username already exists'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Email already exists'
        ],
        'password_hash' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 6 characters'
        ],
        'first_name' => [
            'required' => 'First name is required'
        ],
        'last_name' => [
            'required' => 'Last name is required'
        ],
        'role' => [
            'required' => 'Role is required',
            'in_list' => 'Please select a valid role'
        ]
    ];

    // Hash password before saving
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password_hash']) || empty($data['data']['password_hash'])) {
            unset($data['data']['password_hash']);
            return $data;
        }

        // Only hash if it's not already hashed (for updates)
        if (!password_get_info($data['data']['password_hash'])['algo']) {
            $data['data']['password_hash'] = password_hash($data['data']['password_hash'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }

    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }

    public function getUsersWithRoles()
    {
        // Since we're storing role as a string in the users table, not using a separate roles table
        return $this->select('users.*')
                   ->where('status', 'active')
                   ->findAll();
    }

    public function getUserByUsername(string $username, string $role = null)
    {
        $builder = $this->builder();
        $builder->where('username', $username);
        
        if ($role !== null) {
            $builder->where('role', $role);
        }
        
        return $builder->get()->getRowArray();
    }
    
    public function getUserById(int $id)
    {
        return $this->find($id);
    }
    
    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
    
    public function getUsersByRole(string $role)
    {
        return $this->where('role', $role)
                   ->where('status', 'active')
                   ->findAll();
    }

    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)
                   ->where('status', 'active')
                   ->first();
    }

    public function getUserWithDetails($userId)
    {
        return $this->find($userId);
    }

    public function getUserRoles($userId)
    {
        $builder = $this->db->table('user_roles');
        $builder->select('roles.name, roles.id');
        $builder->join('roles', 'roles.id = user_roles.role_id');
        $builder->where('user_roles.user_id', $userId);
        
        return $builder->get()->getResultArray();
    }
}
