<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDischargeStatusToAdmissions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admissions', [
            'discharge_status' => [
                'type' => 'ENUM',
                'constraint' => ['admitted', 'discharge_pending', 'discharged', 'cancelled'],
                'default' => 'admitted',
                'after' => 'status',
                'comment' => 'Discharge workflow status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admissions', 'discharge_status');
    }
}

