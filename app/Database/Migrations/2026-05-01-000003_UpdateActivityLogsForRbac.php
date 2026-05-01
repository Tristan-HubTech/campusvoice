<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateActivityLogsForRbac extends Migration
{
    public function up()
    {
        // Drop the FK that tied admin_user_id → users.id.
        // Going forward admin_user_id stores admin_users.id values.
        // Wrapped in try/catch because the FK name is environment-dependent.
        try {
            $this->forge->dropForeignKey('admin_activity_logs', 'admin_activity_logs_admin_user_id_foreign');
        } catch (\Throwable $e) {
            log_message('info', 'RBAC migration: FK drop skipped (may not exist): ' . $e->getMessage());
        }

        // Snapshot column: stores "Full Name (email)" at log time so display
        // works even when admin accounts are later renamed or deleted.
        $this->db->query(
            'ALTER TABLE admin_activity_logs ADD COLUMN admin_display VARCHAR(200) NULL AFTER admin_user_id'
        );
    }

    public function down()
    {
        try {
            $this->db->query('ALTER TABLE admin_activity_logs DROP COLUMN IF EXISTS admin_display');
        } catch (\Throwable $e) {
            // Ignore — column may not exist on fresh install reverting.
        }

        $this->forge->addForeignKey('admin_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->processIndexes('admin_activity_logs');
    }
}
