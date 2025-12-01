<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddObservationsAndDiagnosisToConsultations extends Migration
{
    public function up()
    {
        // Add observations field
        if ($this->db->fieldExists('observations', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'observations' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Doctor observations and findings during consultation',
                    'after' => 'notes',
                ],
            ]);
        }

        // Add diagnosis field
        if ($this->db->fieldExists('diagnosis', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'diagnosis' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Doctor diagnosis for the patient',
                    'after' => 'observations',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('observations', 'consultations')) {
            $this->forge->dropColumn('consultations', 'observations');
        }
        if ($this->db->fieldExists('diagnosis', 'consultations')) {
            $this->forge->dropColumn('consultations', 'diagnosis');
        }
    }
}
