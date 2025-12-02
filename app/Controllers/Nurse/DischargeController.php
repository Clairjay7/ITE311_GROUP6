<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use App\Models\DischargeOrderModel;

class DischargeController extends BaseController
{
    protected $admissionModel;
    protected $dischargeOrderModel;

    public function __construct()
    {
        $this->admissionModel = new AdmissionModel();
        $this->dischargeOrderModel = new DischargeOrderModel();
    }

    /**
     * List patients pending discharge
     */
    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse.');
        }

        $db = \Config\Database::connect();

        // Get admissions with discharge orders pending
        $pendingDischarges = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.gender,
                     r.room_number, r.ward,
                     do.id as discharge_order_id, do.final_diagnosis, do.discharge_date as planned_discharge_date,
                     u.username as doctor_name')
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

        $data = [
            'title' => 'Pending Discharges',
            'pendingDischarges' => $pendingDischarges,
        ];

        return view('nurse/discharge/index', $data);
    }

    /**
     * View discharge order and prepare patient
     */
    public function view($admissionId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse.');
        }

        $db = \Config\Database::connect();

        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.gender, ap.birthdate,
                     r.room_number, r.ward, r.room_type,
                     do.*, u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('discharge_orders do', 'do.admission_id = a.id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->where('a.id', $admissionId)
            ->where('a.discharge_status', 'discharge_pending')
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->to('/nurse/discharge')->with('error', 'Discharge order not found.');
        }

        // Check if billing is finalized (all charges paid)
        $allChargesPaid = $db->table('charges')
            ->where('patient_id', $admission['patient_id'])
            ->where('status !=', 'paid')
            ->where('status !=', 'cancelled')
            ->where('deleted_at', null)
            ->countAllResults() == 0;

        $data = [
            'title' => 'Prepare Patient for Discharge',
            'admission' => $admission,
            'allChargesPaid' => $allChargesPaid,
        ];

        return view('nurse/discharge/view', $data);
    }

    /**
     * Print discharge instructions
     */
    public function printInstructions($admissionId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse.');
        }

        $db = \Config\Database::connect();

        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.gender, ap.birthdate,
                     r.room_number, r.ward,
                     do.*, u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('discharge_orders do', 'do.admission_id = a.id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->where('a.id', $admissionId)
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->back()->with('error', 'Discharge order not found.');
        }

        $data = [
            'title' => 'Discharge Instructions',
            'admission' => $admission,
        ];

        return view('nurse/discharge/print_instructions', $data);
    }
}

