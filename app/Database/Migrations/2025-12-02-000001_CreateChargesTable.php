<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChargesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'consultation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Links to consultations table - represents the visit',
            ],
            'patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'charge_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Unique charge number (e.g., CHG-20251202-001)',
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0.00,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'paid', 'cancelled'],
                'default' => 'pending',
                'null' => false,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('consultation_id');
        $this->forge->addKey('patient_id');
        $this->forge->addKey('charge_number');
        $this->forge->addForeignKey('consultation_id', 'consultations', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('patient_id', 'admin_patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('charges');
    }

    public function down()
    {
        $this->forge->dropTable('charges');
    }
}

