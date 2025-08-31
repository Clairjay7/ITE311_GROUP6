<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Nurse Dashboard',
            'username' => session()->get('username'),
            'role' => 'Nurse',
            'rolePath' => 'nurse',
            'user' => [
                'full_name' => session()->get('first_name') . ' ' . session()->get('last_name'),
                'role' => 'Nurse'
            ]
        ];
        return view('Nurse/dashboard', $data);
    }
}
