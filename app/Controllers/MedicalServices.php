<?php

namespace App\Controllers;

use App\Models\MedicalServiceModel;
use CodeIgniter\HTTP\ResponseInterface;

class MedicalServices extends BaseController
{
    protected $medicalServiceModel;
    protected $session;

    public function __construct()
    {
        $this->medicalServiceModel = new MedicalServiceModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Check if user is authenticated and has proper permissions
     */
    private function checkAuth()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to access this page.');
        }

        $userRole = $this->session->get('role');
        $allowedRoles = ['superadmin', 'doctor', 'nurse', 'receptionist', 'accountant'];
        
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->back()->with('error', 'You do not have permission to access medical services.');
        }

        return null;
    }

    /**
     * Display list of medical services
     */
    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $data = [
            'title' => 'Medical Services',
            'services' => $this->medicalServiceModel->getAllServices(),
            'stats' => $this->medicalServiceModel->getServiceStats(),
            'categories' => ['consultation', 'laboratory', 'imaging', 'surgery', 'therapy', 'emergency', 'other']
        ];

        return view('medical_services/index', $data);
    }

    /**
     * Show create service form
     */
    public function create()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Only superadmin and authorized staff can create services
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['superadmin', 'doctor'])) {
            return redirect()->to('/medical-services')->with('error', 'You do not have permission to create medical services.');
        }

        $data = [
            'title' => 'Add Medical Service',
            'categories' => ['consultation', 'laboratory', 'imaging', 'surgery', 'therapy', 'emergency', 'other']
        ];

        return view('medical_services/create', $data);
    }

    /**
     * Store new medical service
     */
    public function store()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Only superadmin and authorized staff can create services
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['superadmin', 'doctor'])) {
            return redirect()->to('/medical-services')->with('error', 'You do not have permission to create medical services.');
        }

        $rules = [
            'service_name' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'category' => 'required|in_list[consultation,laboratory,imaging,surgery,therapy,emergency,other]',
            'price' => 'required|decimal|greater_than_equal_to[0]',
            'status' => 'required|in_list[active,inactive,discontinued]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'service_name' => $this->request->getPost('service_name'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'price' => $this->request->getPost('price'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->medicalServiceModel->insert($data)) {
            return redirect()->to('/medical-services')->with('success', 'Medical service created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create medical service.');
        }
    }

    /**
     * Show edit service form
     */
    public function edit($id)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Only superadmin and authorized staff can edit services
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['superadmin', 'doctor'])) {
            return redirect()->to('/medical-services')->with('error', 'You do not have permission to edit medical services.');
        }

        $service = $this->medicalServiceModel->find($id);
        if (!$service) {
            return redirect()->to('/medical-services')->with('error', 'Medical service not found.');
        }

        $data = [
            'title' => 'Edit Medical Service',
            'service' => $service,
            'categories' => ['consultation', 'laboratory', 'imaging', 'surgery', 'therapy', 'emergency', 'other']
        ];

        return view('medical_services/edit', $data);
    }

    /**
     * Update medical service
     */
    public function update($id)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Only superadmin and authorized staff can update services
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['superadmin', 'doctor'])) {
            return redirect()->to('/medical-services')->with('error', 'You do not have permission to update medical services.');
        }

        $service = $this->medicalServiceModel->find($id);
        if (!$service) {
            return redirect()->to('/medical-services')->with('error', 'Medical service not found.');
        }

        $rules = [
            'service_name' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'category' => 'required|in_list[consultation,laboratory,imaging,surgery,therapy,emergency,other]',
            'price' => 'required|decimal|greater_than_equal_to[0]',
            'status' => 'required|in_list[active,inactive,discontinued]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'service_name' => $this->request->getPost('service_name'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'price' => $this->request->getPost('price'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->medicalServiceModel->update($id, $data)) {
            return redirect()->to('/medical-services')->with('success', 'Medical service updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update medical service.');
        }
    }

    /**
     * Delete medical service
     */
    public function delete($id)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Only superadmin can delete services
        $userRole = $this->session->get('role');
        if ($userRole !== 'superadmin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to delete medical services.'
            ]);
        }

        $service = $this->medicalServiceModel->find($id);
        if (!$service) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Medical service not found.'
            ]);
        }

        if ($this->medicalServiceModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Medical service deleted successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete medical service.'
            ]);
        }
    }

    /**
     * API endpoint to get services for AJAX requests
     */
    public function apiServices()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentication required'
            ]);
        }

        $category = $this->request->getGet('category');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        if ($search) {
            $services = $this->medicalServiceModel->searchServices($search);
        } else {
            $services = $this->medicalServiceModel->getAllServices($status, $category);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * API endpoint to get service details
     */
    public function apiServiceDetails($id)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentication required'
            ]);
        }

        $service = $this->medicalServiceModel->find($id);
        if (!$service) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Service not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * API endpoint for billing integration
     */
    public function apiServicesForBilling()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentication required'
            ]);
        }

        $services = $this->medicalServiceModel->getServicesForBilling();

        return $this->response->setJSON([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Update service status via AJAX
     */
    public function updateStatus($id)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentication required'
            ]);
        }

        // Only superadmin and doctors can update status
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['superadmin', 'doctor'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to update service status.'
            ]);
        }

        $status = $this->request->getPost('status');
        
        if ($this->medicalServiceModel->updateServiceStatus($id, $status)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Service status updated successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update service status.'
            ]);
        }
    }
}
