<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnonymousToSocialTables extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('is_anonymous', 'social_posts')) {
            $this->forge->addColumn('social_posts', [
                'is_anonymous' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'is_public',
                ],
            ]);
        }

        if (! $this->db->fieldExists('is_anonymous', 'social_post_comments')) {
            $this->forge->addColumn('social_post_comments', [
                'is_anonymous' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'body',
                ],
            ]);
        }

        if (! $this->db->fieldExists('is_anonymous', 'social_profiles')) {
            $this->forge->addColumn('social_profiles', [
                'is_anonymous' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'avatar_color',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('is_anonymous', 'social_posts')) {
            $this->forge->dropColumn('social_posts', 'is_anonymous');
        }

        if ($this->db->fieldExists('is_anonymous', 'social_post_comments')) {
            $this->forge->dropColumn('social_post_comments', 'is_anonymous');
        }

        if ($this->db->fieldExists('is_anonymous', 'social_profiles')) {
            $this->forge->dropColumn('social_profiles', 'is_anonymous');
        }
    }
}
