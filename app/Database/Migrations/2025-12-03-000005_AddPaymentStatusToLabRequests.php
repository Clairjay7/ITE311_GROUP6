<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentStatusToLabRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lab_requests', [
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['unpaid', 'paid', 'pending'],
                'default' => 'unpaid',
                'after' => 'status',
            ],
            'charge_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'payment_status',
            ],
        ]);

        // Add index for faster queries
        $this->forge->addKey('payment_status');
        $this->forge->addKey('charge_id');
    }

    public function down()
    {
        $this->forge->dropColumn('lab_requests', ['payment_status', 'charge_id']);
    }
}

