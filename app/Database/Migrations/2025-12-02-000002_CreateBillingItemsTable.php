<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillingItemsTable extends Migration
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
            'charge_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Links to charges table',
            ],
            'item_type' => [
                'type' => 'ENUM',
                'constraint' => ['consultation', 'lab_test', 'medication', 'procedure', 'other'],
                'null' => false,
            ],
            'item_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 1.00,
                'null' => false,
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'related_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID of related record (lab_request_id, order_id, etc.)',
            ],
            'related_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Type of related record (lab_request, doctor_order, etc.)',
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
        $this->forge->addKey('charge_id');
        $this->forge->addKey('related_id');
        $this->forge->addForeignKey('charge_id', 'charges', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('billing_items');
    }

    public function down()
    {
        $this->forge->dropTable('billing_items');
    }
}

