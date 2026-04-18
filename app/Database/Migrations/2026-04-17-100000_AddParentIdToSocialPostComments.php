<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentIdToSocialPostComments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('social_post_comments', [
            'parent_id' => [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'post_id',
            ],
        ]);

        $this->db->query('ALTER TABLE social_post_comments ADD KEY idx_parent_id (parent_id)');
        $this->db->query('ALTER TABLE social_post_comments ADD CONSTRAINT fk_comment_parent FOREIGN KEY (parent_id) REFERENCES social_post_comments(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE social_post_comments DROP FOREIGN KEY fk_comment_parent');
        $this->forge->dropColumn('social_post_comments', 'parent_id');
    }
}
