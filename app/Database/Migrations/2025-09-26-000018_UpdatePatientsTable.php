<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePatientsTable extends Migration
{
    public function up()
    {
        // Add additional fields to patients table for better receptionist functionality
        $fields = [
            'patient_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
                'after' => 'id',
            ],
            'emergency_contact_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'address',
            ],
            'emergency_contact_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'emergency_contact_name',
            ],
            'government_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'emergency_contact_phone',
            ],
            'blood_type' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
                'after' => 'government_id',
            ],
            'allergies' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'blood_type',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'deceased'],
                'default' => 'active',
                'after' => 'allergies',
            ],
        ];

        $this->forge->addColumn('patients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('patients', ['patient_id', 'emergency_contact_name', 'emergency_contact_phone', 'government_id', 'blood_type', 'allergies', 'status']);
    }
}
