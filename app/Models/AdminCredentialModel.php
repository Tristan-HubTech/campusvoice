<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminCredentialModel extends Model
{
    protected $table            = 'admin_credentials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'master_password_hash',
        'last_password_changed_at',
        'created_at',
        'updated_at',
    ];
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getMasterCredentials()
    {
        return $this->first();
    }

    public function updateMasterPassword(string $passwordHash): bool
    {
        $credential = $this->first();

        if ($credential) {
            return (bool) $this->update($credential['id'], [
                'master_password_hash'     => $passwordHash,
                'last_password_changed_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return (bool) $this->insert([
            'master_password_hash'     => $passwordHash,
            'last_password_changed_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
