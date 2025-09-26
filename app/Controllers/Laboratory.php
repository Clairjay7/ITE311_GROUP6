<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Laboratory extends Controller
{
    public function __construct()
    {
    }

    protected function ensureLaboratory()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'laboratory') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureLaboratory();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'title' => 'Laboratory - ' . ucwords(str_replace('_', ' ', basename($view))),
            'username' => session()->get('username'),
        ];
        return view($view, $base + $data);
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();
        $data = [
            'pendingTests' => $db->table('lab_requests')->where('status', 'pending')->countAllResults(),
            'inProgressTests' => $db->table('lab_requests')->where('status', 'in_progress')->countAllResults(),
            'completedToday' => $db->table('lab_requests')->where('status', 'completed')->where('DATE(updated_at)', date('Y-m-d'))->countAllResults(),
            'urgentTests' => $db->table('lab_requests')->where('priority', 'urgent')->where('status !=', 'completed')->countAllResults(),
        ];
        
        return $this->render('Laboratory/dashboard', $data);
    }

    public function tests()
    {
        return $this->render('Laboratory/tests');
    }

    public function pending()
    {
        return $this->render('Laboratory/pending');
    }

    public function results()
    {
        return $this->render('Laboratory/results');
    }

    public function reports()
    {
        return $this->render('Laboratory/reports');
    }

    public function equipment()
    {
        return $this->render('Laboratory/equipment');
    }

    public function inventory()
    {
        return $this->render('Laboratory/inventory');
    }

    public function settings()
    {
        return $this->render('Laboratory/settings');
    }

}
