<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNurseRecommendationToTriage extends Migration
{
    public function up()
    {
        $fields = [
            'nurse_recommendation' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'notes',
                'comment' => 'Nurse assessment and recommendation for doctor review'
            ]
        ];

        $this->forge->addColumn('triage', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('triage', ['nurse_recommendation']);
    }
}

