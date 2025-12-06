<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReferringNurseIdToLabServices extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lab_services', [
            'referring_nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'nurse_id',
                'comment' => 'Nurse who referred the patient for lab service',
            ],
            'requester_type' => [
                'type' => 'ENUM',
                'constraint' => ['physician', 'nurse'],
                'null' => true,
                'after' => 'referring_nurse_id',
                'comment' => 'Type of requester: physician or nurse',
            ],
        ]);

        // Add index for faster queries
        $this->forge->addKey('referring_nurse_id');
        
        // Add foreign key if tables exist
        if ($this->db->tableExists('users')) {
            try {
                $this->forge->addForeignKey('referring_nurse_id', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_lab_services_referring_nurse');
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
                $this->forge->dropForeignKey('lab_services', 'fk_lab_services_referring_nurse');
            } catch (\Exception $e) {
                // Ignore if foreign key doesn't exist
            }
        }
        
        $this->forge->dropColumn('lab_services', ['referring_nurse_id', 'requester_type']);
    }
}

