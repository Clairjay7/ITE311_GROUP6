<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPatientsAddRegistrationFields extends Migration
{
    public function up()
    {
        $fields = [
            'patient_reg_no' => [ 'type' => 'VARCHAR', 'constraint' => 30, 'null' => true ],
            'first_name' => [ 'type' => 'VARCHAR', 'constraint' => 60, 'null' => true ],
            'middle_name' => [ 'type' => 'VARCHAR', 'constraint' => 60, 'null' => true ],
            'last_name' => [ 'type' => 'VARCHAR', 'constraint' => 60, 'null' => true ],
            'date_of_birth' => [ 'type' => 'DATE', 'null' => true ],
            'civil_status' => [ 'type' => 'VARCHAR', 'constraint' => 20, 'null' => true ],
            'address_street' => [ 'type' => 'VARCHAR', 'constraint' => 120, 'null' => true ],
            'address_barangay' => [ 'type' => 'VARCHAR', 'constraint' => 120, 'null' => true ],
            'address_city' => [ 'type' => 'VARCHAR', 'constraint' => 120, 'null' => true ],
            'address_province' => [ 'type' => 'VARCHAR', 'constraint' => 120, 'null' => true ],
            'email' => [ 'type' => 'VARCHAR', 'constraint' => 120, 'null' => true ],
            'nationality' => [ 'type' => 'VARCHAR', 'constraint' => 60, 'null' => true ],
            'religion' => [ 'type' => 'VARCHAR', 'constraint' => 60, 'null' => true ],
            'emergency_name' => [ 'type' => 'VARCHAR', 'constraint' => 120, 'null' => true ],
            'emergency_relationship' => [ 'type' => 'VARCHAR', 'constraint' => 60, 'null' => true ],
            'emergency_contact' => [ 'type' => 'VARCHAR', 'constraint' => 30, 'null' => true ],
            'emergency_address' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
            'blood_type' => [ 'type' => 'VARCHAR', 'constraint' => 5, 'null' => true ],
            'allergies' => [ 'type' => 'TEXT', 'null' => true ],
            'existing_conditions' => [ 'type' => 'TEXT', 'null' => true ],
            'current_medications' => [ 'type' => 'TEXT', 'null' => true ],
            'past_surgeries' => [ 'type' => 'TEXT', 'null' => true ],
            'family_history' => [ 'type' => 'TEXT', 'null' => true ],
            'insurance_provider' => [ 'type' => 'VARCHAR', 'constraint' => 120, 'null' => true ],
            'insurance_number' => [ 'type' => 'VARCHAR', 'constraint' => 80, 'null' => true ],
            'philhealth_number' => [ 'type' => 'VARCHAR', 'constraint' => 50, 'null' => true ],
            'billing_address' => [ 'type' => 'TEXT', 'null' => true ],
            'payment_type' => [ 'type' => 'VARCHAR', 'constraint' => 20, 'null' => true ],
            'registration_date' => [ 'type' => 'DATE', 'null' => true ],
            'registered_by' => [ 'type' => 'VARCHAR', 'constraint' => 80, 'null' => true ],
            'signature_patient' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
            'signature_staff' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
            'date_signed' => [ 'type' => 'DATE', 'null' => true ],
        ];

        $this->forge->addColumn('patients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('patients', [
            'patient_reg_no','first_name','middle_name','last_name','date_of_birth','civil_status',
            'address_street','address_barangay','address_city','address_province','email','nationality','religion',
            'emergency_name','emergency_relationship','emergency_contact','emergency_address',
            'blood_type','allergies','existing_conditions','current_medications','past_surgeries','family_history',
            'insurance_provider','insurance_number','philhealth_number','billing_address','payment_type',
            'registration_date','registered_by','signature_patient','signature_staff','date_signed'
        ]);
    }
}
