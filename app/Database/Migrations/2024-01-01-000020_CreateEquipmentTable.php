<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEquipmentTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('equipment')) {
            echo "Equipment table already exists, skipping creation..." . PHP_EOL;
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'equipment_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'equipment_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'unique'     => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['available', 'in_use', 'under_maintenance', 'out_of_service'],
                'default'    => 'available',
            ],
            'last_maintenance_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'next_maintenance_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'last_calibration_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'next_calibration_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'usage_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'condition' => [
                'type'       => 'ENUM',
                'constraint' => ['good', 'needs_service', 'damaged'],
                'default'    => 'good',
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
        $this->forge->addKey('status');
        $this->forge->addKey('equipment_type');
        $this->forge->createTable('equipment');
    }

    public function down()
    {
        $this->forge->dropTable('equipment', true);
    }
}
