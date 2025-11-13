<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            ['doctor_name' => 'Dr. Juan Dela Cruz', 'specialization' => 'General Medicine', 'created_at' => $now, 'updated_at' => $now],
            ['doctor_name' => 'Dr. Maria Santos', 'specialization' => 'Pediatrics', 'created_at' => $now, 'updated_at' => $now],
            ['doctor_name' => 'Dr. Jose Rizal', 'specialization' => 'Cardiology', 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('doctors')->insertBatch($data);
    }
}
