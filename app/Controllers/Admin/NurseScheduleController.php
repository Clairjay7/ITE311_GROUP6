<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NurseScheduleModel;

class NurseScheduleController extends BaseController
{
    protected $scheduleModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->scheduleModel = new NurseScheduleModel();
    }

    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $db = \Config\Database::connect();
        $selectedDate = $this->request->getGet('date') ?: date('Y-m-d');
        $selectedNurse = $this->request->getGet('nurse_id');

        // Get all active nurses
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get schedules for selected date
        $schedules = [];
        if ($db->tableExists('nurse_schedules')) {
            $scheduleQuery = $db->table('nurse_schedules')
                ->select('nurse_schedules.*, users.username as nurse_name')
                ->join('users', 'users.id = nurse_schedules.nurse_id', 'left')
                ->where('nurse_schedules.shift_date', $selectedDate)
                ->where('nurse_schedules.status !=', 'cancelled')
                ->orderBy('nurse_schedules.shift_type', 'ASC')
                ->orderBy('nurse_schedules.start_time', 'ASC');
            
            if ($selectedNurse) {
                $scheduleQuery->where('nurse_schedules.nurse_id', $selectedNurse);
            }
            
            $schedules = $scheduleQuery->get()->getResultArray();
        }

        // Group schedules by shift type
        $schedulesByShift = [
            'morning' => [],
            'night' => []
        ];
        
        foreach ($schedules as $schedule) {
            $shiftType = $schedule['shift_type'] ?? 'morning';
            $schedulesByShift[$shiftType][] = $schedule;
        }

        $data = [
            'title' => 'Nurse Schedules',
            'nurses' => $nurses,
            'schedules' => $schedules,
            'schedulesByShift' => $schedulesByShift,
            'selectedDate' => $selectedDate,
            'selectedNurse' => $selectedNurse,
        ];

        return view('admin/nurse_schedules/index', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $db = \Config\Database::connect();
        
        // Get all active nurses
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Create Nurse Schedule',
            'nurses' => $nurses,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/nurse_schedules/create', $data);
    }

    public function store()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $rules = [
            'nurse_id' => 'required|integer|greater_than[0]',
            'shift_date' => 'required|valid_date',
            'shift_type' => 'required|in_list[morning,night]',
            'start_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'end_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'status' => 'permit_empty|in_list[active,cancelled,on_leave]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nurseId = $this->request->getPost('nurse_id');
        $shiftDate = $this->request->getPost('shift_date');
        $shiftType = $this->request->getPost('shift_type');
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');

        // Validate 6-hour shift duration
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $duration = ($end - $start) / 3600; // Convert to hours
        
        if ($duration != 6) {
            return redirect()->back()->withInput()->with('error', 'Ang shift ay dapat eksaktong 6 na oras.');
        }

        // Check if nurse already has a schedule for this date and shift type
        $db = \Config\Database::connect();
        $existingSchedule = $db->table('nurse_schedules')
            ->where('nurse_id', $nurseId)
            ->where('shift_date', $shiftDate)
            ->where('shift_type', $shiftType)
            ->where('status !=', 'cancelled')
            ->get()
            ->getRowArray();

        if ($existingSchedule) {
            return redirect()->back()->withInput()->with('error', 'Ang nurse ay mayroon nang schedule para sa petsa at shift type na ito.');
        }

        $data = [
            'nurse_id' => $nurseId,
            'shift_date' => $shiftDate,
            'shift_type' => $shiftType,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $this->request->getPost('status') ?: 'active',
        ];

        if ($this->scheduleModel->insert($data)) {
            return redirect()->to('/admin/nurse-schedules')->with('success', 'Nurse schedule created successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create nurse schedule.');
    }

    public function edit($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            return redirect()->to('/admin/nurse-schedules')->with('error', 'Schedule not found.');
        }

        $db = \Config\Database::connect();
        
        // Get all active nurses
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Edit Nurse Schedule',
            'schedule' => $schedule,
            'nurses' => $nurses,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/nurse_schedules/edit', $data);
    }

    public function update($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            return redirect()->to('/admin/nurse-schedules')->with('error', 'Schedule not found.');
        }

        $rules = [
            'nurse_id' => 'required|integer|greater_than[0]',
            'shift_date' => 'required|valid_date',
            'shift_type' => 'required|in_list[morning,night]',
            'start_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'end_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'status' => 'permit_empty|in_list[active,cancelled,on_leave]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nurseId = $this->request->getPost('nurse_id');
        $shiftDate = $this->request->getPost('shift_date');
        $shiftType = $this->request->getPost('shift_type');
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');

        // Validate 6-hour shift duration
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $duration = ($end - $start) / 3600; // Convert to hours
        
        if ($duration != 6) {
            return redirect()->back()->withInput()->with('error', 'Ang shift ay dapat eksaktong 6 na oras.');
        }

        // Check if nurse already has a schedule for this date and shift type (excluding current)
        $db = \Config\Database::connect();
        $existingSchedule = $db->table('nurse_schedules')
            ->where('nurse_id', $nurseId)
            ->where('shift_date', $shiftDate)
            ->where('shift_type', $shiftType)
            ->where('id !=', $id)
            ->where('status !=', 'cancelled')
            ->get()
            ->getRowArray();

        if ($existingSchedule) {
            return redirect()->back()->withInput()->with('error', 'Ang nurse ay mayroon nang schedule para sa petsa at shift type na ito.');
        }

        $data = [
            'nurse_id' => $nurseId,
            'shift_date' => $shiftDate,
            'shift_type' => $shiftType,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $this->request->getPost('status') ?: 'active',
        ];

        if ($this->scheduleModel->update($id, $data)) {
            return redirect()->to('/admin/nurse-schedules')->with('success', 'Nurse schedule updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update nurse schedule.');
    }

    public function delete($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            return redirect()->to('/admin/nurse-schedules')->with('error', 'Schedule not found.');
        }

        if ($this->scheduleModel->delete($id)) {
            return redirect()->to('/admin/nurse-schedules')->with('success', 'Nurse schedule deleted successfully.');
        }

        return redirect()->to('/admin/nurse-schedules')->with('error', 'Failed to delete nurse schedule.');
    }

    public function bulkAssign()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $db = \Config\Database::connect();
        
        // Get all active nurses
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'title' => 'Bulk Assign Nurse Schedules',
            'nurses' => $nurses,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/nurse_schedules/bulk_assign', $data);
    }

    public function bulkAssignStore()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $rules = [
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'shift_type' => 'required|in_list[morning,night,both]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $shiftType = $this->request->getPost('shift_type');
        $nurseIds = $this->request->getPost('nurse_ids') ?? [];

        if (empty($nurseIds)) {
            return redirect()->back()->withInput()->with('error', 'Pumili ng kahit isang nurse.');
        }

        if (strtotime($endDate) < strtotime($startDate)) {
            return redirect()->back()->withInput()->with('error', 'Ang end date ay dapat mas malaki kaysa sa start date.');
        }

        $db = \Config\Database::connect();
        $schedules = [];
        $currentDate = $startDate;
        $nurseIndex = 0;

        while ($currentDate <= $endDate) {
            // Skip weekends
            $dayOfWeek = date('w', strtotime($currentDate));
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
                continue;
            }

            foreach ($nurseIds as $nurseId) {
                // Assign morning shift
                if ($shiftType === 'morning' || $shiftType === 'both') {
                    $existing = $db->table('nurse_schedules')
                        ->where('nurse_id', $nurseId)
                        ->where('shift_date', $currentDate)
                        ->where('shift_type', 'morning')
                        ->get()
                        ->getRowArray();

                    if (!$existing) {
                        $schedules[] = [
                            'nurse_id' => $nurseId,
                            'shift_date' => $currentDate,
                            'shift_type' => 'morning',
                            'start_time' => '06:00:00',
                            'end_time' => '12:00:00',
                            'status' => 'active',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }

                // Assign night shift
                if ($shiftType === 'night' || $shiftType === 'both') {
                    $existing = $db->table('nurse_schedules')
                        ->where('nurse_id', $nurseId)
                        ->where('shift_date', $currentDate)
                        ->where('shift_type', 'night')
                        ->get()
                        ->getRowArray();

                    if (!$existing) {
                        $schedules[] = [
                            'nurse_id' => $nurseId,
                            'shift_date' => $currentDate,
                            'shift_type' => 'night',
                            'start_time' => '18:00:00',
                            'end_time' => '00:00:00',
                            'status' => 'active',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
            }

            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        if (!empty($schedules)) {
            // Insert in batches
            $batchSize = 50;
            for ($i = 0; $i < count($schedules); $i += $batchSize) {
                $batch = array_slice($schedules, $i, $batchSize);
                $db->table('nurse_schedules')->insertBatch($batch);
            }

            $morningCount = count(array_filter($schedules, fn($s) => $s['shift_type'] === 'morning'));
            $nightCount = count(array_filter($schedules, fn($s) => $s['shift_type'] === 'night'));

            return redirect()->to('/admin/nurse-schedules')->with('success', 
                "Successfully assigned " . count($schedules) . " schedules. " .
                "Morning: {$morningCount}, Night: {$nightCount}");
        }

        return redirect()->back()->withInput()->with('error', 'Walang bagong schedules na na-create. Lahat ay mayroon na.');
    }
}

