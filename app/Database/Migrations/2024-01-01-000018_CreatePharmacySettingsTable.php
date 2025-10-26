<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePharmacySettingsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('pharmacy_settings')) {
            echo "Pharmacy settings table already exists, skipping creation..." . PHP_EOL;
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
                'type' => 'TEXT',
                'null' => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addKey('category');
        $this->forge->addKey('setting_name');
        $this->forge->createTable('pharmacy_settings');
    }

    public function down()
    {
        $this->forge->dropTable('pharmacy_settings', true);
    }
}
