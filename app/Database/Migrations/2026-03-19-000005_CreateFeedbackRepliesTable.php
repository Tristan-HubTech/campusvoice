<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeedbackRepliesTable extends Migration
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
            'feedback_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'admin_user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'message' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('feedback_id');
        $this->forge->addForeignKey('feedback_id', 'feedbacks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('admin_user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('feedback_replies');
    }

    public function down()
    {
        $this->forge->dropTable('feedback_replies', true);
    }
}
