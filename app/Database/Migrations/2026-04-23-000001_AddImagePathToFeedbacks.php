<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagePathToFeedbacks extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('image_path', 'feedbacks')) {
            return;
        }

        $this->forge->addColumn('feedbacks', [
            'image_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'default'    => null,
                'after'      => 'message',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('feedbacks', 'image_path');
    }
}
