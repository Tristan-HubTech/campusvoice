<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Unify post reaction types to match comment reactions.
 * Old: like, love, deslike, shock
 * New: like, love, haha, wow, sad, angry
 *
 * Migrates existing data: deslike → sad, shock → wow
 */
class UnifyReactionTypes extends Migration
{
    public function up()
    {
        // Convert old reaction types to new ones
        $this->db->query("UPDATE social_post_reactions SET reaction_type = 'sad' WHERE reaction_type = 'deslike'");
        $this->db->query("UPDATE social_post_reactions SET reaction_type = 'wow' WHERE reaction_type = 'shock'");
    }

    public function down()
    {
        // Revert to old reaction types
        $this->db->query("UPDATE social_post_reactions SET reaction_type = 'deslike' WHERE reaction_type = 'sad'");
        $this->db->query("UPDATE social_post_reactions SET reaction_type = 'shock' WHERE reaction_type = 'wow'");
    }
}
