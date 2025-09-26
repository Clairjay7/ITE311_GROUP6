<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PatientModel;
use App\Models\MedicalRecordModel;
use App\Models\PrescriptionModel;
use App\Models\LabRequestModel;
use App\Models\AppointmentModel;
use App\Models\SystemLogModel;

class Doctor extends Controller
{
    protected $patientModel;
    protected $medicalRecordModel;
    protected $prescriptionModel;
    protected $labRequestModel;
    protected $appointmentModel;
    protected $systemLogModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->medicalRecordModel = new MedicalRecordModel();
        $this->prescriptionModel = new PrescriptionModel();
        $this->labRequestModel = new LabRequestModel();
        $this->appointmentModel = new AppointmentModel();
        $this->systemLogModel = new SystemLogModel();
    }

    protected function ensureDoctor()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'doctor') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureDoctor();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'title' => 'Doctor - ' . ucwords(str_replace('_', ' ', basename($view))),
            'username' => session()->get('username'),
        ];
        return view($view, $base + $data);
    }

    public function dashboard()
    {
        $doctorId = session()->get('user_id');
        
        $data = [
            'totalPatients' => $this->medicalRecordModel->where('doctor_id', $doctorId)->countAllResults(),
            'todaysAppointments' => $this->appointmentModel->where('doctor_id', $doctorId)->where('DATE(appointment_date)', date('Y-m-d'))->countAllResults(),
            'pendingPrescriptions' => $this->prescriptionModel->where('doctor_id', $doctorId)->where('status', 'pending')->countAllResults(),
            'pendingLabRequests' => $this->labRequestModel->where('doctor_id', $doctorId)->where('status', 'requested')->countAllResults(),
        ];
        
        return $this->render('doctor/dashboard', $data);
    }

    // ============ PATIENTS LIST ============
    public function patients()
    {
        $patients = $this->patientModel->getActivePatients();
        return $this->render('doctor/patients', ['patients' => $patients]);
    }

    public function viewPatient($id)
    {
        $patient = $this->patientModel->find($id);
        if (!$patient) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Patient not found');
        }

        $medicalHistory = $this->medicalRecordModel->getPatientHistory($id);
        $prescriptions = $this->prescriptionModel->getByPatient($id);
        $labRequests = $this->labRequestModel->getRequestsByPatient($id);

        return $this->render('doctor/view_patient', [
            'patient' => $patient,
            'medical_history' => $medicalHistory,
            'prescriptions' => $prescriptions,
            'lab_requests' => $labRequests
        ]);
    }

    // ============ MEDICAL RECORDS ============
    public function medicalRecords()
    {
        $doctorId = session()->get('user_id');
        $records = $this->medicalRecordModel->getRecordsByDoctor($doctorId);
        return $this->render('doctor/medical_records', ['records' => $records]);
    }

    public function addMedicalRecord($patientId = null)
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            $data['doctor_id'] = session()->get('user_id');
            
            if ($this->medicalRecordModel->insert($data)) {
                $this->systemLogModel->info('Medical record created', ['patient_id' => $data['patient_id']], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Medical record added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->medicalRecordModel->errors()]);
            }
        }

        $patients = $this->patientModel->getActivePatients();
        $selectedPatient = $patientId ? $this->patientModel->find($patientId) : null;
        
        return $this->render('doctor/add_medical_record', [
            'patients' => $patients,
            'selected_patient' => $selectedPatient
        ]);
    }

    public function editMedicalRecord($id)
    {
        $record = $this->medicalRecordModel->find($id);
        if (!$record || $record['doctor_id'] != session()->get('user_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Medical record not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->medicalRecordModel->update($id, $data)) {
                $this->systemLogModel->info('Medical record updated', ['record_id' => $id], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Medical record updated successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->medicalRecordModel->errors()]);
            }
        }

        return $this->render('doctor/edit_medical_record', ['record' => $record]);
    }

    // ============ PRESCRIPTIONS ============
    public function prescriptions()
    {
        $doctorId = session()->get('user_id');
        $prescriptions = $this->prescriptionModel->getByDoctor($doctorId);
        return $this->render('doctor/prescriptions', ['prescriptions' => $prescriptions]);
    }

    public function addPrescription($patientId = null)
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            $data['doctor_id'] = session()->get('user_id');
            
            if ($this->prescriptionModel->insert($data)) {
                $this->systemLogModel->info('Prescription created', ['patient_id' => $data['patient_id']], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Prescription added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->prescriptionModel->errors()]);
            }
        }

        $patients = $this->patientModel->getActivePatients();
        $selectedPatient = $patientId ? $this->patientModel->find($patientId) : null;
        
        return $this->render('doctor/add_prescription', [
            'patients' => $patients,
            'selected_patient' => $selectedPatient
        ]);
    }

    public function editPrescription($id)
    {
        $prescription = $this->prescriptionModel->find($id);
        if (!$prescription || $prescription['doctor_id'] != session()->get('user_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Prescription not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->prescriptionModel->update($id, $data)) {
                $this->systemLogModel->info('Prescription updated', ['prescription_id' => $id], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Prescription updated successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->prescriptionModel->errors()]);
            }
        }

        return $this->render('doctor/edit_prescription', ['prescription' => $prescription]);
    }

    public function deletePrescription($id)
    {
        $prescription = $this->prescriptionModel->find($id);
        if (!$prescription || $prescription['doctor_id'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Prescription not found']);
        }

        if ($this->prescriptionModel->delete($id)) {
            $this->systemLogModel->info('Prescription deleted', ['prescription_id' => $id], session()->get('user_id'));
            return $this->response->setJSON(['success' => true, 'message' => 'Prescription deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete prescription']);
        }
    }

    // ============ LAB REQUESTS ============
    public function labRequests()
    {
        $doctorId = session()->get('user_id');
        $requests = $this->labRequestModel->getRequestsByDoctor($doctorId);
        return $this->render('doctor/lab_requests', ['requests' => $requests]);
    }

    public function addLabRequest($patientId = null)
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            $data['doctor_id'] = session()->get('user_id');
            
            if ($this->labRequestModel->insert($data)) {
                $this->systemLogModel->info('Lab request created', ['patient_id' => $data['patient_id']], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Lab request sent successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->labRequestModel->errors()]);
            }
        }

        $patients = $this->patientModel->getActivePatients();
        $selectedPatient = $patientId ? $this->patientModel->find($patientId) : null;
        
        return $this->render('doctor/add_lab_request', [
            'patients' => $patients,
            'selected_patient' => $selectedPatient
        ]);
    }

    public function viewLabResults()
    {
        $doctorId = session()->get('user_id');
        $completedRequests = $this->labRequestModel->getRequestsByStatus('completed')
                                                  ->where('doctor_id', $doctorId)
                                                  ->findAll();
        return $this->render('doctor/lab_results', ['results' => $completedRequests]);
    }

    // ============ APPOINTMENTS ============
    public function appointments()
    {
        $doctorId = session()->get('user_id');
        $appointments = $this->appointmentModel->getByDoctor($doctorId);
        return $this->render('doctor/appointments', ['appointments' => $appointments]);
    }

    public function calendar()
    {
        return $this->render('doctor/calendar');
    }

    public function emr()
    {
        $doctorId = session()->get('user_id');
        $records = $this->medicalRecordModel->getRecordsByDoctor($doctorId);
        return $this->render('doctor/emr', ['records' => $records]);
    }

    public function labResults()
    {
        return $this->viewLabResults();
    }

    public function messaging()
    {
        return $this->render('doctor/messaging');
    }

    public function reports()
    {
        return $this->render('doctor/reports');
    }

    public function profile()
    {
        return $this->render('doctor/profile');
    }

    public function settings()
    {
        return $this->render('doctor/settings');
    }

    // ============ API ENDPOINTS ============
    public function apiPatients()
    {
        return $this->response->setJSON($this->patientModel->getActivePatients());
    }

    public function apiMedicalRecords()
    {
        $doctorId = session()->get('user_id');
        return $this->response->setJSON($this->medicalRecordModel->getRecordsByDoctor($doctorId));
    }

    public function apiPrescriptions()
    {
        $doctorId = session()->get('user_id');
        return $this->response->setJSON($this->prescriptionModel->getByDoctor($doctorId));
    }

    public function apiLabRequests()
    {
        $doctorId = session()->get('user_id');
        return $this->response->setJSON($this->labRequestModel->getRequestsByDoctor($doctorId));
    }

    public function apiStats()
    {
        $doctorId = session()->get('user_id');
        $stats = [
            'total_patients' => $this->medicalRecordModel->where('doctor_id', $doctorId)->countAllResults(),
            'pending_prescriptions' => $this->prescriptionModel->where('doctor_id', $doctorId)->where('status', 'pending')->countAllResults(),
            'pending_lab_requests' => $this->labRequestModel->where('doctor_id', $doctorId)->where('status', 'requested')->countAllResults(),
            'today_appointments' => $this->appointmentModel->where('doctor_id', $doctorId)->where('DATE(appointment_date)', date('Y-m-d'))->countAllResults()
        ];
        return $this->response->setJSON($stats);
    }
}


