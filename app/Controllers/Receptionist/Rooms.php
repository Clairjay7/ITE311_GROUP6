<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\RoomModel;
use App\Models\HMSPatientModel;
use App\Models\BedModel;

class Rooms extends BaseController
{
    protected $roomModel;
    protected $patientModel;
    protected $bedModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->patientModel = new HMSPatientModel();
        $this->bedModel = new BedModel();
        helper('form'); // Load form helper for set_value() and other form functions
    }

    public function ward($slug)
    {
        $map = [
            'pedia'  => 'Pedia Ward',
            'male'   => 'Male Ward',
            'female' => 'Female Ward',
        ];

        if (!isset($map[$slug])) {
            return redirect()->back()->with('error', 'Unknown ward selected.');
        }

        $wardName = $map[$slug];
        $rooms = $this->roomModel
            ->select('rooms.*, patients.full_name AS patient_name')
            ->where('rooms.ward', $wardName)
            ->join('patients', 'patients.patient_id = rooms.current_patient_id', 'left')
            ->orderBy('rooms.room_number', 'ASC')
            ->findAll();

        // Fetch beds for each room
        $db = \Config\Database::connect();
        foreach ($rooms as &$room) {
            $beds = [];
            if ($db->tableExists('beds')) {
                $beds = $db->table('beds')
                    ->select('beds.*, patients.full_name AS patient_name')
                    ->where('beds.room_id', $room['id'])
                    ->join('patients', 'patients.patient_id = beds.current_patient_id', 'left')
                    ->orderBy('beds.bed_number', 'ASC')
                    ->get()
                    ->getResultArray();
            }
            $room['beds'] = $beds;
        }
        unset($room);

        return view('Reception/rooms/ward', [
            'title' => $wardName . ' Rooms',
            'wardSlug' => $slug,
            'wardName' => $wardName,
            'rooms' => $rooms,
        ]);
    }

    public function type($slug)
    {
        $map = [
            'private' => 'Private',
            'semi-private' => 'Semi-Private',
            'ward' => 'Ward',
            'icu' => 'ICU',
            'isolation' => 'Isolation',
        ];

        if (!isset($map[$slug])) {
            return redirect()->back()->with('error', 'Unknown room type selected.');
        }

        $roomType = $map[$slug];
        
        // Get rooms by type - use direct DB query to ensure we get all rooms
        // Join with patients table to get patient name
        $db = \Config\Database::connect();
        $rooms = $db->table('rooms')
            ->select('rooms.*, patients.full_name AS patient_name, patients.patient_id AS assigned_patient_id')
            ->where('rooms.room_type', $roomType)
            ->join('patients', 'patients.patient_id = rooms.current_patient_id', 'left')
            ->orderBy('rooms.room_number', 'ASC')
            ->get()
            ->getResultArray();

        // Fetch beds for each room
        foreach ($rooms as &$room) {
            $beds = [];
            if ($db->tableExists('beds')) {
                $beds = $db->table('beds')
                    ->select('beds.*, patients.full_name AS patient_name')
                    ->where('beds.room_id', $room['id'])
                    ->join('patients', 'patients.patient_id = beds.current_patient_id', 'left')
                    ->orderBy('beds.bed_number', 'ASC')
                    ->get()
                    ->getResultArray();
            }
            $room['beds'] = $beds;
        }
        unset($room);

        // Get room type display name
        $displayNames = [
            'Private' => 'Private Room',
            'Semi-Private' => 'Semi-Private Room',
            'Ward' => 'Ward (General Ward)',
            'ICU' => 'ICU (Intensive Care Unit)',
            'Isolation' => 'Isolation Room',
        ];

        return view('Reception/rooms/type', [
            'title' => $displayNames[$roomType] . ' - Room Management',
            'roomTypeSlug' => $slug,
            'roomType' => $roomType,
            'roomTypeDisplay' => $displayNames[$roomType] ?? $roomType,
            'rooms' => $rooms,
        ]);
    }

    public function assignForm(int $roomId)
    {
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->back()->with('error', 'Room not found.');
        }

        $patients = $this->patientModel
            ->where('type', 'In-Patient')
            ->orderBy('patient_id', 'DESC')
            ->findAll();

        // Get doctors from doctors table
        $doctors = $this->doctorModel->getAllDoctors();

        return view('Reception/rooms/assign', [
            'title' => 'Assign Patient to Room',
            'room' => $room,
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }

    public function assignStore(int $roomId)
    {
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->back()->with('error', 'Room not found.');
        }

        // Check if creating new patient or assigning existing
        $createNewPatient = $this->request->getPost('create_new_patient') === '1';
        
        if ($createNewPatient) {
            // Create new patient
            helper(['form']);
            
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[60]',
                'last_name' => 'required|min_length[2]|max_length[60]',
                'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
                'date_of_birth' => 'required|valid_date',
                'contact' => 'required|min_length[7]|max_length[20]',
                'address' => 'required|min_length[5]',
                'doctor_id' => 'required|integer',
                'purpose' => 'required|min_length[3]',
                'emergency_name' => 'required|min_length[2]',
                'emergency_relationship' => 'required',
                'emergency_contact' => 'required|min_length[7]',
            ];
            
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            // Process new patient data
            $first = trim((string)$this->request->getPost('first_name'));
            $middle = trim((string)$this->request->getPost('middle_name'));
            $last = trim((string)$this->request->getPost('last_name'));
            $nameParts = [$first];
            if ($middle !== '') {
                $nameParts[] = $middle;
            }
            $nameParts[] = $last;
            $fullName = trim(implode(' ', array_filter($nameParts)));
            
            $dob = $this->request->getPost('date_of_birth');
            $age = null;
            if ($dob) {
                try {
                    $birth = new \DateTime($dob);
                    $today = new \DateTime();
                    $age = (int)$today->diff($birth)->y;
                } catch (\Exception $e) {
                    $age = null;
                }
            }
            
            $patientData = [
                'first_name' => $first,
                'middle_name' => $middle ?: null,
                'last_name' => $last,
                'full_name' => $fullName,
                'gender' => $this->request->getPost('gender'),
                'date_of_birth' => $dob ?: null,
                'age' => $age,
                'contact' => $this->request->getPost('contact') ?: null,
                'address' => $this->request->getPost('address') ?: null,
                'type' => 'In-Patient',
                'visit_type' => 'Consultation',
                'doctor_id' => $this->request->getPost('doctor_id'),
                'purpose' => $this->request->getPost('purpose') ?: null,
                'admission_date' => $this->request->getPost('admission_date') ?: date('Y-m-d'),
                'room_id' => $roomId,
                'room_number' => $room['room_number'],
                'registration_date' => date('Y-m-d'),
                'existing_conditions' => $this->request->getPost('existing_conditions') ?: null,
                'allergies' => $this->request->getPost('allergies') ?: null,
                'insurance_provider' => $this->request->getPost('insurance_provider') ?: null,
                'insurance_number' => $this->request->getPost('insurance_number') ?: null,
                'emergency_name' => $this->request->getPost('emergency_name') ?: null,
                'emergency_relationship' => $this->request->getPost('emergency_relationship') ?: null,
                'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            ];
            
            $this->patientModel->save($patientData);
            $patientId = $this->patientModel->getInsertID();
            
            if (empty($patientId)) {
                $db = \Config\Database::connect();
                $lastPatient = $db->table('patients')
                    ->select('patient_id')
                    ->where('full_name', $fullName)
                    ->orderBy('patient_id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
                $patientId = $lastPatient['patient_id'] ?? null;
            }
            
            if (!$patientId) {
                return redirect()->back()->withInput()->with('error', 'Failed to create patient. Please try again.');
            }
        } else {
            // Assign existing patient
            $patientId = (int)$this->request->getPost('patient_id');
            if (!$patientId) {
                return redirect()->back()->with('error', 'Please select a patient.');
            }

            $patient = $this->patientModel->find($patientId);
            if (!$patient) {
                return redirect()->back()->with('error', 'Selected patient does not exist.');
            }
        }
        
        // Assign patient to room
        $selectedBedId = $this->request->getPost('bed_id');
        $selectedBedNumber = $this->request->getPost('bed_number');
        
        // Update room status (only if single bed room or no bed selected)
        if (empty($selectedBedId) || ($room['bed_count'] ?? 1) == 1) {
            $this->roomModel->update($roomId, [
                'current_patient_id' => $patientId,
                'status' => 'Occupied',
            ]);
        }
        
        // Update patient with room info
        $this->patientModel->update($patientId, [
            'room_id' => $roomId,
            'room_number' => $room['room_number'],
        ]);
        
        // Handle bed assignment if bed is selected
        if ($selectedBedId) {
            $db = \Config\Database::connect();
            if ($db->tableExists('beds')) {
                $bed = $db->table('beds')
                    ->where('id', $selectedBedId)
                    ->where('room_id', $roomId)
                    ->get()
                    ->getRowArray();
                
                if ($bed) {
                    $isBedAvailable = (($bed['status'] ?? '') === 'available' || 
                                      ($bed['status'] ?? '') === 'Available' || 
                                      ($bed['status'] ?? '') === 'AVAILABLE') &&
                                      empty($bed['current_patient_id']);
                    
                    if ($isBedAvailable) {
                        $db->table('beds')
                            ->where('id', $selectedBedId)
                            ->update([
                                'current_patient_id' => $patientId,
                                'status' => 'occupied',
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
            }
        }

        // Redirect based on room type or ward
        if (!empty($room['room_type'])) {
            $slug = $this->roomTypeSlugFromName($room['room_type']);
            return redirect()->to(site_url('receptionist/rooms/type/' . $slug))
                ->with('success', 'Patient assigned to room successfully.');
        } else {
            $slug = $this->wardSlugFromName($room['ward'] ?? '');
            return redirect()->to(site_url('receptionist/rooms/ward/' . $slug))
                ->with('success', 'Patient assigned to room successfully.');
        }
    }

    public function vacate(int $roomId)
    {
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->back()->with('error', 'Room not found.');
        }

        $this->roomModel->update($roomId, [
            'current_patient_id' => null,
            'status' => 'Available',
        ]);

        // Redirect based on room type or ward
        if (!empty($room['room_type'])) {
            $slug = $this->roomTypeSlugFromName($room['room_type']);
            return redirect()->to(site_url('receptionist/rooms/type/' . $slug))
                ->with('success', 'Room marked as available.');
        } else {
            $slug = $this->wardSlugFromName($room['ward'] ?? '');
            return redirect()->to(site_url('receptionist/rooms/ward/' . $slug))
                ->with('success', 'Room marked as available.');
        }
    }

    protected function wardSlugFromName(string $wardName): string
    {
        $reverse = [
            'Pedia Ward' => 'pedia',
            'Male Ward' => 'male',
            'Female Ward' => 'female',
        ];

        return $reverse[$wardName] ?? 'pedia';
    }

    protected function roomTypeSlugFromName(string $roomType): string
    {
        $reverse = [
            'Private' => 'private',
            'Semi-Private' => 'semi-private',
            'Ward' => 'ward',
            'ICU' => 'icu',
            'Isolation' => 'isolation',
        ];

        return $reverse[$roomType] ?? 'ward';
    }

    public function getBeds(int $roomId)
    {
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Room not found',
            ]);
        }

        $db = \Config\Database::connect();
        $beds = [];

        if ($db->tableExists('beds')) {
            $beds = $db->table('beds')
                ->select('beds.id, beds.bed_number, beds.status, beds.current_patient_id')
                ->where('beds.room_id', $roomId)
                ->where('beds.current_patient_id', null) // Only available beds (not assigned)
                ->orderBy('beds.bed_number', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $this->response->setJSON([
            'success' => true,
            'beds' => $beds,
        ]);
    }
}
