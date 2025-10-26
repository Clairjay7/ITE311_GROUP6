<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabReportsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('lab_reports')) {
            echo "Lab reports table already exists, skipping creation..." . PHP_EOL;
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'report_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'generated_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
            ],
            'report_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'report_period_start' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'report_period_end' => [
                'type' => 'DATE',
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
        $this->forge->addKey('report_type');
        $this->forge->createTable('lab_reports');
    }

    public function down()
    {
        $this->forge->dropTable('lab_reports', true);
    }
}
