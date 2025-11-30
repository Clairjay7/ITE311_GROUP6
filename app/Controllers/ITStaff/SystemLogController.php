<?php

namespace App\Controllers\ITStaff;

use App\Controllers\BaseController;
use App\Models\SystemLogModel;

class SystemLogController extends BaseController
{
    protected $logModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->logModel = new SystemLogModel();
    }

    public function index()
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $filters = [
            'level' => $this->request->getGet('level'),
            'module' => $this->request->getGet('module'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        $perPage = 50;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;

        $query = $this->logModel->getFilteredLogs($filters);
        $totalLogs = $query->countAllResults(false);
        $logs = $query->limit($perPage, $offset)->findAll();

        $data = [
            'title' => 'System Logs',
            'logs' => $logs,
            'filters' => $filters,
            'pager' => [
                'current_page' => $page,
                'total_items' => $totalLogs,
                'per_page' => $perPage,
                'total_pages' => ceil($totalLogs / $perPage),
            ],
        ];

        return view('itstaff/logs/index', $data);
    }

    public function view($id)
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $log = $this->logModel
            ->select('system_logs.*, users.username as user_name, users.email as user_email')
            ->join('users', 'users.id = system_logs.user_id', 'left')
            ->find($id);

        if (!$log) {
            return redirect()->to('/it/logs')->with('error', 'Log entry not found.');
        }

        $data = [
            'title' => 'Log Details',
            'log' => $log,
        ];

        return view('itstaff/logs/view', $data);
    }

    public function delete($id)
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        if ($this->logModel->delete($id)) {
            return redirect()->to('/it/logs')->with('success', 'Log entry deleted successfully.');
        } else {
            return redirect()->to('/it/logs')->with('error', 'Failed to delete log entry.');
        }
    }

    public function clear()
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $days = $this->request->getPost('days') ?? 30;
        $dateThreshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $deleted = $this->logModel
            ->where('created_at <', $dateThreshold)
            ->delete();

        return redirect()->to('/it/logs')->with('success', "Cleared {$deleted} log entries older than {$days} days.");
    }
}

