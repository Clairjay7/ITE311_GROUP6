<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function dashboard()
    {
        $db = \Config\Database::connect();

        $totalDoctors = (int) $db->table('doctors')->countAllResults();
        $totalPatients = (int) $db->table('patients')->countAllResults();
        $todaysAppointments = (int) $db->table('appointments')
            ->where('DATE(appointment_date) = CURDATE()', null, false)
            ->countAllResults();
        $pendingBills = (int) $db->table('billing')->where('status', 'pending')->countAllResults();

        $recentActivity = $db->table('appointments a')
            ->select('a.id, a.appointment_date, a.status, p.first_name as patient_first_name, p.last_name as patient_last_name, d.full_name as doctor_name')
            ->join('patients p', 'p.id = a.patient_id', 'left')
            ->join('doctors d', 'd.id = a.doctor_id', 'left')
            ->orderBy('a.appointment_date', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Admin Dashboard',
            'totalDoctors' => $totalDoctors,
            'totalPatients' => $totalPatients,
            'todaysAppointments' => $todaysAppointments,
            'pendingBills' => $pendingBills,
            'recentActivity' => $recentActivity,
        ];

        return view('Admin/dashboard', $data);
    }
}


