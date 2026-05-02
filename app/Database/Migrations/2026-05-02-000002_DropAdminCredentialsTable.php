<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * FUTURE MIGRATION — run only after confirming RBAC login works in production.
 *
 * Steps before running:
 *   1. Verify all admins can log in via the new admin_users table.
 *   2. Confirm no fallback login warnings appear in the application log.
 *   3. Then run: php spark migrate --all (or target this file specifically).
 */
class DropAdminCredentialsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('admin_credentials', true);
    }

    public function down()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'master_password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'last_password_changed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->createTable('admin_credentials');
    }
}
