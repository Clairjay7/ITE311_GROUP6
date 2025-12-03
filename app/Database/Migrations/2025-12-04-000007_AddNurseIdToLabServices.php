<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNurseIdToLabServices extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lab_services', [
            'nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'doctor_id',
                'comment' => 'Nurse who will collect the specimen',
            ],
        ]);

        // Add index for faster queries
        $this->forge->addKey('nurse_id');
        
        // Add foreign key if tables exist
        if ($this->db->tableExists('users')) {
            try {
                $this->forge->addForeignKey('nurse_id', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_lab_services_nurse');
            } catch (\Exception $e) {
                // Ignore if foreign key already exists
            }
        }
    }

    public function down()
    {
        // Drop foreign key first
        if ($this->db->tableExists('lab_services')) {
            try {
                $this->forge->dropForeignKey('lab_services', 'fk_lab_services_nurse');
            } catch (\Exception $e) {
                // Ignore if foreign key doesn't exist
            }
        }
        
        $this->forge->dropColumn('lab_services', ['nurse_id']);
    }
}

