<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Login extends Controller
{
    // Legacy entry points redirect to the new Auth controller routes
    public function index()
    {
        return redirect()->to('/login');
    }

    public function login()
    {
        return redirect()->to('/login');
    }

    public function logout()
    {
        return redirect()->to('/logout');
    }
}