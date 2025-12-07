<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePatientMedicationRecordsTable extends Migration
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
            'patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Links to doctor_orders table',
            ],
            'medicine_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'dosage' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'frequency' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'duration' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'prescribed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Doctor ID',
            ],
            'dispensed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'administered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'allergies' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Patient allergies related to this medication',
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('patient_id');
        $this->forge->addKey('order_id');
        $this->forge->addForeignKey('patient_id', 'admin_patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_medication_records');
    }

    public function down()
    {
        $this->forge->dropTable('patient_medication_records');
    }
}

