<?php

namespace App\Models;

use CodeIgniter\Model;

class PharmacySettingsModel extends Model
{
    protected $table = 'pharmacy_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'setting_name',
        'setting_value',
        'category',
    ];

    protected array $casts = [
        'setting_value' => 'json',
    ];

    public function getSettingsByCategory(): array
    {
        $settings = $this->orderBy('category', 'ASC')
            ->orderBy('setting_name', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($settings as $setting) {
            $category = $setting['category'] ?? 'general';
            $grouped[$category][] = $setting;
        }

        return $grouped;
    }

    public function upsertSetting(string $name, $value, string $category): bool
    {
        $existing = $this->where('setting_name', $name)->first();

        $data = [
            'setting_name' => $name,
            'setting_value' => $value,
            'category' => $category,
        ];

        if ($existing) {
            return (bool) $this->update($existing['id'], $data);
        }

        return (bool) $this->insert($data);
    }

    public function getSetting(string $name, $default = null)
    {
        $setting = $this->where('setting_name', $name)->first();
        return $setting['setting_value'] ?? $default;
    }
}
