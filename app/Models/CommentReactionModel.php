<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentReactionModel extends Model
{
    protected $table            = 'comment_reactions';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'comment_id',
        'user_id',
        'reaction_type',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
