<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDoctorIdToAdminPatients extends Migration
{
    public function up()
    {
        $fields = [
            'doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'address',
            ],
        ];
        $this->forge->addColumn('admin_patients', $fields);
        
        // Add foreign key constraint if users table exists
        if ($this->db->tableExists('users')) {
            $this->forge->addForeignKey('doctor_id', 'users', 'id', 'CASCADE', 'SET NULL', 'admin_patients_doctor_fk');
        }
    }

    public function down()
    {
        // Drop foreign key first
        if ($this->db->tableExists('admin_patients')) {
            $this->forge->dropForeignKey('admin_patients', 'admin_patients_doctor_fk');
        }
        $this->forge->dropColumn('admin_patients', 'doctor_id');
    }
}

