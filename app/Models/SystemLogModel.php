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
    protected $protectFields = true;
    protected $allowedFields = [
        'log_level', 'category', 'message', 'context', 'user_id', 'ip_address',
        'user_agent', 'request_uri', 'request_method', 'response_code',
        'execution_time', 'memory_usage', 'session_id'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'context' => 'json',
        'response_code' => 'int',
        'execution_time' => 'float',
        'memory_usage' => 'int'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = null; // Logs are immutable

    // Validation
    protected $validationRules = [
        'log_level' => 'required|in_list[emergency,alert,critical,error,warning,notice,info,debug]',
        'category' => 'required|in_list[authentication,database,application,security,performance,backup,system]',
        'message' => 'required'
    ];

    protected $validationMessages = [
        'log_level' => [
            'required' => 'Log level is required'
        ],
        'category' => [
            'required' => 'Category is required'
        ],
        'message' => [
            'required' => 'Message is required'
        ]
    ];

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
     * Log an entry
     */
    public function logEntry($level, $category, $message, $context = null, $userId = null)
    {
        $data = [
            'log_level' => $level,
            'category' => $category,
            'message' => $message,
            'context' => $context,
            'user_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'session_id' => session_id() ?: null
        ];

        return $this->insert($data);
    }

    /**
     * Get logs by level
     */
    public function getLogsByLevel($level, $limit = 100)
    {
        return $this->where('log_level', $level)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs by category
     */
    public function getLogsByCategory($category, $limit = 100)
    {
        return $this->where('category', $category)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get error logs
     */
    public function getErrorLogs($limit = 100)
    {
        return $this->whereIn('log_level', ['emergency', 'alert', 'critical', 'error'])
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs by user
     */
    public function getLogsByUser($userId, $limit = 100)
    {
        return $this->select('system_logs.*, users.first_name, users.last_name')
                    ->join('users', 'users.id = system_logs.user_id', 'left')
                    ->where('system_logs.user_id', $userId)
                    ->orderBy('system_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs by date range
     */
    public function getLogsByDateRange($startDate, $endDate, $level = null, $category = null)
    {
        $builder = $this->where('created_at >=', $startDate)
                        ->where('created_at <=', $endDate);

        if ($level) {
            $builder->where('log_level', $level);
        }

        if ($category) {
            $builder->where('category', $category);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Search logs
     */
    public function searchLogs($searchTerm, $limit = 100)
    {
        return $this->like('message', $searchTerm)
                    ->orLike('request_uri', $searchTerm)
                    ->orLike('ip_address', $searchTerm)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get authentication logs
     */
    public function getAuthenticationLogs($limit = 100)
    {
        return $this->select('system_logs.*, users.first_name, users.last_name')
                    ->join('users', 'users.id = system_logs.user_id', 'left')
                    ->where('system_logs.category', 'authentication')
                    ->orderBy('system_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get security logs
     */
    public function getSecurityLogs($limit = 100)
    {
        return $this->where('category', 'security')
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get performance logs
     */
    public function getPerformanceLogs($limit = 100)
    {
        return $this->where('category', 'performance')
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get log statistics
     */
    public function getLogStats($days = 7)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->select('
                        log_level,
                        category,
                        COUNT(*) as count,
                        DATE(created_at) as log_date
                    ')
                    ->where('created_at >=', $startDate)
                    ->groupBy(['log_level', 'category', 'DATE(created_at)'])
                    ->orderBy('log_date', 'DESC')
                    ->findAll();
    }

    /**
     * Clean old logs
     */
    public function cleanOldLogs($days = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('created_at <', $cutoffDate)->delete();
    }

    /**
     * Get system activity summary
     */
    public function getActivitySummary($hours = 24)
    {
        $startTime = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
        
        return [
            'total_requests' => $this->where('created_at >=', $startTime)->countAllResults(),
            'error_count' => $this->where('created_at >=', $startTime)
                                 ->whereIn('log_level', ['emergency', 'alert', 'critical', 'error'])
                                 ->countAllResults(),
            'unique_users' => $this->select('DISTINCT user_id')
                                  ->where('created_at >=', $startTime)
                                  ->where('user_id IS NOT NULL')
                                  ->countAllResults(),
            'unique_ips' => $this->select('DISTINCT ip_address')
                                ->where('created_at >=', $startTime)
                                ->where('ip_address IS NOT NULL')
                                ->countAllResults(),
            'avg_execution_time' => $this->selectAvg('execution_time')
                                        ->where('created_at >=', $startTime)
                                        ->where('execution_time IS NOT NULL')
                                        ->first()['execution_time'] ?? 0
        ];
    }

    /**
     * Log emergency
     */
    public function emergency($message, $context = null, $userId = null)
    {
        return $this->logEntry('emergency', 'system', $message, $context, $userId);
    }

    /**
     * Log alert
     */
    public function alert($message, $context = null, $userId = null)
    {
        return $this->logEntry('alert', 'system', $message, $context, $userId);
    }

    /**
     * Log critical
     */
    public function critical($message, $context = null, $userId = null)
    {
        return $this->logEntry('critical', 'system', $message, $context, $userId);
    }

    /**
     * Log error
     */
    public function error($message, $context = null, $userId = null)
    {
        return $this->logEntry('error', 'application', $message, $context, $userId);
    }

    /**
     * Log warning
     */
    public function warning($message, $context = null, $userId = null)
    {
        return $this->logEntry('warning', 'application', $message, $context, $userId);
    }

    /**
     * Log info
     */
    public function info($message, $context = null, $userId = null)
    {
        return $this->logEntry('info', 'application', $message, $context, $userId);
    }

    /**
     * Log debug
     */
    public function debug($message, $context = null, $userId = null)
    {
        return $this->logEntry('debug', 'application', $message, $context, $userId);
    }
}
