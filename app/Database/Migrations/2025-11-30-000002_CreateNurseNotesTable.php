<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNurseNotesTable extends Migration
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
            ],
            'nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'note_type' => [
                'type' => 'ENUM',
                'constraint' => ['progress', 'observation', 'medication', 'incident', 'other'],
                'default' => 'progress',
            ],
            'note' => [
                'type' => 'TEXT',
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'normal', 'high', 'urgent'],
                'default' => 'normal',
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
        $this->forge->addForeignKey('patient_id', 'admin_patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nurse_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nurse_notes');
    }

    public function down()
    {
        $this->forge->dropTable('nurse_notes');
    }
}

