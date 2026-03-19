<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSocialProfilesTable extends Migration
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
            ],
            'bio' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'avatar_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'blue',
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('social_profiles');
    }

    public function down()
    {
        $this->forge->dropTable('social_profiles', true);
    }
}