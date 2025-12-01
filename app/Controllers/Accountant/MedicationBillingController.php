<?php

namespace App\Controllers\Accountant;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminPatientModel;
use App\Models\PaymentReportModel;

class MedicationBillingController extends BaseController
{
    protected $billingModel;
    protected $patientModel;
    protected $paymentReportModel;

    public function __construct()
    {
        $this->billingModel = new BillingModel();
        $this->patientModel = new AdminPatientModel();
        $this->paymentReportModel = new PaymentReportModel();
    }

    /**
     * List all medication bills
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        
        // Only Accountant, Admin, and Doctor (read-only) can access
        if (!in_array($role, ['finance', 'admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();

        // Get medication bills
        $bills = $db->table('billing b')
            ->select('b.*, ap.firstname, ap.lastname, u.username as nurse_name, 
                     processed_by_user.username as processed_by_name, do.doctor_id')
            ->join('admin_patients ap', 'ap.id = b.patient_id', 'left')
            ->join('users u', 'u.id = b.nurse_id', 'left')
            ->join('users as processed_by_user', 'processed_by_user.id = b.processed_by', 'left')
            ->join('doctor_orders do', 'do.id = b.order_id', 'left')
            ->where('b.service', 'Medication Administration')
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Filter by status if requested
        $status = $this->request->getGet('status');
        if ($status && in_array($status, ['pending', 'paid', 'cancelled'])) {
            $bills = array_filter($bills, fn($bill) => $bill['status'] === $status);
        }

        $data = [
            'title' => 'Medication Billing',
            'bills' => $bills,
            'currentStatus' => $status ?? 'all',
            'isReadOnly' => $role === 'doctor', // Doctor can only view
            'userRole' => $role, // Pass role to view for explicit checks
            'canProcessPayment' => in_array($role, ['finance', 'admin']), // Explicit permission check
        ];

        return view('Accountant/medication_billing/index', $data);
    }

    /**
     * View medication bill details
     */
    public function view($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();

        $bill = $db->table('billing b')
            ->select('b.*, ap.*, u.username as nurse_name, 
                     processed_by_user.username as processed_by_name,
                     do.*, doctor_user.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = b.patient_id', 'left')
            ->join('users u', 'u.id = b.nurse_id', 'left')
            ->join('users as processed_by_user', 'processed_by_user.id = b.processed_by', 'left')
            ->join('doctor_orders do', 'do.id = b.order_id', 'left')
            ->join('users as doctor_user', 'doctor_user.id = do.doctor_id', 'left')
            ->where('b.id', $id)
            ->where('b.service', 'Medication Administration')
            ->get()
            ->getRowArray();

        if (!$bill) {
            return redirect()->to('/accounting/medication-billing')->with('error', 'Bill not found.');
        }

        $data = [
            'title' => 'Medication Bill Details',
            'bill' => $bill,
            'isReadOnly' => $role === 'doctor',
            'userRole' => $role,
            'canProcessPayment' => in_array($role, ['finance', 'admin']),
        ];

        return view('Accountant/medication_billing/view', $data);
    }

    /**
     * Generate invoice (PDF/Print view)
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

        $bill = $db->table('billing b')
            ->select('b.*, ap.*, u.username as nurse_name, 
                     processed_by_user.username as processed_by_name,
                     do.*, doctor_user.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = b.patient_id', 'left')
            ->join('users u', 'u.id = b.nurse_id', 'left')
            ->join('users as processed_by_user', 'processed_by_user.id = b.processed_by', 'left')
            ->join('doctor_orders do', 'do.id = b.order_id', 'left')
            ->join('users as doctor_user', 'doctor_user.id = do.doctor_id', 'left')
            ->where('b.id', $id)
            ->where('b.service', 'Medication Administration')
            ->get()
            ->getRowArray();

        if (!$bill) {
            return redirect()->to('/accounting/medication-billing')->with('error', 'Bill not found.');
        }

        $data = [
            'title' => 'Invoice #' . ($bill['invoice_number'] ?? 'N/A'),
            'bill' => $bill,
        ];

        return view('Accountant/medication_billing/invoice', $data);
    }

    /**
     * Process payment (Mark as paid) - Only Admin/Accountant
     */
    public function processPayment($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $role = session()->get('role');
        
        // RESTRICTION: Only Admin and Accountant can process payments
        if (!in_array($role, ['finance', 'admin'])) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Access denied. Only Admin and Accountant can process payments.'])->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        $bill = $this->billingModel->find($id);

        if (!$bill) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Bill not found']);
        }

        if ($bill['status'] === 'paid') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'This bill has already been paid']);
        }

        // Update bill status
        $updateData = [
            'status' => 'paid',
            'processed_by' => session()->get('user_id'),
            'paid_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->billingModel->update($id, $updateData)) {
            // Log payment processing
            if ($db->tableExists('billing_logs')) {
                $db->table('billing_logs')->insert([
                    'billing_id' => $id,
                    'action' => 'payment_processed',
                    'processed_by' => session()->get('user_id'),
                    'notes' => 'Payment processed by ' . (session()->get('username') ?? 'Accountant'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // CREATE PAYMENT RECORD in payment_reports table
            if ($db->tableExists('payment_reports')) {
                $paymentData = [
                    'report_date' => date('Y-m-d'),
                    'patient_id' => $bill['patient_id'],
                    'billing_id' => $id, // Link to billing record
                    'payment_method' => 'cash', // Default, can be updated later
                    'amount' => $bill['amount'],
                    'reference_number' => $bill['invoice_number'] ?? 'MED-' . $id,
                    'status' => 'completed',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'processed_by' => session()->get('user_id'),
                    'notes' => 'Medication Administration Payment - Medicine: ' . ($bill['medicine_name'] ?? 'N/A') . 
                              ', Dosage: ' . ($bill['dosage'] ?? 'N/A') . 
                              ', Invoice: ' . ($bill['invoice_number'] ?? 'N/A'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                // Skip validation for automatic payment records
                $this->paymentReportModel->skipValidation(true);
                $this->paymentReportModel->insert($paymentData);
                $this->paymentReportModel->skipValidation(false);
            }

            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Payment processed successfully. Invoice marked as paid and payment record created.'
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
     * Cancel bill - Only Admin/Accountant
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

        $bill = $this->billingModel->find($id);

        if (!$bill) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Bill not found']);
        }

        if ($bill['status'] === 'paid') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Cannot cancel a paid bill']);
        }

        if ($this->billingModel->update($id, ['status' => 'cancelled'])) {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Bill cancelled successfully'
                ]);
        } else {
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to cancel bill'
                ]);
        }
    }
}

