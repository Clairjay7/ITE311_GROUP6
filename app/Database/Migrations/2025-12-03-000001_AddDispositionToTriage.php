<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDispositionToTriage extends Migration
{
    public function up()
    {
        $fields = [
            'disposition' => [
                'type' => 'ENUM',
                'constraint' => ['ER', 'OPD', 'Admission', 'Pending'],
                'default' => 'Pending',
                'null' => false,
                'after' => 'triage_level',
                'comment' => 'ER = Emergency Room, OPD = Out-Patient Department'
            ],
            'for_admission' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'disposition',
                'comment' => '1 = Marked for admission, 0 = Not for admission'
            ],
            'er_bed_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'for_admission',
                'comment' => 'ER bed assignment if disposition is ER'
            ],
            'opd_queue_number' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'er_bed_id',
                'comment' => 'OPD queue number if disposition is OPD'
            ]
        ];

        $this->forge->addColumn('triage', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('triage', ['disposition', 'for_admission', 'er_bed_id', 'opd_queue_number']);
    }
}

