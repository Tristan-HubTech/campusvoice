<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminActivityLogModel;
use Throwable;

abstract class AdminBaseController extends BaseController
{
    protected function adminUser(): array
    {
        $auth = session('admin_auth');
        return is_array($auth) ? $auth : [];
    }

    protected function hasPermission(string $permission): bool
    {
        $permissions = $this->adminUser()['permissions'] ?? [];
        return is_array($permissions) && ! empty($permissions[$permission]);
    }

    /**
     * Guard helper. Returns a RedirectResponse when the current admin lacks
     * the given permission, null when they have it.
     *
     * Usage:
     *   if ($guard = $this->requirePermission('admin.view')) return $guard;
     */
    protected function requirePermission(string $permission, string $redirectUrl = '')
    {
        if ($this->hasPermission($permission)) {
            return null;
        }

        $url = $redirectUrl !== '' ? $redirectUrl : site_url('admin');
        return redirect()->to($url)->with('error', 'You do not have permission to perform this action.');
    }

    protected function logActivity(string $action, ?string $description = null, array $context = [], ?int $adminUserId = null): void
    {
        try {
            $auth = $this->adminUser();

            if ($adminUserId === null) {
                $adminUserId = (int) ($auth['id'] ?? 0);
                if ($adminUserId <= 0) {
                    $adminUserId = null;
                }
            }

            $adminName  = (string) ($auth['name'] ?? '');
            $adminEmail = (string) ($auth['email'] ?? '');
            $adminDisplay = $adminName !== ''
                ? ($adminEmail !== '' ? "{$adminName} ({$adminEmail})" : $adminName)
                : null;

            $targetType = isset($context['target_type']) ? (string) $context['target_type'] : null;
            $targetId   = isset($context['target_id'])   ? (int) $context['target_id']   : null;

            $metadata = $context;
            unset($metadata['target_type'], $metadata['target_id']);

            $model = new AdminActivityLogModel();
            $model->insert([
                'admin_user_id' => $adminUserId,
                'admin_display' => $adminDisplay,
                'action'        => $action,
                'target_type'   => $targetType,
                'target_id'     => $targetId,
                'description'   => $description,
                'metadata'      => $metadata !== [] ? json_encode($metadata, JSON_UNESCAPED_SLASHES) : null,
                'ip_address'    => method_exists($this->request, 'getIPAddress') ? (string) $this->request->getIPAddress() : null,
                'user_agent'    => method_exists($this->request, 'getUserAgent') ? substr((string) $this->request->getUserAgent(), 0, 255) : null,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Admin activity logging failed: {message}', ['message' => $e->getMessage()]);
        }
    }
}
