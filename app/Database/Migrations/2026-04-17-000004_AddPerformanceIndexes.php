<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add performance indexes for frequently queried columns
 * that currently lack indexes.
 */
class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Feed query: WHERE is_public = 1 AND deleted_at IS NULL ORDER BY created_at DESC
        $this->db->query('ALTER TABLE social_posts ADD INDEX idx_feed (is_public, deleted_at, created_at)');

        // Users active status filtered in every social query
        $this->db->query('ALTER TABLE users ADD INDEX idx_active (is_active)');

        // Comment listing: WHERE post_id = ? AND deleted_at IS NULL ORDER BY created_at
        $this->db->query('ALTER TABLE social_post_comments ADD INDEX idx_post_comments_listing (post_id, deleted_at, created_at)');

        // Comment reactions: viewer lookup WHERE user_id = ? AND comment_id IN(...)
        $this->db->query('ALTER TABLE comment_reactions ADD INDEX idx_comment_reactions_user (user_id)');

        // Feedbacks sorted by date on dashboard and portal
        $this->db->query('ALTER TABLE feedbacks ADD INDEX idx_feedbacks_created (created_at)');

        // Activity logs date-range filtering and purge
        $this->db->query('ALTER TABLE admin_activity_logs ADD INDEX idx_logs_created (created_at)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE social_posts DROP INDEX idx_feed');
        $this->db->query('ALTER TABLE users DROP INDEX idx_active');
        $this->db->query('ALTER TABLE social_post_comments DROP INDEX idx_post_comments_listing');
        $this->db->query('ALTER TABLE comment_reactions DROP INDEX idx_comment_reactions_user');
        $this->db->query('ALTER TABLE feedbacks DROP INDEX idx_feedbacks_created');
        $this->db->query('ALTER TABLE admin_activity_logs DROP INDEX idx_logs_created');
    }
}
