<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditLogsTable extends Migration
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
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'comment' => 'Action performed (e.g., doctor_assignment, patient_registration)',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User who performed the action',
            ],
            'user_role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Role of the user',
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Name of the user',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Human-readable description of the action',
            ],
            'related_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID of related record (patient_id, doctor_id, etc.)',
            ],
            'related_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Type of related record (patient, doctor, appointment, etc.)',
            ],
            'metadata' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON metadata with additional details',
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'comment' => 'IP address of the user',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('action');
        $this->forge->addKey('user_id');
        $this->forge->addKey('related_id');
        $this->forge->addKey('created_at');
        
        if ($this->db->tableExists('users')) {
            $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_audit_logs_user');
        }
        
        $this->forge->createTable('audit_logs');
    }

    public function down()
    {
        if ($this->db->tableExists('audit_logs')) {
            $this->forge->dropForeignKey('audit_logs', 'fk_audit_logs_user');
        }
        $this->forge->dropTable('audit_logs');
    }
}

