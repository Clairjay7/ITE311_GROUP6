<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemControlsTable extends Migration
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
            'setting_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'setting_value' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
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
        $this->forge->addUniqueKey('setting_name');
        $this->forge->createTable('system_controls');
    }

    public function down()
    {
        $this->forge->dropTable('system_controls');
    }
}

