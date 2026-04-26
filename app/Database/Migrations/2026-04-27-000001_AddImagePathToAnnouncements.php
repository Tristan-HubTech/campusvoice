<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagePathToAnnouncements extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('image_path', 'announcements')) {
            return;
        }

        $this->forge->addColumn('announcements', [
            'image_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'default'    => null,
                'after'      => 'body',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('announcements', 'image_path');
    }
}
