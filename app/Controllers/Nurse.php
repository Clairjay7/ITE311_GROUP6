<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Nurse extends Controller
{
    protected function ensureNurse()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'nurse') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureNurse();
        if ($guard !== null) {
            return $guard;
        }
        $base = [ 'title' => 'Nurse - ' . ucwords(str_replace('_', ' ', basename($view))) ];
        return view($view, $base + $data);
    }

    public function dashboard()
    {
        $guard = $this->ensureNurse();
        if ($guard !== null) { return $guard; }

        $db = \Config\Database::connect();
        $nurseUserId = (int) session()->get('user_id');

        $assignedPatients = $db->table('nurse_assignments na')
            ->select('p.id, p.first_name, p.last_name')
            ->join('patients p', 'p.id = na.patient_id', 'left')
            ->where('na.nurse_user_id', $nurseUserId)
            ->get()->getResultArray();

        $assignedPatientIds = array_column($assignedPatients, 'id');
        if (empty($assignedPatientIds)) { $assignedPatientIds = [0]; }

        $dueMedications = (int) $db->table('medication_schedules')
            ->whereIn('patient_id', $assignedPatientIds)
            ->where('status', 'scheduled')
            ->where('scheduled_at >=', date('Y-m-d H:i:s'))
            ->where('scheduled_at <=', date('Y-m-d H:i:s', strtotime('+2 hours')))
            ->countAllResults();

        $openTasks = (int) $db->table('nurse_tasks')
            ->where('nurse_user_id', $nurseUserId)
            ->whereNotIn('status', ['completed','canceled'])
            ->countAllResults();

        $data = [
            'assignedPatientsCount' => count($assignedPatients),
            'dueMedicationsCount' => $dueMedications,
            'openTasksCount' => $openTasks,
            'shiftWindow' => '07:00 - 15:00',
            'assignedPatients' => $assignedPatients,
        ];

        return $this->render('nurse/dashboard', $data);
    }

    public function patients() { return $this->render('nurse/patients'); }
    public function medications() { return $this->render('nurse/medications'); }
    public function vitals() { return $this->render('nurse/vitals'); }
    public function tasks() { return $this->render('nurse/tasks'); }
    public function orders() { return $this->render('nurse/orders'); }
    public function notifications() { return $this->render('nurse/notifications'); }
    public function roster() { return $this->render('nurse/roster'); }
}


