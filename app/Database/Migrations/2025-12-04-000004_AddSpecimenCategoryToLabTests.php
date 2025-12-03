<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSpecimenCategoryToLabTests extends Migration
{
    public function up()
    {
        // Add specimen_category column to lab_tests table
        $this->forge->addColumn('lab_tests', [
            'specimen_category' => [
                'type' => 'ENUM',
                'constraint' => ['with_specimen', 'without_specimen'],
                'default' => 'with_specimen',
                'after' => 'test_type',
                'comment' => 'Category: with_specimen (requires physical specimen) or without_specimen (no physical specimen needed)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('lab_tests', 'specimen_category');
    }
}

