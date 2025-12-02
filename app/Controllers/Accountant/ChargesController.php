<?php

namespace App\Controllers\Accountant;

use App\Controllers\BaseController;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;
use App\Models\AdminPatientModel;
use App\Models\PaymentReportModel;

class ChargesController extends BaseController
{
    protected $chargeModel;
    protected $billingItemModel;
    protected $patientModel;
    protected $paymentReportModel;

    public function __construct()
    {
        $this->chargeModel = new ChargeModel();
        $this->billingItemModel = new BillingItemModel();
        $this->patientModel = new AdminPatientModel();
        $this->paymentReportModel = new PaymentReportModel();
    }

    /**
     * List all pending charges
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        
        // Only Accountant and Admin can access
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();

        // Get charges with patient and doctor information
        $charges = $db->table('charges c')
            ->select('c.*, ap.firstname, ap.lastname, ap.contact, 
                     u.username as doctor_name,
                     COUNT(bi.id) as item_count')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->join('consultations con', 'con.id = c.consultation_id', 'left')
            ->join('users u', 'u.id = con.doctor_id', 'left')
            ->join('billing_items bi', 'bi.charge_id = c.id', 'left')
            ->where('c.deleted_at', null)
            ->groupBy('c.id')
            ->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Filter by status if requested
        $status = $this->request->getGet('status');
        if ($status && in_array($status, ['pending', 'approved', 'paid', 'cancelled'])) {
            $charges = array_filter($charges, fn($charge) => $charge['status'] === $status);
        }

        $data = [
            'title' => 'Pending Charges',
            'charges' => $charges,
            'currentStatus' => $status ?? 'all',
        ];

        return view('Accountant/charges/index', $data);
    }

    /**
     * View charge details
     */
    public function view($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();

        $charge = $db->table('charges c')
            ->select('c.*, ap.*, u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->join('consultations con', 'con.id = c.consultation_id', 'left')
            ->join('users u', 'u.id = con.doctor_id', 'left')
            ->where('c.id', $id)
            ->where('c.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$charge) {
            return redirect()->to('/accountant/charges')->with('error', 'Charge not found.');
        }

        // Get billing items
        $billingItems = $this->billingItemModel
            ->where('charge_id', $id)
            ->findAll();

        $data = [
            'title' => 'Charge Details - ' . $charge['charge_number'],
            'charge' => $charge,
            'billingItems' => $billingItems,
        ];

        return view('Accountant/charges/view', $data);
    }

    /**
     * Approve charge (change status from pending to approved)
     */
    public function approve($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $charge = $this->chargeModel->find($id);

        if (!$charge) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Charge not found']);
        }

        if ($charge['status'] !== 'pending') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Only pending charges can be approved']);
        }

        if ($this->chargeModel->update($id, ['status' => 'approved'])) {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Charge approved successfully'
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to approve charge'
                ]);
        }
    }

    /**
     * Process payment (Mark as paid)
     */
    public function processPayment($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $charge = $this->chargeModel->find($id);

        if (!$charge) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Charge not found']);
        }

        if ($charge['status'] === 'paid') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'This charge has already been paid']);
        }

        $db = \Config\Database::connect();

        // Update charge status
        if ($this->chargeModel->update($id, ['status' => 'paid'])) {
            // Create payment record
            if ($db->tableExists('payment_reports')) {
                $paymentData = [
                    'report_date' => date('Y-m-d'),
                    'patient_id' => $charge['patient_id'],
                    'billing_id' => null, // Not linked to old billing table
                    'payment_method' => $this->request->getPost('payment_method') ?? 'cash',
                    'amount' => $charge['total_amount'],
                    'reference_number' => $charge['charge_number'],
                    'status' => 'completed',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'processed_by' => session()->get('user_id'),
                    'notes' => 'Payment for charge ' . $charge['charge_number'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $this->paymentReportModel->skipValidation(true);
                $this->paymentReportModel->insert($paymentData);
                $this->paymentReportModel->skipValidation(false);
            }

            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Payment processed successfully'
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to process payment'
                ]);
        }
    }

    /**
     * Cancel charge
     */
    public function cancel($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $charge = $this->chargeModel->find($id);

        if (!$charge) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Charge not found']);
        }

        if ($charge['status'] === 'paid') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Cannot cancel a paid charge']);
        }

        if ($this->chargeModel->update($id, ['status' => 'cancelled'])) {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Charge cancelled successfully'
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to cancel charge'
                ]);
        }
    }

    /**
     * Print bill / Generate invoice
     */
    public function invoice($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();

        $charge = $db->table('charges c')
            ->select('c.*, ap.*, u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->join('consultations con', 'con.id = c.consultation_id', 'left')
            ->join('users u', 'u.id = con.doctor_id', 'left')
            ->where('c.id', $id)
            ->where('c.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$charge) {
            return redirect()->to('/accountant/charges')->with('error', 'Charge not found.');
        }

        // Get billing items
        $billingItems = $this->billingItemModel
            ->where('charge_id', $id)
            ->findAll();

        $data = [
            'title' => 'Invoice - ' . $charge['charge_number'],
            'charge' => $charge,
            'billingItems' => $billingItems,
        ];

        return view('Accountant/charges/invoice', $data);
    }
}

