<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'doctor_name', 'specialization', 'user_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all doctors (only active doctors with active users)
     * Returns user_id for proper assignment (patients.doctor_id = users.id)
     */
    public function getAllDoctors()
    {
        $db = \Config\Database::connect();
        
        // Join with users table to get only active doctors
        $doctors = $db->table('doctors')
            ->select('doctors.*, doctors.user_id as assignment_id, users.status as user_status')
            ->join('users', 'users.id = doctors.user_id', 'inner')
            ->where('users.status', 'active')
            ->where('users.deleted_at IS NULL', null, false)
            ->orderBy('doctors.doctor_name', 'ASC')
            ->get()
            ->getResultArray();
        
        // Add user_id as both 'id' (for backward compatibility) and 'user_id' (for correct assignment)
        foreach ($doctors as &$doctor) {
            $doctor['id'] = $doctor['user_id']; // Use user_id as id for assignment
            $doctor['assignment_id'] = $doctor['user_id']; // Explicit assignment_id
        }
        
        return $doctors;
    }

    /**
     * Get doctor by ID
     */
    public function getDoctor($id)
    {
        return $this->find($id);
    }

    /**
     * Get active doctors (alias for getAllDoctors)
     */
    public function getActiveDoctors()
    {
        return $this->getAllDoctors();
    }
    
    /**
     * Get doctor by user_id (for assignment)
     */
    public function getDoctorByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }
    
    /**
     * Get doctors by specialization
     */
    public function getDoctorsBySpecialization($specialization)
    {
        $db = \Config\Database::connect();
        
        $doctors = $db->table('doctors')
            ->select('doctors.*, doctors.user_id as assignment_id, users.status as user_status')
            ->join('users', 'users.id = doctors.user_id', 'inner')
            ->where('users.status', 'active')
            ->where('users.deleted_at IS NULL', null, false)
            ->where('LOWER(doctors.specialization)', strtolower($specialization))
            ->orderBy('doctors.doctor_name', 'ASC')
            ->get()
            ->getResultArray();
        
        foreach ($doctors as &$doctor) {
            $doctor['id'] = $doctor['user_id'];
            $doctor['assignment_id'] = $doctor['user_id'];
        }
        
        return $doctors;
    }

    /**
     * Search doctors by name or specialization
     */
    public function searchDoctors($searchTerm)
    {
        return $this->groupStart()
                    ->like('doctor_name', $searchTerm)
                    ->orLike('specialization', $searchTerm)
                    ->groupEnd()
                    ->orderBy('doctor_name', 'ASC')
                    ->findAll();
    }
}
