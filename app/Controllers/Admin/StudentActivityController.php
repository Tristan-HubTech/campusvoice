<?php

namespace App\Controllers\Admin;

use App\Models\StudentActivityLogModel;

class StudentActivityController extends AdminBaseController
{
    private const SORTABLE_COLUMNS = ['created_at', 'action', 'student_name'];

    public function index(): string
    {
        if ($guard = $this->requirePermission('student_activity.view')) {
            return $guard;
        }

        $adminUser = $this->adminUser();
        $filters   = $this->readFilters();
        $perPage   = 25;
        $page      = max(1, (int) ($this->request->getGet('page') ?? 1));

        $query = (new StudentActivityLogModel());
        $this->applyFilters($query, $filters);

        $total = (int) $query->countAllResults(false);
        $pages = max(1, (int) ceil($total / $perPage));
        if ($page > $pages) {
            $page = $pages;
        }

        $this->applySorting($query, $filters);
        $logs = $total > 0 ? $query->findAll($perPage, ($page - 1) * $perPage) : [];

        $actionOptions = array_column(
            (new StudentActivityLogModel())->select('action')->groupBy('action')->orderBy('action', 'ASC')->findAll(),
            'action'
        );

        return view('admin/student-activity/index', [
            'title'         => 'Student Activity',
            'activeMenu'    => 'student-activity',
            'adminUser'     => $adminUser,
            'logs'          => $logs,
            'filters'       => $filters,
            'pagination'    => compact('page', 'perPage', 'total', 'pages'),
            'actionOptions' => $actionOptions,
        ]);
    }

    private function readFilters(): array
    {
        $sort    = $this->normalizeSort((string) ($this->request->getGet('sort') ?? 'created_at'));
        $dirRaw  = strtolower(trim((string) ($this->request->getGet('dir') ?? '')));
        $dir     = in_array($dirRaw, ['asc', 'desc'], true) ? $dirRaw : 'desc';

        return [
            'q'      => trim((string) ($this->request->getGet('q') ?? '')),
            'action' => trim((string) ($this->request->getGet('action') ?? '')),
            'from'   => $this->normalizeDate((string) ($this->request->getGet('from') ?? '')),
            'to'     => $this->normalizeDate((string) ($this->request->getGet('to') ?? '')),
            'sort'   => $sort,
            'dir'    => $dir,
        ];
    }

    private function applyFilters(StudentActivityLogModel $query, array $filters): void
    {
        if (($filters['q'] ?? '') !== '') {
            $term = (string) $filters['q'];
            $query->groupStart()
                ->like('action', $term)
                ->orLike('description', $term)
                ->orLike('student_name', $term)
                ->orLike('student_email', $term)
                ->groupEnd();
        }

        if (($filters['action'] ?? '') !== '') {
            $query->where('action', (string) $filters['action']);
        }

        if (($filters['from'] ?? '') !== '') {
            $query->where('created_at >=', (string) $filters['from'] . ' 00:00:00');
        }

        if (($filters['to'] ?? '') !== '') {
            $query->where('created_at <=', (string) $filters['to'] . ' 23:59:59');
        }
    }

    private function applySorting(StudentActivityLogModel $query, array $filters): void
    {
        $sort = $this->normalizeSort((string) ($filters['sort'] ?? 'created_at'));
        $dir  = in_array((string) ($filters['dir'] ?? 'desc'), ['asc', 'desc'], true) ? (string) $filters['dir'] : 'desc';
        $query->orderBy($sort, $dir)->orderBy('id', 'DESC');
    }

    private function normalizeSort(string $sort): string
    {
        return in_array($sort, self::SORTABLE_COLUMNS, true) ? $sort : 'created_at';
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
}
