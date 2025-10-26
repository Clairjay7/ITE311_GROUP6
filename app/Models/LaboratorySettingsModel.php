<?php

namespace App\Models;

use CodeIgniter\Model;

class LaboratorySettingsModel extends Model
{
    protected $table = 'laboratory_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'setting_name',
        'setting_value',
        'category',
        'description',
    ];

    public function getSettingsByCategory(array $categories): array
    {
        $results = $this->whereIn('category', $categories)->findAll();
        $grouped = [];
        foreach ($results as $row) {
            $value = $row['setting_value'];
            $decoded = json_decode($value, true);
            $grouped[$row['category']][$row['setting_name']] = [
                'value' => $decoded === null && json_last_error() !== JSON_ERROR_NONE ? $value : ($decoded ?? $value),
                'description' => $row['description'] ?? '',
            ];
        }
        return $grouped;
    }

    public function saveSetting(string $category, string $name, $value, ?string $description = null): bool
    {
        $payload = [
            'category' => $category,
            'setting_name' => $name,
            'description' => $description,
            'setting_value' => is_array($value) ? json_encode($value) : (string) $value,
        ];

        $existing = $this->where(['category' => $category, 'setting_name' => $name])->first();
        if ($existing) {
            return $this->update($existing['id'], $payload);
        }

        return (bool) $this->insert($payload, true);
    }
}
