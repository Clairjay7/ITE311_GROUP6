<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePatientProgressTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'round_date' => [
                'type' => 'DATE',
            ],
            'round_time' => [
                'type' => 'TIME',
            ],
            'shift' => [
                'type' => 'ENUM',
                'constraint' => ['morning', 'afternoon', 'night'],
            ],
            'general_condition' => [
                'type' => 'ENUM',
                'constraint' => ['excellent', 'good', 'fair', 'poor', 'critical'],
                'default' => 'fair',
            ],
            'mobility' => [
                'type' => 'ENUM',
                'constraint' => ['independent', 'assisted', 'bed_bound'],
                'default' => 'independent',
            ],
            'appetite' => [
                'type' => 'ENUM',
                'constraint' => ['good', 'fair', 'poor', 'none'],
                'default' => 'good',
            ],
            'sleep_quality' => [
                'type' => 'ENUM',
                'constraint' => ['good', 'fair', 'poor', 'restless'],
                'default' => 'good',
            ],
            'medications_given' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'treatments_performed' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'observations' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'concerns' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'next_round_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['patient_id', 'round_date', 'shift']);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nurse_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_progress');
    }

    public function down()
    {
        $this->forge->dropTable('patient_progress');
    }
}
