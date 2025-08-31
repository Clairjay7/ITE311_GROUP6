<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Receptionist Dashboard',
            'username' => session()->get('username'),
            'role' => 'Receptionist'
        ];
        return view('Receptionist/dashboard', $data);
    }
}
