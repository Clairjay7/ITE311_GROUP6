<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Doctor extends Controller
{
    protected function ensureDoctor()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'doctor') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureDoctor();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'title' => 'Doctor - ' . ucwords(str_replace('_', ' ', basename($view))),
        ];
        return view($view, $base + $data);
    }

    public function patients() { return $this->render('doctor/patients'); }
    public function appointments() { return $this->render('doctor/appointments'); }
    public function calendar() { return $this->render('doctor/calendar'); }
    public function notifications() { return $this->render('doctor/notifications'); }
    public function emr() { return $this->render('doctor/emr'); }
    public function prescriptions() { return $this->render('doctor/prescriptions'); }
    public function labRequests() { return $this->render('doctor/lab_requests'); }
    public function labResults() { return $this->render('doctor/lab_results'); }
    public function messaging() { return $this->render('doctor/messaging'); }
    public function reports() { return $this->render('doctor/reports'); }
    public function profile() { return $this->render('doctor/profile'); }
    public function settings() { return $this->render('doctor/settings'); }
}


