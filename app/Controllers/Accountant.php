<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\BillingModel;
use App\Models\PaymentModel;
use App\Models\PatientModel;
use App\Models\SystemLogModel;

class Accountant extends Controller
{
    protected $billingModel;
    protected $paymentModel;
    protected $patientModel;
    protected $systemLogModel;

    public function __construct()
    {
        try {
            $this->billingModel = new BillingModel();
            $this->paymentModel = new PaymentModel();
            $this->patientModel = new PatientModel();
            $this->systemLogModel = new SystemLogModel();
        } catch (\Exception $e) {
            log_message('error', 'Accountant Controller: ' . $e->getMessage());
        }
    }

    protected function ensureAccountant()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'accountant') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureAccountant();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'title' => 'Accountant - ' . ucwords(str_replace('_', ' ', basename($view))),
            'username' => session()->get('username'),
        ];
        return view($view, $base + $data);
    }

    public function dashboard()
    {
        $data = [
            'pendingBills' => 15,
            'todayRevenue' => 25000,
            'overduePayments' => 8,
            'monthlyRevenue' => 450000,
        ];
        
        return view('accountant/dashboard', $data);
    }

    public function billing()
    {
        try {
            $bills = isset($this->billingModel) ? $this->billingModel->findAll() : [];
        } catch (\Exception $e) {
            $bills = [];
        }
        return $this->render('accountant/billing', ['bills' => $bills]);
    }

    public function payments()
    {
        try {
            $payments = isset($this->paymentModel) ? $this->paymentModel->findAll() : [];
        } catch (\Exception $e) {
            $payments = [];
        }
        return $this->render('accountant/payments', ['payments' => $payments]);
    }

    public function invoices()
    {
        return $this->render('accountant/invoices');
    }

    public function reports()
    {
        return $this->render('accountant/reports');
    }

    public function expenses()
    {
        return $this->render('accountant/expenses');
    }

    public function revenue()
    {
        return $this->render('accountant/revenue');
    }

    public function taxes()
    {
        return $this->render('accountant/taxes');
    }

    public function settings()
    {
        return $this->render('accountant/settings');
    }
}
