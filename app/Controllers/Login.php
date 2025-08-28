<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index()
    {
        return view('template');
    }

    public function login()
    {
        // username => [password, role]
        $accounts = [
            'doctor1'       => ['docpass', 'doctor'],
            'nurse1'        => ['nursepass', 'nurse'],
            'reception1'    => ['receppass', 'receptionist'],
            'lab1'          => ['labpass', 'laboratory_staff'],
            'pharma1'       => ['pharmapass', 'pharmacist'],
            'account1'      => ['accountpass', 'accountant'],
            'it1'           => ['itpass', 'it_staff'],
            'admin1'        => ['adminpass', 'super_admin']
        ];

        $role = $this->request->getPost('role');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (
            isset($accounts[$username]) &&
            $accounts[$username][0] === $password &&
            $accounts[$username][1] === $role
        ) {
            // Redirect to the correct dashboard
            switch ($role) {
                case 'doctor':
                    return redirect()->to('doctor/dashboard');
                case 'nurse':
                    return redirect()->to('nurse/dashboard');
                case 'receptionist':
                    return redirect()->to('receptionist/dashboard');
                case 'laboratory_staff':
                    return redirect()->to('laboratory/dashboard');
                case 'pharmacist':
                    return redirect()->to('pharmacist/dashboard');
                case 'accountant':
                    return redirect()->to('accountant/dashboard');
                case 'it_staff':
                    return redirect()->to('it/dashboard');
                case 'super_admin':
                    return redirect()->to('superadmin/dashboard');
            }
        }

        // Login failed, redirect back to login
        return redirect()->to('login');
    }

    public function doctorDashboard()         { return view('doctor_dashboard'); }
    public function nurseDashboard()          { return view('nurse_dashboard'); }
    public function receptionistDashboard()   { return view('receptionist_dashboard'); }
    public function laboratoryDashboard()     { return view('laboratory_dashboard'); }
    public function pharmacistDashboard()     { return view('pharmacist_dashboard'); }
    public function accountantDashboard()     { return view('accountant_dashboard'); }
    public function itDashboard()             { return view('it_dashboard'); }
    public function superAdminDashboard()     { return view('superadmin_dashboard'); }

    public function logout()
    {
        return redirect()->to('login');
    }
}