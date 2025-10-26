<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicalServiceModel extends Model
{
    protected $table = 'medical_services';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'service_name',
        'description',
        'category',
        'price',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'service_name' => 'required|min_length[3]|max_length[255]',
        'category' => 'required|in_list[consultation,laboratory,imaging,surgery,therapy,emergency,other]',
        'price' => 'required|decimal|greater_than_equal_to[0]',
        'status' => 'required|in_list[active,inactive,discontinued]'
    ];

    protected $validationMessages = [
        'service_name' => [
            'required' => 'Service name is required',
            'min_length' => 'Service name must be at least 3 characters long',
            'max_length' => 'Service name cannot exceed 255 characters'
        ],
        'category' => [
            'required' => 'Category is required',
            'in_list' => 'Please select a valid category'
        ],
        'price' => [
            'required' => 'Price is required',
            'decimal' => 'Price must be a valid decimal number',
            'greater_than_equal_to' => 'Price cannot be negative'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Please select a valid status'
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
     * Get all medical services with optional filters
     */
    public function getAllServices($status = null, $category = null)
    {
        $builder = $this;
        
        if ($status) {
            $builder = $builder->where('status', $status);
        }
        
        if ($category) {
            $builder = $builder->where('category', $category);
        }
        
        return $builder->orderBy('service_name', 'ASC')->findAll();
    }

    /**
     * Get services by category
     */
    public function getServicesByCategory($category)
    {
        return $this->where('category', $category)
                   ->where('status', 'active')
                   ->orderBy('service_name', 'ASC')
                   ->findAll();
    }

    /**
     * Get active services only
     */
    public function getActiveServices()
    {
        return $this->where('status', 'active')
                   ->orderBy('service_name', 'ASC')
                   ->findAll();
    }

    /**
     * Search services by name or description
     */
    public function searchServices($query)
    {
        return $this->groupStart()
                   ->like('service_name', $query)
                   ->orLike('description', $query)
                   ->groupEnd()
                   ->where('status', 'active')
                   ->orderBy('service_name', 'ASC')
                   ->findAll();
    }

    /**
     * Get service statistics
     */
    public function getServiceStats()
    {
        $db = \Config\Database::connect();
        
        $stats = [
            'total_services' => $this->countAll(),
            'active_services' => $db->table($this->table)->where('status', 'active')->countAllResults(),
            'inactive_services' => $db->table($this->table)->where('status', 'inactive')->countAllResults(),
            'discontinued_services' => $db->table($this->table)->where('status', 'discontinued')->countAllResults(),
        ];

        // Get services by category
        $categories = ['consultation', 'laboratory', 'imaging', 'surgery', 'therapy', 'emergency', 'other'];
        foreach ($categories as $category) {
            $stats['category_' . $category] = $db->table($this->table)->where('category', $category)->countAllResults();
        }

        // Get average price by category
        $avgPrices = $db->query("
            SELECT category, AVG(price) as avg_price, COUNT(*) as count
            FROM medical_services 
            WHERE status = 'active'
            GROUP BY category
        ")->getResultArray();

        $stats['avg_prices'] = [];
        foreach ($avgPrices as $price) {
            $stats['avg_prices'][$price['category']] = [
                'average' => round($price['avg_price'], 2),
                'count' => $price['count']
            ];
        }

        return $stats;
    }

    /**
     * Get services for billing integration
     */
    public function getServicesForBilling()
    {
        return $this->select('id, service_name, category, price')
                   ->where('status', 'active')
                   ->orderBy('category', 'ASC')
                   ->orderBy('service_name', 'ASC')
                   ->findAll();
    }

    /**
     * Update service status
     */
    public function updateServiceStatus($id, $status)
    {
        if (!in_array($status, ['active', 'inactive', 'discontinued'])) {
            return false;
        }

        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get popular services (most referenced in appointments/billing)
     */
    public function getPopularServices($limit = 10)
    {
        // This would need to be implemented based on your billing/appointment tables
        // For now, return active services ordered by name
        return $this->where('status', 'active')
                   ->orderBy('service_name', 'ASC')
                   ->limit($limit)
                   ->findAll();
    }
}
