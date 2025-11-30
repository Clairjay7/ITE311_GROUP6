<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminPatientsTable extends Migration
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
            'firstname' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'birthdate' => [
                'type' => 'DATE',
            ],
            'gender' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'contact' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('admin_patients');
    }

    public function down()
    {
        $this->forge->dropTable('admin_patients');
    }
}

