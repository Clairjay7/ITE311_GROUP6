<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDoctorNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'doctor_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'type' => [ 'type' => 'VARCHAR', 'constraint' => 80 ],
            'message' => [ 'type' => 'TEXT' ],
            'is_read' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 0 ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctor_notifications');
    }

    public function down()
    {
        $this->forge->dropTable('doctor_notifications');
    }
}


