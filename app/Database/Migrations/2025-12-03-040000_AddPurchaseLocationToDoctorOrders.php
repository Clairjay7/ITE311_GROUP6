<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPurchaseLocationToDoctorOrders extends Migration
{
    public function up()
    {
        $fields = [
            'purchase_location' => [
                'type' => 'ENUM',
                'constraint' => ['hospital_pharmacy', 'outside'],
                'null' => true,
                'default' => null,
                'after' => 'pharmacy_dispensed_at',
                'comment' => 'Where patient will purchase medication: hospital_pharmacy or outside'
            ],
        ];
        
        $this->forge->addColumn('doctor_orders', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('doctor_orders', 'purchase_location');
    }
}


