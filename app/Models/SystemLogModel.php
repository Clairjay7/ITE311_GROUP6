<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemLogModel extends Model
{
    protected $table = 'system_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'level',
        'message',
        'context',
        'user_id',
        'ip_address',
        'user_agent',
        'module',
        'action',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    protected $validationRules = [
        'level' => 'required|in_list[emergency,alert,critical,error,warning,notice,info,debug]',
        'message' => 'required',
    ];

    /**
     * Get logs with filters
     */
    public function getFilteredLogs($filters = [])
    {
        $builder = $this->select('system_logs.*, users.username as user_name')
            ->join('users', 'users.id = system_logs.user_id', 'left');

        if (!empty($filters['level'])) {
            $builder->where('system_logs.level', $filters['level']);
        }

        if (!empty($filters['module'])) {
            $builder->where('system_logs.module', $filters['module']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(system_logs.created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(system_logs.created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('system_logs.message', $filters['search'])
                ->orLike('system_logs.context', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('system_logs.created_at', 'DESC');
    }
}

