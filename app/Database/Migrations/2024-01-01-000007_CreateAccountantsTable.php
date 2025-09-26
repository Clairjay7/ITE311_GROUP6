<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountantsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('accountants')) {
            echo "Accountants table already exists, skipping creation...\n";
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'employee_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'department' => [
                'type'       => 'ENUM',
                'constraint' => ['billing', 'payroll', 'accounts_receivable', 'accounts_payable', 'general'],
                'default'    => 'general',
            ],
            'access_level' => [
                'type'       => 'ENUM',
                'constraint' => ['junior', 'senior', 'supervisor', 'manager'],
                'default'    => 'junior',
            ],
            'certifications' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'years_experience' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'billing_access' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
            'financial_reports_access' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'payment_processing_access' => [
                'type'    => 'BOOLEAN',
                'default' => true,
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
        $this->forge->addKey('user_id');
        
        // Try to add unique key, but don't fail if it exists
        try {
            $this->forge->addUniqueKey('employee_id');
        } catch (\Exception $e) {
            echo "Employee ID unique key already exists, skipping...\n";
        }
        
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('accountants');
    }

    public function down()
    {
        $this->forge->dropTable('accountants');
    }
}
