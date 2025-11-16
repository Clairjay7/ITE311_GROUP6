<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPatientsAddWardAndRoomId extends Migration
{
    public function up()
    {
        $fields = [
            'ward' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'room_number',
            ],
            'room_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'ward',
            ],
        ];

        $this->forge->addColumn('patients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('patients', ['ward', 'room_id']);
    }
}
