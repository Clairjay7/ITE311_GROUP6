<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'full_name', 'specialization', 'phone', 'email'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function allWithUsers()
    {
        return $this->select('doctors.*, users.username')
                    ->join('users', 'users.id = doctors.user_id', 'left')
                    ->findAll();
    }
}
