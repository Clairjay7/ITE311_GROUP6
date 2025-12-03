<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false; // patients table doesn't have deleted_at column
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'patient_reg_no',
        'first_name',
        'middle_name', 
        'last_name',
        'extension_name',
        'date_of_birth',
        'gender',
        'contact',
        'email',
        'address_street',
        'address_barangay',
        'address_city',
        'address_province',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $skipValidation = false;

    protected $validationRules = [
        'first_name' => "required|min_length[2]|max_length[60]|regex_match[/^[A-Za-z\s\-\'\.]+$/]",
        'last_name' => "required|min_length[2]|max_length[60]|regex_match[/^[A-Za-z\s\-\'\.]+$/]",
        'date_of_birth' => 'required|valid_date',
        'gender' => 'required|in_list[male,female,other]',
        'contact' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[120]'
    ];

    protected $beforeInsert = ['setCreatedAt'];
    protected $beforeUpdate = ['setUpdatedAt'];

    protected $validationMessages = [
        'first_name' => [
            'required' => 'First name is required.',
            'regex_match' => 'First name can only contain letters, spaces, hyphens, apostrophes, and dots.'
        ],
        'last_name' => [
            'required' => 'Last name is required.',
            'regex_match' => 'Last name can only contain letters, spaces, hyphens, apostrophes, and dots.'
        ],
        'date_of_birth' => [
            'required' => 'Birthdate is required.',
            'valid_date' => 'Please enter a valid birthdate.'
        ],
        'gender' => [
            'required' => 'Gender is required.',
            'in_list' => 'Please select a valid gender option.'
        ]
    ];

    protected function setCreatedAt(array $data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function setUpdatedAt(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }
}

