<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<style>
/* ── Admin Support Index ── */
.adsup-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;
}
.adsup-title {
    font-size: 1.15rem; font-weight: 700;
    color: var(--cv-ink); margin: 0; letter-spacing: -.015em;
}

/* Stats strip */
.adsup-stats { display: flex; gap: .45rem; flex-wrap: wrap; }
.adsup-stat {
    display: inline-flex; align-items: center; gap: .32rem;
    padding: .26rem .82rem; border-radius: 999px;
    font-size: .72rem; font-weight: 700;
}
.adsup-stat--open   { background: #fef3c7; color: #92400e; }
.adsup-stat--prog   { background: #dbeafe; color: #1e3a8a; }
.adsup-stat--res    { background: #d1fae5; color: #065f46; }
.adsup-stat--closed { background: #f1f5f9; color: #475569; }
.adsup-stat-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.adsup-stat--open .adsup-stat-dot   { background: #f59e0b; }
.adsup-stat--prog .adsup-stat-dot   { background: #3b82f6; }
.adsup-stat--res .adsup-stat-dot    { background: #10b981; }
.adsup-stat--closed .adsup-stat-dot { background: #94a3b8; }

/* Filter bar — horizontal, no wrapping issues */
.adsup-filters {
    display: flex; flex-wrap: wrap; gap: .5rem; align-items: center;
    padding: .85rem 1rem;
    background: var(--cv-surface-2, rgba(255,255,255,.6));
    border: 1px solid var(--cv-border, #dce6f5);
    border-radius: 10px; margin-bottom: 1rem;
}
.adsup-select {
    flex: 0 0 auto;
    width: auto !important;
    min-width: 130px;
    max-width: 180px;
    padding: .38rem .65rem;
    border: 1.5px solid var(--cv-border-2, #c2d0ea) !important;
    border-radius: 8px !important;
    background-color: #ffffff !important;
    color: var(--cv-ink, #141e38) !important;
    font-size: .8rem; outline: none; cursor: pointer;
    transition: border-color .15s, box-shadow .15s;
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237888a8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right .55rem center !important;
    padding-right: 1.75rem; font-family: inherit;
    box-shadow: none !important;
}
.adsup-select:focus { border-color: var(--cv-navy-600, #235096) !important; box-shadow: 0 0 0 2px rgba(35,80,150,.1) !important; }
.adsup-filter-btn {
    flex: 0 0 auto;
    padding: .4rem 1rem;
    background: var(--cv-navy-800, #1a365d); color: #fff;
    border: none; border-radius: 8px;
    font-size: .8rem; font-weight: 600; cursor: pointer;
    font-family: inherit; transition: opacity .15s;
}
.adsup-filter-btn:hover { opacity: .88; }
.adsup-clear-link {
    flex: 0 0 auto;
    padding: .4rem .75rem;
    border: 1px solid var(--cv-border, #dce6f5); border-radius: 8px;
    font-size: .8rem; color: var(--cv-muted, #7888a8); text-decoration: none;
    transition: border-color .15s, color .15s;
}
.adsup-clear-link:hover { border-color: var(--cv-border-2, #c2d0ea); color: var(--cv-ink, #141e38); text-decoration: none; }

/* Empty */
.adsup-empty { text-align: center; padding: 3.5rem 1rem; color: var(--cv-muted); font-size: .875rem; }

/* Table */
.adsup-table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid var(--cv-border, #dce6f5); }
.adsup-table { width: 100%; border-collapse: collapse; min-width: 720px; }
.adsup-table thead tr { background: var(--cv-surface-2, rgba(246,249,253,.9)); }
.adsup-table th {
    padding: .65rem 1rem; text-align: left;
    font-size: .7rem; font-weight: 700; color: var(--cv-muted);
    border-bottom: 1px solid var(--cv-border, #dce6f5);
    white-space: nowrap; letter-spacing: .04em; text-transform: uppercase;
}
.adsup-table tbody tr {
    border-bottom: 1px solid var(--cv-border, #dce6f5);
    transition: background .12s;
}
.adsup-table tbody tr:last-child { border-bottom: none; }
.adsup-table tbody tr:hover { background: var(--cv-surface-hover, rgba(232,240,255,.7)); }
.adsup-td {
    padding: .7rem 1rem; font-size: .82rem;
    color: var(--cv-ink); vertical-align: middle;
}
.adsup-td--muted { color: var(--cv-muted); }
.adsup-td--id    { font-weight: 700; color: var(--cv-muted); font-size: .78rem; }
.adsup-td--subj  { max-width: 210px; }
.adsup-subj-text {
    display: block; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
    font-weight: 600;
}

/* Status & priority badges */
.adsup-pill {
    display: inline-flex; align-items: center;
    padding: .18rem .62rem; border-radius: 999px;
    font-size: .7rem; font-weight: 600; white-space: nowrap;
}
.adsup-pill--open   { background: #fef3c7; color: #92400e; }
.adsup-pill--prog   { background: #dbeafe; color: #1e3a8a; }
.adsup-pill--res    { background: #d1fae5; color: #065f46; }
.adsup-pill--closed { background: #f1f5f9; color: #475569; }
.adsup-pri--low    { background: #f1f5f9; color: #475569; }
.adsup-pri--normal { background: #dbeafe; color: #1e3a8a; }
.adsup-pri--high   { background: #ffedd5; color: #9a3412; }
.adsup-pri--urgent { background: #fee2e2; color: #991b1b; }

/* Priority left indicator on rows */
.adsup-table tbody tr[data-pri="high"]   .adsup-td:first-child { box-shadow: inset 3px 0 0 #f97316; }
.adsup-table tbody tr[data-pri="urgent"] .adsup-td:first-child { box-shadow: inset 3px 0 0 #ef4444; }

.adsup-view-link {
    font-size: .78rem; color: var(--cv-navy-700, #1e4070);
    text-decoration: none; font-weight: 600;
    padding: .25rem .6rem; border-radius: 6px;
    transition: background .12s;
}
.adsup-view-link:hover { background: var(--cv-navy-50, #eef4fb); text-decoration: none; }

/* ── Dark mode overrides for admin ── */
[data-theme="dark"] .adsup-select {
    background-color: #0f1c30 !important;
    border-color: rgba(255,255,255,.18) !important;
    color: #e4ecff !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236f82a6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right .55rem center !important;
}
[data-theme="dark"] .adsup-select:focus { border-color: #638cff !important; box-shadow: 0 0 0 2px rgba(99,140,255,.15) !important; }
[data-theme="dark"] .adsup-table-wrap { border-color: rgba(255,255,255,.1); }
[data-theme="dark"] .adsup-table th { border-color: rgba(255,255,255,.1); }
[data-theme="dark"] .adsup-table tbody tr { border-color: rgba(255,255,255,.07); }
[data-theme="dark"] .adsup-stat--open   { background: rgba(245,158,11,.18); color: #fcd34d; }
[data-theme="dark"] .adsup-stat--prog   { background: rgba(99,140,255,.18); color: #93c5fd; }
[data-theme="dark"] .adsup-stat--res    { background: rgba(52,211,153,.18); color: #6ee7b7; }
[data-theme="dark"] .adsup-stat--closed { background: rgba(148,163,184,.15); color: #94a3b8; }
[data-theme="dark"] .adsup-pill--open   { background: rgba(245,158,11,.18); color: #fcd34d; }
[data-theme="dark"] .adsup-pill--prog   { background: rgba(99,140,255,.18); color: #93c5fd; }
[data-theme="dark"] .adsup-pill--res    { background: rgba(52,211,153,.18); color: #6ee7b7; }
[data-theme="dark"] .adsup-pill--closed { background: rgba(148,163,184,.13); color: #94a3b8; }
[data-theme="dark"] .adsup-pri--high   { background: rgba(249,115,22,.18); color: #fb923c; }
[data-theme="dark"] .adsup-pri--urgent { background: rgba(239,68,68,.18); color: #fca5a5; }
</style>

<div class="cv-card" style="padding:1.35rem 1.5rem;">

    <div class="adsup-header">
        <h2 class="adsup-title">Support Tickets</h2>
        <div class="adsup-stats">
            <?php
            $statItems = [
                ['key' => 'open',        'cls' => 'open',   'label' => 'Open'],
                ['key' => 'in_progress', 'cls' => 'prog',   'label' => 'In Progress'],
                ['key' => 'resolved',    'cls' => 'res',    'label' => 'Resolved'],
                ['key' => 'closed',      'cls' => 'closed', 'label' => 'Closed'],
            ];
            foreach ($statItems as $s): $n = (int) ($stats[$s['key']] ?? 0); ?>
            <span class="adsup-stat adsup-stat--<?= $s['cls'] ?>">
                <span class="adsup-stat-dot"></span>
                <?= $s['label'] ?>: <?= $n ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>

    <form method="get" action="<?= site_url('admin/support') ?>" class="adsup-filters">
        <select name="status" class="adsup-select">
            <option value="">All Status</option>
            <?php foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $v => $l): ?>
            <option value="<?= $v ?>" <?= ($filters['status'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>
        <select name="category" class="adsup-select">
            <option value="">All Category</option>
            <?php foreach (['general' => 'General', 'technical' => 'Technical', 'account' => 'Account', 'other' => 'Other'] as $v => $l): ?>
            <option value="<?= $v ?>" <?= ($filters['category'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>
        <select name="priority" class="adsup-select">
            <option value="">All Priority</option>
            <?php foreach (['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'] as $v => $l): ?>
            <option value="<?= $v ?>" <?= ($filters['priority'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="adsup-filter-btn">Filter</button>
        <?php if ($filters['status'] || $filters['category'] || $filters['priority']): ?>
        <a href="<?= site_url('admin/support') ?>" class="adsup-clear-link">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (empty($tickets)): ?>
        <div class="adsup-empty">No tickets found.</div>
    <?php else: ?>
    <div class="adsup-table-wrap">
        <table class="adsup-table">
            <thead>
                <tr>
                    <th>#</th><th>Subject</th><th>Student</th>
                    <th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tickets as $ticket):
                $s    = $ticket['status'];
                $p    = $ticket['priority'];
                $name = trim(esc($ticket['first_name'] ?? '') . ' ' . esc($ticket['last_name'] ?? ''));
                $sCls = ['open'=>'open','in_progress'=>'prog','resolved'=>'res','closed'=>'closed'][$s] ?? 'closed';
                $pCls = ['low'=>'low','normal'=>'normal','high'=>'high','urgent'=>'urgent'][$p] ?? 'normal';
            ?>
            <tr data-pri="<?= esc($p) ?>">
                <td class="adsup-td adsup-td--id">#<?= (int) $ticket['id'] ?></td>
                <td class="adsup-td adsup-td--subj">
                    <span class="adsup-subj-text"><?= esc($ticket['subject']) ?></span>
                </td>
                <td class="adsup-td"><?= $name ?: esc($ticket['user_email'] ?? '') ?></td>
                <td class="adsup-td adsup-td--muted"><?= esc(ucfirst($ticket['category'])) ?></td>
                <td class="adsup-td"><span class="adsup-pill adsup-pri--<?= esc($pCls) ?>"><?= esc(ucfirst($p)) ?></span></td>
                <td class="adsup-td"><span class="adsup-pill adsup-pill--<?= esc($sCls) ?>"><?= esc(ucfirst(str_replace('_', ' ', $s))) ?></span></td>
                <td class="adsup-td adsup-td--muted" style="white-space:nowrap;"><?= date('M j, Y', strtotime($ticket['created_at'])) ?></td>
                <td class="adsup-td"><a href="<?= site_url('admin/support/' . $ticket['id']) ?>" class="adsup-view-link">View →</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
