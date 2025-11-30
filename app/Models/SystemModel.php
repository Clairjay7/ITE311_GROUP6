<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemModel extends Model
{
    protected $table = 'system_controls';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'setting_name',
        'setting_value',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'setting_name' => 'required|max_length[255]|is_unique[system_controls.setting_name]',
        'setting_value' => 'required|max_length[500]',
    ];
}

