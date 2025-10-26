<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;

class EquipmentController extends BaseController
{
    protected EquipmentModel $equipmentModel;

    protected array $statusOptions = [
        'available' => 'Available',
        'in_use' => 'In Use',
        'under_maintenance' => 'Under Maintenance',
        'out_of_service' => 'Out of Service',
    ];

    protected array $conditionOptions = [
        'good' => 'Good',
        'needs_service' => 'Needs Service',
        'damaged' => 'Damaged',
    ];

    public function __construct()
    {
        $this->equipmentModel = new EquipmentModel();
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

        $filters = [
            'status' => $this->request->getGet('status'),
            'condition' => $this->request->getGet('condition'),
            'search' => $this->request->getGet('search'),
        ];

        $equipment = $this->equipmentModel->getEquipment(array_filter($filters));

        return view('SuperAdmin/equipment/index', [
            'title' => 'Equipment Management',
            'equipment' => $equipment,
            'filters' => $filters,
            'statusOptions' => $this->statusOptions,
            'conditionOptions' => $this->conditionOptions,
        ]);
    }

    public function store()
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
            return redirect()->to('super-admin/equipment');
        }

        $rules = [
            'equipment_name' => 'required|min_length[3]',
            'equipment_type' => 'permit_empty|max_length[120]',
            'serial_number' => 'permit_empty|max_length[120]',
            'status' => 'required|in_list[available,in_use,under_maintenance,out_of_service]',
            'last_maintenance_date' => 'permit_empty|valid_date',
            'next_maintenance_date' => 'permit_empty|valid_date',
            'last_calibration_date' => 'permit_empty|valid_date',
            'next_calibration_date' => 'permit_empty|valid_date',
            'usage_hours' => 'permit_empty|decimal',
            'condition' => 'required|in_list[good,needs_service,damaged]',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator?->getErrors() ?? ['validation' => 'Invalid input'];
            if ($isAjax) {
                return $this->respondError('Please correct the highlighted fields.', 422, $errors);
            }
            return redirect()->back()->withInput()->with('error', 'Please correct the highlighted fields.');
        }

        $payload = $this->request->getPost([
            'equipment_name',
            'equipment_type',
            'serial_number',
            'status',
            'last_maintenance_date',
            'next_maintenance_date',
            'last_calibration_date',
            'next_calibration_date',
            'usage_hours',
            'condition',
        ]);

        $payload['usage_hours'] = $payload['usage_hours'] !== '' ? (float) $payload['usage_hours'] : 0;

        $this->equipmentModel->insert($payload);
        $newId = $this->equipmentModel->getInsertID();
        $record = $this->equipmentModel->find($newId);

        if ($isAjax) {
            return $this->respondSuccess('Equipment added successfully.', $record);
        }

        return redirect()->to('super-admin/equipment')->with('message', 'Equipment added successfully.');
    }

    public function update($id = null)
    {
        $isAjax = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('accept'), 'application/json');

        if ($redirect = $this->ensureSuperAdmin()) {
            if ($isAjax) {
                return $this->respondError('Unauthorized access.', 401);
            }
            return $redirect;
        }

        if (!$id || !($record = $this->equipmentModel->find($id))) {
            if ($isAjax) {
                return $this->respondError('Equipment record not found.', 404);
            }
            return redirect()->to('super-admin/equipment')->with('error', 'Equipment record not found.');
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            if ($isAjax) {
                return $this->respondError('Invalid request method.', 405);
            }
            return redirect()->to('super-admin/equipment');
        }

        $rules = [
            'status' => 'required|in_list[available,in_use,under_maintenance,out_of_service]',
            'condition' => 'required|in_list[good,needs_service,damaged]',
            'last_maintenance_date' => 'permit_empty|valid_date',
            'next_maintenance_date' => 'permit_empty|valid_date',
            'last_calibration_date' => 'permit_empty|valid_date',
            'next_calibration_date' => 'permit_empty|valid_date',
            'usage_hours' => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator?->getErrors() ?? ['validation' => 'Invalid input'];
            if ($isAjax) {
                return $this->respondError('Please correct the highlighted fields.', 422, $errors);
            }
            return redirect()->back()->withInput()->with('error', 'Please correct the highlighted fields.');
        }

        $payload = $this->request->getPost([
            'status',
            'condition',
            'last_maintenance_date',
            'next_maintenance_date',
            'last_calibration_date',
            'next_calibration_date',
            'usage_hours',
        ]);

        if ($payload['usage_hours'] !== '') {
            $payload['usage_hours'] = (float) $payload['usage_hours'];
        } else {
            unset($payload['usage_hours']);
        }

        $this->equipmentModel->update($id, array_filter($payload, static fn($value) => $value !== null));
        $record = $this->equipmentModel->find($id);

        if ($isAjax) {
            return $this->respondSuccess('Equipment updated successfully.', $record);
        }

        return redirect()->to('super-admin/equipment')->with('message', 'Equipment updated successfully.');
    }

    private function respondSuccess(string $message, ?array $record = null)
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'record' => $this->formatRecord($record),
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

    private function formatRecord(?array $record): ?array
    {
        if (!$record) {
            return null;
        }

        return [
            'id' => $record['id'],
            'equipment_name' => $record['equipment_name'],
            'equipment_type' => $record['equipment_type'] ?? '—',
            'serial_number' => $record['serial_number'] ?? '—',
            'status' => $record['status'],
            'status_label' => $this->statusOptions[$record['status']] ?? ucfirst($record['status']),
            'last_maintenance_date' => $record['last_maintenance_date'],
            'next_maintenance_date' => $record['next_maintenance_date'],
            'last_calibration_date' => $record['last_calibration_date'],
            'next_calibration_date' => $record['next_calibration_date'],
            'usage_hours' => $record['usage_hours'],
            'condition' => $record['condition'],
            'condition_label' => $this->conditionOptions[$record['condition']] ?? ucfirst($record['condition']),
            'created_at' => $record['created_at'],
            'updated_at' => $record['updated_at'],
            'updated_at_formatted' => !empty($record['updated_at']) ? date('M d, Y h:i A', strtotime($record['updated_at'])) : '—',
        ];
    }
}
