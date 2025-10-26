<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\PharmacySettingsModel;

class PharmacySettingsController extends BaseController
{
    protected PharmacySettingsModel $settingsModel;
    protected array $sections = [
        'staff' => 'Staff Permissions',
        'notifications' => 'Notification Settings',
        'integrations' => 'Integration Settings',
        'security' => 'Backup & Security',
    ];

    public function __construct()
    {
        $this->settingsModel = new PharmacySettingsModel();
    }

    protected function ensureSuperAdmin()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'superadmin') {
            return redirect()->to('/login');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $storedSettings = $this->settingsModel->getSettingsByCategory();

        $defaults = [
            'staff' => [
                ['setting_name' => 'allowed_roles', 'setting_value' => ['pharmacist', 'assistant'], 'description' => 'Roles allowed to access pharmacy module'],
                ['setting_name' => 'require_dual_approval', 'setting_value' => false, 'description' => 'Require dual approval for high-risk medications'],
                ['setting_name' => 'shift_handovers_enabled', 'setting_value' => true, 'description' => 'Enable shift handover checklist'],
            ],
            'notifications' => [
                ['setting_name' => 'low_stock_alert', 'setting_value' => ['enabled' => true, 'threshold' => 10], 'description' => 'Notify when inventory hits threshold'],
                ['setting_name' => 'expiry_alert', 'setting_value' => ['enabled' => true, 'days_before' => 14], 'description' => 'Alert before medication expiration'],
                ['setting_name' => 'daily_summary', 'setting_value' => ['enabled' => false], 'description' => 'Email daily operations summary'],
            ],
            'integrations' => [
                ['setting_name' => 'emr_sync', 'setting_value' => ['enabled' => true, 'api_url' => ''], 'description' => 'Electronic medical records synchronization'],
                ['setting_name' => 'sms_notifications', 'setting_value' => ['enabled' => false, 'provider' => ''], 'description' => 'SMS provider for patient notifications'],
                ['setting_name' => 'inventory_supplier_api', 'setting_value' => ['enabled' => false, 'api_key' => ''], 'description' => 'Automatic supplier restock API'],
            ],
            'security' => [
                ['setting_name' => 'auto_backup', 'setting_value' => ['enabled' => true, 'time' => '02:00'], 'description' => 'Automatic database backup schedule'],
                ['setting_name' => 'encryption_at_rest', 'setting_value' => true, 'description' => 'Encrypt sensitive data at rest'],
                ['setting_name' => 'two_factor_required', 'setting_value' => false, 'description' => 'Require 2FA for pharmacy admins'],
            ],
        ];

        $groupedSettings = [];

        foreach ($defaults as $category => $settings) {
            $groupedSettings[$category] = [];
            foreach ($settings as $defaultSetting) {
                $name = $defaultSetting['setting_name'];
                $groupedSettings[$category][$name] = $defaultSetting + ['category' => $category];
            }

            if (!empty($storedSettings[$category])) {
                foreach ($storedSettings[$category] as $storedSetting) {
                    $name = $storedSetting['setting_name'];
                    if (isset($groupedSettings[$category][$name])) {
                        $groupedSettings[$category][$name]['setting_value'] = $storedSetting['setting_value'];
                    } else {
                        $groupedSettings[$category][$name] = $storedSetting + ['description' => $storedSetting['description'] ?? '', 'category' => $category];
                    }
                }
            }
        }

        foreach ($storedSettings as $category => $settings) {
            if (!isset($groupedSettings[$category])) {
                $groupedSettings[$category] = [];
            }
            foreach ($settings as $storedSetting) {
                $name = $storedSetting['setting_name'];
                if (!isset($groupedSettings[$category][$name])) {
                    $groupedSettings[$category][$name] = $storedSetting + ['description' => $storedSetting['description'] ?? '', 'category' => $category];
                }
            }
        }

        return view('SuperAdmin/pharmacy_settings/index', [
            'title' => 'Pharmacy Settings',
            'sections' => $this->sections,
            'settings' => $groupedSettings,
        ]);
    }

    public function update()
    {
        $method = strtolower($this->request->getMethod());
        $isAjax = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('accept'), 'application/json');

        if ($redirect = $this->ensureSuperAdmin()) {
            if ($isAjax) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'error' => 'Unauthorized access.',
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return $redirect;
        }

        if ($method !== 'post') {
            if ($isAjax) {
                return $this->response->setStatusCode(405)->setJSON([
                    'success' => false,
                    'error' => 'Invalid request method.',
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->to('super-admin/pharmacy-settings');
        }

        $category = $this->request->getPost('category');
        $settingName = $this->request->getPost('setting_name');
        $rawValue = $this->request->getPost('setting_value');

        if (empty($category) || empty($settingName)) {
            $message = 'Setting category and name are required.';
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'error' => $message,
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->with('error', $message);
        }

        $decodedValue = $this->decodeSettingValue($rawValue);

        $success = $this->settingsModel->upsertSetting($settingName, $decodedValue, $category);

        if ($isAjax) {
            if ($success) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Setting updated successfully.',
                    'setting' => [
                        'category' => $category,
                        'setting_name' => $settingName,
                        'setting_value' => $decodedValue,
                    ],
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Failed to update setting.',
                'csrfToken' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]);
        }

        if ($success) {
            return redirect()->to('super-admin/pharmacy-settings')->with('message', 'Setting updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update setting.');
    }

    private function decodeSettingValue($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            if ($lower === 'true' || $lower === 'false') {
                return $lower === 'true';
            }

            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return $value;
    }
}
