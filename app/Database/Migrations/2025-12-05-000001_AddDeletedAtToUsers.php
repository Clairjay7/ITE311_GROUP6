<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToUsers extends Migration
{
    public function up()
    {
        // Check if column already exists
        $fields = $this->db->getFieldData('users');
        $columnExists = false;
        foreach ($fields as $field) {
            if ($field->name === 'deleted_at') {
                $columnExists = true;
                break;
            }
        }

        if (!$columnExists) {
            $this->forge->addColumn('users', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'default' => null,
                ],
            ]);
        }
    }

    public function down()
    {
        // Check if column exists before dropping
        $fields = $this->db->getFieldData('users');
        $columnExists = false;
        foreach ($fields as $field) {
            if ($field->name === 'deleted_at') {
                $columnExists = true;
                break;
            }
        }

        if ($columnExists) {
            $this->forge->dropColumn('users', 'deleted_at');
        }
    }
}

