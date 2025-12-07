<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserFields extends Migration
{
    public function up()
    {
        // Add new columns to users table
        $fields = [
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'email',
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'first_name',
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'middle_name',
            ],
            'contact' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'last_name',
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'contact',
            ],
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'address',
            ],
            'prc_license' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'employee_id',
            ],
            'nursing_license' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'prc_license',
            ],
            'specialization' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'nursing_license',
            ],
        ];

        foreach ($fields as $fieldName => $fieldDef) {
            // Check if column already exists
            if (!$this->db->fieldExists($fieldName, 'users')) {
                $this->forge->addColumn('users', [$fieldName => $fieldDef]);
            }
        }
    }

    public function down()
    {
        // Remove the columns
        $fieldsToRemove = [
            'first_name',
            'middle_name',
            'last_name',
            'contact',
            'address',
            'employee_id',
            'prc_license',
            'nursing_license',
            'specialization',
        ];

        foreach ($fieldsToRemove as $fieldName) {
            if ($this->db->fieldExists($fieldName, 'users')) {
                $this->forge->dropColumn('users', $fieldName);
            }
        }
    }
}

