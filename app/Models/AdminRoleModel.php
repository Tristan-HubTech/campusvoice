<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminRoleModel extends Model
{
    public const ALL_PERMISSIONS = [
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

    public const PERMISSION_GROUPS = [
        'Dashboard'      => ['dashboard.view'],
        'Feedback'       => ['feedback.view', 'feedback.approve', 'feedback.reject', 'feedback.reply', 'feedback.bulk_approve'],
        'Announcements'  => ['announcements.create', 'announcements.edit', 'announcements.delete', 'announcements.pin'],
        'Students'       => ['users.view', 'users.toggle_status', 'users.reset_password'],
        'Categories'     => ['categories.view', 'categories.create', 'categories.edit', 'categories.delete'],
        'Activity Log'   => ['activity.view', 'activity.purge'],
        'Admin Accounts' => ['admin.view', 'admin.create', 'admin.edit', 'admin.delete', 'admin.assign_roles'],
        'Roles'          => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
        'Tools'          => ['otp_tool.view'],
    ];

    protected $table            = 'admin_roles';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description', 'permissions', 'is_system'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getPermissions(array $role): array
    {
        $raw = $role['permissions'] ?? '';

        if (is_array($raw)) {
            return $raw;
        }

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function buildPermissionsMap(array $selectedKeys): array
    {
        $map = [];
        foreach (self::ALL_PERMISSIONS as $key) {
            $map[$key] = in_array($key, $selectedKeys, true);
        }
        return $map;
    }
}
