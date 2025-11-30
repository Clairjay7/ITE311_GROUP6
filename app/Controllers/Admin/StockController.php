<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StockModel;

class StockController extends BaseController
{
    protected $stockModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->stockModel = new StockModel();
    }

    public function index()
    {
        $stocks = $this->stockModel->orderBy('created_at', 'DESC')->findAll();
        
        $data = [
            'title' => 'Stock Monitoring',
            'stocks' => $stocks,
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

