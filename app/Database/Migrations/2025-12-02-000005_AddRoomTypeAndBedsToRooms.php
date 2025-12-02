<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoomTypeAndBedsToRooms extends Migration
{
    public function up()
    {
        // Add room_type column
        $this->forge->addColumn('rooms', [
            'room_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'default' => 'Ward',
                'comment' => 'Private, Semi-Private, Ward, ICU, etc.',
                'after' => 'ward',
            ],
        ]);

        // Add bed_count column
        $this->forge->addColumn('rooms', [
            'bed_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 1,
                'null' => false,
                'after' => 'room_type',
            ],
        ]);

        // Create beds table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'room_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'bed_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['available', 'occupied', 'maintenance'],
                'default' => 'available',
                'null' => false,
            ],
            'current_patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('room_id');
        $this->forge->addForeignKey('room_id', 'rooms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('beds');
    }

    public function down()
    {
        $this->forge->dropTable('beds');
        $this->forge->dropColumn('rooms', 'bed_count');
        $this->forge->dropColumn('rooms', 'room_type');
    }
}

