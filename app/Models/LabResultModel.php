<?php

namespace App\Models;

use CodeIgniter\Model;

class LabResultModel extends Model
{
    protected $table = 'lab_results';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'lab_request_id',
        'result',
        'result_file',
        'result_file_type',
        'interpretation',
        'completed_by',
        'completed_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'lab_request_id' => 'required|integer',
    ];
}

