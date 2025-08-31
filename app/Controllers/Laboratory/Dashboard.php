<?php

namespace App\Controllers\Laboratory;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Laboratory Staff Dashboard',
            'username' => session()->get('username'),
            'role' => 'Laboratory Staff'
        ];
        return view('Laboratory/dashboard', $data);
    }
}
