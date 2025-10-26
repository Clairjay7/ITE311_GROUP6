<?php

namespace App\Models;

use CodeIgniter\Model;

class LabReportModel extends Model
{
    protected $table = 'lab_reports';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'report_type',
        'description',
        'generated_by',
        'report_data',
        'report_period_start',
        'report_period_end',
    ];

    protected array $casts = [
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'report_data' => 'json',
    ];

    public function getReports(array $filters = []): array
    {
        $builder = $this->builder()->orderBy('created_at', 'DESC');

        if (!empty($filters['type'])) {
            $builder->where('report_type', $filters['type']);
        }

        if (!empty($filters['period_start'])) {
            $builder->where('report_period_start >=', $filters['period_start']);
        }

        if (!empty($filters['period_end'])) {
            $builder->where('report_period_end <=', $filters['period_end']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('report_type', $filters['search'])
                ->orLike('description', $filters['search'])
                ->orLike('generated_by', $filters['search'])
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}
