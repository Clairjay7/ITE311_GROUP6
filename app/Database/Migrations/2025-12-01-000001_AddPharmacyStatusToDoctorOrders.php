<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPharmacyStatusToDoctorOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('doctor_orders', [
            'medicine_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'order_description',
            ],
            'dosage' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'medicine_name',
            ],
            'duration' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'frequency',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'instructions',
            ],
            'pharmacy_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'prepared', 'dispensed'],
                'default' => 'pending',
                'null' => true,
                'after' => 'status',
            ],
            'pharmacy_approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'pharmacy_status',
            ],
            'pharmacy_prepared_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'pharmacy_approved_at',
            ],
            'pharmacy_dispensed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'pharmacy_prepared_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('doctor_orders', [
            'medicine_name',
            'dosage',
            'duration',
            'remarks',
            'pharmacy_status',
            'pharmacy_approved_at',
            'pharmacy_prepared_at',
            'pharmacy_dispensed_at',
        ]);
    }
}

