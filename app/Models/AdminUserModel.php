<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    private const MAX_ATTEMPTS    = 5;
    private const LOCKOUT_MINUTES = 15;

    protected $table            = 'admin_users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'role_id',
        'full_name',
        'email',
        'password_hash',
        'is_active',
        'login_attempts',
        'locked_until',
        'last_login_at',
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function findByEmail(string $email): ?array
    {
        $user = $this->where('email', strtolower(trim($email)))->first();
        return is_array($user) ? $user : null;
    }

    /**
     * Attempts login. Returns result array:
     *   ['success' => bool, 'user' => array|null, 'error' => string]
     */
    public function attemptLogin(string $email, string $password): array
    {
        $user = $this->findByEmail($email);

        if ($user === null) {
            return ['success' => false, 'user' => null, 'error' => 'Invalid email or password.'];
        }

        if ((int) $user['is_active'] !== 1) {
            return ['success' => false, 'user' => null, 'error' => 'This admin account has been deactivated.'];
        }

        if ($user['locked_until'] !== null && strtotime((string) $user['locked_until']) > time()) {
            $remaining = (int) ceil((strtotime((string) $user['locked_until']) - time()) / 60);
            return ['success' => false, 'user' => null, 'error' => "Too many failed attempts. Try again in {$remaining} minute(s)."];
        }

        if (! password_verify($password, (string) $user['password_hash'])) {
            $this->recordFailedAttempt((int) $user['id'], (int) $user['login_attempts']);
            return ['success' => false, 'user' => null, 'error' => 'Invalid email or password.'];
        }

        $this->update((int) $user['id'], [
            'login_attempts' => 0,
            'locked_until'   => null,
            'last_login_at'  => date('Y-m-d H:i:s'),
        ]);

        // Re-fetch so last_login_at and cleared attempts are reflected.
        $fresh = $this->find((int) $user['id']);
        return ['success' => true, 'user' => is_array($fresh) ? $fresh : $user, 'error' => ''];
    }

    private function recordFailedAttempt(int $userId, int $currentAttempts): void
    {
        $newAttempts = $currentAttempts + 1;
        $data = ['login_attempts' => $newAttempts];

        if ($newAttempts >= self::MAX_ATTEMPTS) {
            $data['locked_until'] = date('Y-m-d H:i:s', time() + self::LOCKOUT_MINUTES * 60);
        }

        $this->update($userId, $data);
    }

    public function unlockAccount(int $userId): void
    {
        $this->update($userId, [
            'login_attempts' => 0,
            'locked_until'   => null,
        ]);
    }
}
