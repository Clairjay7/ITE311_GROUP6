<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNurseIdToDoctorOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('doctor_orders', [
            'nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'doctor_id',
            ],
        ]);

        $this->forge->addForeignKey('nurse_id', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('doctor_orders', 'doctor_orders_nurse_id_foreign');
        $this->forge->dropColumn('doctor_orders', 'nurse_id');
    }
}

