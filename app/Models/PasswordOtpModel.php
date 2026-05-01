<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordOtpModel extends Model
{
    protected $table            = 'password_otps';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'email',
        'purpose',
        'otp_hash',
        'attempts',
        'max_attempts',
        'expires_at',
        'used_at',
        'opened_session',
        'opened_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
