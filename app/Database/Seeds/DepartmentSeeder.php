<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            ['department_name' => 'General Medicine', 'created_at' => $now, 'updated_at' => $now],
            ['department_name' => 'Pediatrics', 'created_at' => $now, 'updated_at' => $now],
            ['department_name' => 'Cardiology', 'created_at' => $now, 'updated_at' => $now],
            ['department_name' => 'Orthopedics', 'created_at' => $now, 'updated_at' => $now],
            ['department_name' => 'Neurology', 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('departments')->insertBatch($data);
    }
}
