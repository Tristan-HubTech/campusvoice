<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

class StudentActivityLogModel extends Model
{
    protected $table            = 'student_activity_logs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'student_name',
        'student_email',
        'action',
        'description',
        'target_type',
        'target_id',
        'metadata',
        'ip_address',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = false;

    public function log(
        ?int $userId,
        string $action,
        string $description,
        ?string $targetType = null,
        ?int $targetId = null,
        ?array $metadata = null,
        string $studentName = '',
        string $studentEmail = '',
        string $ip = ''
    ): void {
        try {
            $this->insert([
                'user_id'       => $userId,
                'student_name'  => $studentName !== '' ? $studentName : null,
                'student_email' => $studentEmail !== '' ? $studentEmail : null,
                'action'        => $action,
                'description'   => $description,
                'target_type'   => $targetType,
                'target_id'     => $targetId,
                'metadata'      => $metadata !== null && $metadata !== [] ? json_encode($metadata, JSON_UNESCAPED_SLASHES) : null,
                'ip_address'    => $ip !== '' ? $ip : null,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Student activity logging failed: {message}', ['message' => $e->getMessage()]);
        }
    }
}
