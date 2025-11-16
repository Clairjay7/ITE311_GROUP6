<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExtensionNameToPatients extends Migration
{
    public function up()
    {
        $fields = [
            'extension_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'last_name',
            ],
        ];

        $this->forge->addColumn('patients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('patients', ['extension_name']);
    }
}
