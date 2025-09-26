<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSecuritySettingsTable extends Migration
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
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
            ],
            'setting_type' => [
                'type' => 'ENUM',
                'constraint' => ['string', 'integer', 'boolean', 'json', 'encrypted'],
                'default' => 'string',
            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['authentication', 'password_policy', 'session', 'encryption', 'backup', 'system', 'security'],
                'default' => 'system',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_editable' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'requires_restart' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'validation_rule' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'default_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'last_modified_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'last_modified_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey(['category', 'setting_key']);
        $this->forge->addForeignKey('last_modified_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('security_settings');
    }

    public function down()
    {
        $this->forge->dropTable('security_settings');
    }
}
