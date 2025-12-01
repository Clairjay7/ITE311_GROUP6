<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMedicationBillingFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('billing', [
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'patient_id',
                'comment' => 'Reference to doctor_orders.id for medication orders',
            ],
            'medicine_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'service',
            ],
            'dosage' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'medicine_name',
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'null' => true,
                'after' => 'dosage',
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'quantity',
                'comment' => 'Price per unit from pharmacy',
            ],
            'administration_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => true,
                'after' => 'unit_price',
                'comment' => 'Additional fee for medication administration',
            ],
            'nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'administration_fee',
                'comment' => 'Nurse who administered the medication',
            ],
            'administered_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'nurse_id',
                'comment' => 'Timestamp when medication was administered',
            ],
            'invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'status',
                'comment' => 'Auto-generated invoice number',
            ],
            'processed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'invoice_number',
                'comment' => 'Accountant/Admin who processed the payment',
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'processed_by',
                'comment' => 'Timestamp when payment was processed',
            ],
        ]);

        // Add foreign keys
        if ($this->db->tableExists('doctor_orders')) {
            $this->forge->addForeignKey('order_id', 'doctor_orders', 'id', 'CASCADE', 'CASCADE', 'fk_billing_order');
        }
        if ($this->db->tableExists('users')) {
            $this->forge->addForeignKey('nurse_id', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_billing_nurse');
            $this->forge->addForeignKey('processed_by', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_billing_processed_by');
        }
    }

    public function down()
    {
        // Drop foreign keys first
        if ($this->db->tableExists('billing')) {
            $this->forge->dropForeignKey('billing', 'fk_billing_order');
            $this->forge->dropForeignKey('billing', 'fk_billing_nurse');
            $this->forge->dropForeignKey('billing', 'fk_billing_processed_by');
        }

        $this->forge->dropColumn('billing', [
            'order_id',
            'medicine_name',
            'dosage',
            'quantity',
            'unit_price',
            'administration_fee',
            'nurse_id',
            'administered_at',
            'invoice_number',
            'processed_by',
            'paid_at',
        ]);
    }
}

