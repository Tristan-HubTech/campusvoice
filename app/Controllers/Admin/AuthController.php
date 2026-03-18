<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class AuthController extends AdminBaseController
{
    public function login()
    {
        if (session('admin_auth')) {
            return redirect()->to(site_url('admin'));
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $payload = $this->request->getPost();

            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required',
            ];

            if (! $this->validateData($payload, $rules)) {
                return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
            }

            $userModel = new UserModel();
            $user = $userModel
                ->select('users.id, users.first_name, users.last_name, users.email, users.password_hash, users.is_active, roles.name as role')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.email', strtolower(trim((string) $payload['email'])))
                ->first();

            if ($user === null || ! password_verify((string) $payload['password'], (string) $user['password_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
            }

            if ((int) $user['is_active'] !== 1) {
                return redirect()->back()->withInput()->with('error', 'Your account is inactive.');
            }

            if (! in_array($user['role'], ['system_admin', 'admin'], true)) {
                return redirect()->back()->withInput()->with('error', 'This account has no admin privileges.');
            }

            session()->set('admin_auth', [
                'id'    => (int) $user['id'],
                'name'  => trim($user['first_name'] . ' ' . $user['last_name']),
                'email' => $user['email'],
                'role'  => $user['role'],
            ]);

            $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

            $this->logActivity(
                'auth.login',
                'Admin logged into control panel.',
                [
                    'target_type' => 'admin_user',
                    'target_id'   => (int) $user['id'],
                    'email'       => (string) $user['email'],
                    'role'        => (string) $user['role'],
                ],
                (int) $user['id']
            );

            return redirect()->to(site_url('admin'))->with('success', 'Welcome back, ' . $user['first_name'] . '.');
        }

        return view('admin/auth/login', [
            'title' => 'Admin Login',
        ]);
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
