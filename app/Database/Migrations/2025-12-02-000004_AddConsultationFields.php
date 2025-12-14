<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConsultationFields extends Migration
{
    public function up()
    {
        // Add chief_complaint field
        if ($this->db->fieldExists('chief_complaint', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'chief_complaint' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Patient chief complaint',
                    'after' => 'consultation_time',
                ],
            ]);
        }

        // Add prescriptions field (JSON)
        if ($this->db->fieldExists('prescriptions', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'prescriptions' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'JSON array of prescription medicine IDs',
                    'after' => 'diagnosis',
                ],
            ]);
        }

        // Add prescription_details field (JSON)
        if ($this->db->fieldExists('prescription_details', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'prescription_details' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'JSON array of prescription details (dosage, frequency, etc.)',
                    'after' => 'prescriptions',
                ],
            ]);
        }

        // Add lab_tests field (JSON)
        if ($this->db->fieldExists('lab_tests', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'lab_tests' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'JSON array of lab test IDs',
                    'after' => 'prescription_details',
                ],
            ]);
        }

        // Add follow_up field
        if ($this->db->fieldExists('follow_up', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'follow_up' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'null' => false,
                    'comment' => 'Whether follow-up is scheduled',
                    'after' => 'lab_tests',
                ],
            ]);
        }

        // Add follow_up_date field
        if ($this->db->fieldExists('follow_up_date', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'follow_up_date' => [
                    'type' => 'DATE',
                    'null' => true,
                    'comment' => 'Follow-up consultation date',
                    'after' => 'follow_up',
                ],
            ]);
        }

        // Add follow_up_time field
        if ($this->db->fieldExists('follow_up_time', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'follow_up_time' => [
                    'type' => 'TIME',
                    'null' => true,
                    'comment' => 'Follow-up consultation time',
                    'after' => 'follow_up_date',
                ],
            ]);
        }

        // Add follow_up_reason field
        if ($this->db->fieldExists('follow_up_reason', 'consultations') === false) {
            $this->forge->addColumn('consultations', [
                'follow_up_reason' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Reason for follow-up consultation',
                    'after' => 'follow_up_time',
                ],
            ]);
        }

        // Update type enum to include 'consultation'
        $this->db->query("ALTER TABLE consultations MODIFY COLUMN type ENUM('upcoming', 'completed', 'consultation') NOT NULL DEFAULT 'upcoming'");

        // Update status enum to include 'completed'
        $this->db->query("ALTER TABLE consultations MODIFY COLUMN status ENUM('pending', 'approved', 'cancelled', 'completed', 'upcoming') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        if ($this->db->fieldExists('chief_complaint', 'consultations')) {
            $this->forge->dropColumn('consultations', 'chief_complaint');
        }
        if ($this->db->fieldExists('prescriptions', 'consultations')) {
            $this->forge->dropColumn('consultations', 'prescriptions');
        }
        if ($this->db->fieldExists('prescription_details', 'consultations')) {
            $this->forge->dropColumn('consultations', 'prescription_details');
        }
        if ($this->db->fieldExists('lab_tests', 'consultations')) {
            $this->forge->dropColumn('consultations', 'lab_tests');
        }
        if ($this->db->fieldExists('follow_up', 'consultations')) {
            $this->forge->dropColumn('consultations', 'follow_up');
        }
        if ($this->db->fieldExists('follow_up_date', 'consultations')) {
            $this->forge->dropColumn('consultations', 'follow_up_date');
        }
        if ($this->db->fieldExists('follow_up_time', 'consultations')) {
            $this->forge->dropColumn('consultations', 'follow_up_time');
        }
        if ($this->db->fieldExists('follow_up_reason', 'consultations')) {
            $this->forge->dropColumn('consultations', 'follow_up_reason');
        }
    }
}

