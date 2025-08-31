<?php

namespace App\Controllers\Accountant;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Sample data - replace with actual database queries
        $data = [
            'title' => 'Accountant Dashboard',
            'username' => session()->get('username'),
            'role' => 'Accountant',
            'todayRevenue' => 1500,
            'outstandingBalance' => 25000, // Add the missing outstanding balance
            'pendingBills' => array_fill(0, 5, ['id' => 1, 'patient' => 'John Doe', 'amount' => 5000]),
            'insuranceClaims' => array_fill(0, 3, ['id' => 1, 'patient' => 'Jane Smith', 'amount' => 10000, 'status' => 'Pending']),
            'rolePath' => 'accountant',
            'user' => [
                'full_name' => session()->get('first_name') . ' ' . session()->get('last_name'),
                'role' => 'Accountant'
            ]
        ];
        
        return view('Accountant/dashboard', $data);
    }
}
