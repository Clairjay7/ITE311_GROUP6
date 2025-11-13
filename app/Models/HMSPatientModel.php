<?php

namespace App\Models;

use CodeIgniter\Model;

class HMSPatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'full_name',
        'gender',
        'age',
        'contact',
        'address',
        'type',
        'doctor_id',
        'department_id',
        'purpose',
        'admission_date',
        'room_number',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'full_name' => 'required|min_length[3]|max_length[100]',
        'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
        'age' => 'required|integer|greater_than_equal_to[0]',
        'type' => 'required|in_list[In-Patient,Out-Patient]',
    ];
}
