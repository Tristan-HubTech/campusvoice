<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<?php
$filters    = $filters    ?? [];
$pagination = $pagination ?? ['page' => 1, 'pages' => 1, 'total' => 0, 'perPage' => 25];
$curSort    = (string) ($filters['sort'] ?? 'created_at');
$curDir     = (string) ($filters['dir'] ?? 'desc');
$baseParams = array_filter([
    'q'      => $filters['q'] ?? '',
    'action' => $filters['action'] ?? '',
    'from'   => $filters['from'] ?? '',
    'to'     => $filters['to'] ?? '',
]);
$buildSortUrl = static function (string $col) use ($curSort, $curDir, $baseParams): string {
    $dir = ($curSort === $col && $curDir === 'asc') ? 'desc' : 'asc';
    $params = $baseParams + ['sort' => $col, 'dir' => $dir];
    return site_url('admin/student-activity') . '?' . http_build_query($params);
};
$sortIndicator = static function (string $col) use ($curSort, $curDir): string {
    if ($curSort !== $col) { return ''; }
    return ' <span class="sort-indicator">' . ($curDir === 'asc' ? '↑' : '↓') . '</span>';
};
$pageUrl = static function (int $p) use ($baseParams, $curSort, $curDir): string {
    $params = $baseParams;
    if ($curSort !== 'created_at') { $params['sort'] = $curSort; }
    if ($curDir  !== 'desc')       { $params['dir']  = $curDir;  }
    if ($p > 1)                    { $params['page'] = $p; }
    $qs = $params ? '?' . http_build_query($params) : '';
    return site_url('admin/student-activity') . $qs;
};
?>

<!-- Page Header -->
<div class="cv-page-header">
    <h2>Student Activity</h2>
    <p>Track student actions across the platform</p>
</div>

<!-- Content Card -->
<div class="cv-table-card">

    <!-- Filter Form -->
    <div class="cv-table-toolbar" style="flex-wrap:wrap;gap:.75rem;">
        <form method="get" action="<?= site_url('admin/student-activity') ?>"
              style="display:flex;gap:.6rem;flex-wrap:wrap;align-items:center;flex:1;">
            <div class="cv-search-wrap" style="flex:1;min-width:180px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" value="<?= esc($filters['q'] ?? '') ?>"
                       class="cv-search" placeholder="Search student, action…">
            </div>

            <select name="action" class="cv-input" style="min-width:150px;max-width:200px;">
                <option value="">All Actions</option>
                <?php foreach (($actionOptions ?? []) as $opt): ?>
                    <option value="<?= esc($opt) ?>" <?= ($filters['action'] ?? '') === $opt ? 'selected' : '' ?>>
                        <?= esc($opt) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="from" value="<?= esc($filters['from'] ?? '') ?>"
                   class="cv-input" style="max-width:140px;" aria-label="From date">
            <span style="color:var(--muted);">–</span>
            <input type="date" name="to" value="<?= esc($filters['to'] ?? '') ?>"
                   class="cv-input" style="max-width:140px;" aria-label="To date">

            <?php if ($curSort !== 'created_at'): ?>
                <input type="hidden" name="sort" value="<?= esc($curSort) ?>">
                <input type="hidden" name="dir"  value="<?= esc($curDir) ?>">
            <?php endif; ?>

            <button type="submit" class="cv-btn-navy">Filter</button>
            <?php if (($filters['q'] ?? '') !== '' || ($filters['action'] ?? '') !== '' || ($filters['from'] ?? '') !== '' || ($filters['to'] ?? '') !== ''): ?>
                <a href="<?= site_url('admin/student-activity') ?>" class="btn-link secondary">Clear</a>
            <?php endif; ?>
        </form>

        <span class="muted" style="font-size:.85rem;white-space:nowrap;">
            <?= number_format($pagination['total']) ?> record<?= $pagination['total'] !== 1 ? 's' : '' ?>
        </span>
    </div>

    <!-- Table -->
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th><a class="sort-link" href="<?= esc($buildSortUrl('created_at')) ?>">Time<?= $sortIndicator('created_at') ?></a></th>
                    <th><a class="sort-link" href="<?= esc($buildSortUrl('student_name')) ?>">Student<?= $sortIndicator('student_name') ?></a></th>
                    <th><a class="sort-link" href="<?= esc($buildSortUrl('action')) ?>">Action<?= $sortIndicator('action') ?></a></th>
                    <th>Description</th>
                    <th>Target</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logs === []): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">
                            No student activity found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <?php
                    $action = (string) ($log['action'] ?? '');
                    $pillClass = match(true) {
                        str_starts_with($action, 'auth.')     => 'status-reviewed',
                        str_starts_with($action, 'post.')     => 'status-approved',
                        str_starts_with($action, 'comment.')  => 'status-resolved',
                        str_starts_with($action, 'reaction.') => 'status-pending',
                        str_starts_with($action, 'settings.') => 'status-inactive',
                        str_starts_with($action, 'password.') => 'status-rejected',
                        default                               => 'status-inactive',
                    };
                    $targetLabel = '—';
                    if (! empty($log['target_type']) && ! empty($log['target_id'])) {
                        $targetLabel = (string) $log['target_type'] . ' #' . (int) $log['target_id'];
                    } elseif (! empty($log['target_type'])) {
                        $targetLabel = (string) $log['target_type'];
                    }
                    ?>
                    <tr>
                        <td style="white-space:nowrap;font-size:.8rem;color:var(--muted);">
                            <?= esc(date('M d, Y H:i:s', strtotime((string) ($log['created_at'] ?? 'now')))) ?>
                        </td>
                        <td>
                            <?php if (! empty($log['student_name'])): ?>
                                <strong><?= esc((string) $log['student_name']) ?></strong>
                                <?php if (! empty($log['student_email'])): ?>
                                    <div class="muted" style="font-size:.8rem;"><?= esc((string) $log['student_email']) ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="pill <?= esc($pillClass) ?>"><?= esc($action) ?></span>
                        </td>
                        <td style="font-size:.875rem;"><?= esc((string) ($log['description'] ?? '—')) ?></td>
                        <td style="font-size:.8rem;color:var(--muted);"><?= esc($targetLabel) ?></td>
                        <td style="font-size:.8rem;color:var(--muted);white-space:nowrap;">
                            <?= esc((string) ($log['ip_address'] ?? '—')) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['pages'] > 1): ?>
    <div style="padding:1rem 1.5rem;display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;border-top:1px solid var(--line);">
        <?php if ($pagination['page'] > 1): ?>
            <a href="<?= esc($pageUrl($pagination['page'] - 1)) ?>" class="btn-link secondary">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($p = max(1, $pagination['page'] - 2); $p <= min($pagination['pages'], $pagination['page'] + 2); $p++): ?>
            <?php if ($p === $pagination['page']): ?>
                <span class="cv-btn-navy" style="padding:.35rem .75rem;font-size:.85rem;cursor:default;"><?= $p ?></span>
            <?php else: ?>
                <a href="<?= esc($pageUrl($p)) ?>" class="btn-link secondary" style="padding:.35rem .75rem;font-size:.85rem;"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($pagination['page'] < $pagination['pages']): ?>
            <a href="<?= esc($pageUrl($pagination['page'] + 1)) ?>" class="btn-link secondary">Next &raquo;</a>
        <?php endif; ?>

        <span class="muted" style="font-size:.8rem;margin-left:.5rem;">
            Page <?= $pagination['page'] ?> of <?= $pagination['pages'] ?>
        </span>
    </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
