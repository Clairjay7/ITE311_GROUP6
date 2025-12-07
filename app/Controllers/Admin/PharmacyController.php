<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PharmacyModel;

class PharmacyController extends BaseController
{
    protected $pharmacyModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->pharmacyModel = new PharmacyModel();
    }

    public function index()
    {
        $pharmacyItems = $this->pharmacyModel->orderBy('created_at', 'DESC')->findAll();
        
        $db = \Config\Database::connect();
        
        // Get prescription queue data (same as pharmacy controller but read-only)
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

        // Get dispensed prescriptions
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

        // Get stock monitoring data - use same filters as Medicine Inventory Module
        // Valid categories from Add Item form (12 hospital pharmacy categories)
        $validCategories = [
            'Analgesics / Antipyretics',
            'Anti-inflammatory (NSAIDs)',
            'Antibiotics',
            'Antihistamines',
            'Cough & Cold (Respiratory)',
            'Gastrointestinal Medicines',
            'Cardiovascular Medicines',
            'Diabetic Medicines',
            'Vitamins & Supplements',
            'IV Fluids / Electrolytes',
            'Emergency Drugs',
            'Medical Supplies'
        ];
        
        // Get stock monitoring data with same filters as inventory
        $stockBuilder = $this->pharmacyModel->builder();
        $stockBuilder->where('status', 'active')
            ->whereIn('category', $validCategories)
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->where('expiration_date IS NOT NULL')
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '')
            ->orderBy('quantity', 'ASC');
        
        $medicines = $stockBuilder->get()->getResultArray();

        // Categorize by stock level
        $criticalStock = [];
        $lowStock = [];
        $normalStock = [];

        foreach ($medicines as $medicine) {
            $reorderLevel = $medicine['reorder_level'] ?? 10;
            if ($medicine['quantity'] == 0) {
                $criticalStock[] = $medicine;
            } elseif ($medicine['quantity'] <= $reorderLevel) {
                $criticalStock[] = $medicine;
            } elseif ($medicine['quantity'] < ($reorderLevel * 2)) {
                $lowStock[] = $medicine;
            } else {
                $normalStock[] = $medicine;
            }
        }
        
        // Store all medicines for "All Medicines" tab
        $allMedicines = $medicines;

        // Get Medicine Inventory data with pagination and search
        $search = trim((string)$this->request->getGet('inventory_search'));
        $selectedCategory = trim((string)$this->request->getGet('inventory_category'));
        $perPage = 10; // Items per page
        
        // Valid categories from Add Item form (12 hospital pharmacy categories)
        $validCategories = [
            'Analgesics / Antipyretics',
            'Anti-inflammatory (NSAIDs)',
            'Antibiotics',
            'Antihistamines',
            'Cough & Cold (Respiratory)',
            'Gastrointestinal Medicines',
            'Cardiovascular Medicines',
            'Diabetic Medicines',
            'Vitamins & Supplements',
            'IV Fluids / Electrolytes',
            'Emergency Drugs',
            'Medical Supplies'
        ];
        
        // Get category statistics (count per category) - before filtering
        $categoryStatsBuilder = $this->pharmacyModel->builder();
        $categoryStatsBuilder->where('status', 'active')
            ->whereIn('category', $validCategories)
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->where('expiration_date IS NOT NULL')
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '');
        
        $categoryStats = $categoryStatsBuilder->select('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('category', 'ASC')
            ->get()
            ->getResultArray();
        
        // Create category count map
        $categoryCountMap = [];
        foreach ($categoryStats as $stat) {
            $categoryCountMap[$stat['category']] = $stat['count'];
        }
        
        $inventoryBuilder = $this->pharmacyModel->builder();
        $inventoryBuilder->where('status', 'active');
        
        // Only show medicines with valid categories from Add Item form
        $inventoryBuilder->whereIn('category', $validCategories);
        
        // Filter by selected category if provided
        if ($selectedCategory !== '' && in_array($selectedCategory, $validCategories)) {
            $inventoryBuilder->where('category', $selectedCategory);
        }
        
        // Only show medicines that have batch_number, expiration_date, and supplier_name
        $inventoryBuilder->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->where('expiration_date IS NOT NULL')
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '');
        
        // Apply search filter
        if ($search !== '') {
            $inventoryBuilder->groupStart()
                ->like('item_name', $search)
                ->orLike('generic_name', $search)
                ->orLike('category', $search)
                ->orLike('strength', $search)
                ->orLike('dosage_form', $search)
                ->orLike('batch_number', $search)
                ->orLike('supplier_name', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }
        
        $inventoryBuilder->orderBy('item_name', 'ASC');
        
        // Get total count for pagination (before limit)
        $totalInventory = $inventoryBuilder->countAllResults(false);
        
        // Get paginated results
        $pager = \Config\Services::pager();
        $page = (int)($this->request->getGet('inventory_page') ?? 1);
        $page = max(1, $page); // Ensure page is at least 1
        $offset = ($page - 1) * $perPage;
        
        $inventoryMedicines = $inventoryBuilder->get($perPage, $offset)->getResultArray();
        
        // Create pagination data manually
        $totalPages = ceil($totalInventory / $perPage);
        $inventoryPager = (object)[
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'total' => $totalInventory,
            'hasMore' => $page < $totalPages,
        ];
        
        // Get category statistics (count per category)
        $categoryStatsBuilder = $this->pharmacyModel->builder();
        $categoryStatsBuilder->where('status', 'active')
            ->whereIn('category', $validCategories)
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->where('expiration_date IS NOT NULL')
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '');
        
        $categoryStats = $categoryStatsBuilder->select('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('category', 'ASC')
            ->get()
            ->getResultArray();
        
        // Create category count map
        $categoryCountMap = [];
        foreach ($categoryStats as $stat) {
            $categoryCountMap[$stat['category']] = $stat['count'];
        }

        // Get Patient Medication Records
        $patientMedicationRecords = [];
        
        // Check if admin_patients has allergies column
        $hasAllergiesColumn = false;
        if ($db->tableExists('admin_patients')) {
            try {
                $fields = $db->getFieldData('admin_patients');
                $fieldNames = array_column($fields, 'name');
                $hasAllergiesColumn = in_array('allergies', $fieldNames);
            } catch (\Exception $e) {
                // If we can't check, assume it doesn't exist
                $hasAllergiesColumn = false;
            }
        }
        
        if ($db->tableExists('patient_medication_records')) {
            $baseSelect = 'pmr.*, ap.firstname, ap.lastname, 
                         u.username as doctor_name, do.medicine_name, do.dosage, do.frequency, do.duration,
                         do.pharmacy_dispensed_at, do.status as order_status';
            
            if ($hasAllergiesColumn) {
                $baseSelect .= ', ap.allergies as patient_allergies';
            } else {
                $baseSelect .= ", '' as patient_allergies";
            }
            
            $patientMedicationRecords = $db->table('patient_medication_records pmr')
                ->select($baseSelect)
                ->join('admin_patients ap', 'ap.id = pmr.patient_id', 'left')
                ->join('users u', 'u.id = pmr.prescribed_by', 'left')
                ->join('doctor_orders do', 'do.id = pmr.order_id', 'left')
                ->orderBy('pmr.created_at', 'DESC')
                ->get()
                ->getResultArray();
        } else {
            // If table doesn't exist, get from doctor_orders (dispensed medications)
            $baseSelect = 'do.*, ap.firstname, ap.lastname,
                         u.username as doctor_name, do.medicine_name, do.dosage, do.frequency, do.duration,
                         do.pharmacy_dispensed_at, do.status as order_status,
                         do.id as order_id, do.patient_id, do.created_at';
            
            if ($hasAllergiesColumn) {
                $baseSelect .= ', ap.allergies as patient_allergies';
            } else {
                $baseSelect .= ", '' as patient_allergies";
            }
            
            $patientMedicationRecords = $db->table('doctor_orders do')
                ->select($baseSelect)
                ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
                ->join('users u', 'u.id = do.doctor_id', 'left')
                ->where('do.order_type', 'medication')
                ->where('do.pharmacy_status', 'dispensed')
                ->orderBy('do.pharmacy_dispensed_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        // Group medication records by patient
        $patientRecords = [];
        foreach ($patientMedicationRecords as $record) {
            $patientId = $record['patient_id'];
            if (!isset($patientRecords[$patientId])) {
                $patientRecords[$patientId] = [
                    'patient_id' => $patientId,
                    'patient_name' => ($record['firstname'] ?? '') . ' ' . ($record['lastname'] ?? ''),
                    'allergies' => $record['patient_allergies'] ?? '',
                    'medications' => [],
                    'total_transactions' => 0,
                ];
            }
            $patientRecords[$patientId]['medications'][] = $record;
            $patientRecords[$patientId]['total_transactions']++;
        }
        
        $data = [
            'title' => 'Pharmacy Desk',
            'pharmacyItems' => $pharmacyItems,
            'prescriptions' => $prescriptions,
            'pendingPrescriptions' => $pendingPrescriptions,
            'approvedPrescriptions' => $approvedPrescriptions,
            'preparedPrescriptions' => $preparedPrescriptions,
            'dispensedPrescriptions' => $dispensedWaiting,
            'administeredPrescriptions' => $administeredPrescriptions,
            'criticalStock' => $criticalStock,
            'lowStock' => $lowStock,
            'normalStock' => $normalStock,
            'allMedicines' => $allMedicines,
            'inventoryMedicines' => $inventoryMedicines,
            'inventoryPager' => $inventoryPager,
            'inventorySearch' => $search,
            'patientMedicationRecords' => $patientMedicationRecords,
            'patientRecords' => $patientRecords,
            'validCategories' => $validCategories,
            'categoryCountMap' => $categoryCountMap,
            'selectedCategory' => $selectedCategory ?? '',
        ];

        return view('admin/pharmacy/index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        
        // Get unique suppliers from existing pharmacy records
        $existingSuppliers = $db->table('pharmacy')
            ->select('supplier_name')
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '')
            ->distinct()
            ->orderBy('supplier_name', 'ASC')
            ->get()
            ->getResultArray();
        
        $suppliers = array_column($existingSuppliers, 'supplier_name');
        
        // Add common suppliers if not already in the list
        $commonSuppliers = [
            'MedSupply Co.',
            'PharmaDist Inc.',
            'Healthcare Supplies Ltd.',
            'Medical Equipment Corp.',
            'Pharmaceutical Distributors',
            'MedTech Solutions',
            'HealthCare Partners',
            'PharmaLink International',
        ];
        
        foreach ($commonSuppliers as $supplier) {
            if (!in_array($supplier, $suppliers)) {
                $suppliers[] = $supplier;
            }
        }
        
        sort($suppliers);
        
        // Get unique batch numbers from existing pharmacy records
        $existingBatchNumbers = $db->table('pharmacy')
            ->select('batch_number')
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->distinct()
            ->orderBy('batch_number', 'ASC')
            ->get()
            ->getResultArray();
        
        $batchNumbers = array_column($existingBatchNumbers, 'batch_number');
        
        // Generate common batch number patterns
        $currentYear = date('Y');
        $commonBatchNumbers = [];
        
        // Generate BATCH-001 to BATCH-050
        for ($i = 1; $i <= 50; $i++) {
            $batchNum = 'BATCH-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            if (!in_array($batchNum, $batchNumbers)) {
                $commonBatchNumbers[] = $batchNum;
            }
        }
        
        // Generate BATCH-2025-001 to BATCH-2025-050
        for ($i = 1; $i <= 50; $i++) {
            $batchNum = 'BATCH-' . $currentYear . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            if (!in_array($batchNum, $batchNumbers)) {
                $commonBatchNumbers[] = $batchNum;
            }
        }
        
        // Merge and sort
        $batchNumbers = array_merge($batchNumbers, $commonBatchNumbers);
        sort($batchNumbers);
        
        // Get unique generic names from existing pharmacy records
        $existingGenericNames = $db->table('pharmacy')
            ->select('generic_name')
            ->where('generic_name IS NOT NULL')
            ->where('generic_name !=', '')
            ->distinct()
            ->orderBy('generic_name', 'ASC')
            ->get()
            ->getResultArray();
        
        $genericNames = array_column($existingGenericNames, 'generic_name');
        
        // Add common generic names if not already in the list
        $commonGenericNames = [
            'Acetaminophen',
            'Paracetamol',
            'Ibuprofen',
            'Amoxicillin',
            'Cephalexin',
            'Azithromycin',
            'Ciprofloxacin',
            'Cetirizine',
            'Loratadine',
            'Omeprazole',
            'Metformin',
            'Amlodipine',
            'Losartan',
            'Atorvastatin',
            'Ascorbic Acid',
            'Calcium Carbonate',
            'Ferrous Sulfate',
            'Salbutamol',
            'Dextromethorphan',
            'Epinephrine',
        ];
        
        foreach ($commonGenericNames as $genericName) {
            if (!in_array($genericName, $genericNames)) {
                $genericNames[] = $genericName;
            }
        }
        
        sort($genericNames);
        
        $data = [
            'title' => 'Add Pharmacy Item',
            'validation' => \Config\Services::validation(),
            'suppliers' => $suppliers,
            'batchNumbers' => $batchNumbers,
            'genericNames' => $genericNames,
        ];

        return view('admin/pharmacy/create', $data);
    }
    
    public function getMedicinesByCategory()
    {
        $category = $this->request->getGet('category');
        
        if (empty($category)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category is required']);
        }
        
        $medicines = $this->pharmacyModel
            ->where('category', $category)
            ->where('status', 'active')
            ->orderBy('item_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'medicines' => $medicines,
            'count' => count($medicines)
        ]);
    }
    
    public function getGenericNamesByCategory()
    {
        $category = $this->request->getGet('category');
        
        if (empty($category)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category is required']);
        }
        
        $db = \Config\Database::connect();
        
        // Get unique generic names from medicines in this category
        $genericNames = $db->table('pharmacy')
            ->select('generic_name')
            ->where('category', $category)
            ->where('generic_name IS NOT NULL')
            ->where('generic_name !=', '')
            ->where('status', 'active')
            ->distinct()
            ->orderBy('generic_name', 'ASC')
            ->get()
            ->getResultArray();
        
        $genericNameList = array_column($genericNames, 'generic_name');
        
        return $this->response->setJSON([
            'success' => true,
            'genericNames' => $genericNameList,
            'count' => count($genericNameList)
        ]);
    }
    
    public function getDosageStrengthByCategory()
    {
        $category = $this->request->getGet('category');
        
        if (empty($category)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category is required']);
        }
        
        $db = \Config\Database::connect();
        
        // Get unique dosage forms and strengths from medicines in this category
        $dosageForms = $db->table('pharmacy')
            ->select('dosage_form')
            ->where('category', $category)
            ->where('dosage_form IS NOT NULL')
            ->where('dosage_form !=', '')
            ->where('status', 'active')
            ->distinct()
            ->orderBy('dosage_form', 'ASC')
            ->get()
            ->getResultArray();
        
        $strengths = $db->table('pharmacy')
            ->select('strength')
            ->where('category', $category)
            ->where('strength IS NOT NULL')
            ->where('strength !=', '')
            ->where('status', 'active')
            ->distinct()
            ->orderBy('strength', 'ASC')
            ->get()
            ->getResultArray();
        
        $dosageFormList = array_column($dosageForms, 'dosage_form');
        $strengthList = array_column($strengths, 'strength');
        
        return $this->response->setJSON([
            'success' => true,
            'category' => $category,
            'dosageForms' => $dosageFormList,
            'strengths' => $strengthList,
            'dosageFormCount' => count($dosageFormList),
            'strengthCount' => count($strengthList)
        ]);
    }
    
    public function getInventoryInfoByCategory()
    {
        $category = $this->request->getGet('category');
        
        if (empty($category)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category is required']);
        }
        
        $db = \Config\Database::connect();
        
        // Get inventory information from medicines in this category
        $medicines = $db->table('pharmacy')
            ->select('id, item_name, batch_number, expiration_date, supplier_name, supplier_contact, quantity, unit_price, selling_price, price, markup_percent')
            ->where('category', $category)
            ->where('status', 'active')
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->where('expiration_date IS NOT NULL')
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '')
            ->orderBy('item_name', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get unique batch numbers, expiration dates, and suppliers
        $batchNumbers = $db->table('pharmacy')
            ->select('batch_number')
            ->where('category', $category)
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->where('status', 'active')
            ->distinct()
            ->orderBy('batch_number', 'ASC')
            ->get()
            ->getResultArray();
        
        $suppliers = $db->table('pharmacy')
            ->select('supplier_name, supplier_contact')
            ->where('category', $category)
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '')
            ->where('status', 'active')
            ->distinct()
            ->orderBy('supplier_name', 'ASC')
            ->get()
            ->getResultArray();
        
        // Calculate total quantity in this category
        $totalQuantity = $db->table('pharmacy')
            ->selectSum('quantity')
            ->where('category', $category)
            ->where('status', 'active')
            ->get()
            ->getRowArray();
        
        // Get most common batch number and expiration date pattern
        $mostCommonBatch = $db->table('pharmacy')
            ->select('batch_number, COUNT(*) as count')
            ->where('category', $category)
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->where('status', 'active')
            ->groupBy('batch_number')
            ->orderBy('count', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
        
        // Generate next batch number based on pattern
        $nextBatchNumber = 'BATCH-001';
        if ($mostCommonBatch && isset($mostCommonBatch['batch_number'])) {
            $lastBatch = $mostCommonBatch['batch_number'];
            if (preg_match('/BATCH-(\d+)/', $lastBatch, $matches)) {
                $nextNum = intval($matches[1]) + 1;
                $nextBatchNumber = 'BATCH-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            }
        }
        
        // Get future expiration date (2 years from now)
        $nextExpirationDate = date('Y-m-d', strtotime('+2 years'));
        
        // Get most common supplier
        $mostCommonSupplier = $db->table('pharmacy')
            ->select('supplier_name, supplier_contact, COUNT(*) as count')
            ->where('category', $category)
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '')
            ->where('status', 'active')
            ->groupBy('supplier_name, supplier_contact')
            ->orderBy('count', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
        
        return $this->response->setJSON([
            'success' => true,
            'category' => $category,
            'medicines' => $medicines,
            'totalQuantity' => (int)($totalQuantity['quantity'] ?? 0),
            'batchNumbers' => array_column($batchNumbers, 'batch_number'),
            'suppliers' => $suppliers,
            'nextBatchNumber' => $nextBatchNumber,
            'nextExpirationDate' => $nextExpirationDate,
            'defaultSupplier' => $mostCommonSupplier ? [
                'name' => $mostCommonSupplier['supplier_name'],
                'contact' => $mostCommonSupplier['supplier_contact'] ?? ''
            ] : null
        ]);
    }
    
    public function getAveragePricingByCategory()
    {
        $category = $this->request->getGet('category');
        
        if (empty($category)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category is required']);
        }
        
        $db = \Config\Database::connect();
        
        // Get average pricing from existing medicines in this category
        $pricing = $db->table('pharmacy')
            ->select('AVG(unit_price) as avg_unit_price, AVG(selling_price) as avg_selling_price, AVG(price) as avg_price, AVG(markup_percent) as avg_markup')
            ->where('category', $category)
            ->where('status', 'active')
            ->where('unit_price IS NOT NULL')
            ->where('unit_price >', 0)
            ->where('selling_price IS NOT NULL')
            ->where('selling_price >', 0)
            ->get()
            ->getRowArray();
        
        // If no data found, use default pricing based on category
        if (!$pricing || !$pricing['avg_unit_price']) {
            $defaultPricing = $this->getDefaultPricingByCategory($category);
            return $this->response->setJSON([
                'success' => true,
                'unit_price' => $defaultPricing['unit_price'],
                'selling_price' => $defaultPricing['selling_price'],
                'price' => $defaultPricing['selling_price'],
                'markup_percent' => $defaultPricing['markup_percent'],
                'is_default' => true
            ]);
        }
        
        $avgUnitPrice = round((float)$pricing['avg_unit_price'], 2);
        $avgSellingPrice = round((float)$pricing['avg_selling_price'], 2);
        $avgPrice = round((float)($pricing['avg_price'] ?? $avgSellingPrice), 2);
        $avgMarkup = $avgUnitPrice > 0 ? round((($avgSellingPrice - $avgUnitPrice) / $avgUnitPrice) * 100, 2) : 0;
        
        return $this->response->setJSON([
            'success' => true,
            'unit_price' => $avgUnitPrice,
            'selling_price' => $avgSellingPrice,
            'price' => $avgPrice,
            'markup_percent' => $avgMarkup,
            'is_default' => false
        ]);
    }
    
    private function getDefaultPricingByCategory($category)
    {
        // Default pricing based on category (from seeder analysis)
        $defaultPricing = [
            'Analgesics / Antipyretics' => ['unit_price' => 15.00, 'selling_price' => 22.00, 'markup_percent' => 46.67],
            'Anti-inflammatory (NSAIDs)' => ['unit_price' => 18.00, 'selling_price' => 27.00, 'markup_percent' => 50.00],
            'Antibiotics' => ['unit_price' => 45.00, 'selling_price' => 65.00, 'markup_percent' => 44.44],
            'Antihistamines' => ['unit_price' => 8.00, 'selling_price' => 12.00, 'markup_percent' => 50.00],
            'Cough & Cold (Respiratory)' => ['unit_price' => 12.00, 'selling_price' => 18.00, 'markup_percent' => 50.00],
            'Gastrointestinal Medicines' => ['unit_price' => 10.00, 'selling_price' => 15.00, 'markup_percent' => 50.00],
            'Cardiovascular Medicines' => ['unit_price' => 12.00, 'selling_price' => 18.00, 'markup_percent' => 50.00],
            'Diabetic Medicines' => ['unit_price' => 15.00, 'selling_price' => 22.00, 'markup_percent' => 46.67],
            'Vitamins & Supplements' => ['unit_price' => 6.00, 'selling_price' => 9.00, 'markup_percent' => 50.00],
            'IV Fluids / Electrolytes' => ['unit_price' => 75.00, 'selling_price' => 110.00, 'markup_percent' => 46.67],
            'Emergency Drugs' => ['unit_price' => 180.00, 'selling_price' => 260.00, 'markup_percent' => 44.44],
            'Medical Supplies' => ['unit_price' => 25.00, 'selling_price' => 37.00, 'markup_percent' => 48.00],
        ];
        
        return $defaultPricing[$category] ?? ['unit_price' => 10.00, 'selling_price' => 15.00, 'markup_percent' => 50.00];
    }

    public function addStock()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $medicineId = $this->request->getJSON(true)['medicine_id'] ?? null;
        $quantity = $this->request->getJSON(true)['quantity'] ?? null;
        $action = $this->request->getJSON(true)['action'] ?? 'add';

        if (!$medicineId || $quantity === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Medicine ID and quantity are required']);
        }

        try {
            $medicine = $this->pharmacyModel->find($medicineId);
            
            if (!$medicine) {
                return $this->response->setJSON(['success' => false, 'message' => 'Medicine not found']);
            }

            $newQuantity = $action === 'add' 
                ? $medicine['quantity'] + (int)$quantity 
                : (int)$quantity;

            if ($newQuantity < 0) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid quantity']);
            }

            $this->pharmacyModel->update($medicineId, ['quantity' => $newQuantity]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Stock added successfully',
                'new_quantity' => $newQuantity
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error adding stock: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add stock: ' . $e->getMessage()
            ]);
        }
    }
    
    public function updatePricing()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $pricingUpdates = $this->request->getJSON(true)['pricing_updates'] ?? [];

        if (empty($pricingUpdates)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No pricing updates provided']);
        }

        try {
            $successCount = 0;
            $failedCount = 0;
            
            foreach ($pricingUpdates as $update) {
                $medicineId = $update['medicine_id'] ?? null;
                if (!$medicineId) {
                    $failedCount++;
                    continue;
                }
                
                $medicine = $this->pharmacyModel->find($medicineId);
                if (!$medicine) {
                    $failedCount++;
                    continue;
                }
                
                $data = [];
                if (isset($update['unit_price'])) {
                    $data['unit_price'] = $update['unit_price'] ?: null;
                }
                if (isset($update['selling_price'])) {
                    $data['selling_price'] = $update['selling_price'] ?: null;
                }
                if (isset($update['price'])) {
                    $data['price'] = $update['price'];
                }
                if (isset($update['markup_percent'])) {
                    $data['markup_percent'] = $update['markup_percent'] ?: null;
                }
                
                if ($this->pharmacyModel->update($medicineId, $data)) {
                    $successCount++;
                } else {
                    $failedCount++;
                }
            }
            
            $message = "Pricing updated for {$successCount} medicine(s)";
            if ($failedCount > 0) {
                $message .= " ({$failedCount} failed)";
            }
            
            return $this->response->setJSON([
                'success' => $successCount > 0,
                'message' => $message,
                'success_count' => $successCount,
                'failed_count' => $failedCount
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error updating pricing: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update pricing: ' . $e->getMessage()
            ]);
        }
    }

    public function store()
    {
        // Only require fields that are in the form: category and quantity
        $rules = [
            'item_name' => 'permit_empty|max_length[255]',
            'generic_name' => 'permit_empty|max_length[255]',
            'category' => 'required|max_length[100]',
            'description' => 'permit_empty',
            'strength' => 'permit_empty|max_length[100]',
            'dosage_form' => 'permit_empty|max_length[100]',
            'quantity' => 'required|integer|greater_than_equal_to[0]',
            'reorder_level' => 'permit_empty|integer|greater_than_equal_to[0]',
            'batch_number' => 'permit_empty|max_length[100]',
            'expiration_date' => 'permit_empty|valid_date',
            'price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'unit_price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'selling_price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'markup_percent' => 'permit_empty|decimal',
            'supplier_name' => 'permit_empty|max_length[255]',
            'supplier_contact' => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get category for default item name
        $category = $this->request->getPost('category');
        $itemName = $this->request->getPost('item_name');
        
        // If item_name is empty, use a default based on category
        if (empty($itemName)) {
            $itemName = 'New ' . ($category ?: 'Item') . ' Item';
        }
        
        $data = [
            'item_name' => $itemName,
            'generic_name' => $this->request->getPost('generic_name') ?: null,
            'category' => $category,
            'description' => $this->request->getPost('description') ?: null,
            'strength' => $this->request->getPost('strength') ?: null,
            'dosage_form' => $this->request->getPost('dosage_form') ?: null,
            'quantity' => $this->request->getPost('quantity'),
            'reorder_level' => $this->request->getPost('reorder_level') ?: 10,
            'batch_number' => $this->request->getPost('batch_number') ?: null,
            'expiration_date' => $this->request->getPost('expiration_date') ?: null,
            'price' => $this->request->getPost('price') ?: 0, // Default to 0 instead of null
            'unit_price' => $this->request->getPost('unit_price') ?: null,
            'selling_price' => $this->request->getPost('selling_price') ?: null,
            'markup_percent' => $this->request->getPost('markup_percent') ?: null,
            'supplier_name' => $this->request->getPost('supplier_name') ?: null,
            'supplier_contact' => $this->request->getPost('supplier_contact') ?: null,
            'status' => 'active', // Default to active
        ];

        log_message('debug', 'Attempting to insert new pharmacy item with data: ' . json_encode($data));
        
        $insertedId = false;
        $insertErrors = [];
        
        // Try to insert new item only if we have minimum required data
        // Skip model validation - we're handling validation in the controller
        $this->pharmacyModel->skipValidation(true);
        
        if (!empty($data['category']) && isset($data['quantity'])) {
            log_message('debug', 'Attempting to insert new pharmacy item with data: ' . json_encode($data));
            $insertedId = $this->pharmacyModel->insert($data);
            log_message('debug', 'Insert result - ID: ' . ($insertedId ?: 'failed'));
            
            if (!$insertedId) {
                $insertErrors = $this->pharmacyModel->errors();
                log_message('error', 'Failed to insert pharmacy item. Errors: ' . json_encode($insertErrors));
            }
        } else {
            log_message('debug', 'Skipping new item creation - missing category or quantity');
        }
        
        // Re-enable validation for future operations
        $this->pharmacyModel->skipValidation(false);
        
        $updatedMedicines = [];
        $successMessage = $insertedId ? 'Pharmacy item created successfully.' : 'Processing updates...';
        
        // Process selected medicines (add quantity and update pricing) regardless of new item creation
        // This allows updating existing medicines even if new item creation fails
        
        // Get all POST data for debugging
        $allPostData = $this->request->getPost();
        log_message('debug', 'All POST data keys: ' . implode(', ', array_keys($allPostData)));
        log_message('debug', 'All POST data: ' . json_encode($allPostData));
        
        $selectedMedicinesData = $this->request->getPost('selected_medicines_data');
        
        // Log for debugging
        log_message('debug', 'Selected medicines data received: ' . ($selectedMedicinesData ?: 'empty'));
        log_message('debug', 'Selected medicines data type: ' . gettype($selectedMedicinesData));
        if ($selectedMedicinesData) {
            log_message('debug', 'Selected medicines data length: ' . strlen($selectedMedicinesData));
        }
        
        if ($selectedMedicinesData) {
                $medicinesData = json_decode($selectedMedicinesData, true);
                
                // Log for debugging
                log_message('debug', 'Decoded medicines data: ' . json_encode($medicinesData));
                
                if (is_array($medicinesData) && !empty($medicinesData)) {
                    foreach ($medicinesData as $medicineData) {
                        $medicineId = $medicineData['medicine_id'] ?? null;
                        if (!$medicineId) {
                            log_message('debug', 'Skipping medicine - no ID found');
                            continue;
                        }
                        
                        // Get current medicine data
                        $medicine = $this->pharmacyModel->find($medicineId);
                        if (!$medicine) {
                            log_message('debug', 'Medicine not found with ID: ' . $medicineId);
                            continue;
                        }
                        
                        // Prepare update data
                        $updateData = [];
                        $quantityUpdated = false;
                        $pricingUpdated = false;
                        
                        // Add quantity to existing quantity
                        $quantityToAdd = isset($medicineData['quantity_to_add']) ? (int)$medicineData['quantity_to_add'] : 0;
                        log_message('debug', 'Medicine ID: ' . $medicineId . ', Quantity to add: ' . $quantityToAdd);
                        log_message('debug', 'Medicine data keys: ' . implode(', ', array_keys($medicineData)));
                        log_message('debug', 'Medicine data: ' . json_encode($medicineData));
                        
                        // Always update quantity if quantity_to_add is provided and > 0
                        if (isset($medicineData['quantity_to_add']) && $quantityToAdd > 0) {
                            $currentQuantity = (int)($medicine['quantity'] ?? 0);
                            $newQuantity = $currentQuantity + $quantityToAdd;
                            $updateData['quantity'] = $newQuantity;
                            $quantityUpdated = true;
                            log_message('debug', 'Updating quantity: ' . $currentQuantity . ' + ' . $quantityToAdd . ' = ' . $newQuantity);
                        } else {
                            log_message('debug', 'Skipping quantity update - quantity_to_add is: ' . $quantityToAdd . ' (isset: ' . (isset($medicineData['quantity_to_add']) ? 'yes' : 'no') . ')');
                        }
                        
                        // Update pricing if provided (only if not null and not empty string)
                        if (isset($medicineData['unit_price']) && $medicineData['unit_price'] !== null && $medicineData['unit_price'] !== '' && $medicineData['unit_price'] !== 0) {
                            $updateData['unit_price'] = (float)$medicineData['unit_price'];
                            $pricingUpdated = true;
                        }
                        if (isset($medicineData['selling_price']) && $medicineData['selling_price'] !== null && $medicineData['selling_price'] !== '' && $medicineData['selling_price'] !== 0) {
                            $updateData['selling_price'] = (float)$medicineData['selling_price'];
                            $pricingUpdated = true;
                            // Recalculate markup if unit_price is available
                            $unitPriceForMarkup = $updateData['unit_price'] ?? $medicine['unit_price'] ?? 0;
                            if ($unitPriceForMarkup > 0) {
                                $updateData['markup_percent'] = round((($updateData['selling_price'] - $unitPriceForMarkup) / $unitPriceForMarkup) * 100, 2);
                            }
                        }
                        if (isset($medicineData['price']) && $medicineData['price'] !== null && $medicineData['price'] !== '' && $medicineData['price'] !== 0) {
                            $updateData['price'] = (float)$medicineData['price'];
                            $pricingUpdated = true;
                        }
                        
                        // Update medicine if there's data to update
                        if (!empty($updateData)) {
                            log_message('debug', 'Updating medicine ID ' . $medicineId . ' with data: ' . json_encode($updateData));
                            log_message('debug', 'Current medicine data before update: ' . json_encode($medicine));
                            
                            // Skip validation for updates - we're handling validation in the controller
                            $this->pharmacyModel->skipValidation(true);
                            $result = $this->pharmacyModel->update($medicineId, $updateData);
                            $this->pharmacyModel->skipValidation(false);
                            
                            log_message('debug', 'Update result: ' . ($result ? 'success' : 'failed'));
                            
                            if ($result) {
                                // Verify the update by fetching the medicine again
                                $updatedMedicine = $this->pharmacyModel->find($medicineId);
                                log_message('debug', 'Medicine data after update: ' . json_encode($updatedMedicine));
                            } else {
                                $errors = $this->pharmacyModel->errors();
                                log_message('error', 'Failed to update medicine ID ' . $medicineId . '. Errors: ' . json_encode($errors));
                            }
                            
                            if ($result) {
                                $updatedMedicines[] = [
                                    'name' => $medicine['item_name'] ?? 'Medicine #' . $medicineId,
                                    'quantity_added' => $quantityUpdated ? $quantityToAdd : 0,
                                    'pricing_updated' => $pricingUpdated
                                ];
                            }
                        } else {
                            log_message('debug', 'No update data for medicine ID: ' . $medicineId);
                        }
                    }
                    
                    // Build success message with update details
                    if (!empty($updatedMedicines)) {
                        $updateDetails = [];
                        foreach ($updatedMedicines as $updated) {
                            $details = $updated['name'];
                            if ($updated['quantity_added'] > 0) {
                                $details .= ' (+' . $updated['quantity_added'] . ' stock)';
                            }
                            if ($updated['pricing_updated']) {
                                $details .= ' (pricing updated)';
                            }
                            $updateDetails[] = $details;
                        }
                        $successMessage .= ' Updated ' . count($updatedMedicines) . ' medicine(s): ' . implode(', ', $updateDetails);
                    }
            } else {
                log_message('debug', 'Medicines data is not a valid array or is empty');
            }
        } else {
            log_message('debug', 'No selected medicines data received');
        }

        return redirect()->to('/admin/pharmacy')->with('success', $successMessage);
    }

    public function edit($id)
    {
        $pharmacyItem = $this->pharmacyModel->find($id);
        
        if (!$pharmacyItem) {
            return redirect()->to('/admin/pharmacy')->with('error', 'Pharmacy item not found.');
        }

        $db = \Config\Database::connect();
        
        // Get unique suppliers from existing pharmacy records
        $existingSuppliers = $db->table('pharmacy')
            ->select('supplier_name')
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '')
            ->distinct()
            ->orderBy('supplier_name', 'ASC')
            ->get()
            ->getResultArray();
        
        $suppliers = array_column($existingSuppliers, 'supplier_name');
        
        // Add common suppliers if not already in the list
        $commonSuppliers = [
            'MedSupply Co.',
            'PharmaDist Inc.',
            'Healthcare Supplies Ltd.',
            'Medical Equipment Corp.',
            'Pharmaceutical Distributors',
            'MedTech Solutions',
            'HealthCare Partners',
            'PharmaLink International',
        ];
        
        foreach ($commonSuppliers as $supplier) {
            if (!in_array($supplier, $suppliers)) {
                $suppliers[] = $supplier;
            }
        }
        
        sort($suppliers);
        
        // Get unique batch numbers from existing pharmacy records
        $existingBatchNumbers = $db->table('pharmacy')
            ->select('batch_number')
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->distinct()
            ->orderBy('batch_number', 'ASC')
            ->get()
            ->getResultArray();
        
        $batchNumbers = array_column($existingBatchNumbers, 'batch_number');
        
        // Generate common batch number patterns
        $currentYear = date('Y');
        $commonBatchNumbers = [];
        
        // Generate BATCH-001 to BATCH-050
        for ($i = 1; $i <= 50; $i++) {
            $batchNum = 'BATCH-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            if (!in_array($batchNum, $batchNumbers)) {
                $commonBatchNumbers[] = $batchNum;
            }
        }
        
        // Generate BATCH-2025-001 to BATCH-2025-050
        for ($i = 1; $i <= 50; $i++) {
            $batchNum = 'BATCH-' . $currentYear . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            if (!in_array($batchNum, $batchNumbers)) {
                $commonBatchNumbers[] = $batchNum;
            }
        }
        
        // Merge and sort
        $batchNumbers = array_merge($batchNumbers, $commonBatchNumbers);
        sort($batchNumbers);
        
        // Get unique generic names from existing pharmacy records
        $existingGenericNames = $db->table('pharmacy')
            ->select('generic_name')
            ->where('generic_name IS NOT NULL')
            ->where('generic_name !=', '')
            ->distinct()
            ->orderBy('generic_name', 'ASC')
            ->get()
            ->getResultArray();
        
        $genericNames = array_column($existingGenericNames, 'generic_name');
        
        // Add common generic names if not already in the list
        $commonGenericNames = [
            'Acetaminophen',
            'Paracetamol',
            'Ibuprofen',
            'Amoxicillin',
            'Cephalexin',
            'Azithromycin',
            'Ciprofloxacin',
            'Cetirizine',
            'Loratadine',
            'Omeprazole',
            'Metformin',
            'Amlodipine',
            'Losartan',
            'Atorvastatin',
            'Ascorbic Acid',
            'Calcium Carbonate',
            'Ferrous Sulfate',
            'Salbutamol',
            'Dextromethorphan',
            'Epinephrine',
        ];
        
        foreach ($commonGenericNames as $genericName) {
            if (!in_array($genericName, $genericNames)) {
                $genericNames[] = $genericName;
            }
        }
        
        sort($genericNames);

        $data = [
            'title' => 'Edit Pharmacy Item',
            'pharmacyItem' => $pharmacyItem,
            'validation' => \Config\Services::validation(),
            'suppliers' => $suppliers,
            'batchNumbers' => $batchNumbers,
            'genericNames' => $genericNames,
        ];

        return view('admin/pharmacy/edit', $data);
    }

    public function update($id)
    {
        $pharmacyItem = $this->pharmacyModel->find($id);
        
        if (!$pharmacyItem) {
            return redirect()->to('/admin/pharmacy')->with('error', 'Pharmacy item not found.');
        }

        $rules = [
            'item_name' => 'required|max_length[255]',
            'generic_name' => 'permit_empty|max_length[255]',
            'category' => 'required|max_length[100]',
            'description' => 'permit_empty',
            'strength' => 'permit_empty|max_length[100]',
            'dosage_form' => 'permit_empty|max_length[100]',
            'quantity' => 'required|integer|greater_than_equal_to[0]',
            'reorder_level' => 'required|integer|greater_than_equal_to[0]',
            'batch_number' => 'required|max_length[100]',
            'expiration_date' => 'required|valid_date',
            'price' => 'required|decimal|greater_than_equal_to[0]',
            'unit_price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'selling_price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'markup_percent' => 'permit_empty|decimal',
            'supplier_name' => 'required|max_length[255]',
            'supplier_contact' => 'permit_empty|max_length[100]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'item_name' => $this->request->getPost('item_name'),
            'generic_name' => $this->request->getPost('generic_name') ?: null,
            'category' => $this->request->getPost('category'),
            'description' => $this->request->getPost('description') ?: null,
            'strength' => $this->request->getPost('strength') ?: null,
            'dosage_form' => $this->request->getPost('dosage_form') ?: null,
            'quantity' => $this->request->getPost('quantity'),
            'reorder_level' => $this->request->getPost('reorder_level') ?: 10,
            'batch_number' => $this->request->getPost('batch_number'),
            'expiration_date' => $this->request->getPost('expiration_date'),
            'price' => $this->request->getPost('price'),
            'unit_price' => $this->request->getPost('unit_price') ?: null,
            'selling_price' => $this->request->getPost('selling_price') ?: null,
            'markup_percent' => $this->request->getPost('markup_percent') ?: null,
            'supplier_name' => $this->request->getPost('supplier_name') ?: null,
            'supplier_contact' => $this->request->getPost('supplier_contact') ?: null,
            'status' => $this->request->getPost('status') ?: 'active',
        ];

        $this->pharmacyModel->update($id, $data);

        return redirect()->to('/admin/pharmacy')->with('success', 'Pharmacy item updated successfully.');
    }

    public function delete($id)
    {
        $pharmacyItem = $this->pharmacyModel->find($id);
        
        if (!$pharmacyItem) {
            return redirect()->to('/admin/pharmacy')->with('error', 'Pharmacy item not found.');
        }

        $this->pharmacyModel->delete($id);

        return redirect()->to('/admin/pharmacy')->with('success', 'Pharmacy item deleted successfully.');
    }
}

