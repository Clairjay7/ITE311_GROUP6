<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table            = 'appointments';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'status',
        'reason',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Base query joining patients and doctors (users) where possible.
     */
    protected function baseSelect()
    {
        return $this->builder()
            ->select('appointments.*, '
                . 'p.full_name AS patient_name, '
                . 'u.username AS doctor_name')
            ->join('patients p', 'p.patient_id = appointments.patient_id', 'left')
            ->join('users u', 'u.id = appointments.doctor_id', 'left');
    }

    public function getAppointmentsWithDetails(): array
    {
        return $this->baseSelect()
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'DESC')
            ->get()->getResultArray();
    }

    public function getAppointmentsByDateRange(string $start, string $end): array
    {
        return $this->baseSelect()
            ->where('appointment_date >=', $start)
            ->where('appointment_date <=', $end)
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->get()->getResultArray();
    }

    public function getUnifiedList(string $doctorId, ?string $date = null): array
    {
        $builder = $this->baseSelect()
            ->where('appointments.doctor_id', $doctorId);

        if ($date !== null) {
            $builder->where('appointments.appointment_date', $date);
        }

        return $builder
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->get()->getResultArray();
    }

    public function checkAppointmentConflict($doctorId, string $date, string $time, ?int $ignoreId = null): bool
    {
        $builder = $this->builder()
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereNotIn('status', ['cancelled', 'no_show']);

        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    public function updateAppointmentStatus(int $id, string $status, ?string $notes = null): bool
    {
        $data = ['status' => $status];
        if ($notes !== null) {
            $data['notes'] = $notes;
        }

        return $this->update($id, $data);
    }

    public function getAppointmentsByDoctor(int $doctorId, ?string $date = null): array
    {
        $builder = $this->baseSelect()
            ->where('appointments.doctor_id', $doctorId);

        if ($date !== null) {
            $builder->where('appointments.appointment_date', $date);
        }

        return $builder
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->get()->getResultArray();
    }

    public function getAppointmentsByPatient(int $patientId): array
    {
        return $this->baseSelect()
            ->where('appointments.patient_id', $patientId)
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'DESC')
            ->get()->getResultArray();
    }

    public function getTodaysAppointments(): array
    {
        $today = date('Y-m-d');

        return $this->getAppointmentsByDateRange($today, $today);
    }

    public function getUpcomingAppointments(int $limit = 10): array
    {
        $today = date('Y-m-d');

        return $this->baseSelect()
            ->where('appointment_date >=', $today)
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function searchAppointments(string $term): array
    {
        $builder = $this->baseSelect();

        $builder->groupStart()
            ->like('p.full_name', $term)
            ->orLike('u.username', $term)
            ->orLike('appointments.reason', $term)
            ->orLike('appointments.notes', $term)
            ->groupEnd();

        return $builder
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'DESC')
            ->get()->getResultArray();
    }

    public function getAppointmentStats(?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->builder();

        if ($startDate) {
            $builder->where('appointment_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('appointment_date <=', $endDate);
        }

        $statusCounts = $builder
            ->select('status, COUNT(*) AS cnt')
            ->groupBy('status')
            ->get()->getResultArray();

        $stats = [
            'total' => 0,
            'by_status' => [],
        ];

        foreach ($statusCounts as $row) {
            $stats['by_status'][$row['status']] = (int) $row['cnt'];
            $stats['total'] += (int) $row['cnt'];
        }

        return $stats;
    }
}
