<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role',
        'role_id',
        'status',
        'first_name',
        'middle_name',
        'last_name',
        'contact',
        'address',
        'employee_id',
        'prc_license',
        'nursing_license',
        'specialization',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;
    protected $dateFormat = 'datetime';
}
