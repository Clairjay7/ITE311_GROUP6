<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForAdmissionToConsultations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('consultations', [
            'for_admission' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'comment' => '1 if patient is marked for admission, 0 otherwise',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('consultations', 'for_admission');
    }
}

