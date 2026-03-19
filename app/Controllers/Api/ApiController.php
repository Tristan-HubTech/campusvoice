<?php

namespace App\Controllers\Api;

use App\Models\ApiTokenModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

abstract class ApiController extends ResourceController
{
    protected $format = 'json';

    protected function getBearerToken(): ?string
    {
        $header = $this->request->getHeaderLine('Authorization');

        if ($header === '') {
            return null;
        }

        if (preg_match('/Bearer\s+(\S+)/i', $header, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    protected function authUser(): ?array
    {
        $token = $this->getBearerToken();
        if ($token === null) {
            return null;
        }

        $tokenHash  = hash('sha256', $token);
        $tokenModel = new ApiTokenModel();
        $tokenRow   = $tokenModel->where('token_hash', $tokenHash)->first();

        if ($tokenRow === null) {
            return null;
        }

        if (! empty($tokenRow['expires_at']) && strtotime($tokenRow['expires_at']) < time()) {
            $tokenModel->delete($tokenRow['id']);
            return null;
        }

        $userModel = new UserModel();
        $user = $userModel
            ->select('users.id, users.role_id, users.student_no, users.first_name, users.last_name, users.email, users.is_active, roles.name as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.id', $tokenRow['user_id'])
            ->first();

        if ($user === null || (int) $user['is_active'] !== 1) {
            return null;
        }

        $tokenModel->update($tokenRow['id'], ['last_used_at' => date('Y-m-d H:i:s')]);

        return $user;
    }

    protected function isAdmin(array $user): bool
    {
        return in_array($user['role'], ['system_admin', 'admin'], true);
    }
}
