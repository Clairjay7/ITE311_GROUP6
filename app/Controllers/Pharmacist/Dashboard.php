<?php

namespace App\Controllers\Pharmacist;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Pharmacist Dashboard',
            'username' => session()->get('username'),
            'role' => 'Pharmacist'
        ];
        return view('Pharmacist/dashboard', $data);
    }
}
