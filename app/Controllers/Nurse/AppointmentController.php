<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\AppointmentLogModel;

class AppointmentController extends BaseController
{
    public function list()
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $appointmentModel = new AppointmentModel();
        $today = date('Y-m-d');

        // Get today's appointments (appointments table references patients table)
        $appointments = $appointmentModel
            ->select('appointments.*, patients.full_name as patient_name, users.username as doctor_name')
            ->join('patients', 'patients.patient_id = appointments.patient_id', 'left')
            ->join('users', 'users.id = appointments.doctor_id', 'left')
            ->where('appointments.appointment_date', $today)
            ->whereNotIn('appointments.status', ['cancelled', 'no_show'])
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();

        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = $appointmentModel
            ->select('appointments.*, patients.full_name as patient_name, users.username as doctor_name')
            ->join('patients', 'patients.patient_id = appointments.patient_id', 'left')
            ->join('users', 'users.id = appointments.doctor_id', 'left')
            ->where('appointments.appointment_date >', $today)
            ->where('appointments.appointment_date <=', date('Y-m-d', strtotime('+7 days')))
            ->whereNotIn('appointments.status', ['cancelled', 'no_show'])
            ->orderBy('appointments.appointment_date', 'ASC')
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Appointment Overview',
            'appointments' => $appointments,
            'upcomingAppointments' => $upcomingAppointments
        ];

        return view('nurse/appointments/list', $data);
    }

    public function updateStatus($id)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $appointmentModel = new AppointmentModel();
        $logModel = new AppointmentLogModel();
        $nurseId = session()->get('user_id');

        $appointment = $appointmentModel->find($id);
        if (!$appointment) {
            return redirect()->back()->with('error', 'Appointment not found.');
        }

        $validation = $this->validate([
            'status' => 'required|in_list[scheduled,confirmed,in_progress,completed,cancelled,no_show]',
        ]);

        if (!$validation) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        if ($appointmentModel->update($id, ['status' => $newStatus])) {
            // Log status change
            $logModel->insert([
                'appointment_id' => $id,
                'status' => $newStatus,
                'changed_by' => $nurseId,
                'notes' => $notes,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()->with('success', 'Appointment status updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update appointment status.');
        }
    }

    public function history($patientId = null)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a nurse to access this page.');
        }

        $appointmentModel = new AppointmentModel();
        $query = $appointmentModel
            ->select('appointments.*, patients.full_name as patient_name, users.username as doctor_name')
            ->join('patients', 'patients.patient_id = appointments.patient_id', 'left')
            ->join('users', 'users.id = appointments.doctor_id', 'left')
            ->orderBy('appointments.appointment_date', 'DESC')
            ->orderBy('appointments.appointment_time', 'DESC');

        if ($patientId) {
            $query->where('appointments.patient_id', $patientId);
        }

        $appointments = $query->findAll();

        $data = [
            'title' => 'Appointment History',
            'appointments' => $appointments
        ];

        return view('nurse/appointments/history', $data);
    }
}

