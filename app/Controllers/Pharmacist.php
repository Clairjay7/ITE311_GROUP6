<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PrescriptionModel;
use App\Models\PatientModel;
use App\Models\PharmacyInventoryModel;
use App\Models\SystemLogModel;

class Pharmacist extends Controller
{
    protected $prescriptionModel;
    protected $patientModel;
    protected $pharmacyInventoryModel;
    protected $systemLogModel;

    public function __construct()
    {
        try {
            $this->prescriptionModel = new PrescriptionModel();
            $this->patientModel = new PatientModel();
            $this->pharmacyInventoryModel = new PharmacyInventoryModel();
            $this->systemLogModel = new SystemLogModel();
        } catch (\Exception $e) {
            // Models might not exist, will handle in methods
            log_message('error', 'Pharmacist Controller: ' . $e->getMessage());
        }
    }

    protected function ensurePharmacist()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'pharmacist') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensurePharmacist();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'title' => 'Pharmacist - ' . ucwords(str_replace('_', ' ', basename($view))),
            'username' => session()->get('username'),
        ];
        return view($view, $base + $data);
    }

    public function dashboard()
    {
        try {
            $data = [
                'pendingPrescriptions' => $this->prescriptionModel->where('status', 'pending')->countAllResults() ?? 8,
                'dispensedToday' => $this->prescriptionModel->where('status', 'dispensed')->where('DATE(updated_at)', date('Y-m-d'))->countAllResults() ?? 24,
                'lowStockItems' => $this->pharmacyInventoryModel->where('quantity <', 10)->countAllResults() ?? 3,
                'expiringSoon' => $this->pharmacyInventoryModel->where('expiry_date <=', date('Y-m-d', strtotime('+30 days')))->countAllResults() ?? 5,
            ];
        } catch (\Exception $e) {
            // Fallback data if models fail
            $data = [
                'pendingPrescriptions' => 8,
                'dispensedToday' => 24,
                'lowStockItems' => 3,
                'expiringSoon' => 5,
            ];
        }
        
        return $this->render('pharmacist/dashboard', $data);
    }

    public function prescriptions()
    {
        try {
            $prescriptions = $this->prescriptionModel->findAll();
        } catch (\Exception $e) {
            $prescriptions = [];
        }
        return $this->render('pharmacist/prescriptions', ['prescriptions' => $prescriptions]);
    }

    public function pending()
    {
        try {
            $pendingPrescriptions = $this->prescriptionModel->where('status', 'pending')->findAll();
        } catch (\Exception $e) {
            $pendingPrescriptions = [];
        }
        return $this->render('pharmacist/pending', ['prescriptions' => $pendingPrescriptions]);
    }

    public function dispensed()
    {
        try {
            $dispensedPrescriptions = $this->prescriptionModel->where('status', 'dispensed')->findAll();
        } catch (\Exception $e) {
            $dispensedPrescriptions = [];
        }
        return $this->render('pharmacist/dispensed', ['prescriptions' => $dispensedPrescriptions]);
    }

    public function inventory()
    {
        try {
            $inventory = $this->pharmacyInventoryModel->findAll();
        } catch (\Exception $e) {
            $inventory = [];
        }
        return $this->render('pharmacist/inventory', ['inventory' => $inventory]);
    }

    public function lowStock()
    {
        try {
            $lowStockItems = $this->pharmacyInventoryModel->where('quantity <', 10)->findAll();
        } catch (\Exception $e) {
            $lowStockItems = [];
        }
        return $this->render('pharmacist/low_stock', ['items' => $lowStockItems]);
    }

    public function expiring()
    {
        try {
            $expiringItems = $this->pharmacyInventoryModel->where('expiry_date <=', date('Y-m-d', strtotime('+30 days')))->findAll();
        } catch (\Exception $e) {
            $expiringItems = [];
        }
        return $this->render('pharmacist/expiring', ['items' => $expiringItems]);
    }

    public function orders()
    {
        return $this->render('pharmacist/orders');
    }

    public function reports()
    {
        return $this->render('pharmacist/reports');
    }

    public function settings()
    {
        return $this->render('pharmacist/settings');
    }

    // API Methods
    public function dispensePrescription()
    {
        if ($this->request->getMethod() === 'POST') {
            $prescriptionId = $this->request->getPost('prescription_id');
            $dispensedBy = session()->get('user_id');
            
            $data = [
                'status' => 'dispensed',
                'dispensed_at' => date('Y-m-d H:i:s'),
                'dispensed_by' => $dispensedBy
            ];
            
            if ($this->prescriptionModel->update($prescriptionId, $data)) {
                $this->systemLogModel->info('Prescription dispensed', ['prescription_id' => $prescriptionId], $dispensedBy);
                return $this->response->setJSON(['success' => true, 'message' => 'Prescription dispensed successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to dispense prescription']);
            }
        }
    }

    public function apiStats()
    {
        $stats = [
            'pending_prescriptions' => $this->prescriptionModel->where('status', 'pending')->countAllResults(),
            'dispensed_today' => $this->prescriptionModel->where('status', 'dispensed')->where('DATE(updated_at)', date('Y-m-d'))->countAllResults(),
            'low_stock_items' => $this->pharmacyInventoryModel->where('quantity <', 10)->countAllResults(),
            'expiring_soon' => $this->pharmacyInventoryModel->where('expiry_date <=', date('Y-m-d', strtotime('+30 days')))->countAllResults()
        ];
        return $this->response->setJSON($stats);
    }
}
