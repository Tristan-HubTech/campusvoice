<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add feedback_id FK to social_posts so feedback-linked posts
 * can be reliably identified without fragile body-text matching.
 */
class AddFeedbackIdToSocialPosts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('social_posts', [
            'feedback_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'user_id',
            ],
        ]);

        // Index for looking up posts by feedback_id
        $this->db->query('ALTER TABLE social_posts ADD INDEX idx_feedback_id (feedback_id)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE social_posts DROP INDEX idx_feedback_id');
        $this->forge->dropColumn('social_posts', 'feedback_id');
    }
}
