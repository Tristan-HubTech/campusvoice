<?php
/**
 * FEEDBACK MODEL
 * Maps to the `feedbacks` database table. Handles all queries for user complaints, suggestions, and praise.
 * 
 * CONNECTS TO:
 * - Database Table: feedbacks
 * - Controllers: Admin\FeedbackController, Student\PortalController
 */

namespace App\Models;

use CodeIgniter\Model;

class FeedbackModel extends Model
{
    protected $table            = 'feedbacks';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'category_id',
        'type',
        'subject',
        'message',
        'image_path',
        'is_anonymous',
        'status',
        'submitted_at',
        'resolved_at',
        'admin_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
