<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinanceOverviewTable extends Migration
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
            'period_type' => [
                'type'       => 'ENUM',
                'constraint' => ['daily', 'weekly', 'monthly', 'yearly'],
                'default'    => 'monthly',
            ],
            'period_start' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'period_end' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'total_revenue' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'total_expenses' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'net_profit' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'total_bills' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'paid_bills' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'pending_bills' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'insurance_claims_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('period_type');
        $this->forge->addKey('period_start');
        $this->forge->addKey('period_end');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('finance_overview');
    }

    public function down()
    {
        $this->forge->dropTable('finance_overview');
    }
}

