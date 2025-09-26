<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoomsTable extends Migration
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
            'room_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'department_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'room_type' => [
                'type' => 'ENUM',
                'constraint' => ['consultation', 'surgery', 'ward', 'icu', 'emergency', 'lab', 'pharmacy', 'admin'],
                'default' => 'consultation',
            ],
            'capacity' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 1,
            ],
            'current_occupancy' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
            ],
            'floor' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
            ],
            'equipment' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['available', 'occupied', 'maintenance', 'reserved'],
                'default' => 'available',
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
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rooms');
    }

    public function down()
    {
        $this->forge->dropTable('rooms');
    }
}
