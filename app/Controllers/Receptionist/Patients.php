<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\HMSPatientModel;
use App\Models\DepartmentModel;
use App\Models\DoctorDirectoryModel;

class Patients extends BaseController
{
    protected $patientModel;
    protected $departmentModel;
    protected $doctorModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->patientModel = new HMSPatientModel();
        $this->departmentModel = new DepartmentModel();
        $this->doctorModel = new DoctorDirectoryModel();
    }

    public function index()
    {
        $type = $this->request->getGet('type'); // In-Patient | Out-Patient
        $search = trim((string)$this->request->getGet('q'));

        $builder = $this->patientModel->builder();
        $builder->select('patients.*, doctors.doctor_name, departments.department_name')
                ->join('doctors', 'doctors.id = patients.doctor_id', 'left')
                ->join('departments', 'departments.id = patients.department_id', 'left');

        if ($type && in_array($type, ['In-Patient', 'Out-Patient'])) {
            $builder->where('patients.type', $type);
        }
        if ($search !== '') {
            $builder->groupStart()
                    ->like('patients.full_name', $search)
                    ->orLike('patients.patient_id', $search)
                    ->orLike('doctors.doctor_name', $search)
                    ->groupEnd();
        }
        $builder->orderBy('patients.patient_id', 'DESC');
        $patients = $builder->get()->getResultArray();

        return view('Reception/patients/index', [
            'title' => 'Patient Records',
            'patients' => $patients,
            'filterType' => $type,
            'query' => $search,
        ]);
    }

    public function create()
    {
        $prefType = $this->request->getGet('type'); // In-Patient | Out-Patient from link
        if (!in_array($prefType, ['In-Patient', 'Out-Patient'], true)) {
            $prefType = 'Out-Patient';
        }

        $viewName = $prefType === 'Out-Patient'
            ? 'Reception/patients/Outpatient'
            : 'Reception/patients/register';

        return view($viewName, [
            'title' => 'Register Patient',
            'departments' => $this->departmentModel->findAll(),
            'doctors' => $this->doctorModel->findAll(),
            'validation' => \Config\Services::validation(),
            'initialType' => $prefType,
        ]);
    }

    public function store()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[60]',
            'last_name' => 'required|min_length[2]|max_length[60]',
            'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
            'civil_status' => 'permit_empty|in_list[Single,Married,Widowed,Divorced,Separated,Annulled,Other]',
            'date_of_birth' => 'permit_empty|valid_date',
            'type' => 'required|in_list[In-Patient,Out-Patient]',
            'payment_type' => 'permit_empty|in_list[Cash,Insurance,Credit]',
            'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $first = trim((string)$this->request->getPost('first_name'));
        $middle = trim((string)$this->request->getPost('middle_name'));
        $last = trim((string)$this->request->getPost('last_name'));
        $ext = trim((string)$this->request->getPost('extension_name'));
        $nameParts = [$first];
        if ($middle !== '') {
            $nameParts[] = $middle;
        }
        $nameParts[] = $last;
        if ($ext !== '') {
            $nameParts[] = $ext;
        }
        $fullName = trim(implode(' ', array_filter($nameParts)));

        $dob = $this->request->getPost('date_of_birth');
        $age = $this->request->getPost('age');
        if ($dob) {
            try {
                $birth = new \DateTime($dob);
                $today = new \DateTime();
                $age = (int)$today->diff($birth)->y;
            } catch (\Exception $e) {
                $age = $age !== null ? (int)$age : null;
            }
        } else {
            $age = $age !== null ? (int)$age : null;
        }

        $addressStreet = trim((string)$this->request->getPost('address_street'));
        $addressBarangay = trim((string)$this->request->getPost('address_barangay'));
        $addressCity = trim((string)$this->request->getPost('address_city'));
        $addressProvince = trim((string)$this->request->getPost('address_province'));
        $composedAddress = trim(implode(', ', array_filter([$addressStreet, $addressBarangay, $addressCity, $addressProvince])));

        // Prevent duplicate: same full_name + date_of_birth or contact
        $exists = $this->patientModel->groupStart()
                ->where('full_name', $fullName)
                ->groupStart()
                    ->where('date_of_birth', $dob ?: null)
                    ->orWhere('contact', $this->request->getPost('contact') ?: null)
                ->groupEnd()
            ->groupEnd()
            ->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Duplicate patient detected.');
        }

        $data = [
            'patient_reg_no' => $this->request->getPost('patient_reg_no') ?: null,
            'first_name' => $first,
            'middle_name' => $middle ?: null,
            'last_name' => $last,
            'extension_name' => $ext !== '' ? $ext : null,
            'full_name' => $fullName,
            'gender' => $this->request->getPost('gender'),
            'civil_status' => $this->request->getPost('civil_status') ?: null,
            'date_of_birth' => $dob ?: null,
            'age' => $age,
            'contact' => $this->request->getPost('contact') ?: null,
            'email' => $this->request->getPost('email') ?: null,
            'address_street' => $addressStreet ?: null,
            'address_barangay' => $addressBarangay ?: null,
            'address_city' => $addressCity ?: null,
            'address_province' => $addressProvince ?: null,
            'address' => $composedAddress ?: null,
            'nationality' => $this->request->getPost('nationality') ?: null,
            'religion' => $this->request->getPost('religion') ?: null,
            'type' => $this->request->getPost('type'),
            'doctor_id' => $this->request->getPost('doctor_id') ?: null,
            'department_id' => $this->request->getPost('department_id') ?: null,
            'purpose' => $this->request->getPost('purpose') ?: null,
            'admission_date' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('admission_date') : null,
            'room_number' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('room_number') : null,
            'emergency_name' => $this->request->getPost('emergency_name') ?: null,
            'emergency_relationship' => $this->request->getPost('emergency_relationship') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_address' => $this->request->getPost('emergency_address') ?: null,
            'blood_type' => $this->request->getPost('blood_type') ?: null,
            'allergies' => $this->request->getPost('allergies') ?: null,
            'existing_conditions' => $this->request->getPost('existing_conditions') ?: null,
            'current_medications' => $this->request->getPost('current_medications') ?: null,
            'past_surgeries' => $this->request->getPost('past_surgeries') ?: null,
            'family_history' => $this->request->getPost('family_history') ?: null,
            'insurance_provider' => $this->request->getPost('insurance_provider') ?: null,
            'insurance_number' => $this->request->getPost('insurance_number') ?: null,
            'philhealth_number' => $this->request->getPost('philhealth_number') ?: null,
            'billing_address' => $this->request->getPost('billing_address') ?: null,
            'payment_type' => $this->request->getPost('payment_type') ?: null,
            'registration_date' => $this->request->getPost('registration_date') ?: date('Y-m-d'),
            'registered_by' => $this->request->getPost('registered_by') ?: null,
            'signature_patient' => $this->request->getPost('signature_patient') ?: null,
            'signature_staff' => $this->request->getPost('signature_staff') ?: null,
        ];

        $this->patientModel->save($data);
        return redirect()->to('/receptionist/patients')->with('success', 'Patient registered successfully.');
    }

    public function show($id)
    {
        $patient = $this->patientModel
            ->select('patients.*, doctors.doctor_name, departments.department_name')
            ->join('doctors', 'doctors.id = patients.doctor_id', 'left')
            ->join('departments', 'departments.id = patients.department_id', 'left')
            ->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        return view('Reception/patients/view', [
            'title' => 'Patient Details',
            'patient' => $patient,
        ]);
    }

    public function edit($id)
    {
        $patient = $this->patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        return view('Reception/patients/edit', [
            'title' => 'Edit Patient',
            'patient' => $patient,
            'departments' => $this->departmentModel->findAll(),
            'doctors' => $this->doctorModel->findAll(),
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[60]',
            'last_name'  => 'required|min_length[2]|max_length[60]',
            'gender'     => 'required|in_list[male,female,other,Male,Female,Other]',
            'civil_status' => 'permit_empty|in_list[Single,Married,Widowed,Divorced,Separated,Annulled,Other]',
            'date_of_birth' => 'permit_empty|valid_date',
            'type'       => 'required|in_list[In-Patient,Out-Patient]',
            'payment_type' => 'permit_empty|in_list[Cash,Insurance,Credit]',
            'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $first  = trim((string)$this->request->getPost('first_name'));
        $middle = trim((string)$this->request->getPost('middle_name'));
        $last   = trim((string)$this->request->getPost('last_name'));
        $ext    = trim((string)$this->request->getPost('extension_name'));
        $nameParts = [$first];
        if ($middle !== '') {
            $nameParts[] = $middle;
        }
        $nameParts[] = $last;
        if ($ext !== '') {
            $nameParts[] = $ext;
        }
        $fullName = trim(implode(' ', array_filter($nameParts)));

        $dob = $this->request->getPost('date_of_birth');
        $age = $this->request->getPost('age');
        if ($dob) {
            try {
                $birth = new \DateTime($dob);
                $today = new \DateTime();
                $age = (int)$today->diff($birth)->y;
            } catch (\Exception $e) {
                $age = $age !== null ? (int)$age : null;
            }
        } else {
            $age = $age !== null ? (int)$age : null;
        }

        $addressStreet   = trim((string)$this->request->getPost('address_street'));
        $addressBarangay = trim((string)$this->request->getPost('address_barangay'));
        $addressCity     = trim((string)$this->request->getPost('address_city'));
        $addressProvince = trim((string)$this->request->getPost('address_province'));
        $composedAddress = trim(implode(', ', array_filter([$addressStreet, $addressBarangay, $addressCity, $addressProvince])));

        $data = [
            'patient_id' => $id,
            'patient_reg_no' => $this->request->getPost('patient_reg_no') ?: null,
            'first_name' => $first,
            'middle_name' => $middle ?: null,
            'last_name' => $last,
            'full_name' => $fullName,
            'gender' => $this->request->getPost('gender'),
            'civil_status' => $this->request->getPost('civil_status') ?: null,
            'date_of_birth' => $dob ?: null,
            'age' => $age,
            'contact' => $this->request->getPost('contact') ?: null,
            'email' => $this->request->getPost('email') ?: null,
            'address_street' => $addressStreet ?: null,
            'address_barangay' => $addressBarangay ?: null,
            'address_city' => $addressCity ?: null,
            'address_province' => $addressProvince ?: null,
            'address' => $composedAddress ?: null,
            'nationality' => $this->request->getPost('nationality') ?: null,
            'religion' => $this->request->getPost('religion') ?: null,
            'type' => $this->request->getPost('type'),
            'doctor_id' => $this->request->getPost('doctor_id') ?: null,
            'department_id' => $this->request->getPost('department_id') ?: null,
            'purpose' => $this->request->getPost('purpose') ?: null,
            'admission_date' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('admission_date') : null,
            'room_number' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('room_number') : null,
            'emergency_name' => $this->request->getPost('emergency_name') ?: null,
            'emergency_relationship' => $this->request->getPost('emergency_relationship') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_address' => $this->request->getPost('emergency_address') ?: null,
            'blood_type' => $this->request->getPost('blood_type') ?: null,
            'allergies' => $this->request->getPost('allergies') ?: null,
            'existing_conditions' => $this->request->getPost('existing_conditions') ?: null,
            'current_medications' => $this->request->getPost('current_medications') ?: null,
            'past_surgeries' => $this->request->getPost('past_surgeries') ?: null,
            'family_history' => $this->request->getPost('family_history') ?: null,
            'insurance_provider' => $this->request->getPost('insurance_provider') ?: null,
            'insurance_number' => $this->request->getPost('insurance_number') ?: null,
            'philhealth_number' => $this->request->getPost('philhealth_number') ?: null,
            'billing_address' => $this->request->getPost('billing_address') ?: null,
            'payment_type' => $this->request->getPost('payment_type') ?: null,
            'registration_date' => $this->request->getPost('registration_date') ?: null,
            'registered_by' => $this->request->getPost('registered_by') ?: null,
            'signature_patient' => $this->request->getPost('signature_patient') ?: null,
            'signature_staff' => $this->request->getPost('signature_staff') ?: null,
            'date_signed' => $this->request->getPost('date_signed') ?: null,
        ];
        $this->patientModel->save($data);
        return redirect()->to('/receptionist/patients')->with('success', 'Patient updated successfully.');
    }

    public function delete($id)
    {
        $patient = $this->patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        $this->patientModel->delete($id);
        return redirect()->to('/receptionist/patients')->with('success', 'Patient deleted successfully.');
    }
}
