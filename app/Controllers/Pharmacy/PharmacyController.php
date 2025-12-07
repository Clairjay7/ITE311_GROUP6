<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\PharmacyModel;
use App\Models\DoctorOrderModel;

class PharmacyController extends BaseController
{
    protected $pharmacyModel;
    protected $orderModel;

    public function __construct()
    {
        $this->pharmacyModel = new PharmacyModel();
        $this->orderModel = new DoctorOrderModel();
    }

    /**
     * Display pharmacy dashboard
     */
    public function index()
    {
        // Check if user is logged in and is pharmacy staff
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Get dashboard data
        $data = [
            'prescriptionsToday' => 0,
            'pendingFulfillment' => 0,
            'lowStockItems' => 0,
            'totalInventory' => 0,
            'criticalItems' => 0,
            'expiringSoon' => 0,
            'outOfStock' => 0,
            'categoriesCount' => 0,
        ];

        try {
            // Get medication orders (prescriptions) with pharmacy status
            if ($db->tableExists('doctor_orders')) {
                $data['prescriptionsToday'] = $this->orderModel
                    ->where('order_type', 'medication')
                    ->where('DATE(created_at)', $today)
                    ->countAllResults();

                // Count by pharmacy status
                $data['pendingFulfillment'] = $db->table('doctor_orders')
                    ->where('order_type', 'medication')
                    ->where('pharmacy_status', 'pending')
                    ->countAllResults();
                
                $data['approvedCount'] = $db->table('doctor_orders')
                    ->where('order_type', 'medication')
                    ->where('pharmacy_status', 'approved')
                    ->countAllResults();
                
                $data['preparedCount'] = $db->table('doctor_orders')
                    ->where('order_type', 'medication')
                    ->where('pharmacy_status', 'prepared')
                    ->countAllResults();
                
                $data['dispensedToday'] = $db->table('doctor_orders')
                    ->where('order_type', 'medication')
                    ->where('pharmacy_status', 'dispensed')
                    ->where('DATE(pharmacy_dispensed_at)', $today)
                    ->countAllResults();
                
                $data['administeredCount'] = $db->table('doctor_orders')
                    ->where('order_type', 'medication')
                    ->where('pharmacy_status', 'dispensed')
                    ->where('status', 'completed')
                    ->countAllResults();
            }

            // Get inventory stats
            if ($db->tableExists('pharmacy')) {
                $data['totalInventory'] = $this->pharmacyModel->countAllResults();
                
                $data['lowStockItems'] = $this->pharmacyModel
                    ->where('quantity <', 20)
                    ->where('quantity >', 0)
                    ->countAllResults();
                
                $data['criticalItems'] = $this->pharmacyModel
                    ->where('quantity <', 10)
                    ->where('quantity >', 0)
                    ->countAllResults();
                
                $data['outOfStock'] = $this->pharmacyModel
                    ->where('quantity', 0)
                    ->countAllResults();

                // Count distinct categories
                $categories = $db->table('pharmacy')
                    ->select('DISTINCT SUBSTRING_INDEX(item_name, " ", 1) as category')
                    ->get()
                    ->getResultArray();
                $data['categoriesCount'] = count($categories);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error loading pharmacy dashboard: ' . $e->getMessage());
        }

        return view('pharmacy/dashboard', $data);
    }

    /**
     * Display prescription queue
     */
    public function prescriptionQueue()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();
        
        // Get medication orders with pharmacy status (including dispensed and administered)
        $prescriptions = $db->table('doctor_orders do')
            ->select('do.*, ap.firstname as patient_first, ap.lastname as patient_last, 
                      u.username as doctor_name, nu.username as nurse_name, 
                      completed_nurse.username as administered_by_name, do.created_at as order_date')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->join('users nu', 'nu.id = do.nurse_id', 'left')
            ->join('users as completed_nurse', 'completed_nurse.id = do.completed_by', 'left')
            ->where('do.order_type', 'medication')
            ->whereIn('do.pharmacy_status', ['pending', 'approved', 'prepared', 'dispensed'])
            ->orderBy('do.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get dispensed prescriptions (ready for nurse or already administered)
        $dispensedPrescriptions = $db->table('doctor_orders do')
            ->select('do.*, ap.firstname as patient_first, ap.lastname as patient_last, 
                      u.username as doctor_name, nu.username as nurse_name, 
                      completed_nurse.username as administered_by_name, do.created_at as order_date')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->join('users nu', 'nu.id = do.nurse_id', 'left')
            ->join('users as completed_nurse', 'completed_nurse.id = do.completed_by', 'left')
            ->where('do.order_type', 'medication')
            ->where('do.pharmacy_status', 'dispensed')
            ->orderBy('do.pharmacy_dispensed_at', 'DESC')
            ->get()
            ->getResultArray();

        // Group by pharmacy status
        $pendingPrescriptions = array_filter($prescriptions, fn($p) => ($p['pharmacy_status'] ?? 'pending') === 'pending');
        $approvedPrescriptions = array_filter($prescriptions, fn($p) => ($p['pharmacy_status'] ?? 'pending') === 'approved');
        $preparedPrescriptions = array_filter($prescriptions, fn($p) => ($p['pharmacy_status'] ?? 'pending') === 'prepared');
        
        // Separate dispensed into waiting for nurse and administered
        $dispensedWaiting = array_filter($dispensedPrescriptions, fn($p) => ($p['status'] ?? 'pending') !== 'completed');
        $administeredPrescriptions = array_filter($dispensedPrescriptions, fn($p) => ($p['status'] ?? 'pending') === 'completed');

        $data = [
            'prescriptions' => $prescriptions,
            'pendingPrescriptions' => $pendingPrescriptions,
            'approvedPrescriptions' => $approvedPrescriptions,
            'preparedPrescriptions' => $preparedPrescriptions,
            'dispensedPrescriptions' => $dispensedWaiting,
            'administeredPrescriptions' => $administeredPrescriptions,
        ];

        return view('pharmacy/prescription_queue', $data);
    }

    /**
     * Display medicine inventory/release
     */
    public function medicineRelease()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get all medicines
        $medicines = $this->pharmacyModel->findAll();

        $data = [
            'medicines' => $medicines
        ];

        return view('pharmacy/medicine_release', $data);
    }

    /**
     * Display stock monitoring
     */
    public function stockMonitoring()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get all medicines with stock status
        $medicines = $this->pharmacyModel
            ->orderBy('quantity', 'ASC')
            ->findAll();

        // Categorize by stock level
        $criticalStock = [];
        $lowStock = [];
        $normalStock = [];

        foreach ($medicines as $medicine) {
            if ($medicine['quantity'] == 0) {
                $criticalStock[] = $medicine;
            } elseif ($medicine['quantity'] < 10) {
                $criticalStock[] = $medicine;
            } elseif ($medicine['quantity'] < 20) {
                $lowStock[] = $medicine;
            } else {
                $normalStock[] = $medicine;
            }
        }

        $data = [
            'criticalStock' => $criticalStock,
            'lowStock' => $lowStock,
            'normalStock' => $normalStock,
            'allMedicines' => $medicines
        ];

        return view('pharmacy/stock_monitoring', $data);
    }

    /**
     * Update pharmacy status (Approve, Prepare, Dispense)
     */
    public function updatePharmacyStatus($orderId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $action = $this->request->getPost('action'); // approve, prepare, dispense
        $db = \Config\Database::connect();

        try {
            $order = $this->orderModel->find($orderId);
            
            if (!$order) {
                return $this->response->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Order not found']);
            }

            if ($order['order_type'] !== 'medication') {
                return $this->response->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'This is not a medication order']);
            }

            $currentStatus = $order['pharmacy_status'] ?? 'pending';
            $updateData = [];
            $statusMessage = '';

            switch ($action) {
                case 'approve':
                    if ($currentStatus !== 'pending') {
                        return $this->response->setContentType('application/json')
                            ->setJSON(['success' => false, 'message' => 'Order must be pending to approve']);
                    }
                    $updateData = [
                        'pharmacy_status' => 'approved',
                        'pharmacy_approved_at' => date('Y-m-d H:i:s')
                    ];
                    $statusMessage = 'Prescription approved successfully';
                    break;

                case 'prepare':
                    if ($currentStatus !== 'approved') {
                        return $this->response->setContentType('application/json')
                            ->setJSON(['success' => false, 'message' => 'Order must be approved before preparing']);
                    }
                    $updateData = [
                        'pharmacy_status' => 'prepared',
                        'pharmacy_prepared_at' => date('Y-m-d H:i:s')
                    ];
                    $statusMessage = 'Medicine prepared successfully';
                    break;

                case 'dispense':
                    if ($currentStatus !== 'prepared') {
                        return $this->response->setContentType('application/json')
                            ->setJSON(['success' => false, 'message' => 'Order must be prepared before dispensing']);
                    }
                    $updateData = [
                        'pharmacy_status' => 'dispensed',
                        'pharmacy_dispensed_at' => date('Y-m-d H:i:s'),
                        'status' => 'in_progress' // Order is ready for nurse to administer
                    ];
                    $statusMessage = 'Medicine dispensed successfully. Nurse can now administer to patient.';
                    
                    // Get patient allergies
                    $patient = $db->table('admin_patients')->where('id', $order['patient_id'])->get()->getRowArray();
                    $patientAllergies = $patient['allergies'] ?? null;
                    
                    // Save to patient_medication_records table
                    if ($db->tableExists('patient_medication_records')) {
                        $medicationRecordData = [
                            'patient_id' => $order['patient_id'],
                            'order_id' => $orderId,
                            'medicine_name' => $order['medicine_name'] ?? $order['order_description'] ?? 'N/A',
                            'dosage' => $order['dosage'] ?? null,
                            'frequency' => $order['frequency'] ?? null,
                            'duration' => $order['duration'] ?? null,
                            'prescribed_by' => $order['doctor_id'] ?? null,
                            'dispensed_at' => date('Y-m-d H:i:s'),
                            'allergies' => $patientAllergies,
                            'notes' => 'Dispensed by pharmacy',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        
                        $db->table('patient_medication_records')->insert($medicationRecordData);
                    }
                    
                    // Notify nurse that medicine is ready
                    if ($order['nurse_id']) {
                        if ($db->tableExists('nurse_notifications')) {
                            $db->table('nurse_notifications')->insert([
                                'nurse_id' => $order['nurse_id'],
                                'type' => 'medication_ready',
                                'title' => 'Medication Ready for Administration',
                                'message' => 'Medication for ' . ($patient ? $patient['firstname'] . ' ' . $patient['lastname'] : 'patient') . ' has been dispensed. Medicine: ' . ($order['medicine_name'] ?? 'N/A') . '. Please administer to patient.',
                                'related_id' => $orderId,
                                'related_type' => 'doctor_order',
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                    break;

                default:
                    return $this->response->setContentType('application/json')
                        ->setJSON(['success' => false, 'message' => 'Invalid action']);
            }

            // Update order
            $this->orderModel->update($orderId, $updateData);

            // Log the activity
            if ($db->tableExists('order_status_logs')) {
                $db->table('order_status_logs')->insert([
                    'order_id' => $orderId,
                    'status' => $order['status'],
                    'changed_by' => session()->get('user_id'),
                    'notes' => 'Pharmacy status changed to: ' . $updateData['pharmacy_status'] . ' by ' . (session()->get('username') ?? 'Pharmacy'),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => $statusMessage,
                    'pharmacy_status' => $updateData['pharmacy_status']
                ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error updating pharmacy status: ' . $e->getMessage());
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to update status: ' . $e->getMessage()
                ])->setStatusCode(500);
        }
    }

    /**
     * Process prescription (dispense medicine) - Legacy method for backward compatibility
     */
    public function dispensePrescription($orderId)
    {
        // Redirect to new method
        $this->request->setMethod('post');
        $_POST['action'] = 'dispense';
        return $this->updatePharmacyStatus($orderId);
    }

    /**
     * Update medicine stock
     */
    public function updateStock($medicineId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $quantity = $this->request->getPost('quantity');
        $action = $this->request->getPost('action'); // 'add' or 'set'

        if ($quantity === null || $action === null) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Invalid parameters']);
        }

        try {
            $db = \Config\Database::connect();
            $medicine = $db->table('pharmacy')->where('id', $medicineId)->get()->getRowArray();
            
            if (!$medicine) {
                return $this->response->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Medicine not found']);
            }

            $newQuantity = $action === 'add' 
                ? $medicine['quantity'] + (int)$quantity 
                : (int)$quantity;

            if ($newQuantity < 0) {
                return $this->response->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Invalid quantity']);
            }

            $db->table('pharmacy')->where('id', $medicineId)->update(['quantity' => $newQuantity]);

            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'message' => 'Stock updated successfully',
                    'new_quantity' => $newQuantity
                ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error updating stock: ' . $e->getMessage());
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to update stock'
                ])->setStatusCode(500);
        }
    }

    /**
     * Add new medicine
     */
    public function addMedicine()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Handle POST request
        if ($this->request->getMethod() === 'POST' || $_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemName = trim($this->request->getPost('item_name') ?? '');
            $description = trim($this->request->getPost('description') ?? '');
            $quantity = $this->request->getPost('quantity');
            $price = $this->request->getPost('price');
            
            // Validation
            if (empty($itemName)) {
                return redirect()->back()->withInput()->with('error', 'Medicine name is required');
            }
            
            if ($quantity === '' || $quantity === null || $quantity < 0) {
                return redirect()->back()->withInput()->with('error', 'Valid quantity is required');
            }
            
            if ($price === '' || $price === null || $price < 0) {
                return redirect()->back()->withInput()->with('error', 'Valid price is required');
            }
            
            // Insert to database
            $db = \Config\Database::connect();
            $data = [
                'item_name' => $itemName,
                'description' => $description,
                'quantity' => (int)$quantity,
                'price' => (float)$price,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $db->table('pharmacy')->insert($data);
            
            if ($result) {
                return redirect()->to(site_url('pharmacy/stock-monitoring'))
                    ->with('success', 'Medicine added successfully!');
            } else {
                $error = $db->error();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to add medicine. ' . ($error['message'] ?? ''));
            }
        }

        // Show form for GET request
        return view('pharmacy/add_medicine');
    }

    /**
     * Edit medicine
     */
    public function editMedicine($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $medicine = $this->pharmacyModel->find($id);

        if (!$medicine) {
            return redirect()->to('/pharmacy/stock-monitoring')
                ->with('error', 'Medicine not found');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'item_name' => $this->request->getPost('item_name'),
                'description' => $this->request->getPost('description'),
                'quantity' => $this->request->getPost('quantity'),
                'price' => $this->request->getPost('price'),
            ];

            if ($this->pharmacyModel->update($id, $data)) {
                return redirect()->to('/pharmacy/stock-monitoring')
                    ->with('success', 'Medicine updated successfully');
            } else {
                $errors = $this->pharmacyModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Failed to update medicine';
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage)
                    ->with('errors', $errors);
            }
        }

        $data = ['medicine' => $medicine];
        return view('pharmacy/edit_medicine', $data);
    }

    /**
     * Delete medicine
     */
    public function deleteMedicine($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $role = session()->get('role');
        if ($role !== 'pharmacy' && $role !== 'admin') {
            return $this->response->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        try {
            $db = \Config\Database::connect();
            $result = $db->table('pharmacy')->where('id', $id)->delete();
            
            if ($result) {
                return $this->response->setContentType('application/json')
                    ->setJSON([
                        'success' => true,
                        'message' => 'Medicine deleted successfully'
                    ]);
            } else {
                return $this->response->setContentType('application/json')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Failed to delete medicine'
                    ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error deleting medicine: ' . $e->getMessage());
            return $this->response->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete medicine'
                ])->setStatusCode(500);
        }
    }
}

