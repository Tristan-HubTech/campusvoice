<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasswordOtpsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'purpose' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'password_reset',
            ],
            'otp_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'max_attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'default'    => 5,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'used_at' => [
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

        $this->forge->addKey('id', true);
        $this->forge->addKey(['email', 'purpose', 'used_at', 'expires_at']);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('password_otps');
    }

    public function down()
    {
        $this->forge->dropTable('password_otps', true);
    }
}
