<?php

namespace App\Models;

use CodeIgniter\Model;

class LabServiceModel extends Model
{
    protected $table = 'lab_services';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'patient_id',
        'test_type',
        'result',
        'remarks',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer',
        'test_type' => 'required|max_length[255]',
        'result' => 'permit_empty|max_length[500]',
        'remarks' => 'permit_empty|max_length[500]',
    ];
}

