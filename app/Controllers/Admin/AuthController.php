<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;
use App\Models\AdminCredentialModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

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
                'password' => 'required',
            ];

            if (! $this->validateData($payload, $rules)) {
                return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
            }

            $inputPassword = (string) $payload['password'];
            $passwordMatch = false;
            $sessionAuth = [
                'id'    => 1,
                'name'  => 'System Administrator',
                'email' => 'admin@campusvoice.local',
                'role'  => 'system_admin',
            ];

            try {
                $adminCredentialModel = new AdminCredentialModel();
                $credential = $adminCredentialModel->getMasterCredentials();

                if ($credential !== null) {
                    $storedHash = (string) $credential['master_password_hash'];
                    $passwordMatch = (strpos($storedHash, '$2y$') === 0)
                        ? password_verify($inputPassword, $storedHash)
                        : ($inputPassword === $storedHash);
                }
            } catch (DatabaseException $e) {
                $passwordMatch = false;
            }

            // Fallback: authenticate against active admin users from users table.
            if (! $passwordMatch) {
                $userModel = new UserModel();
                $adminUser = $userModel
                    ->select('users.id, users.first_name, users.last_name, users.email, users.password_hash, roles.name as role')
                    ->join('roles', 'roles.id = users.role_id', 'left')
                    ->whereIn('roles.name', ['admin', 'system_admin'])
                    ->where('users.is_active', 1)
                    ->first();

                if ($adminUser !== null) {
                    $userPasswordHash = (string) ($adminUser['password_hash'] ?? '');
                    $passwordMatch = $userPasswordHash !== '' && password_verify($inputPassword, $userPasswordHash);

                    if ($passwordMatch) {
                        $fullName = trim(((string) ($adminUser['first_name'] ?? '')) . ' ' . ((string) ($adminUser['last_name'] ?? '')));
                        $sessionAuth = [
                            'id'    => (int) $adminUser['id'],
                            'name'  => $fullName !== '' ? $fullName : 'Administrator',
                            'email' => (string) ($adminUser['email'] ?? 'admin@campusvoice.local'),
                            'role'  => (string) ($adminUser['role'] ?? 'admin'),
                        ];
                    }
                }
            }

            if (! $passwordMatch) {
                return redirect()->back()->with('error', 'Invalid admin password.');
            }

            // Hard reset: never let an admin session coexist with a student session in the same browser.
            session()->remove('student_auth');

            session()->set('admin_auth', $sessionAuth);

            $this->logActivity(
                'auth.login',
                'Admin logged into control panel via master password.',
                [
                    'target_type' => 'admin_user',
                    'target_id'   => (int) $sessionAuth['id'],
                    'email'       => (string) $sessionAuth['email'],
                    'role'        => (string) $sessionAuth['role'],
                ],
                (int) $sessionAuth['id']
            );

            return redirect()->to(site_url('admin'))->with('success', 'Welcome to the admin control panel.');
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
