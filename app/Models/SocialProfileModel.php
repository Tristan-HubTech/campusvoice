<?php

namespace App\Models;

use CodeIgniter\Model;

class SocialProfileModel extends Model
{
    protected $table            = 'social_profiles';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'bio',
        'avatar_color',
        'is_anonymous',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}