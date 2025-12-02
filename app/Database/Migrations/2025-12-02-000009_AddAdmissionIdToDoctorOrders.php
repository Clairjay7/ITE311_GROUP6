<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdmissionIdToDoctorOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('doctor_orders', [
            'admission_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'patient_id',
                'comment' => 'Link to admissions table for admission-specific orders',
            ],
        ]);

        // Add foreign key if admissions table exists
        if ($this->db->tableExists('admissions')) {
            $this->forge->addForeignKey('admission_id', 'admissions', 'id', 'CASCADE', 'CASCADE', 'fk_doctor_orders_admission');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('doctor_orders')) {
            // Drop foreign key first
            if ($this->db->fieldExists('admission_id', 'doctor_orders')) {
                $this->forge->dropForeignKey('doctor_orders', 'fk_doctor_orders_admission');
            }
            $this->forge->dropColumn('doctor_orders', 'admission_id');
        }
    }
}


