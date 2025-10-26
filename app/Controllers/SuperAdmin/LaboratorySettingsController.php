<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\LaboratorySettingsModel;

class LaboratorySettingsController extends BaseController
{
    protected LaboratorySettingsModel $settingsModel;

    protected array $categories = [
        'test_parameters',
        'reference_ranges',
        'staff_permissions',
        'integrations',
    ];

    protected array $defaultSettings = [
        'test_parameters' => [
            'default_units' => [
                'value' => [
                    'hematology' => 'cells/µL',
                    'chemistry' => 'mg/dL',
                    'immunology' => 'IU/mL',
                ],
                'description' => 'Default measurement units used for each test category.',
            ],
            'auto_verify_threshold' => [
                'value' => 5,
                'description' => 'Percent deviation allowed before manual verification is required.',
            ],
        ],
        'reference_ranges' => [
            'cbc_wbc_range' => [
                'value' => [
                    'min' => 4000,
                    'max' => 11000,
                    'unit' => 'cells/µL',
                ],
                'description' => 'Normal white blood cell count range.',
            ],
            'glucose_fasting_range' => [
                'value' => [
                    'min' => 70,
                    'max' => 100,
                    'unit' => 'mg/dL',
                ],
                'description' => 'Normal fasting blood glucose range.',
            ],
        ],
        'staff_permissions' => [
            'allow_tech_result_edit' => [
                'value' => true,
                'description' => 'Allow lab technicians to edit results before verification.',
            ],
            'require_dual_signoff' => [
                'value' => false,
                'description' => 'Require dual sign-off for critical test results.',
            ],
        ],
        'integrations' => [
            'emr_sync_enabled' => [
                'value' => true,
                'description' => 'Enable synchronization with EMR for lab results.',
            ],
            'emr_api_endpoint' => [
                'value' => 'https://emr.example.com/api/labs',
                'description' => 'API endpoint for EMR integration.',
            ],
        ],
    ];

    public function __construct()
    {
        $this->settingsModel = new LaboratorySettingsModel();
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

        $existing = $this->settingsModel->getSettingsByCategory($this->categories);
        $settings = $this->mergeSettings($existing);

        return view('SuperAdmin/laboratory_settings/index', [
            'title' => 'Laboratory Settings',
            'settings' => $settings,
        ]);
    }

    public function update()
    {
        $isAjax = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('accept'), 'application/json');

        if ($redirect = $this->ensureSuperAdmin()) {
            if ($isAjax) {
                return $this->respondError('Unauthorized access.', 401);
            }
            return $redirect;
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            if ($isAjax) {
                return $this->respondError('Invalid request method.', 405);
            }
            return redirect()->to('super-admin/laboratory-settings');
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($payload) || empty($payload['category']) || empty($payload['settings']) || !is_array($payload['settings'])) {
            if ($isAjax) {
                return $this->respondError('Invalid payload.');
            }
            return redirect()->back()->with('error', 'Invalid payload.');
        }

        $category = $payload['category'];
        if (!in_array($category, $this->categories, true)) {
            if ($isAjax) {
                return $this->respondError('Unknown category specified.');
            }
            return redirect()->back()->with('error', 'Unknown category specified.');
        }

        $settings = $payload['settings'];
        foreach ($settings as $name => $data) {
            $value = $data['value'] ?? $data;
            $description = $data['description'] ?? null;
            $this->settingsModel->saveSetting($category, $name, $value, $description);
        }

        $fresh = $this->settingsModel->getSettingsByCategory([$category]);
        $merged = $this->mergeSettings($fresh);
        $responseData = $merged[$category] ?? [];

        if ($isAjax) {
            return $this->respondSuccess('Settings updated successfully.', [
                'category' => $category,
                'settings' => $responseData,
            ]);
        }

        return redirect()->to('super-admin/laboratory-settings')->with('message', 'Settings updated successfully.');
    }

    private function mergeSettings(array $existing): array
    {
        $merged = $this->defaultSettings;
        foreach ($existing as $category => $settings) {
            foreach ($settings as $name => $data) {
                $merged[$category][$name]['value'] = $data['value'];
                if (!empty($data['description'])) {
                    $merged[$category][$name]['description'] = $data['description'];
                }
            }
        }
        return $merged;
    }

    private function respondSuccess(string $message, array $data = [])
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'csrfToken' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ]);
    }

    private function respondError(string $message, int $status = 400, array $errors = [])
    {
        return $this->response->setStatusCode($status)->setJSON([
            'success' => false,
            'error' => $message,
            'errors' => $errors,
            'csrfToken' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ]);
    }
}
