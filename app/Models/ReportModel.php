<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'report_type',
        'title',
        'description',
        'generated_by',
        'filters',
        'report_data',
        'summary',
    ];

    protected array $casts = [
        'filters' => 'json',
        'report_data' => 'json',
    ];

    public function getReports(array $filters = []): array
    {
        $builder = $this->orderBy('created_at', 'DESC');

        if (!empty($filters['type'])) {
            $builder->where('report_type', $filters['type']);
        }

        if (!empty($filters['from'])) {
            $builder->where('DATE(created_at) >=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $builder->where('DATE(created_at) <=', $filters['to']);
        }

        return $builder->findAll();
    }
}
