<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Super Admin Dashboard',
            'username' => session()->get('username'),
            'role' => 'Super Admin'
        ];
        return view('SuperAdmin/dashboard', $data);
    }
}
