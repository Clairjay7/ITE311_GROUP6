<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminPatientModel extends Model
{
    protected $table = 'admin_patients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'firstname',
        'lastname',
        'birthdate',
        'gender',
        'contact',
        'address',
        'doctor_id',
        'assigned_nurse_id',
        'visit_type',
        'triage_status',
        'is_doctor_checked',
        'doctor_check_status',
        'nurse_vital_status',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'firstname' => 'required|min_length[2]|max_length[100]',
        'lastname' => 'required|min_length[2]|max_length[100]',
        'birthdate' => 'required|valid_date',
        'gender' => 'required|in_list[male,female,other]',
        'contact' => 'permit_empty|max_length[20]',
        'address' => 'permit_empty|max_length[255]',
    ];
}

