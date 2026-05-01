<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPinnedToAnnouncements extends Migration
{
    public function up()
    {
        $this->forge->addColumn('announcements', [
            'pinned' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'is_published',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('announcements', 'pinned');
    }
}
