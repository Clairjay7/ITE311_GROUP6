<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillingTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('billing')) {
            echo "Billing table already exists, skipping creation...\n";
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bill_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'service_type' => [
                'type'       => 'ENUM',
                'constraint' => ['consultation', 'laboratory', 'pharmacy', 'surgery', 'emergency', 'other'],
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'overdue', 'cancelled'],
                'default'    => 'pending',
            ],
            'payment_method' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'card', 'bank_transfer', 'insurance', 'installment'],
                'null'       => true,
            ],
            'paid_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        
        // Try to add unique key, but don't fail if it exists
        try {
            $this->forge->addUniqueKey('bill_id');
        } catch (\Exception $e) {
            echo "Bill ID unique key already exists, skipping...\n";
        }
        
        $this->forge->addKey('patient_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('billing');
    }

    public function down()
    {
        $this->forge->dropTable('billing');
    }
}
