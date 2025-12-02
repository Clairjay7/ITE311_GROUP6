<?php

namespace App\Controllers\Accountant;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use App\Models\ChargeModel;
use App\Models\DischargeOrderModel;

class DischargeController extends BaseController
{
    protected $admissionModel;
    protected $chargeModel;
    protected $dischargeOrderModel;

    public function __construct()
    {
        $this->admissionModel = new AdmissionModel();
        $this->chargeModel = new ChargeModel();
        $this->dischargeOrderModel = new DischargeOrderModel();
    }

    /**
     * List all discharge pending patients
     */
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Accountant can access this page.');
        }

        $db = \Config\Database::connect();

        // Get all discharge pending admissions
        $dischargePending = [];
        if ($db->tableExists('admissions') && $db->tableExists('discharge_orders')) {
            $dischargePending = $db->table('admissions a')
                ->select('a.*, ap.firstname, ap.lastname, ap.contact,
                         r.room_number, r.ward,
                         do.id as discharge_order_id, do.discharge_date as planned_discharge_date,
                         u.username as doctor_name,
                         (SELECT SUM(total_amount) FROM charges WHERE patient_id = a.patient_id AND status = "pending" AND deleted_at IS NULL) as total_charges')
                ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
                ->join('rooms r', 'r.id = a.room_id', 'left')
                ->join('discharge_orders do', 'do.admission_id = a.id', 'left')
                ->join('users u', 'u.id = do.doctor_id', 'left')
                ->where('a.discharge_status', 'discharge_pending')
                ->where('a.status', 'admitted')
                ->where('do.status', 'pending')
                ->where('a.deleted_at', null)
                ->orderBy('do.discharge_date', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Discharge Billing',
            'dischargePending' => $dischargePending,
        ];

        return view('accountant/discharge/index', $data);
    }

    /**
     * Finalize billing for discharge
     */
    public function finalize($admissionId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Accountant can finalize billing.');
        }

        $db = \Config\Database::connect();

        // Get admission with discharge order
        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname,
                     do.id as discharge_order_id')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('discharge_orders do', 'do.admission_id = a.id', 'left')
            ->where('a.id', $admissionId)
            ->where('a.discharge_status', 'discharge_pending')
            ->where('a.status', 'admitted')
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->to('/accounting/dashboard')->with('error', 'Admission not found or not pending discharge.');
        }

        // Get all pending charges for this patient
        $pendingCharges = $db->table('charges')
            ->where('patient_id', $admission['patient_id'])
            ->where('status', 'pending')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Finalize Billing for Discharge',
            'admission' => $admission,
            'pendingCharges' => $pendingCharges,
        ];

        return view('accountant/discharge/finalize', $data);
    }

    /**
     * Process payment and complete discharge
     */
    public function processDischarge($admissionId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['finance', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        // Get admission
        $admission = $this->admissionModel
            ->where('id', $admissionId)
            ->where('discharge_status', 'discharge_pending')
            ->where('status', 'admitted')
            ->first();

        if (!$admission) {
            return redirect()->back()->with('error', 'Admission not found or not pending discharge.');
        }

        // Get all charges for this patient
        $charges = $db->table('charges')
            ->where('patient_id', $admission['patient_id'])
            ->where('status !=', 'cancelled')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $db->transStart();

        try {
            // Mark all pending charges as paid
            foreach ($charges as $charge) {
                if ($charge['status'] === 'pending') {
                    $this->chargeModel->update($charge['id'], [
                        'status' => 'paid',
                        'processed_by' => $userId,
                        'paid_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // Update admission status to discharged
            $this->admissionModel->update($admissionId, [
                'discharge_status' => 'discharged',
                'status' => 'discharged',
                'discharge_date' => date('Y-m-d H:i:s'),
            ]);

            // Update discharge order status
            $dischargeOrder = $this->dischargeOrderModel
                ->where('admission_id', $admissionId)
                ->first();
            
            if ($dischargeOrder) {
                $this->dischargeOrderModel->update($dischargeOrder['id'], [
                    'status' => 'completed',
                ]);
            }

            // Free up room and bed
            if ($admission['room_id']) {
                $db->table('rooms')
                    ->where('id', $admission['room_id'])
                    ->update([
                        'status' => 'Available',
                        'current_patient_id' => null,
                    ]);
            }

            // Free up bed if exists
            if (!empty($admission['bed_number'])) {
                $bed = $db->table('beds')
                    ->where('room_id', $admission['room_id'])
                    ->where('bed_number', $admission['bed_number'])
                    ->get()
                    ->getRowArray();
                
                if ($bed) {
                    $db->table('beds')
                        ->where('id', $bed['id'])
                        ->update([
                            'status' => 'available',
                            'current_patient_id' => null,
                        ]);
                }
            }

            // Update patient status in patients table if exists
            if ($db->tableExists('patients')) {
                $adminPatient = $db->table('admin_patients')
                    ->where('id', $admission['patient_id'])
                    ->get()
                    ->getRowArray();
                
                if ($adminPatient) {
                    $hmsPatient = $db->table('patients')
                        ->where('first_name', $adminPatient['firstname'] ?? '')
                        ->where('last_name', $adminPatient['lastname'] ?? '')
                        ->get()
                        ->getRowArray();
                    
                    if ($hmsPatient) {
                        $db->table('patients')
                            ->where('patient_id', $hmsPatient['patient_id'])
                            ->update(['type' => 'Out-Patient']);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/accounting/dashboard')->with('success', 'Patient discharged successfully. All charges have been paid and room/bed are now available.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to process discharge: ' . $e->getMessage());
        }
    }
}

