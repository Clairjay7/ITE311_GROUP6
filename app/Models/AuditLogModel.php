<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'action', 'table_name', 'record_id', 'old_values', 
        'new_values', 'ip_address', 'user_agent'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'old_values' => 'json',
        'new_values' => 'json'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = null; // Audit logs are immutable

    // Validation
    protected $validationRules = [
        'action' => 'required|max_length[100]',
        'table_name' => 'required|max_length[50]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get recent logs
     */
    public function getRecentLogs($limit = 100)
    {
        return $this->select('audit_logs.*, users.username, users.first_name, users.last_name')
                    ->join('users', 'users.id = audit_logs.user_id', 'left')
                    ->orderBy('audit_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Log an action
     */
    public function logAction($action, $tableName, $recordId = null, $oldValues = null, $newValues = null, $userId = null)
    {
        $data = [
            'user_id' => $userId ?: session()->get('user_id'),
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        return $this->insert($data);
    }

    /**
     * Get logs by user
     */
    public function getLogsByUser($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs by table
     */
    public function getLogsByTable($tableName, $limit = 50)
    {
        return $this->where('table_name', $tableName)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
