<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SystemModel;

class SystemController extends BaseController
{
    protected $systemModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->systemModel = new SystemModel();
    }

    public function index()
    {
        $settings = $this->systemModel->orderBy('created_at', 'DESC')->findAll();
        
        $data = [
            'title' => 'System Controls',
            'settings' => $settings,
        ];

        return view('admin/system/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add System Setting',
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/system/create', $data);
    }

    public function store()
    {
        $rules = [
            'setting_name' => 'required|max_length[255]|is_unique[system_controls.setting_name]',
            'setting_value' => 'required|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'setting_name' => $this->request->getPost('setting_name'),
            'setting_value' => $this->request->getPost('setting_value'),
        ];

        $this->systemModel->insert($data);

        return redirect()->to('/admin/system')->with('success', 'System setting created successfully.');
    }

    public function edit($id)
    {
        $setting = $this->systemModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/system')->with('error', 'System setting not found.');
        }

        $data = [
            'title' => 'Edit System Setting',
            'setting' => $setting,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/system/edit', $data);
    }

    public function update($id)
    {
        $setting = $this->systemModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/system')->with('error', 'System setting not found.');
        }

        $rules = [
            'setting_name' => "required|max_length[255]|is_unique[system_controls.setting_name,id,{$id}]",
            'setting_value' => 'required|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'setting_name' => $this->request->getPost('setting_name'),
            'setting_value' => $this->request->getPost('setting_value'),
        ];

        $this->systemModel->update($id, $data);

        return redirect()->to('/admin/system')->with('success', 'System setting updated successfully.');
    }

    public function delete($id)
    {
        $setting = $this->systemModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/system')->with('error', 'System setting not found.');
        }

        $this->systemModel->delete($id);

        return redirect()->to('/admin/system')->with('success', 'System setting deleted successfully.');
    }
}

