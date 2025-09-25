<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
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
    protected $deletedField  = 'deleted_at';

    // Hash password before saving
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password_hash']) || empty($data['data']['password_hash'])) {
            unset($data['data']['password_hash']);
            return $data;
        }

        $data['data']['password_hash'] = password_hash($data['data']['password_hash'], PASSWORD_DEFAULT);
        return $data;
    }

    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
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
                    ->where('deleted_at', null)
                    ->findAll();
    }

    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)
                   ->where('deleted_at', null)
                   ->first();
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
