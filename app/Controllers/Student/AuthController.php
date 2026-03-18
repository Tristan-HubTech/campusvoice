<?php

namespace App\Controllers\Student;

use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function login()
    {
        if (! empty((array) session()->get('student_auth')) && ! empty(session()->get('student_auth')['id'])) {
            return redirect()->to(site_url('portal'));
        }

        $mode = strtolower(trim((string) ($this->request->getGet('mode') ?? 'login')));
        if (! in_array($mode, ['login', 'register'], true)) {
            $mode = 'login';
        }

        if ($this->request->getMethod() === 'POST') {
            $postedMode = strtolower(trim((string) ($this->request->getPost('auth_mode') ?? 'login')));
            if ($postedMode === 'register') {
                return $this->handleRegister();
            }

            return $this->handleLogin();
        }

        return view('student/auth/login', [
            'title'    => 'Student Portal Access',
            'authMode' => $mode,
        ]);
    }

    public function register()
    {
        if (! empty((array) session()->get('student_auth')) && ! empty(session()->get('student_auth')['id'])) {
            return redirect()->to(site_url('portal'));
        }

        // Keep compatibility for old route/form actions.
        if ($this->request->getMethod() === 'POST') {
            return $this->handleRegister();
        }

        return redirect()->to(site_url('portal/login?mode=register'));
    }

    public function logout()
    {
        session()->remove('student_auth');
        return redirect()->to(site_url('portal/login'))->with('success', 'You have been logged out.');
    }

    private function handleLogin()
    {
        $post = $this->request->getPost();
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validateData($post, $rules)) {
            return redirect()->to(site_url('portal/login?mode=login'))->with('error', implode(' ', $this->validator->getErrors()))->withInput();
        }

        $email    = strtolower(trim((string) ($post['email'] ?? '')));
        $password = (string) ($post['password'] ?? '');

        $userModel = new UserModel();
        $user = $userModel
            ->select('users.*, roles.name as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.email', $email)
            ->first();

        if ($user === null || ! password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            return redirect()->to(site_url('portal/login?mode=login'))->with('error', 'Invalid email or password.')->withInput();
        }

        if ((int) ($user['is_active'] ?? 0) !== 1) {
            return redirect()->to(site_url('portal/login?mode=login'))->with('error', 'Your account is inactive. Please contact the administrator.')->withInput();
        }

        $userModel->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        session()->set('student_auth', [
            'id'    => (int) $user['id'],
            'name'  => trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? ''))),
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        return redirect()->to(site_url('portal'));
    }

    private function handleRegister()
    {
        $post = $this->request->getPost();
        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'student_no'       => 'permit_empty|max_length[50]|is_unique[users.student_no]',
            'phone'            => 'permit_empty|max_length[30]',
            'password'         => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validateData($post, $rules)) {
            return redirect()->to(site_url('portal/login?mode=register'))->with('error', implode(' ', $this->validator->getErrors()))->withInput();
        }

        $roleModel = new RoleModel();
        $studentRole = $roleModel->where('name', 'student')->first();
        if ($studentRole === null) {
            return redirect()->to(site_url('portal/login?mode=register'))->with('error', 'Registration is temporarily unavailable.')->withInput();
        }

        $userModel = new UserModel();
        $userId = $userModel->insert([
            'role_id'       => (int) $studentRole['id'],
            'first_name'    => trim((string) $post['first_name']),
            'last_name'     => trim((string) $post['last_name']),
            'email'         => strtolower(trim((string) $post['email'])),
            'student_no'    => trim((string) ($post['student_no'] ?? '')) ?: null,
            'phone'         => trim((string) ($post['phone'] ?? '')) ?: null,
            'password_hash' => password_hash((string) $post['password'], PASSWORD_DEFAULT),
            'is_active'     => 1,
        ]);

        if ($userId === false) {
            return redirect()->to(site_url('portal/login?mode=register'))->with('error', 'Registration failed. Please try again.')->withInput();
        }

        return redirect()->to(site_url('portal/login?mode=login'))->with('success', 'Account created successfully. You can now log in.');
    }
}
