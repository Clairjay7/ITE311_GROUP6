<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePharmacyInventoryTable extends Migration
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
            'medicine_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'medicine_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'generic_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'brand_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['tablet', 'capsule', 'syrup', 'injection', 'cream', 'drops', 'inhaler', 'other'],
                'default' => 'tablet',
            ],
            'strength' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'manufacturer' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'batch_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'manufacturing_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'expiry_date' => [
                'type' => 'DATE',
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
                'default' => 1000,
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'piece',
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'selling_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
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
            'prescription_required' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'expired', 'recalled', 'out_of_stock'],
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
        $this->forge->addKey(['medicine_name', 'status']);
        $this->forge->addKey(['expiry_date', 'status']);
        $this->forge->addKey(['category', 'status']);
        $this->forge->createTable('pharmacy_inventory');
    }

    public function down()
    {
        $this->forge->dropTable('pharmacy_inventory');
    }
}
