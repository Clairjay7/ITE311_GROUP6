<?php

namespace App\Models;

use CodeIgniter\Model;

class TestModel extends Model
{
    protected $table = 'tests';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'patient_id',
        'test_name',
        'test_type',
        'sample_id',
        'requested_by',
        'status',
        'result',
        'quality_check',
    ];

    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getTests(array $filters = []): array
    {
        $builder = $this->select('tests.*, patients.first_name, patients.last_name')
            ->join('patients', 'patients.id = tests.patient_id', 'left')
            ->orderBy('tests.created_at', 'DESC');

        if (!empty($filters['status'])) {
            $builder->where('tests.status', $filters['status']);
        }

        if (!empty($filters['quality_check'])) {
            $builder->where('tests.quality_check', $filters['quality_check']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('tests.test_name', $filters['search'])
                ->orLike('tests.test_type', $filters['search'])
                ->orLike('tests.sample_id', $filters['search'])
                ->orLike("CONCAT(patients.first_name, ' ', patients.last_name)", $filters['search'])
                ->groupEnd();
        }

        return $builder->findAll();
    }
}
