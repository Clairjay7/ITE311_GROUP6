<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderDescriptionToBilling extends Migration
{
    public function up()
    {
        // Add order_description column to billing table for IV Fluids
        if ($this->db->tableExists('billing')) {
            // Check if column already exists
            $fields = $this->db->getFieldData('billing');
            $columnExists = false;
            foreach ($fields as $field) {
                if ($field->name === 'order_description') {
                    $columnExists = true;
                    break;
                }
            }
            
            if (!$columnExists) {
                $this->forge->addColumn('billing', [
                    'order_description' => [
                        'type' => 'TEXT',
                        'null' => true,
                        'after' => 'dosage',
                        'comment' => 'Full order description for IV Fluids and other orders',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('billing')) {
            $this->forge->dropColumn('billing', 'order_description');
        }
    }
}

