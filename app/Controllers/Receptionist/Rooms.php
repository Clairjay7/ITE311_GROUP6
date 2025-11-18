<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\RoomModel;
use App\Models\HMSPatientModel;

class Rooms extends BaseController
{
    protected $roomModel;
    protected $patientModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->patientModel = new HMSPatientModel();
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

        return view('Reception/rooms/ward', [
            'title' => $wardName . ' Rooms',
            'wardSlug' => $slug,
            'wardName' => $wardName,
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

        return view('Reception/rooms/assign', [
            'title' => 'Assign Patient to Room',
            'room' => $room,
            'patients' => $patients,
        ]);
    }

    public function assignStore(int $roomId)
    {
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->back()->with('error', 'Room not found.');
        }

        $patientId = (int)$this->request->getPost('patient_id');
        if (!$patientId) {
            return redirect()->back()->with('error', 'Please select a patient.');
        }

        $patient = $this->patientModel->find($patientId);
        if (!$patient) {
            return redirect()->back()->with('error', 'Selected patient does not exist.');
        }

        $this->roomModel->update($roomId, [
            'current_patient_id' => $patientId,
            'status' => 'Occupied',
        ]);

        $slug = $this->wardSlugFromName($room['ward'] ?? '');
        return redirect()->to(site_url('receptionist/rooms/ward/' . $slug))
            ->with('success', 'Patient assigned to room successfully.');
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

        $slug = $this->wardSlugFromName($room['ward'] ?? '');
        return redirect()->to(site_url('receptionist/rooms/ward/' . $slug))
            ->with('success', 'Room marked as available.');
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
}
