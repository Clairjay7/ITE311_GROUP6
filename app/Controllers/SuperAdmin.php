<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class SuperAdmin extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    protected function ensureSuperAdmin()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'superadmin') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureSuperAdmin();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'title' => 'Super Admin - ' . ucwords(str_replace('_', ' ', basename($view))),
            'username' => session()->get('username'),
        ];
        return view($view, $base + $data);
    }

    public function dashboard()
    {
        $data = [
            'totalDoctors' => $this->userModel->where('role', 'doctor')->countAllResults(),
            'totalPatients' => 0, // You can add PatientModel later
            'todaysAppointments' => 0, // You can add AppointmentModel later
            'pendingBills' => 0, // You can add BillingModel later
        ];
        return $this->render('SuperAdmin/dashboard', $data);
    }

    // ============ USERS MANAGEMENT ============
    public function users()
    {
        $users = $this->userModel->getUsersWithRoles();
        return $this->render('SuperAdmin/users', ['users' => $users]);
    }

    public function addUser()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->userModel->insert($data)) {
                $this->systemLogModel->info('New user created', ['user_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'User added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->userModel->errors()]);
            }
        }

        $roles = $this->roleModel->getActiveRoles();
        $departments = $this->departmentModel->getActiveDepartments();
        return $this->render('SuperAdmin/add_user', ['roles' => $roles, 'departments' => $departments]);
    }

    public function editUser($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->userModel->update($id, $data)) {
                $this->systemLogModel->info('User updated', ['user_id' => $id, 'user_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->userModel->errors()]);
            }
        }

        $roles = $this->roleModel->getActiveRoles();
        $departments = $this->departmentModel->getActiveDepartments();
        return $this->render('SuperAdmin/edit_user', ['user' => $user, 'roles' => $roles, 'departments' => $departments]);
    }

    public function deleteUser($id)
    {
        if ($this->userModel->delete($id)) {
            $this->systemLogModel->warning('User deleted', ['user_id' => $id], session()->get('user_id'));
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
        }
    }

    public function viewUser($id)
    {
        $user = $this->userModel->getUserWithDetails($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }
        return $this->render('SuperAdmin/view_user', ['user' => $user]);
    }

    // ============ DEPARTMENTS MANAGEMENT ============
    public function departments()
    {
        $departments = $this->departmentModel->getDepartmentsWithHeadDoctor();
        return $this->render('SuperAdmin/departments', ['departments' => $departments]);
    }

    public function addDepartment()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->departmentModel->insert($data)) {
                $this->systemLogModel->info('New department created', ['department_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Department added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->departmentModel->errors()]);
            }
        }

        $doctors = $this->userModel->getUsersByRole('doctor');
        return $this->render('SuperAdmin/add_department', ['doctors' => $doctors]);
    }

    public function editDepartment($id)
    {
        $department = $this->departmentModel->find($id);
        if (!$department) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Department not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->departmentModel->update($id, $data)) {
                $this->systemLogModel->info('Department updated', ['department_id' => $id, 'department_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Department updated successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->departmentModel->errors()]);
            }
        }

        $doctors = $this->userModel->getUsersByRole('doctor');
        return $this->render('SuperAdmin/edit_department', ['department' => $department, 'doctors' => $doctors]);
    }

    public function deleteDepartment($id)
    {
        if ($this->departmentModel->delete($id)) {
            $this->systemLogModel->warning('Department deleted', ['department_id' => $id], session()->get('user_id'));
            return $this->response->setJSON(['success' => true, 'message' => 'Department deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete department']);
        }
    }

    // ============ ROOMS MANAGEMENT ============
    public function rooms()
    {
        $rooms = $this->roomModel->getRoomsWithDepartment();
        return $this->render('SuperAdmin/rooms', ['rooms' => $rooms]);
    }

    public function addRoom()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->roomModel->insert($data)) {
                $this->systemLogModel->info('New room created', ['room_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Room added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->roomModel->errors()]);
            }
        }

        $departments = $this->departmentModel->getActiveDepartments();
        return $this->render('SuperAdmin/add_room', ['departments' => $departments]);
    }

    public function editRoom($id)
    {
        $room = $this->roomModel->find($id);
        if (!$room) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Room not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->roomModel->update($id, $data)) {
                $this->systemLogModel->info('Room updated', ['room_id' => $id, 'room_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Room updated successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->roomModel->errors()]);
            }
        }

        $departments = $this->departmentModel->getActiveDepartments();
        return $this->render('SuperAdmin/edit_room', ['room' => $room, 'departments' => $departments]);
    }

    public function deleteRoom($id)
    {
        if ($this->roomModel->delete($id)) {
            $this->systemLogModel->warning('Room deleted', ['room_id' => $id], session()->get('user_id'));
            return $this->response->setJSON(['success' => true, 'message' => 'Room deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete room']);
        }
    }

    // ============ AUDIT LOGS ============
    public function patients()
    {
        return $this->render('SuperAdmin/patients');
    }

    public function admissions()
    {
        return $this->render('SuperAdmin/admissions');
    }

    public function doctors()
    {
        $doctors = $this->userModel->getUsersByRole('doctor');
        return $this->render('SuperAdmin/doctors', ['doctors' => $doctors]);
    }

    public function staff()
    {
        $staff = $this->userModel->whereIn('role', ['nurse', 'receptionist', 'laboratory_staff', 'pharmacist', 'accountant', 'it_staff'])->findAll();
        return $this->render('SuperAdmin/staff', ['staff' => $staff]);
    }

    public function billing()
    {
        return $this->render('SuperAdmin/billing');
    }

    public function roles()
    {
        $roles = $this->userModel->findAll();
        return $this->render('SuperAdmin/roles', ['roles' => $roles]);
    }

    public function appointments()
    {
        return $this->render('SuperAdmin/appointments');
    }

    public function calendars()
    {
        return $this->render('SuperAdmin/calendars');
    }

    public function financeReports()
    {
        return $this->render('SuperAdmin/finance_reports');
    }

    public function laboratory()
    {
        return $this->render('SuperAdmin/laboratory');
    }

    public function pharmacy()
    {
        return $this->render('SuperAdmin/pharmacy');
    }

    public function occupancy()
    {
        return $this->render('SuperAdmin/occupancy');
    }

    public function reports()
    {
        return $this->render('SuperAdmin/reports');
    }

    public function analytics()
    {
        return $this->render('SuperAdmin/analytics');
    }

    public function settings()
    {
        return $this->render('SuperAdmin/settings');
    }

    public function security()
    {
        return $this->render('SuperAdmin/security');
    }

    public function auditLogs()
    {
        $logs = $this->auditLogModel->getRecentLogs(100);
        return $this->render('SuperAdmin/audit_logs', ['logs' => $logs]);
    }

    // ============ API ENDPOINTS ============
    public function apiUsers()
    {
        return $this->response->setJSON($this->userModel->getUsersWithRoles());
    }

    public function apiDepartments()
    {
        return $this->response->setJSON($this->departmentModel->getActiveDepartments());
    }

    public function apiStats()
    {
        $stats = [
            'total_users' => $this->userModel->countAll(),
            'active_users' => $this->userModel->where('status', 'active')->countAllResults(),
            'total_rooms' => $this->roomModel->countAll(),
            'available_rooms' => $this->roomModel->where('status', 'available')->countAllResults(),
            'occupied_rooms' => $this->roomModel->where('status', 'occupied')->countAllResults(),
            'total_departments' => $this->departmentModel->countAll(),
            'recent_activity' => $this->auditLogModel->getRecentActivity(5)
        ];
        return $this->response->setJSON($stats);
    }
}
