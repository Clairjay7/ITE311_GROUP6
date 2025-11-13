<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorDirectoryModel extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['doctor_name', 'specialization', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
