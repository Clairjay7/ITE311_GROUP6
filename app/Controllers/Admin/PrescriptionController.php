<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrescriptionModel;

class PrescriptionController extends BaseController
{
    /**
     * @var PrescriptionModel
     */
    protected $prescriptionModel;

    /**
     * @var array<string>
     */
    protected $allowedStatuses = ['pending', 'dispensed', 'completed', 'cancelled'];

    public function __construct()
    {
        $this->prescriptionModel = new PrescriptionModel();
    }

    public function index()
    {
        $prescriptions = $this->prescriptionModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/prescriptions/index', [
            'title' => 'Prescription Management',
            'prescriptions' => $prescriptions,
            'statuses' => $this->allowedStatuses,
        ]);
    }

    public function updateStatus($id = null)
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('admin/prescriptions');
        }

        $status = $this->request->getPost('status');

        if (!in_array($status, $this->allowedStatuses, true)) {
            return redirect()->to('admin/prescriptions')
                ->with('error', 'Invalid status selected.');
        }

        if (!$this->prescriptionModel->find($id)) {
            return redirect()->to('admin/prescriptions')
                ->with('error', 'Prescription not found.');
        }

        $updated = $this->prescriptionModel->update($id, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($updated) {
            return redirect()->to('admin/prescriptions')
                ->with('message', 'Prescription status updated successfully.');
        }

        return redirect()->to('admin/prescriptions')
            ->with('error', 'Failed to update prescription status.');
    }
}
