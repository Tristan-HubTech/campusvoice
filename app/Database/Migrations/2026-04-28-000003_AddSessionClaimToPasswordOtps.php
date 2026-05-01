<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSessionClaimToPasswordOtps extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('password_otps', [
            'opened_session' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'default'    => null,
                'after'      => 'used_at',
            ],
            'opened_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'opened_session',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('password_otps', ['opened_session', 'opened_at']);
    }
}
