<?php

namespace App\Controllers\Api;

use App\Models\ApiTokenModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class AuthController extends ApiController
{
    public function register()
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'password'   => 'required|min_length[8]|max_length[255]',
            'student_no' => 'permit_empty|max_length[50]|is_unique[users.student_no]',
            'phone'      => 'permit_empty|max_length[30]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $roleModel = new RoleModel();
        $studentRole = $roleModel->where('name', 'student')->first();
        if ($studentRole === null) {
            return $this->failServerError('Roles are not initialized. Run InitialDataSeeder first.');
        }

        $userModel = new UserModel();
        $inserted = $userModel->insert([
            'role_id'       => $studentRole['id'],
            'student_no'    => $payload['student_no'] ?? null,
            'first_name'    => trim($payload['first_name']),
            'last_name'     => trim($payload['last_name']),
            'email'         => strtolower(trim($payload['email'])),
            'password_hash' => password_hash($payload['password'], PASSWORD_DEFAULT),
            'phone'         => $payload['phone'] ?? null,
            'is_active'     => 1,
        ]);

        if ($inserted === false) {
            return $this->failServerError('Unable to register the user.');
        }

        return $this->respondCreated([
            'message' => 'Student account created successfully.',
            'user_id' => $inserted,
        ]);
    }

    public function login()
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
            'device'   => 'permit_empty|max_length[80]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel
            ->select('users.*, roles.name as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.email', strtolower(trim($payload['email'])))
            ->first();

        if ($user === null || ! password_verify($payload['password'], $user['password_hash'])) {
            return $this->failUnauthorized('Invalid email or password.');
        }

        if ((int) $user['is_active'] !== 1) {
            return $this->failForbidden('Your account is currently inactive.');
        }

        $plainToken = bin2hex(random_bytes(32));
        $tokenModel = new ApiTokenModel();
        $tokenModel->insert([
            'user_id'    => $user['id'],
            'name'       => $payload['device'] ?? 'default',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => null,
        ]);

        $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        unset($user['password_hash'], $user['deleted_at']);

        return $this->respond([
            'token' => $plainToken,
            'user'  => $user,
        ]);
    }

    public function logout()
    {
        $token = $this->getBearerToken();
        if ($token === null) {
            return $this->failUnauthorized('Missing bearer token.');
        }

        $tokenModel = new ApiTokenModel();
        $tokenModel->where('token_hash', hash('sha256', $token))->delete();

        return $this->respond([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function profile()
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        return $this->respond(['user' => $user]);
    }
}
