<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Appointment extends BaseController
{
    public function index()
    {
        // List page
        return view('Reception/appointments/list');
    }

    public function book()
    {
        // Load doctors from users table by role 'doctor'
        $db = \Config\Database::connect();
        $role = $db->table('roles')->select('id')->where('name', 'doctor')->get()->getRowArray();
        $doctors = [];
        if ($role) {
            $doctors = $db->table('users')
                ->select('id, username, email')
                ->where('role_id', (int)$role['id'])
                ->where('status', 'active')
                ->orderBy('username', 'ASC')
                ->get()->getResultArray();
        }
        // Load departments
        $departments = [];
        try {
            $deptModel = new \App\Models\DepartmentModel();
            $departments = $deptModel->select('id, department_name')->orderBy('department_name','ASC')->findAll();
        } catch (\Throwable $e) {
            $departments = [];
        }
        return view('Reception/appointments/book', ['doctors' => $doctors, 'departments' => $departments]);
    }

    public function schedule()
    {
        // Placeholder for a future schedule/calendar view
        return view('Reception/appointments/list');
    }

    public function create(): RedirectResponse
    {
        $payload = $this->request->getPost([
            'patient_ref', 'department', 'doctor', 'doctor_id', 'appointment_date', 'appointment_time', 'notes'
        ]);

        // Validate basic inputs (allow doctor_id OR doctor text)
        $hasDoctorInput = !empty($payload['doctor_id']) || !empty($payload['doctor']);
        if (empty($payload['patient_ref']) || !$hasDoctorInput || empty($payload['appointment_date']) || empty($payload['appointment_time'])) {
            return redirect()->back()->withInput()->with('error', 'Please complete all required fields, including selecting a doctor.');
        }

        // Resolve patient_id from reference (numeric id like #123 or name)
        $patientId = null;
        $ref = trim((string)($payload['patient_ref'] ?? ''));
        if ($ref !== '') {
            if (preg_match('/#?(\d+)/', $ref, $m)) {
                $patientId = (int)$m[1];
            }
            if (!$patientId) {
                // Try to find by name (schema uses patient_id)
                $patientModel = new \App\Models\HMSPatientModel();
                $term = preg_replace('/\s+/', ' ', $ref);
                $matches = $patientModel->builder()
                    ->select('patient_id, first_name, last_name')
                    ->groupStart()
                        ->like('first_name', $term)
                        ->orLike('last_name', $term)
                    ->groupEnd()
                    ->orderBy('patient_id', 'ASC')
                    ->limit(1)
                    ->get()->getResultArray();
                if (!empty($matches)) {
                    $patientId = (int)$matches[0]['patient_id'];
                }
            }
        }

        if (!$patientId) {
            return redirect()->back()->withInput()->with('error', 'Patient not found. Please use a valid patient ID (e.g., #123) or an existing name.');
        }

        // Resolve doctor_id from dropdown if provided; else fallback to username/email field
        $doctorId = null;
        if (!empty($payload['doctor_id'])) {
            $doctorId = (int)$payload['doctor_id'];
        } else {
            $doctorRef = trim((string)($payload['doctor'] ?? ''));
            if ($doctorRef !== '') {
                $userModel = new \App\Models\UserModel();
                // Prefer exact email or username match, fallback to like
                $builder = $userModel->builder()->select('id, username, email');
                if (filter_var($doctorRef, FILTER_VALIDATE_EMAIL)) {
                    $builder->where('email', $doctorRef);
                } else {
                    $builder->groupStart()
                        ->where('username', $doctorRef)
                        ->orLike('username', $doctorRef)
                    ->groupEnd();
                }
                $doctor = $builder->limit(1)->get()->getRowArray();
                if ($doctor) {
                    $doctorId = (int)$doctor['id'];
                }
            }
        }

        if (!$doctorId) {
            return redirect()->back()->withInput()->with('error', 'Doctor not found. Please use an existing doctor username or email.');
        }

        // Prepare data for AppointmentModel
        $appointmentData = [
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'appointment_date' => $payload['appointment_date'],
            'appointment_time' => $payload['appointment_time'],
            'appointment_type' => 'consultation', // default to satisfy validation
            'reason' => $payload['department'] ?? null,
            'status' => 'scheduled',
            'notes' => $payload['notes'] ?? null,
        ];

        $model = new \App\Models\AppointmentModel();
        if (!$model->insert($appointmentData)) {
            // Collect validation errors
            $errors = $model->errors();
            $msg = $errors ? implode("\n", $errors) : 'Unable to save appointment.';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        session()->setFlashdata('success', 'Appointment saved successfully.');
        return redirect()->to(site_url('receptionist/appointments/list'));
    }

    // The following endpoints are placeholders to satisfy existing routes.
    public function show(int $id)
    {
        return $this->response->setJSON(['id' => $id]);
    }

    public function update(int $id)
    {
        return $this->response->setJSON(['updated' => $id]);
    }

    public function cancel(int $id)
    {
        return $this->response->setJSON(['canceled' => $id]);
    }

    public function complete(int $id)
    {
        return $this->response->setJSON(['completed' => $id]);
    }

    public function noShow(int $id)
    {
        return $this->response->setJSON(['no_show' => $id]);
    }

    public function delete(int $id)
    {
        return $this->response->setJSON(['deleted' => $id]);
    }

    // Query helpers (stubs)
    public function getByDoctor(int $doctorId)
    {
        return $this->response->setJSON(['doctorId' => $doctorId, 'items' => []]);
    }

    public function getByPatient(int $patientId)
    {
        return $this->response->setJSON(['patientId' => $patientId, 'items' => []]);
    }

    public function getTodays()
    {
        return $this->response->setJSON(['items' => []]);
    }

    public function getUpcoming()
    {
        return $this->response->setJSON(['items' => []]);
    }

    public function search()
    {
        $q = $this->request->getGet('q');
        return $this->response->setJSON(['q' => $q, 'items' => []]);
    }

    public function getStats()
    {
        return $this->response->setJSON(['total' => 0, 'completed' => 0, 'canceled' => 0]);
    }
}
