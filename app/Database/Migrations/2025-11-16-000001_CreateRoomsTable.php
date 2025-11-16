<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoomsTable extends Migration
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
            'ward' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'room_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Available',
                'comment'    => 'Available or Occupied',
            ],
            'current_patient_id' => [
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
        $this->forge->createTable('rooms');

        // Seed a few default rooms per ward
        $db = \Config\Database::connect();
        $builder = $db->table('rooms');
        $rooms = [];
        foreach (['Pedia Ward', 'Male Ward', 'Female Ward'] as $ward) {
            for ($i = 1; $i <= 5; $i++) {
                $rooms[] = [
                    'ward'         => $ward,
                    'room_number'  => substr($ward, 0, 1) . sprintf('%02d', $i),
                    'status'       => 'Available',
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];
            }
        }
        if (!empty($rooms)) {
            $builder->insertBatch($rooms);
        }
    }

    public function down()
    {
        $this->forge->dropTable('rooms');
    }
}
