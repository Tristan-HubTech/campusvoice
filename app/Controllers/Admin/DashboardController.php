<?php

namespace App\Controllers\Admin;

use App\Models\AdminActivityLogModel;
use App\Models\AnnouncementModel;
use App\Models\FeedbackCategoryModel;
use App\Models\FeedbackModel;
use App\Models\UserModel;

class DashboardController extends AdminBaseController
{
    private const ACTIVITY_EXPORT_BATCH_SIZE = 1000;
    private const ACTIVITY_EXPORT_MAX_ROWS = 20000;
    private const ACTIVITY_SORTABLE_COLUMNS = ['created_at', 'action', 'admin', 'target', 'ip'];
    private const ACTIVITY_PURGE_RETENTION_OPTIONS = [30, 90, 180, 365, 730];

    public function index(): string
    {
        $adminUser = $this->adminUser();
        $canViewActivity = ($adminUser['role'] ?? '') === 'system_admin';

        $requestedTab = (string) ($this->request->getGet('tab') ?? 'overview');
        $allowedTabs = ['overview', 'feedback', 'announcements', 'users', 'categories'];
        if ($canViewActivity) {
            $allowedTabs[] = 'activity';
        }

        $panelTab = in_array($requestedTab, $allowedTabs, true) ? $requestedTab : 'overview';

        $stats = [
            'feedback_total'     => (new FeedbackModel())->countAllResults(),
            'feedback_new'       => (new FeedbackModel())->where('status', 'new')->countAllResults(),
            'feedback_reviewed'  => (new FeedbackModel())->where('status', 'reviewed')->countAllResults(),
            'feedback_resolved'  => (new FeedbackModel())->where('status', 'resolved')->countAllResults(),
            'student_total'      => (new UserModel())
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('roles.name', 'student')
                ->countAllResults(),
            'announcement_total' => (new AnnouncementModel())->countAllResults(),
        ];

        $latestFeedback = (new FeedbackModel())
            ->select('feedbacks.id, feedbacks.type, feedbacks.subject, feedbacks.status, feedbacks.created_at, feedback_categories.name as category_name, users.first_name, users.last_name, feedbacks.is_anonymous')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->join('users', 'users.id = feedbacks.user_id', 'left')
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll(10);

        $feedbackList = (new FeedbackModel())
            ->select('feedbacks.*, feedback_categories.name as category_name, users.first_name, users.last_name, users.email')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->join('users', 'users.id = feedbacks.user_id', 'left')
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll(200);

        $latestAnnouncements = (new AnnouncementModel())
            ->select('announcements.id, announcements.title, announcements.audience, announcements.is_published, announcements.publish_at, announcements.created_at, users.first_name, users.last_name')
            ->join('users', 'users.id = announcements.posted_by', 'left')
            ->orderBy('announcements.created_at', 'DESC')
            ->findAll(5);

        $announcements = (new AnnouncementModel())
            ->select('announcements.*, users.first_name, users.last_name')
            ->join('users', 'users.id = announcements.posted_by', 'left')
            ->orderBy('announcements.created_at', 'DESC')
            ->findAll(200);

        $categories = (new FeedbackCategoryModel())
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $allCategories = (new FeedbackCategoryModel())
            ->orderBy('name', 'ASC')
            ->findAll();

        $usersList = (new UserModel())
            ->select('users.*, roles.name as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('roles.name', 'student')
            ->orderBy('users.created_at', 'DESC')
            ->findAll(500);

        $activityLogs = [];
        $activityToday = 0;
        $activityFilters = [
            'q'        => '',
            'action'   => '',
            'admin_id' => 0,
            'from'     => '',
            'to'       => '',
            'sort'     => 'created_at',
            'dir'      => 'desc',
        ];
        $activityPagination = [
            'page'    => 1,
            'perPage' => 20,
            'total'   => 0,
            'pages'   => 1,
        ];
        $activityActionOptions = [];
        $activityAdminOptions = [];

        if ($canViewActivity) {
            $activityFilters = $this->readActivityFilters();
            $activityPagination['page'] = max(1, (int) ($this->request->getGet('activity_page') ?? 1));

            $activityQuery = (new AdminActivityLogModel())
                ->select('admin_activity_logs.*, users.first_name, users.last_name, users.email')
                ->join('users', 'users.id = admin_activity_logs.admin_user_id', 'left');

            $this->applyActivityFilters($activityQuery, $activityFilters);

            $activityPagination['total'] = (int) $activityQuery->countAllResults(false);
            if ($activityPagination['total'] > 0) {
                $activityPagination['pages'] = (int) ceil($activityPagination['total'] / $activityPagination['perPage']);
                if ($activityPagination['page'] > $activityPagination['pages']) {
                    $activityPagination['page'] = $activityPagination['pages'];
                }

                $offset = ($activityPagination['page'] - 1) * $activityPagination['perPage'];
                $this->applyActivitySorting($activityQuery, $activityFilters);
                $activityLogs = $activityQuery
                    ->findAll($activityPagination['perPage'], $offset);
            }

            $activityToday = (new AdminActivityLogModel())
                ->where('DATE(created_at)', date('Y-m-d'))
                ->countAllResults();

            $activityActionRows = (new AdminActivityLogModel())
                ->select('action')
                ->groupBy('action')
                ->orderBy('action', 'ASC')
                ->findAll();

            $activityActionOptions = array_values(array_filter(array_map(static function (array $row): string {
                return (string) ($row['action'] ?? '');
            }, $activityActionRows)));

            $activityAdminOptions = (new UserModel())
                ->select('users.id, users.first_name, users.last_name, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->whereIn('roles.name', ['system_admin', 'admin'])
                ->where('users.is_active', 1)
                ->orderBy('users.first_name', 'ASC')
                ->orderBy('users.last_name', 'ASC')
                ->findAll();
        }

        return view('admin/dashboard/index', [
            'title'               => 'Control Panel',
            'activeMenu'          => 'dashboard',
            'adminUser'           => $adminUser,
            'canViewActivity'     => $canViewActivity,
            'stats'               => $stats,
            'latestFeedback'      => $latestFeedback,
            'latestAnnouncements' => $latestAnnouncements,
            'feedbackList'        => $feedbackList,
            'announcements'       => $announcements,
            'categories'          => $categories,
            'allCategories'       => $allCategories,
            'usersList'           => $usersList,
            'activityLogs'        => $activityLogs,
            'activityToday'       => $activityToday,
            'activityFilters'     => $activityFilters,
            'activityPagination'  => $activityPagination,
            'activityActionOptions' => $activityActionOptions,
            'activityAdminOptions' => $activityAdminOptions,
            'activityExportMaxRows' => self::ACTIVITY_EXPORT_MAX_ROWS,
            'activityPurgeRetentionOptions' => self::ACTIVITY_PURGE_RETENTION_OPTIONS,
            'panelTab'            => $panelTab,
        ]);
    }

    public function exportActivity()
    {
        if (($this->adminUser()['role'] ?? '') !== 'system_admin') {
            return redirect()->to(site_url('admin'))->with('error', 'Only system admin can export activity logs.');
        }

        $activityFilters = $this->readActivityFilters();

        $query = (new AdminActivityLogModel())
            ->select('admin_activity_logs.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = admin_activity_logs.admin_user_id', 'left');
        $this->applyActivityFilters($query, $activityFilters);

        $totalRows = (int) $query->countAllResults(false);
        $exportLimit = min($totalRows, self::ACTIVITY_EXPORT_MAX_ROWS);
        $isTruncated = $totalRows > self::ACTIVITY_EXPORT_MAX_ROWS;

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Time', 'Admin Name', 'Admin Email', 'Action', 'Description', 'Target', 'IP Address', 'User Agent', 'Metadata']);

        $exported = 0;
        $offset = 0;
        while ($exported < $exportLimit) {
            $batchLimit = min(self::ACTIVITY_EXPORT_BATCH_SIZE, $exportLimit - $exported);

            $batchQuery = (new AdminActivityLogModel())
                ->select('admin_activity_logs.*, users.first_name, users.last_name, users.email')
                ->join('users', 'users.id = admin_activity_logs.admin_user_id', 'left');
            $this->applyActivityFilters($batchQuery, $activityFilters);
            $this->applyActivitySorting($batchQuery, $activityFilters);

            $rows = $batchQuery->findAll($batchLimit, $offset);
            if ($rows === []) {
                break;
            }

            foreach ($rows as $row) {
                $fullName = trim(((string) ($row['first_name'] ?? '')) . ' ' . ((string) ($row['last_name'] ?? '')));
                $target = '-';
                if (! empty($row['target_type']) && ! empty($row['target_id'])) {
                    $target = (string) $row['target_type'] . ' #' . (int) $row['target_id'];
                } elseif (! empty($row['target_type'])) {
                    $target = (string) $row['target_type'];
                }

                $metadataText = (string) ($row['metadata'] ?? '');
                if ($metadataText !== '') {
                    $decoded = json_decode($metadataText, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $metadataText = (string) json_encode($decoded, JSON_UNESCAPED_SLASHES);
                    }
                }

                fputcsv($handle, [
                    (string) ($row['created_at'] ?? ''),
                    $fullName !== '' ? $fullName : 'System',
                    (string) ($row['email'] ?? ''),
                    (string) ($row['action'] ?? ''),
                    (string) ($row['description'] ?? ''),
                    $target,
                    (string) ($row['ip_address'] ?? ''),
                    (string) ($row['user_agent'] ?? ''),
                    $metadataText,
                ]);
            }

            $fetchedCount = count($rows);
            $exported += $fetchedCount;
            $offset += $fetchedCount;
            if ($fetchedCount < $batchLimit) {
                break;
            }
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        $filename = 'campusvoice-activity-' . date('Ymd-His') . '.csv';

        $response = $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('X-Activity-Export-Total', (string) $totalRows)
            ->setHeader('X-Activity-Export-Count', (string) $exported)
            ->setBody($csv);

        if ($isTruncated) {
            $response->setHeader('X-Activity-Export-Truncated', '1');
        }

        return $response;
    }

    public function purgeActivity()
    {
        if (($this->adminUser()['role'] ?? '') !== 'system_admin') {
            return redirect()->to(site_url('admin'))->with('error', 'Only system admin can purge activity logs.');
        }

        $retentionDays = (int) ($this->request->getPost('retention_days') ?? 0);
        $confirmation = strtoupper(trim((string) ($this->request->getPost('confirm_text') ?? '')));

        if (! in_array($retentionDays, self::ACTIVITY_PURGE_RETENTION_OPTIONS, true)) {
            return redirect()->to(site_url('admin?tab=activity'))->with('error', 'Invalid retention period selected.');
        }

        if ($confirmation !== 'PURGE') {
            return redirect()->to(site_url('admin?tab=activity'))->with('error', 'Type PURGE to confirm activity cleanup.');
        }

        $cutoffDate = date('Y-m-d H:i:s', strtotime('-' . $retentionDays . ' days'));

        $deleteQuery = (new AdminActivityLogModel())
            ->where('created_at <', $cutoffDate);

        $deleteCount = (int) $deleteQuery->countAllResults();
        if ($deleteCount > 0) {
            (new AdminActivityLogModel())
                ->where('created_at <', $cutoffDate)
                ->delete();
        }

        $this->logActivity(
            'activity.purge',
            'Purged activity logs older than retention threshold.',
            [
                'target_type'    => 'admin_activity_logs',
                'retention_days' => $retentionDays,
                'cutoff_date'    => $cutoffDate,
                'deleted_count'  => $deleteCount,
            ]
        );

        return redirect()->to(site_url('admin?tab=activity'))->with(
            'success',
            'Activity cleanup complete. Deleted ' . $deleteCount . ' records older than ' . $retentionDays . ' days.'
        );
    }

    private function readActivityFilters(): array
    {
        $sort = $this->normalizeActivitySort((string) ($this->request->getGet('activity_sort') ?? 'created_at'));
        $dirInput = strtolower(trim((string) ($this->request->getGet('activity_dir') ?? '')));

        return [
            'q'        => trim((string) ($this->request->getGet('activity_q') ?? '')),
            'action'   => trim((string) ($this->request->getGet('activity_action') ?? '')),
            'admin_id' => max(0, (int) ($this->request->getGet('activity_admin_id') ?? 0)),
            'from'     => $this->normalizeDate((string) ($this->request->getGet('activity_from') ?? '')),
            'to'       => $this->normalizeDate((string) ($this->request->getGet('activity_to') ?? '')),
            'sort'     => $sort,
            'dir'      => in_array($dirInput, ['asc', 'desc'], true) ? $dirInput : $this->defaultSortDirection($sort),
        ];
    }

    private function normalizeActivitySort(string $sort): string
    {
        $value = trim($sort);
        return in_array($value, self::ACTIVITY_SORTABLE_COLUMNS, true) ? $value : 'created_at';
    }

    private function defaultSortDirection(string $sort): string
    {
        return $sort === 'created_at' ? 'desc' : 'asc';
    }

    private function normalizeDate(string $date): string
    {
        $value = trim($date);
        if ($value === '') {
            return '';
        }

        $dt = date_create_from_format('Y-m-d', $value);
        return $dt !== false ? $dt->format('Y-m-d') : '';
    }

    private function applyActivityFilters(AdminActivityLogModel $query, array $filters): void
    {
        if (($filters['q'] ?? '') !== '') {
            $term = (string) $filters['q'];
            $query->groupStart()
                ->like('admin_activity_logs.action', $term)
                ->orLike('admin_activity_logs.description', $term)
                ->orLike('admin_activity_logs.target_type', $term)
                ->orLike('admin_activity_logs.ip_address', $term)
                ->orLike('users.email', $term)
                ->orLike('users.first_name', $term)
                ->orLike('users.last_name', $term)
                ->groupEnd();
        }

        if (($filters['action'] ?? '') !== '') {
            $query->where('admin_activity_logs.action', (string) $filters['action']);
        }

        if ((int) ($filters['admin_id'] ?? 0) > 0) {
            $query->where('admin_activity_logs.admin_user_id', (int) $filters['admin_id']);
        }

        if (($filters['from'] ?? '') !== '') {
            $query->where('admin_activity_logs.created_at >=', (string) $filters['from'] . ' 00:00:00');
        }

        if (($filters['to'] ?? '') !== '') {
            $query->where('admin_activity_logs.created_at <=', (string) $filters['to'] . ' 23:59:59');
        }
    }

    private function applyActivitySorting(AdminActivityLogModel $query, array $filters): void
    {
        $sort = $this->normalizeActivitySort((string) ($filters['sort'] ?? 'created_at'));
        $dir = strtolower((string) ($filters['dir'] ?? 'desc'));
        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = $this->defaultSortDirection($sort);
        }

        if ($sort === 'action') {
            $query->orderBy('admin_activity_logs.action', $dir);
        } elseif ($sort === 'admin') {
            $query->orderBy('users.first_name', $dir)
                ->orderBy('users.last_name', $dir);
        } elseif ($sort === 'target') {
            $query->orderBy('admin_activity_logs.target_type', $dir)
                ->orderBy('admin_activity_logs.target_id', $dir);
        } elseif ($sort === 'ip') {
            $query->orderBy('admin_activity_logs.ip_address', $dir);
        } else {
            $query->orderBy('admin_activity_logs.created_at', $dir);
        }

        $query->orderBy('admin_activity_logs.id', 'DESC');
    }
}
