<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDoctorOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'patient_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'doctor_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'order_text' => [ 'type' => 'TEXT' ],
            'acknowledged_by_user_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
            'acknowledged_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('acknowledged_by_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('doctor_orders');
    }

    public function down()
    {
        $this->forge->dropTable('doctor_orders');
    }
}


