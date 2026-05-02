<?php

namespace App\Controllers\Admin;

use App\Models\AdminRoleModel;
use App\Models\AdminUserModel;

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

        $adminUserModel = new AdminUserModel();
        $result = $adminUserModel->attemptLogin($email, $password);

        if (! $result['success']) {
            return redirect()->back()->with('error', $result['error']);
        }

        $user = $result['user'];
        $role = (new AdminRoleModel())->find((int) $user['role_id']);

        $permissions = $role !== null
            ? (new AdminRoleModel())->getPermissions($role)
            : [];

        $sessionAuth = [
            'id'          => (int) $user['id'],
            'name'        => (string) $user['full_name'],
            'email'       => (string) $user['email'],
            'role'        => $role !== null ? (string) $role['name'] : 'Admin',
            'role_id'     => (int) $user['role_id'],
            'permissions' => $permissions,
        ];

        session()->remove('student_auth');
        session()->set('admin_auth', $sessionAuth);

        $this->logActivity(
            'auth.login',
            'Admin logged into control panel.',
            [
                'target_type' => 'admin_user',
                'target_id'   => (int) $user['id'],
                'email'       => (string) $user['email'],
                'role'        => $sessionAuth['role'],
            ],
            (int) $user['id']
        );

        return redirect()->to(site_url('admin'))->with('success', 'Welcome back, ' . $user['full_name'] . '.');
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
}
