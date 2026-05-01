<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the Super Admin role (full permissions) and one Super Admin account.
 *
 * Configure via .env before running:
 *   RBAC_ADMIN_EMAIL    = your-email@school.edu
 *   RBAC_ADMIN_PASSWORD = YourSecurePassword1!
 *   RBAC_ADMIN_NAME     = Your Full Name
 *
 * Run: php spark db:seed AdminRbacSeeder
 */
class AdminRbacSeeder extends Seeder
{
    private const ALL_PERMISSIONS = [
        'dashboard.view',
        'feedback.view',
        'feedback.approve',
        'feedback.reject',
        'feedback.reply',
        'feedback.bulk_approve',
        'announcements.create',
        'announcements.edit',
        'announcements.delete',
        'announcements.pin',
        'users.view',
        'users.toggle_status',
        'users.reset_password',
        'categories.view',
        'categories.create',
        'categories.edit',
        'categories.delete',
        'activity.view',
        'activity.purge',
        'admin.view',
        'admin.create',
        'admin.edit',
        'admin.delete',
        'admin.assign_roles',
        'roles.view',
        'roles.create',
        'roles.edit',
        'roles.delete',
        'otp_tool.view',
    ];

    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // ── 1. Super Admin Role ──────────────────────────────────────────────
        $existing = $this->db->table('admin_roles')
            ->where('name', 'Super Admin')
            ->get()
            ->getFirstRow('array');

        if ($existing) {
            $roleId = (int) $existing['id'];
            echo "Super Admin role already exists (id: {$roleId}). Skipping role creation.\n";
        } else {
            $this->db->table('admin_roles')->insert([
                'name'        => 'Super Admin',
                'description' => 'Full access to all admin features. Protected system role.',
                'permissions' => json_encode(array_fill_keys(self::ALL_PERMISSIONS, true)),
                'is_system'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $roleId = (int) $this->db->insertID();
            echo "Created Super Admin role (id: {$roleId}).\n";
        }

        // ── 2. Super Admin Account ───────────────────────────────────────────
        $email    = env('RBAC_ADMIN_EMAIL',    'superadmin@campusvoice.local');
        $password = env('RBAC_ADMIN_PASSWORD', 'Admin@CampusVoice1!');
        $fullName = env('RBAC_ADMIN_NAME',     'Super Administrator');

        $existingUser = $this->db->table('admin_users')
            ->where('email', $email)
            ->get()
            ->getFirstRow('array');

        if ($existingUser) {
            echo "Admin user '{$email}' already exists. Skipping account creation.\n";
        } else {
            $this->db->table('admin_users')->insert([
                'role_id'       => $roleId,
                'full_name'     => $fullName,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            echo "Created Super Admin account: {$email}\n";
            echo "Temporary password: {$password}\n";
            echo "*** CHANGE THIS PASSWORD IMMEDIATELY AFTER FIRST LOGIN ***\n";
        }
    }
}
