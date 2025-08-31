<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Doctor Dashboard',
            'username' => session()->get('username'),
            'role' => 'Doctor',
            'rolePath' => 'doctor',
            'user' => [
                'full_name' => session()->get('first_name') . ' ' . session()->get('last_name'),
                'role' => 'Doctor'
            ]
        ];
        return view('doctor/doctor_dashboard', $data);
    }
}
