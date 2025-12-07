<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInventoryFieldsToPharmacy extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check if columns exist before adding
        $fields = $db->getFieldData('pharmacy');
        $fieldNames = array_column($fields, 'name');
        
        // Add columns one by one to handle 'after' positioning correctly
        if (!in_array('generic_name', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'generic_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'item_name',
                ],
            ]);
        }
        
        if (!in_array('category', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'category' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'generic_name',
                ],
            ]);
        }
        
        if (!in_array('strength', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'strength' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'category',
                ],
            ]);
        }
        
        if (!in_array('dosage_form', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'dosage_form' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'strength',
                ],
            ]);
        }
        
        if (!in_array('batch_number', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'batch_number' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'quantity',
                ],
            ]);
        }
        
        if (!in_array('expiration_date', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'expiration_date' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'batch_number',
                ],
            ]);
        }
        
        if (!in_array('reorder_level', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'reorder_level' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 10,
                    'after' => 'expiration_date',
                ],
            ]);
        }
        
        if (!in_array('supplier_name', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'supplier_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'price',
                ],
            ]);
        }
        
        if (!in_array('supplier_contact', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'supplier_contact' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'supplier_name',
                ],
            ]);
        }
        
        if (!in_array('unit_price', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'unit_price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => true,
                    'after' => 'supplier_contact',
                ],
            ]);
        }
        
        if (!in_array('selling_price', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'selling_price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => true,
                    'after' => 'unit_price',
                ],
            ]);
        }
        
        if (!in_array('markup_percent', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'markup_percent' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                    'after' => 'selling_price',
                ],
            ]);
        }
        
        if (!in_array('status', $fieldNames)) {
            $this->forge->addColumn('pharmacy', [
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default' => 'active',
                    'after' => 'markup_percent',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('pharmacy', [
            'generic_name', 'category', 'strength', 'dosage_form',
            'batch_number', 'expiration_date', 'reorder_level',
            'supplier_name', 'supplier_contact', 'unit_price',
            'selling_price', 'markup_percent', 'status'
        ]);
    }
}

