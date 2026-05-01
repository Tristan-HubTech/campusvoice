<?php

namespace App\Controllers\Admin;

use App\Models\AdminCredentialModel;
use App\Models\AdminRoleModel;
use App\Models\AdminUserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AuthController extends AdminBaseController
{
    public function login()
    {
        if (session('admin_auth')) {
            return redirect()->to(site_url('admin'));
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('admin/auth/login', ['title' => 'Admin Login']);
        }

        $payload = $this->request->getPost();

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $email    = strtolower(trim((string) ($payload['email'] ?? '')));
        $password = (string) ($payload['password'] ?? '');

        // ── 1. RBAC login via admin_users ────────────────────────────────────
        $rbacResult = $this->attemptRbacLogin($email, $password);

        if ($rbacResult !== null) {
            if (! $rbacResult['success']) {
                return redirect()->back()->with('error', $rbacResult['error']);
            }

            $this->finalizeLogin($rbacResult['session']);
            return redirect()->to(site_url('admin'))->with('success', 'Welcome back, ' . $rbacResult['session']['name'] . '.');
        }

        // ── 2. Legacy fallback: admin_credentials master password ────────────
        // Runs only when no admin_users records exist yet.
        $legacyResult = $this->attemptLegacyLogin($password);

        if ($legacyResult !== null) {
            if (! $legacyResult['success']) {
                return redirect()->back()->with('error', $legacyResult['error']);
            }

            log_message('warning', '[AdminAuth] Legacy admin_credentials login used. Migrate to RBAC admin_users.');
            $this->finalizeLogin($legacyResult['session']);
            return redirect()->to(site_url('admin'))->with('success', 'Welcome to the admin control panel.');
        }

        return redirect()->back()->with('error', 'Invalid credentials.');
    }

    public function logout()
    {
        $auth = session('admin_auth');

        if (is_array($auth) && isset($auth['id'])) {
            $this->logActivity(
                'auth.logout',
                'Admin logged out from control panel.',
                [
                    'target_type' => 'admin_user',
                    'target_id'   => (int) $auth['id'],
                    'email'       => (string) ($auth['email'] ?? ''),
                    'role'        => (string) ($auth['role'] ?? ''),
                ],
                (int) $auth['id']
            );
        }

        session()->remove('admin_auth');
        session()->regenerate(true);

        return redirect()->to(site_url('admin/login'))->with('success', 'Logged out successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Attempts login against admin_users + admin_roles.
     * Returns null when admin_users table has no records (first-run / legacy env).
     * Returns ['success' => bool, 'error' => string, 'session' => array] otherwise.
     */
    private function attemptRbacLogin(string $email, string $password): ?array
    {
        try {
            $adminUserModel = new AdminUserModel();

            if ($adminUserModel->countAllResults() === 0) {
                return null;
            }

            $result = $adminUserModel->attemptLogin($email, $password);

            if (! $result['success']) {
                return ['success' => false, 'error' => $result['error'], 'session' => []];
            }

            $user = $result['user'];
            $role = (new AdminRoleModel())->find((int) $user['role_id']);

            $permissions = $role !== null
                ? (new AdminRoleModel())->getPermissions($role)
                : [];

            $roleName = $role !== null ? (string) $role['name'] : 'Admin';

            $sessionAuth = [
                'id'          => (int) $user['id'],
                'name'        => (string) $user['full_name'],
                'email'       => (string) $user['email'],
                'role'        => $roleName,
                'role_id'     => (int) $user['role_id'],
                'permissions' => $permissions,
                'legacy'      => false,
            ];

            return ['success' => true, 'error' => '', 'session' => $sessionAuth];
        } catch (DatabaseException $e) {
            log_message('error', '[AdminAuth] RBAC login DB error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Attempts login via the legacy admin_credentials master password.
     * Returns null when the table is missing or empty.
     */
    private function attemptLegacyLogin(string $password): ?array
    {
        try {
            $credModel  = new AdminCredentialModel();
            $credential = $credModel->getMasterCredentials();

            if ($credential === null) {
                return null;
            }

            $storedHash    = (string) $credential['master_password_hash'];
            $passwordMatch = (str_starts_with($storedHash, '$2y$'))
                ? password_verify($password, $storedHash)
                : ($password === $storedHash);

            if (! $passwordMatch) {
                return ['success' => false, 'error' => 'Invalid credentials.', 'session' => []];
            }

            $allPermissions = array_fill_keys(AdminRoleModel::ALL_PERMISSIONS, true);

            $sessionAuth = [
                'id'          => 0,
                'name'        => 'System Administrator',
                'email'       => 'admin@campusvoice.local',
                'role'        => 'system_admin',
                'role_id'     => 0,
                'permissions' => $allPermissions,
                'legacy'      => true,
            ];

            return ['success' => true, 'error' => '', 'session' => $sessionAuth];
        } catch (DatabaseException $e) {
            log_message('error', '[AdminAuth] Legacy login DB error: ' . $e->getMessage());
            return null;
        }
    }

    private function finalizeLogin(array $sessionAuth): void
    {
        session()->remove('student_auth');
        session()->set('admin_auth', $sessionAuth);

        $this->logActivity(
            'auth.login',
            'Admin logged into control panel.',
            [
                'target_type' => 'admin_user',
                'target_id'   => (int) $sessionAuth['id'],
                'email'       => (string) $sessionAuth['email'],
                'role'        => (string) $sessionAuth['role'],
                'legacy'      => (bool) ($sessionAuth['legacy'] ?? false),
            ],
            (int) $sessionAuth['id'] > 0 ? (int) $sessionAuth['id'] : null
        );
    }
}
