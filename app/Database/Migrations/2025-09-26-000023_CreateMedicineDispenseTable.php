<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMedicineDispenseTable extends Migration
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
            'prescription_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'pharmacy_inventory_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'dispensed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'dispense_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'quantity_prescribed' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'quantity_dispensed' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'dispensed_at' => [
                'type' => 'DATETIME',
            ],
            'batch_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'instructions_given' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'counseling_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'patient_acknowledged' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['dispensed', 'partial', 'returned', 'cancelled'],
                'default' => 'dispensed',
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
        $this->forge->addKey(['patient_id', 'dispensed_at']);
        $this->forge->addKey(['dispensed_by', 'dispensed_at']);
        $this->forge->addForeignKey('prescription_id', 'prescriptions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pharmacy_inventory_id', 'pharmacy_inventory', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dispensed_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('medicine_dispense');
    }

    public function down()
    {
        $this->forge->dropTable('medicine_dispense');
    }
}
