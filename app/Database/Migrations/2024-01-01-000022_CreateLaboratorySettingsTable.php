<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaboratorySettingsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('laboratory_settings')) {
            echo "Laboratory settings table already exists, skipping creation..." . PHP_EOL;
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'setting_value' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'general',
            ],
            'description' => [
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
        $this->forge->addKey(['category', 'setting_name']);
        $this->forge->createTable('laboratory_settings');
    }

    public function down()
    {
        $this->forge->dropTable('laboratory_settings', true);
    }
}
