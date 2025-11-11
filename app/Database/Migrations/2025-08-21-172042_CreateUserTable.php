<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration
{
    public function up()
    {
        // Create roles table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'description' => [
                'type'       => 'TEXT',
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
        $this->forge->createTable('roles');

        // Insert default roles
        $defaultRoles = [
            ['name' => 'admin', 'description' => 'Administrator with full access'],
            ['name' => 'doctor', 'description' => 'Medical doctor'],
            ['name' => 'nurse', 'description' => 'Nursing staff'],
            ['name' => 'receptionist', 'description' => 'Front desk staff'],
            ['name' => 'patient', 'description' => 'Patient']
        ];

        foreach ($defaultRoles as &$role) {
            $role['created_at'] = date('Y-m-d H:i:s');
            $role['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('roles')->insertBatch($defaultRoles);

        // Create users table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 5, // Default to patient role
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('users', true);
    }

    public function down()
    {
        // Drop tables in reverse order
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('roles', true);
    }
}
