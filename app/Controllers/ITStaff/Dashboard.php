<?php

namespace App\Controllers\ITStaff;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'IT Staff Dashboard',
            'username' => session()->get('username'),
            'role' => 'IT Staff'
        ];
        return view('ITStaff/dashboard', $data);
    }
}
