<?php

namespace App\Models;

use CodeIgniter\Model;

class SupportReplyModel extends Model
{
    protected $table            = 'support_replies';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ticket_id',
        'user_id',
        'admin_user_id',
        'message',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = false;
}
