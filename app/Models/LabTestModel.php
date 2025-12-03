<?php

namespace App\Models;

use CodeIgniter\Model;

class LabTestModel extends Model
{
    protected $table = 'lab_tests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'test_name',
        'test_type',
        'specimen_category',
        'description',
        'normal_range',
        'price',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'test_name' => 'required|max_length[255]',
        'test_type' => 'required|max_length[100]',
        'specimen_category' => 'permit_empty|in_list[with_specimen,without_specimen]',
        'price' => 'permit_empty|decimal',
        'is_active' => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Get all active lab tests grouped by test type
     */
    public function getActiveTestsGrouped()
    {
        $tests = $this->where('is_active', 1)
            ->where('deleted_at', null)
            ->orderBy('test_type', 'ASC')
            ->orderBy('test_name', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($tests as $test) {
            $type = $test['test_type'] ?? 'Other';
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $test;
        }

        return $grouped;
    }

    /**
     * Get all active lab tests as flat list
     */
    public function getActiveTests()
    {
        return $this->where('is_active', 1)
            ->where('deleted_at', null)
            ->orderBy('specimen_category', 'ASC')
            ->orderBy('test_type', 'ASC')
            ->orderBy('test_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get active lab tests grouped by specimen category and test type
     */
    public function getActiveTestsGroupedByCategory()
    {
        $tests = $this->where('is_active', 1)
            ->where('deleted_at', null)
            ->orderBy('specimen_category', 'ASC')
            ->orderBy('test_type', 'ASC')
            ->orderBy('test_name', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($tests as $test) {
            $category = $test['specimen_category'] ?? 'with_specimen';
            $type = $test['test_type'] ?? 'Other';
            
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            if (!isset($grouped[$category][$type])) {
                $grouped[$category][$type] = [];
            }
            $grouped[$category][$type][] = $test;
        }

        return $grouped;
    }
}


