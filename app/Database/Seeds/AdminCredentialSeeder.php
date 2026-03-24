<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminCredentialSeeder extends Seeder
{
    public function run()
    {
        // Default password: "admin"
        // To generate new hash: php -r "echo password_hash('your-password', PASSWORD_BCRYPT);"
        $masterPasswordHash = password_hash('admin', PASSWORD_BCRYPT);

        $data = [
            'master_password_hash'     => $masterPasswordHash,
            'last_password_changed_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('admin_credentials')->insert($data);
    }
}
