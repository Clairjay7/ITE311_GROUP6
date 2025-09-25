<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SuperAdmin extends Controller
{
    protected function ensureSuperAdmin()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'super_admin') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureSuperAdmin();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'title' => 'Super Admin - ' . ucwords(str_replace('_', ' ', basename($view))),
            'username' => session()->get('username'),
        ];
        return view($view, $base + $data);
    }

    public function users() { return $this->render('SuperAdmin/users'); }
    public function roles() { return $this->render('SuperAdmin/roles'); }
    public function appointments() { return $this->render('SuperAdmin/appointments'); }
    public function calendars() { return $this->render('SuperAdmin/calendars'); }
    public function patients() { return $this->render('SuperAdmin/patients'); }
    public function admissions() { return $this->render('SuperAdmin/admissions'); }
    public function doctors() { return $this->render('SuperAdmin/doctors'); }
    public function staff() { return $this->render('SuperAdmin/staff'); }
    public function billing() { return $this->render('SuperAdmin/billing'); }
    public function financeReports() { return $this->render('SuperAdmin/finance_reports'); }
    public function laboratory() { return $this->render('SuperAdmin/laboratory'); }
    public function pharmacy() { return $this->render('SuperAdmin/pharmacy'); }
    public function rooms() { return $this->render('SuperAdmin/rooms'); }
    public function occupancy() { return $this->render('SuperAdmin/occupancy'); }
    public function reports() { return $this->render('SuperAdmin/reports'); }
    public function analytics() { return $this->render('SuperAdmin/analytics'); }
    public function settings() { return $this->render('SuperAdmin/settings'); }
    public function security() { return $this->render('SuperAdmin/security'); }
}


