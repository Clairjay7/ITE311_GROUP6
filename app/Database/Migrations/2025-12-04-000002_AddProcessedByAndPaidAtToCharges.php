<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProcessedByAndPaidAtToCharges extends Migration
{
    public function up()
    {
        // Check if columns don't exist before adding
        if (!$this->db->fieldExists('processed_by', 'charges')) {
            $this->forge->addColumn('charges', [
                'processed_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'notes',
                    'comment' => 'User who processed the payment',
                ],
            ]);
            
            // Add foreign key if users table exists
            if ($this->db->tableExists('users')) {
                try {
                    $this->forge->addForeignKey('processed_by', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_charges_processed_by');
                } catch (\Exception $e) {
                    // Ignore if foreign key already exists
                }
            }
        }
        
        if (!$this->db->fieldExists('paid_at', 'charges')) {
            $this->forge->addColumn('charges', [
                'paid_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'processed_by',
                    'comment' => 'Date and time when payment was received',
                ],
            ]);
        }
    }

    public function down()
    {
        // Drop foreign key first
        if ($this->db->tableExists('charges') && $this->db->fieldExists('processed_by', 'charges')) {
            try {
                $this->forge->dropForeignKey('charges', 'fk_charges_processed_by');
            } catch (\Exception $e) {
                // Ignore if foreign key doesn't exist
            }
        }
        
        // Drop columns
        if ($this->db->tableExists('charges')) {
            $columnsToDrop = [];
            if ($this->db->fieldExists('processed_by', 'charges')) {
                $columnsToDrop[] = 'processed_by';
            }
            if ($this->db->fieldExists('paid_at', 'charges')) {
                $columnsToDrop[] = 'paid_at';
            }
            
            if (!empty($columnsToDrop)) {
                $this->forge->dropColumn('charges', $columnsToDrop);
            }
        }
    }
}

