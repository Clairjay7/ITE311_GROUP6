<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StockModel;
use App\Models\PharmacyModel;

class StockController extends BaseController
{
    protected $stockModel;
    protected $pharmacyModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->stockModel = new StockModel();
        $this->pharmacyModel = new PharmacyModel();
    }

    public function index()
    {
        // Get medicines from pharmacy table (same as pharmacy stock monitoring)
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
        
        // Get selected category from query parameter
        $selectedCategory = $this->request->getGet('category') ?? '';
        
        // Get stock monitoring data - base sa seeder format
        // Dapat may complete information: batch_number, expiration_date, supplier_name
        // Para makita ang newly added medicines na may complete information
        $stockBuilder = $this->pharmacyModel->builder();
        $stockBuilder->where('status', 'active')
            ->whereIn('category', $validCategories)
            ->where('batch_number IS NOT NULL')
            ->where('batch_number !=', '')
            ->where('expiration_date IS NOT NULL')
            ->where('supplier_name IS NOT NULL')
            ->where('supplier_name !=', '');
        
        // Filter by category if selected
        if (!empty($selectedCategory) && in_array($selectedCategory, $validCategories)) {
            $stockBuilder->where('category', $selectedCategory);
        }
        
        $stockBuilder->orderBy('quantity', 'ASC');
        
        $medicines = $stockBuilder->get()->getResultArray();

        // Categorize by stock level
        $criticalStock = [];
        $lowStock = [];
        $normalStock = [];

        foreach ($medicines as $medicine) {
            $quantity = $medicine['quantity'] ?? 0;
            $reorderLevel = $medicine['reorder_level'] ?? 10;
            
            if ($quantity == 0) {
                $criticalStock[] = $medicine;
            } elseif ($quantity <= $reorderLevel) {
                $criticalStock[] = $medicine;
            } elseif ($quantity < ($reorderLevel * 2)) {
                $lowStock[] = $medicine;
            } else {
                $normalStock[] = $medicine;
            }
        }
        
        // Store all medicines for "All Medicines" tab
        $allMedicines = $medicines;
        
        // Get current tab from query parameter
        $currentTab = $this->request->getGet('tab') ?? 'all';
        $perPage = 10;
        
        // Get current page from query parameter
        $currentPage = (int)($this->request->getGet('page') ?? 1);
        
        // Determine which array to paginate
        $itemsToPaginate = [];
        $totalItems = 0;
        
        switch ($currentTab) {
            case 'critical':
                $itemsToPaginate = $criticalStock;
                $totalItems = count($criticalStock);
                break;
            case 'low':
                $itemsToPaginate = $lowStock;
                $totalItems = count($lowStock);
                break;
            case 'all':
            default:
                $itemsToPaginate = $allMedicines;
                $totalItems = count($allMedicines);
                break;
        }
        
        // Calculate pagination
        $totalPages = ceil($totalItems / $perPage);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = array_slice($itemsToPaginate, $offset, $perPage);
        
        // Prepare pagination data
        $pager = [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'perPage' => $perPage,
            'hasNext' => $currentPage < $totalPages,
            'hasPrev' => $currentPage > 1,
        ];
        
        $data = [
            'title' => 'Stock Monitoring',
            'criticalStock' => $currentTab === 'critical' ? $paginatedItems : [],
            'lowStock' => $currentTab === 'low' ? $paginatedItems : [],
            'allMedicines' => ($currentTab === 'all' || $currentTab === '') ? $paginatedItems : [],
            'currentTab' => $currentTab,
            'pager' => $pager,
            'totalCritical' => count($criticalStock),
            'totalLow' => count($lowStock),
            'totalAll' => count($allMedicines),
            'validCategories' => $validCategories,
            'selectedCategory' => $selectedCategory,
        ];

        return view('admin/stock/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Stock Item',
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/stock/create', $data);
    }

    public function store()
    {
        $rules = [
            'item_name' => 'required|max_length[255]',
            'category' => 'required|max_length[100]',
            'quantity' => 'required|integer|greater_than_equal_to[0]',
            'threshold' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'item_name' => $this->request->getPost('item_name'),
            'category' => $this->request->getPost('category'),
            'quantity' => $this->request->getPost('quantity'),
            'threshold' => $this->request->getPost('threshold'),
        ];

        $this->stockModel->insert($data);

        return redirect()->to('/admin/stock')->with('success', 'Stock item created successfully.');
    }

    public function edit($id)
    {
        $stock = $this->stockModel->find($id);
        
        if (!$stock) {
            return redirect()->to('/admin/stock')->with('error', 'Stock item not found.');
        }

        $data = [
            'title' => 'Edit Stock Item',
            'stock' => $stock,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/stock/edit', $data);
    }

    public function update($id)
    {
        $stock = $this->stockModel->find($id);
        
        if (!$stock) {
            return redirect()->to('/admin/stock')->with('error', 'Stock item not found.');
        }

        $rules = [
            'item_name' => 'required|max_length[255]',
            'category' => 'required|max_length[100]',
            'quantity' => 'required|integer|greater_than_equal_to[0]',
            'threshold' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'item_name' => $this->request->getPost('item_name'),
            'category' => $this->request->getPost('category'),
            'quantity' => $this->request->getPost('quantity'),
            'threshold' => $this->request->getPost('threshold'),
        ];

        $this->stockModel->update($id, $data);

        return redirect()->to('/admin/stock')->with('success', 'Stock item updated successfully.');
    }

    public function delete($id)
    {
        $stock = $this->stockModel->find($id);
        
        if (!$stock) {
            return redirect()->to('/admin/stock')->with('error', 'Stock item not found.');
        }

        $this->stockModel->delete($id);

        return redirect()->to('/admin/stock')->with('success', 'Stock item deleted successfully.');
    }
}

