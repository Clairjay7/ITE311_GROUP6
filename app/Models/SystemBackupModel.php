<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemBackupModel extends Model
{
    protected $table = 'system_backups';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'backup_name',
        'backup_type',
        'file_path',
        'file_size',
        'status',
        'created_by',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'backup_name' => 'required|max_length[255]',
        'backup_type' => 'required|in_list[database,files,full]',
        'status' => 'required|in_list[pending,in_progress,completed,failed]',
    ];

    /**
     * Get backups with user information
     */
    public function getBackupsWithUser()
    {
        return $this->select('system_backups.*, users.username as created_by_name')
            ->join('users', 'users.id = system_backups.created_by', 'left')
            ->orderBy('system_backups.created_at', 'DESC')
            ->findAll();
    }
}

