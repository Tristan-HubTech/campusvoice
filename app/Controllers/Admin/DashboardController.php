<?php

namespace App\Controllers\Admin;

use App\Models\AdminActivityLogModel;
use App\Models\AdminUserModel;
use App\Models\AnnouncementModel;
use App\Models\FeedbackCategoryModel;
use App\Models\FeedbackModel;
use App\Models\StudentActivityLogModel;
use App\Models\UserModel;

class DashboardController extends AdminBaseController
{
    private const ACTIVITY_SORTABLE_COLUMNS = ['created_at', 'action', 'admin', 'target'];
    private const ACTIVITY_PURGE_RETENTION_OPTIONS = [30, 90, 180, 365, 730];

    public function index(): string
    {
        $adminUser = $this->adminUser();
        $canViewActivity = $this->hasPermission('activity.view');

        $requestedTab = (string) ($this->request->getGet('tab') ?? 'overview');
        $allowedTabs = ['overview', 'feedback', 'announcements', 'users', 'categories'];
        if ($canViewActivity) {
            $allowedTabs[] = 'activity';
            $allowedTabs[] = 'student-activity';
        }

        $panelTab = in_array($requestedTab, $allowedTabs, true) ? $requestedTab : 'overview';

        // ── Always load lightweight stats ──────────────────────────
        $stats = [
            'feedback_total'     => (new FeedbackModel())->countAllResults(),
            'feedback_pending'   => (new FeedbackModel())->where('status', 'pending')->countAllResults(),
            'feedback_approved'  => (new FeedbackModel())->where('status', 'approved')->countAllResults(),
            'feedback_rejected'  => (new FeedbackModel())->where('status', 'rejected')->countAllResults(),
            'feedback_reviewed'  => (new FeedbackModel())->where('status', 'reviewed')->countAllResults(),
            'feedback_resolved'  => (new FeedbackModel())->where('status', 'resolved')->countAllResults(),
            'student_total'      => (new UserModel())
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('roles.name', 'student')
                ->countAllResults(),
            'announcement_total' => (new AnnouncementModel())->countAllResults(),
        ];

        // ── Load all tab data upfront so hash-based tab switching always has data ───
        $latestFeedback = (new FeedbackModel())
            ->select('feedbacks.id, feedbacks.type, feedbacks.subject, feedbacks.status, feedbacks.created_at, feedback_categories.name as category_name, feedback_categories.color as category_color, users.first_name, users.last_name, feedbacks.is_anonymous')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->join('users', 'users.id = feedbacks.user_id', 'left')
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll(10);

        $latestAnnouncements = (new AnnouncementModel())
            ->select('announcements.id, announcements.title, announcements.audience, announcements.is_published, announcements.publish_at, announcements.created_at, users.first_name, users.last_name')
            ->join('users', 'users.id = announcements.posted_by', 'left')
            ->orderBy('announcements.created_at', 'DESC')
            ->findAll(5);

        $feedbackList = (new FeedbackModel())
            ->select('feedbacks.*, feedback_categories.name as category_name, feedback_categories.color as category_color, users.first_name, users.last_name, users.email')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->join('users', 'users.id = feedbacks.user_id', 'left')
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll(200);

        $categories = (new FeedbackCategoryModel())
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $announcements = (new AnnouncementModel())
            ->select('announcements.*, users.first_name, users.last_name')
            ->join('users', 'users.id = announcements.posted_by', 'left')
            ->orderBy('announcements.created_at', 'DESC')
            ->findAll(200);

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

        $studentActivityLogs = [];
        $studentActivityFilters = [
            'q'      => '',
            'action' => '',
            'from'   => '',
            'to'     => '',
            'sort'   => 'created_at',
            'dir'    => 'desc',
        ];
        $studentActivityPagination = [
            'page'    => 1,
            'perPage' => 20,
            'total'   => 0,
            'pages'   => 1,
        ];
        $studentActivityActionOptions = [];

        if ($canViewActivity) {
            $activityFilters = $this->readActivityFilters();
            $activityPagination['page'] = max(1, (int) ($this->request->getGet('activity_page') ?? 1));

            $activityQuery = (new AdminActivityLogModel())
                ->select('admin_activity_logs.*, admin_users.full_name, admin_users.email as admin_email')
                ->join('admin_users', 'admin_users.id = admin_activity_logs.admin_user_id', 'left');

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

            $activityAdminOptions = (new AdminUserModel())
                ->select('id, full_name, email')
                ->orderBy('full_name', 'ASC')
                ->findAll();

            // ── Student Activity ────────────────────────────────────
            $saQ      = trim((string) ($this->request->getGet('sa_q') ?? ''));
            $saAction = trim((string) ($this->request->getGet('sa_action') ?? ''));
            $saFrom   = $this->normalizeDate((string) ($this->request->getGet('sa_from') ?? ''));
            $saTo     = $this->normalizeDate((string) ($this->request->getGet('sa_to') ?? ''));
            $saSortIn = trim((string) ($this->request->getGet('sa_sort') ?? ''));
            $saSort   = in_array($saSortIn, ['created_at', 'student_name', 'action'], true) ? $saSortIn : 'created_at';
            $saDirIn  = strtolower(trim((string) ($this->request->getGet('sa_dir') ?? '')));
            $saDir    = in_array($saDirIn, ['asc', 'desc'], true) ? $saDirIn : 'desc';

            $studentActivityFilters = [
                'q'      => $saQ,
                'action' => $saAction,
                'from'   => $saFrom,
                'to'     => $saTo,
                'sort'   => $saSort,
                'dir'    => $saDir,
            ];

            $studentActivityPagination['page'] = max(1, (int) ($this->request->getGet('sa_page') ?? 1));

            $saQuery = (new StudentActivityLogModel())->select('student_activity_logs.*');

            if ($saQ !== '') {
                $saQuery->groupStart()
                    ->like('student_activity_logs.action', $saQ)
                    ->orLike('student_activity_logs.description', $saQ)
                    ->orLike('student_activity_logs.student_name', $saQ)
                    ->orLike('student_activity_logs.student_email', $saQ)
                    ->groupEnd();
            }
            if ($saAction !== '') {
                $saQuery->where('student_activity_logs.action', $saAction);
            }
            if ($saFrom !== '') {
                $saQuery->where('student_activity_logs.created_at >=', $saFrom . ' 00:00:00');
            }
            if ($saTo !== '') {
                $saQuery->where('student_activity_logs.created_at <=', $saTo . ' 23:59:59');
            }

            $studentActivityPagination['total'] = (int) $saQuery->countAllResults(false);
            if ($studentActivityPagination['total'] > 0) {
                $studentActivityPagination['pages'] = (int) ceil($studentActivityPagination['total'] / $studentActivityPagination['perPage']);
                if ($studentActivityPagination['page'] > $studentActivityPagination['pages']) {
                    $studentActivityPagination['page'] = $studentActivityPagination['pages'];
                }
                $saOffset = ($studentActivityPagination['page'] - 1) * $studentActivityPagination['perPage'];

                if ($saSort === 'student_name') {
                    $saQuery->orderBy('student_activity_logs.student_name', $saDir);
                } elseif ($saSort === 'action') {
                    $saQuery->orderBy('student_activity_logs.action', $saDir);
                } else {
                    $saQuery->orderBy('student_activity_logs.created_at', $saDir);
                }
                $saQuery->orderBy('student_activity_logs.id', 'DESC');

                $studentActivityLogs = $saQuery->findAll($studentActivityPagination['perPage'], $saOffset);
            }

            $saActionRows = (new StudentActivityLogModel())
                ->select('action')
                ->groupBy('action')
                ->orderBy('action', 'ASC')
                ->findAll();
            $studentActivityActionOptions = array_values(array_filter(array_map(static function (array $row): string {
                return (string) ($row['action'] ?? '');
            }, $saActionRows)));
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
            'activityPurgeRetentionOptions' => self::ACTIVITY_PURGE_RETENTION_OPTIONS,
            'studentActivityLogs'          => $studentActivityLogs,
            'studentActivityFilters'       => $studentActivityFilters,
            'studentActivityPagination'    => $studentActivityPagination,
            'studentActivityActionOptions' => $studentActivityActionOptions,
            'panelTab'            => $panelTab,
            'safePanelTab'        => $panelTab,
            'allowedTabs'         => $allowedTabs,
        ]);
    }

    public function purgeActivity()
    {
        if (! $this->hasPermission('activity.purge')) {
            return redirect()->to(site_url('admin'))->with('error', 'You do not have permission to purge activity logs.');
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
                ->orLike('admin_activity_logs.admin_display', $term)
                ->orLike('admin_users.full_name', $term)
                ->orLike('admin_users.email', $term)
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
            $query->orderBy('admin_users.full_name', $dir);
        } elseif ($sort === 'target') {
            $query->orderBy('admin_activity_logs.target_type', $dir)
                ->orderBy('admin_activity_logs.target_id', $dir);
        } else {
            $query->orderBy('admin_activity_logs.created_at', $dir);
        }

        $query->orderBy('admin_activity_logs.id', 'DESC');
    }
}
