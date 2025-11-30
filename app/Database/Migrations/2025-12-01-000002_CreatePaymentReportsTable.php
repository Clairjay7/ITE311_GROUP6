<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentReportsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'report_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'billing_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'payment_method' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'check', 'insurance', 'other'],
                'default'    => 'cash',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
            ],
            'reference_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'completed', 'failed', 'refunded'],
                'default'    => 'completed',
            ],
            'payment_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'processed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('report_date');
        $this->forge->addKey('patient_id');
        $this->forge->addKey('billing_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('patient_id', 'admin_patients', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('processed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('payment_reports');
    }

    public function down()
    {
        $this->forge->dropTable('payment_reports');
    }
}

