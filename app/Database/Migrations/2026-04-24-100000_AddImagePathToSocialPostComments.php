<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagePathToSocialPostComments extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('image_path', 'social_post_comments')) {
            return;
        }

        $this->forge->addColumn('social_post_comments', [
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
        if ($this->db->fieldExists('image_path', 'social_post_comments')) {
            $this->forge->dropColumn('social_post_comments', 'image_path');
        }
    }
}
