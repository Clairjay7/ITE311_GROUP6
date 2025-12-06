<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdToDoctorsTable extends Migration
{
    public function up()
    {
        $fields = [
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'specialization',
            ],
        ];
        $this->forge->addColumn('doctors', $fields);
        
        // Add foreign key constraint if users table exists
        if ($this->db->tableExists('users')) {
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL', 'doctors_user_id_fk');
        }
    }

    public function down()
    {
        // Drop foreign key first
        if ($this->db->tableExists('doctors')) {
            $this->forge->dropForeignKey('doctors', 'doctors_user_id_fk');
        }
        $this->forge->dropColumn('doctors', 'user_id');
    }
}

