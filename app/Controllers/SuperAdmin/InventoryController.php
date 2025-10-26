<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\InventoryModel;
use Config\Database;

class InventoryController extends BaseController
{
    protected InventoryModel $inventoryModel;
    protected string $tableName = 'inventory';

    protected array $statusOptions = [
        'available',
        'low_stock',
        'out_of_stock',
        'expired',
        'inactive',
    ];

    private function formatItemForResponse(array $item): array
    {
        $quantity = (int) ($item['quantity'] ?? 0);
        $rowClasses = [];
        if ($quantity <= 5) {
            $rowClasses[] = 'row-low-stock';
        }

        $formattedExpiration = '—';
        $rawExpiration = '';
        if (!empty($item['expiration_date'])) {
            $expiryTimestamp = strtotime($item['expiration_date']);
            if ($expiryTimestamp !== false) {
                $formattedExpiration = date('M d, Y', $expiryTimestamp);
                $rawExpiration = date('Y-m-d', $expiryTimestamp);

                $today = strtotime(date('Y-m-d'));
                $soon = strtotime('+7 days', $today);
                if ($expiryTimestamp >= $today && $expiryTimestamp <= $soon) {
                    $rowClasses[] = 'row-expiring';
                }
            }
        }

        $status = $item['status'] ?? 'inactive';
        $statusLabel = ucwords(str_replace('_', ' ', $status));
        $statusBadgeClass = 'status-' . strtolower($status);

        $updatedAt = '—';
        if (!empty($item['updated_at'])) {
            $updatedAtTimestamp = strtotime($item['updated_at']);
            if ($updatedAtTimestamp !== false) {
                $updatedAt = date('M d, Y h:i A', $updatedAtTimestamp);
            }
        }

        return [
            'id' => $item['id'],
            'quantity' => $quantity,
            'status' => $status,
            'status_label' => $statusLabel,
            'status_badge_class' => $statusBadgeClass,
            'row_classes' => $rowClasses,
            'formatted_expiration' => $formattedExpiration,
            'raw_expiration' => $rawExpiration,
            'formatted_updated_at' => $updatedAt,
        ];
    }

    public function __construct()
    {
        $this->inventoryModel = new InventoryModel();
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

        $db = Database::connect();
        $tableExists = $db->tableExists($this->tableName);
        $items = $tableExists ? $this->inventoryModel->getAllWithHighlights() : [];

        return view('SuperAdmin/inventory/index', [
            'title' => 'Inventory Management',
            'items' => $items,
            'statusOptions' => $this->statusOptions,
            'tableExists' => $tableExists,
        ]);
    }

    public function updateStock($id = null)
    {
        $method = strtolower($this->request->getMethod());
        $isAjax = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('accept'), 'application/json');

        if ($method === 'options') {
            return $this->response
                ->setStatusCode(204)
                ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With')
                ->setHeader('Access-Control-Max-Age', '86400');
        }

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

        $db = Database::connect();
        if (!$db->tableExists($this->tableName)) {
            $message = 'Inventory table not found. Please run the inventory migration.';
            if ($isAjax) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'error' => $message,
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->to(site_url('super-admin/inventory'))
                ->with('error', $message);
        }

        if (!in_array($method, ['post', 'put', 'patch'], true)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Invalid request method.',
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->to(site_url('super-admin/inventory'));
        }

        $data = $this->request->getPost([
            'quantity',
            'status',
            'expiration_date',
        ]);

        if (isset($data['status']) && !in_array($data['status'], $this->statusOptions, true)) {
            $message = 'Invalid status selected.';
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'error' => $message,
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        if (!empty($data['expiration_date']) && !strtotime($data['expiration_date'])) {
            $message = 'Invalid expiration date format.';
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'error' => $message,
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('error', $message);
        }

        if (isset($data['quantity'])) {
            $data['quantity'] = max(0, (int) $data['quantity']);
            if (!isset($data['status']) || $data['status'] === '') {
                if ($data['quantity'] === 0) {
                    $data['status'] = 'out_of_stock';
                } elseif ($data['quantity'] <= 5) {
                    $data['status'] = 'low_stock';
                } else {
                    $data['status'] = 'available';
                }
            }
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $filteredData = array_filter($data, static function ($value) {
            return $value !== null && $value !== '';
        });

        if (empty($filteredData)) {
            $message = 'No changes detected for this inventory item.';
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => $message,
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->with('error', $message);
        }

        if ($this->inventoryModel->update($id, $filteredData)) {
            $updatedItem = $this->inventoryModel->find($id);
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Inventory item updated successfully.',
                    'item' => $this->formatItemForResponse($updatedItem),
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }

            return redirect()->to(site_url('super-admin/inventory'))
                ->with('message', 'Inventory item updated successfully.');
        }

        $errors = $this->inventoryModel->errors();
        $errorMessage = !empty($errors)
            ? implode(' ', $errors)
            : 'Failed to update inventory item.';

        if ($isAjax) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => $errorMessage,
                'csrfToken' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]);
        }

        return redirect()->back()->withInput()->with('error', $errorMessage);
    }
}
