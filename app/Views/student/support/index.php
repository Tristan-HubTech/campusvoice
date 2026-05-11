<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>
<style>
/* ── Student Support Index ── */
.sup-page { max-width: 860px; margin: 0 auto; padding: 2rem 1.25rem; }

.sup-topbar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.75rem; flex-wrap: wrap; gap: .75rem;
}
.sup-heading {
    font-size: 1.5rem; font-weight: 700;
    color: var(--ink); margin: 0; letter-spacing: -.025em;
}
.sup-new-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .52rem 1.15rem;
    background: var(--primary); color: #fff;
    border-radius: 10px; text-decoration: none;
    font-size: .82rem; font-weight: 600;
    transition: transform .12s ease, opacity .15s;
}
.sup-new-btn:hover { opacity: .85; transform: translateY(-1px); text-decoration: none; }

/* Stats chips */
.sup-stats { display: flex; gap: .45rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
.sup-stat-chip {
    display: inline-flex; align-items: center; gap: .32rem;
    padding: .25rem .78rem; border-radius: 999px;
    font-size: .72rem; font-weight: 600;
}
.sup-stat-chip--open   { background: rgba(245,158,11,.15); color: #b45309; border: 1px solid rgba(245,158,11,.35); }
.sup-stat-chip--prog   { background: rgba(59,130,246,.15); color: #1d4ed8; border: 1px solid rgba(59,130,246,.35); }
.sup-stat-chip--res    { background: rgba(16,185,129,.15); color: #047857; border: 1px solid rgba(16,185,129,.35); }
.sup-stat-chip--closed { background: rgba(100,116,139,.12); color: #475569; border: 1px solid rgba(100,116,139,.28); }
.sup-stat-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
.sup-stat-chip--open .sup-stat-dot   { background: #f59e0b; }
.sup-stat-chip--prog .sup-stat-dot   { background: #3b82f6; }
.sup-stat-chip--res .sup-stat-dot    { background: #10b981; }
.sup-stat-chip--closed .sup-stat-dot { background: #94a3b8; }

/* Empty state */
.sup-empty {
    text-align: center; padding: 4.5rem 1rem;
    background: var(--surface); border: 1px solid var(--line);
    border-radius: 16px;
}
.sup-empty-icon-wrap {
    width: 60px; height: 60px; border-radius: 16px;
    margin: 0 auto 1.25rem;
    background: rgba(59,130,246,.12);
    border: 1px solid rgba(59,130,246,.2);
    display: flex; align-items: center; justify-content: center;
    color: #3b82f6;
}
.sup-empty-title { font-size: 1rem; font-weight: 700; color: var(--ink); margin: 0 0 .4rem; }
.sup-empty-desc  { font-size: .86rem; color: var(--ink-soft); margin: 0 0 1.5rem; }
.sup-empty-cta {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .55rem 1.35rem;
    background: var(--primary); color: #fff;
    border-radius: 10px; text-decoration: none;
    font-size: .84rem; font-weight: 600;
    transition: opacity .15s, transform .12s;
}
.sup-empty-cta:hover { opacity: .85; transform: translateY(-1px); text-decoration: none; }

/* Ticket list */
.sup-list { display: flex; flex-direction: column; gap: .55rem; }
.sup-ticket-card {
    display: flex; align-items: center; gap: 1rem;
    text-decoration: none;
    background: var(--surface); border: 1px solid var(--line);
    border-radius: 12px; padding: 1rem 1.2rem;
    position: relative; overflow: hidden;
    transition: border-color .15s, box-shadow .15s, transform .12s;
}
.sup-ticket-card::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
}
.sup-ticket-card[data-s="open"]::before        { background: #f59e0b; }
.sup-ticket-card[data-s="in_progress"]::before { background: #3b82f6; }
.sup-ticket-card[data-s="resolved"]::before    { background: #10b981; }
.sup-ticket-card[data-s="closed"]::before      { background: #94a3b8; }
.sup-ticket-card:hover {
    border-color: rgba(59,130,246,.45);
    box-shadow: 0 4px 20px rgba(10,30,80,.1);
    transform: translateY(-1px);
    text-decoration: none;
}
.sup-ticket-id { font-size: .7rem; font-weight: 700; color: var(--ink-soft); min-width: 30px; flex-shrink: 0; }
.sup-ticket-info { flex: 1; min-width: 0; }
.sup-ticket-subject {
    font-size: .91rem; font-weight: 600; color: var(--ink);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sup-ticket-meta { font-size: .74rem; color: var(--ink-soft); margin-top: .18rem; }
.sup-ticket-end { display: flex; align-items: center; gap: .55rem; flex-shrink: 0; }

.sup-status-pill {
    display: inline-flex; align-items: center;
    padding: .2rem .68rem; border-radius: 999px;
    font-size: .7rem; font-weight: 600; white-space: nowrap;
}
.sup-status-pill[data-s="open"]        { background: rgba(245,158,11,.15); color: #b45309; border: 1px solid rgba(245,158,11,.3); }
.sup-status-pill[data-s="in_progress"] { background: rgba(59,130,246,.15); color: #1d4ed8; border: 1px solid rgba(59,130,246,.3); }
.sup-status-pill[data-s="resolved"]    { background: rgba(16,185,129,.15); color: #047857; border: 1px solid rgba(16,185,129,.3); }
.sup-status-pill[data-s="closed"]      { background: rgba(100,116,139,.12); color: #475569; border: 1px solid rgba(100,116,139,.25); }
.sup-ticket-chevron { color: var(--ink-soft); font-size: 1rem; line-height: 1; }

/* ── Dark mode overrides ── */
html[data-theme="dark"] .sup-heading { color: #e0eaff; }
html[data-theme="dark"] .sup-new-btn { background: #1e3a72; }
html[data-theme="dark"] .sup-empty { background: #131b2e; border-color: #1e2d4a; }
html[data-theme="dark"] .sup-empty-title { color: #e0eaff; }
html[data-theme="dark"] .sup-empty-desc { color: #8aaad0; }
html[data-theme="dark"] .sup-empty-cta { background: #1e3a72; }
html[data-theme="dark"] .sup-ticket-card { background: #131b2e; border-color: #1e2d4a; }
html[data-theme="dark"] .sup-ticket-card:hover { border-color: rgba(99,140,255,.5); box-shadow: 0 4px 20px rgba(0,0,0,.35); }
html[data-theme="dark"] .sup-ticket-id { color: #6f82a6; }
html[data-theme="dark"] .sup-ticket-subject { color: #e0eaff; }
html[data-theme="dark"] .sup-ticket-meta { color: #8aaad0; }
html[data-theme="dark"] .sup-ticket-chevron { color: #6f82a6; }
html[data-theme="dark"] .sup-status-pill[data-s="open"]        { background: rgba(245,158,11,.18); color: #fcd34d; border-color: rgba(245,158,11,.35); }
html[data-theme="dark"] .sup-status-pill[data-s="in_progress"] { background: rgba(99,140,255,.18); color: #93c5fd; border-color: rgba(99,140,255,.35); }
html[data-theme="dark"] .sup-status-pill[data-s="resolved"]    { background: rgba(52,211,153,.18); color: #6ee7b7; border-color: rgba(52,211,153,.35); }
html[data-theme="dark"] .sup-status-pill[data-s="closed"]      { background: rgba(148,163,184,.12); color: #94a3b8; border-color: rgba(148,163,184,.25); }
html[data-theme="dark"] .sup-stat-chip--open   { background: rgba(245,158,11,.18); color: #fcd34d; border-color: rgba(245,158,11,.35); }
html[data-theme="dark"] .sup-stat-chip--prog   { background: rgba(99,140,255,.18); color: #93c5fd; border-color: rgba(99,140,255,.35); }
html[data-theme="dark"] .sup-stat-chip--res    { background: rgba(52,211,153,.18); color: #6ee7b7; border-color: rgba(52,211,153,.35); }
html[data-theme="dark"] .sup-stat-chip--closed { background: rgba(148,163,184,.12); color: #94a3b8; border-color: rgba(148,163,184,.25); }
</style>

<div class="sup-page">
    <div class="sup-topbar">
        <h1 class="sup-heading">My Support Tickets</h1>
        <a href="<?= site_url('users/support/create') ?>" class="sup-new-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Ticket
        </a>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="sup-empty">
            <div class="sup-empty-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <p class="sup-empty-title">No support tickets yet</p>
            <p class="sup-empty-desc">Need help or have an issue? Our team is here for you.</p>
            <a href="<?= site_url('users/support/create') ?>" class="sup-empty-cta">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Submit your first ticket
            </a>
        </div>
    <?php else: ?>
        <?php
        $counts = ['open' => 0, 'in_progress' => 0, 'resolved' => 0, 'closed' => 0];
        foreach ($tickets as $t) { if (isset($counts[$t['status']])) $counts[$t['status']]++; }
        ?>
        <div class="sup-stats">
            <?php if ($counts['open']): ?><span class="sup-stat-chip sup-stat-chip--open"><span class="sup-stat-dot"></span><?= $counts['open'] ?> Open</span><?php endif; ?>
            <?php if ($counts['in_progress']): ?><span class="sup-stat-chip sup-stat-chip--prog"><span class="sup-stat-dot"></span><?= $counts['in_progress'] ?> In Progress</span><?php endif; ?>
            <?php if ($counts['resolved']): ?><span class="sup-stat-chip sup-stat-chip--res"><span class="sup-stat-dot"></span><?= $counts['resolved'] ?> Resolved</span><?php endif; ?>
            <?php if ($counts['closed']): ?><span class="sup-stat-chip sup-stat-chip--closed"><span class="sup-stat-dot"></span><?= $counts['closed'] ?> Closed</span><?php endif; ?>
        </div>

        <div class="sup-list">
        <?php foreach ($tickets as $ticket):
            $slug  = $ticket['status'];
            $label = ucfirst(str_replace('_', ' ', $slug));
        ?>
            <a href="<?= site_url('users/support/' . $ticket['id']) ?>" class="sup-ticket-card" data-s="<?= esc($slug) ?>">
                <span class="sup-ticket-id">#<?= (int) $ticket['id'] ?></span>
                <div class="sup-ticket-info">
                    <div class="sup-ticket-subject"><?= esc($ticket['subject']) ?></div>
                    <div class="sup-ticket-meta"><?= esc(ucfirst($ticket['category'])) ?> &middot; <?= date('M j, Y', strtotime($ticket['created_at'])) ?></div>
                </div>
                <div class="sup-ticket-end">
                    <span class="sup-status-pill" data-s="<?= esc($slug) ?>"><?= esc($label) ?></span>
                    <span class="sup-ticket-chevron">›</span>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
