<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNurseTasksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'nurse_user_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'patient_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
            'title' => [ 'type' => 'VARCHAR', 'constraint' => 150 ],
            'status' => [ 'type' => 'ENUM', 'constraint' => ['pending','in_progress','completed','canceled'], 'default' => 'pending' ],
            'scheduled_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'completed_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('nurse_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('nurse_tasks');
    }

    public function down()
    {
        $this->forge->dropTable('nurse_tasks');
    }
}


