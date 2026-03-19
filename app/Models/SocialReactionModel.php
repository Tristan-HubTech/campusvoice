<?php

namespace App\Models;

use CodeIgniter\Model;

class SocialReactionModel extends Model
{
    protected $table            = 'social_post_reactions';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'post_id',
        'user_id',
        'reaction_type',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}