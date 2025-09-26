<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabInventoryTable extends Migration
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
            'item_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'item_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['reagent', 'consumable', 'equipment', 'kit', 'control', 'calibrator'],
                'default' => 'consumable',
            ],
            'manufacturer' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'lot_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'current_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'minimum_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 10,
            ],
            'maximum_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 100,
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'piece',
            ],
            'unit_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'storage_location' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'storage_conditions' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'supplier' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'expired', 'recalled'],
                'default' => 'active',
            ],
            'notes' => [
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
        $this->forge->addKey(['category', 'status']);
        $this->forge->addKey(['expiry_date', 'status']);
        $this->forge->createTable('lab_inventory');
    }

    public function down()
    {
        $this->forge->dropTable('lab_inventory');
    }
}
