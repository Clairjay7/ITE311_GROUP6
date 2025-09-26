<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePatientNotesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'patient_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'doctor_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'subjective' => [ 'type' => 'TEXT', 'null' => true ],
            'objective' => [ 'type' => 'TEXT', 'null' => true ],
            'assessment' => [ 'type' => 'TEXT', 'null' => true ],
            'plan' => [ 'type' => 'TEXT', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_notes');
    }

    public function down()
    {
        $this->forge->dropTable('patient_notes');
    }
}


