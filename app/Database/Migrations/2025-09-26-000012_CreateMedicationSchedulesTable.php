<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMedicationSchedulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'patient_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'prescription_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
            'medicine_name' => [ 'type' => 'VARCHAR', 'constraint' => 150 ],
            'dosage' => [ 'type' => 'VARCHAR', 'constraint' => 100, 'null' => true ],
            'scheduled_at' => [ 'type' => 'DATETIME' ],
            'status' => [ 'type' => 'ENUM', 'constraint' => ['scheduled','given','missed','skipped'], 'default' => 'scheduled' ],
            'given_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('prescription_id', 'prescriptions', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('medication_schedules');
    }

    public function down()
    {
        $this->forge->dropTable('medication_schedules');
    }
}


