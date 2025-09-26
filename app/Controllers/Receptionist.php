<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PatientModel;
use App\Models\AppointmentModel;
use App\Models\PatientCheckInModel;
use App\Models\BillingModel;
use App\Models\VisitorModel;
use App\Models\DoctorModel;

class Receptionist extends Controller
{
    protected function ensureReceptionist()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'receptionist') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureReceptionist();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'title' => 'Receptionist - ' . ucwords(str_replace('_', ' ', basename($view))),
        ];
        return view($view, $base + $data);
    }

    // Page renders
    public function patients() { return $this->render('Receptionist/patients'); }
    public function appointments() { return $this->render('Receptionist/appointments'); }
    public function calendar() { return $this->render('Receptionist/calendar'); }
    public function checkins() { return $this->render('Receptionist/checkins'); }
    public function billing() { return $this->render('Receptionist/billing'); }
    public function visitors() { return $this->render('Receptionist/visitors'); }
    public function notifications() { return $this->render('Receptionist/notifications'); }
    public function settings() { return $this->render('Receptionist/settings'); }

    // JSON APIs
    public function patientSearch()
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $q = $this->request->getGet('q') ?? '';
        $patients = (new PatientModel())->searchPatients($q);
        return $this->response->setJSON(['data' => $patients]);
    }

    public function patientStore()
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $data = $this->request->getPost([
            'first_name','last_name','date_of_birth','gender','phone','email','address',
            'emergency_contact_name','emergency_contact_phone','government_id','blood_type','allergies','status'
        ]);
        $model = new PatientModel();
        if (!$model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $model->errors()]);
        }
        return $this->response->setJSON(['message' => 'Patient created', 'id' => $model->getInsertID()]);
    }

    public function patientUpdate($id)
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $data = $this->request->getPost();
        $model = new PatientModel();
        if (!$model->update($id, $data)) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $model->errors()]);
        }
        return $this->response->setJSON(['message' => 'Patient updated']);
    }

    public function patientDelete($id)
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $model = new PatientModel();
        $model->delete($id);
        return $this->response->setJSON(['message' => 'Patient deleted']);
    }

    public function appointmentStore()
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $data = $this->request->getPost(['patient_id','doctor_id','appointment_date','notes']);
        $data['status'] = 'scheduled';
        $model = new AppointmentModel();
        if (!$model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $model->errors()]);
        }
        return $this->response->setJSON(['message' => 'Appointment created', 'id' => $model->getInsertID()]);
    }

    public function appointmentReschedule($id)
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $date = $this->request->getPost('appointment_date');
        $model = new AppointmentModel();
        $model->update($id, ['appointment_date' => $date, 'status' => 'scheduled']);
        return $this->response->setJSON(['message' => 'Appointment rescheduled']);
    }

    public function appointmentCancel($id)
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $model = new AppointmentModel();
        $model->update($id, ['status' => 'canceled']);
        return $this->response->setJSON(['message' => 'Appointment canceled']);
    }

    public function doctorAvailability()
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $doctors = (new DoctorModel())->findAll();
        // Placeholder availability; in real app pull schedules
        foreach ($doctors as &$d) { $d['availability'] = 'available'; }
        return $this->response->setJSON(['data' => $doctors]);
    }

    public function checkinMark()
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $data = $this->request->getPost(['appointment_id','patient_id','notes']);
        $data['check_in_time'] = date('Y-m-d H:i:s');
        $data['status'] = 'waiting';
        $model = new PatientCheckInModel();
        if (!$model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $model->errors()]);
        }
        return $this->response->setJSON(['message' => 'Checked-in']);
    }

    public function checkoutMark($id)
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $model = new PatientCheckInModel();
        $model->update($id, ['check_out_time' => date('Y-m-d H:i:s'), 'status' => 'completed']);
        return $this->response->setJSON(['message' => 'Checked-out']);
    }

    public function billingStore()
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $data = $this->request->getPost(['patient_id','amount','due_date']);
        $data['status'] = 'pending';
        $model = new BillingModel();
        if (!$model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $model->errors()]);
        }
        return $this->response->setJSON(['message' => 'Invoice created']);
    }

    public function billingMarkPaid($id)
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $model = new BillingModel();
        $model->update($id, ['status' => 'paid']);
        return $this->response->setJSON(['message' => 'Marked as paid']);
    }

    public function visitorStore()
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $data = $this->request->getPost(['patient_id','visitor_name','visitor_phone','relation_to_patient','purpose']);
        $data['visit_date'] = date('Y-m-d');
        $data['time_in'] = date('H:i:s');
        $data['status'] = 'in';
        $model = new VisitorModel();
        if (!$model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $model->errors()]);
        }
        return $this->response->setJSON(['message' => 'Visitor recorded']);
    }

    public function visitorCheckout($id)
    {
        $guard = $this->ensureReceptionist(); if ($guard) return $guard;
        $model = new VisitorModel();
        $model->update($id, ['time_out' => date('H:i:s'), 'status' => 'out']);
        return $this->response->setJSON(['message' => 'Visitor checked-out']);
    }
}
