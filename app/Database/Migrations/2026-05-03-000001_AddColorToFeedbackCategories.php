<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColorToFeedbackCategories extends Migration
{
    public function up()
    {
        $this->forge->addColumn('feedback_categories', [
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null'       => true,
                'default'    => null,
                'after'      => 'name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('feedback_categories', 'color');
    }
}
