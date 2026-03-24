<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminCredentialsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'master_password_hash' => [
                'type'   => 'VARCHAR',
                'constraint' => 255,
                'null'   => false,
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

    public function down()
    {
        $this->forge->dropTable('admin_credentials');
    }
}
