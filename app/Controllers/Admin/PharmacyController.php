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
        
        $data = [
            'title' => 'Pharmacy Desk',
            'pharmacyItems' => $pharmacyItems,
        ];

        return view('admin/pharmacy/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Pharmacy Item',
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/pharmacy/create', $data);
    }

    public function store()
    {
        $rules = [
            'item_name' => 'required|max_length[255]',
            'description' => 'permit_empty|max_length[500]',
            'quantity' => 'required|integer|greater_than_equal_to[0]',
            'price' => 'required|decimal|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'item_name' => $this->request->getPost('item_name'),
            'description' => $this->request->getPost('description'),
            'quantity' => $this->request->getPost('quantity'),
            'price' => $this->request->getPost('price'),
        ];

        $this->pharmacyModel->insert($data);

        return redirect()->to('/admin/pharmacy')->with('success', 'Pharmacy item created successfully.');
    }

    public function edit($id)
    {
        $pharmacyItem = $this->pharmacyModel->find($id);
        
        if (!$pharmacyItem) {
            return redirect()->to('/admin/pharmacy')->with('error', 'Pharmacy item not found.');
        }

        $data = [
            'title' => 'Edit Pharmacy Item',
            'pharmacyItem' => $pharmacyItem,
            'validation' => \Config\Services::validation(),
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
            'description' => 'permit_empty|max_length[500]',
            'quantity' => 'required|integer|greater_than_equal_to[0]',
            'price' => 'required|decimal|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'item_name' => $this->request->getPost('item_name'),
            'description' => $this->request->getPost('description'),
            'quantity' => $this->request->getPost('quantity'),
            'price' => $this->request->getPost('price'),
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

