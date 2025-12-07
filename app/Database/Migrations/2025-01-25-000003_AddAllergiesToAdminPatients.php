<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAllergiesToAdminPatients extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check if admin_patients table exists and if allergies column already exists
        if ($db->tableExists('admin_patients')) {
            $fields = $db->getFieldData('admin_patients');
            $fieldNames = array_column($fields, 'name');
            
            if (!in_array('allergies', $fieldNames)) {
                $this->forge->addColumn('admin_patients', [
                    'allergies' => [
                        'type' => 'TEXT',
                        'null' => true,
                        'after' => 'address',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropColumn('admin_patients', ['allergies']);
    }
}

