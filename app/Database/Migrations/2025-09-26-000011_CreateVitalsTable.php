<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVitalsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'patient_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'recorded_by_user_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'bp_systolic' => [ 'type' => 'INT', 'null' => true ],
            'bp_diastolic' => [ 'type' => 'INT', 'null' => true ],
            'temperature_c' => [ 'type' => 'DECIMAL', 'constraint' => '4,1', 'null' => true ],
            'pulse_bpm' => [ 'type' => 'INT', 'null' => true ],
            'respiration_rate' => [ 'type' => 'INT', 'null' => true ],
            'recorded_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('recorded_by_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vitals');
    }

    public function down()
    {
        $this->forge->dropTable('vitals');
    }
}


