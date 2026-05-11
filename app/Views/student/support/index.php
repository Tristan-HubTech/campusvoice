<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>
<style>
/* ── Student Support Index ── */
.sup-page { max-width: 860px; margin: 0 auto; padding: 2rem 1.25rem 3rem; }

.sup-topbar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 2rem; flex-wrap: wrap; gap: .75rem;
}
.sup-heading {
    font-size: 1.6rem; font-weight: 700;
    color: var(--ink); margin: 0; letter-spacing: -.03em;
    font-family: 'Fraunces', Georgia, serif;
}
.sup-new-btn {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .55rem 1.2rem;
    background: linear-gradient(135deg, #1a3a72, #1f4a90);
    color: #fff; border-radius: 10px; text-decoration: none;
    font-size: .82rem; font-weight: 600;
    border: 1px solid rgba(100,160,255,.25);
    box-shadow: 0 4px 14px rgba(20,60,150,.35);
    transition: transform .15s, box-shadow .15s, opacity .15s;
}
.sup-new-btn:hover {
    opacity: .9; transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(20,60,150,.45);
    text-decoration: none;
}

/* Stats row */
.sup-stats { display: flex; gap: .4rem; flex-wrap: wrap; margin-bottom: 1.75rem; }
.sup-stat-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .22rem .72rem; border-radius: 999px;
    font-size: .7rem; font-weight: 700; letter-spacing: .01em;
}
.sup-stat-chip--open   { background: rgba(245,158,11,.14); color: #f59e0b; border: 1px solid rgba(245,158,11,.28); }
.sup-stat-chip--prog   { background: rgba(99,140,255,.14); color: #7aaeff; border: 1px solid rgba(99,140,255,.28); }
.sup-stat-chip--res    { background: rgba(52,211,153,.14); color: #34d399; border: 1px solid rgba(52,211,153,.28); }
.sup-stat-chip--closed { background: rgba(148,163,184,.1);  color: #94a3b8; border: 1px solid rgba(148,163,184,.22); }
.sup-stat-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
.sup-stat-chip--open .sup-stat-dot   { background: #f59e0b; }
.sup-stat-chip--prog .sup-stat-dot   { background: #7aaeff; }
.sup-stat-chip--res .sup-stat-dot    { background: #34d399; }
.sup-stat-chip--closed .sup-stat-dot { background: #94a3b8; }

/* Empty state */
.sup-empty {
    text-align: center; padding: 5rem 1rem;
    background: rgba(255,255,255,.025);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    backdrop-filter: blur(4px);
}
.sup-empty-icon {
    width: 64px; height: 64px; border-radius: 18px;
    margin: 0 auto 1.25rem;
    background: linear-gradient(135deg, rgba(99,140,255,.2), rgba(52,211,153,.1));
    border: 1px solid rgba(99,140,255,.25);
    display: flex; align-items: center; justify-content: center;
    color: #7aaeff;
}
.sup-empty-title { font-size: 1.05rem; font-weight: 700; color: var(--ink); margin: 0 0 .4rem; font-family: 'Fraunces', serif; }
.sup-empty-desc  { font-size: .86rem; color: var(--ink-soft); margin: 0 0 1.5rem; }
.sup-empty-cta {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .58rem 1.4rem;
    background: linear-gradient(135deg, #1a3a72, #1f4a90);
    color: #fff; border-radius: 10px; text-decoration: none;
    font-size: .84rem; font-weight: 600;
    border: 1px solid rgba(100,160,255,.25);
    box-shadow: 0 4px 14px rgba(20,60,150,.35);
    transition: opacity .15s, transform .12s;
}
.sup-empty-cta:hover { opacity: .88; transform: translateY(-1px); text-decoration: none; }

/* Ticket list */
.sup-list { display: flex; flex-direction: column; gap: .6rem; }

.sup-ticket-card {
    display: flex; align-items: center; gap: 1rem;
    text-decoration: none;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px; padding: 1rem 1.2rem;
    position: relative; overflow: hidden;
    transition: border-color .18s, background .18s, transform .15s, box-shadow .18s;
}
.sup-ticket-card::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
    border-radius: 14px 0 0 14px;
}
.sup-ticket-card[data-s="open"]::before        { background: linear-gradient(180deg, #f59e0b, #fbbf24); }
.sup-ticket-card[data-s="in_progress"]::before { background: linear-gradient(180deg, #3b82f6, #60a5fa); }
.sup-ticket-card[data-s="resolved"]::before    { background: linear-gradient(180deg, #10b981, #34d399); }
.sup-ticket-card[data-s="closed"]::before      { background: linear-gradient(180deg, #475569, #64748b); }
.sup-ticket-card:hover {
    border-color: rgba(99,140,255,.35);
    background: rgba(99,140,255,.06);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(10,30,80,.25);
    text-decoration: none;
}
/* glow on hover matching status color */
.sup-ticket-card[data-s="open"]:hover       { border-color: rgba(245,158,11,.4); background: rgba(245,158,11,.05); }
.sup-ticket-card[data-s="in_progress"]:hover { border-color: rgba(59,130,246,.4); background: rgba(59,130,246,.05); }
.sup-ticket-card[data-s="resolved"]:hover   { border-color: rgba(16,185,129,.4); background: rgba(16,185,129,.05); }

.sup-ticket-id {
    font-size: .68rem; font-weight: 800; color: var(--ink-soft);
    min-width: 28px; flex-shrink: 0; letter-spacing: .04em;
}
.sup-ticket-info { flex: 1; min-width: 0; }
.sup-ticket-subject {
    font-size: .91rem; font-weight: 600; color: var(--ink);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: .18rem;
}
.sup-ticket-meta { font-size: .73rem; color: var(--ink-soft); }

.sup-ticket-end { display: flex; align-items: center; gap: .55rem; flex-shrink: 0; }
.sup-status-pill {
    display: inline-flex; align-items: center;
    padding: .2rem .68rem; border-radius: 999px;
    font-size: .68rem; font-weight: 700; white-space: nowrap; letter-spacing: .02em;
}
.sup-status-pill[data-s="open"]        { background: rgba(245,158,11,.18); color: #fbbf24; border: 1px solid rgba(245,158,11,.3); }
.sup-status-pill[data-s="in_progress"] { background: rgba(59,130,246,.18); color: #93c5fd; border: 1px solid rgba(59,130,246,.3); }
.sup-status-pill[data-s="resolved"]    { background: rgba(16,185,129,.18); color: #6ee7b7; border: 1px solid rgba(16,185,129,.3); }
.sup-status-pill[data-s="closed"]      { background: rgba(100,116,139,.12); color: #94a3b8; border: 1px solid rgba(100,116,139,.25); }
.sup-ticket-chevron { color: var(--ink-soft); font-size: .9rem; opacity: .5; }

/* Light mode overrides */
html:not([data-theme="dark"]) .sup-ticket-card { background: #fff; border-color: #dde8f8; }
html:not([data-theme="dark"]) .sup-ticket-card:hover { background: #f4f8ff; border-color: #a8c0f0; box-shadow: 0 4px 16px rgba(30,60,130,.1); }
html:not([data-theme="dark"]) .sup-heading { color: #0d1e42; }
html:not([data-theme="dark"]) .sup-new-btn { background: linear-gradient(135deg, #1a3a72, #235296); }
html:not([data-theme="dark"]) .sup-empty { background: #f8faff; border-color: #dde8f8; }
html:not([data-theme="dark"]) .sup-stat-chip--open   { background: #fef3c7; color: #92400e; border-color: rgba(245,158,11,.35); }
html:not([data-theme="dark"]) .sup-stat-chip--prog   { background: #dbeafe; color: #1e3a8a; border-color: rgba(59,130,246,.35); }
html:not([data-theme="dark"]) .sup-stat-chip--res    { background: #d1fae5; color: #065f46; border-color: rgba(16,185,129,.35); }
html:not([data-theme="dark"]) .sup-stat-chip--closed { background: #f1f5f9; color: #475569; border-color: rgba(100,116,139,.3); }
html:not([data-theme="dark"]) .sup-status-pill[data-s="open"]        { background: #fef3c7; color: #92400e; border-color: rgba(245,158,11,.4); }
html:not([data-theme="dark"]) .sup-status-pill[data-s="in_progress"] { background: #dbeafe; color: #1e3a8a; border-color: rgba(59,130,246,.4); }
html:not([data-theme="dark"]) .sup-status-pill[data-s="resolved"]    { background: #d1fae5; color: #065f46; border-color: rgba(16,185,129,.4); }
html:not([data-theme="dark"]) .sup-status-pill[data-s="closed"]      { background: #f1f5f9; color: #475569; border-color: rgba(100,116,139,.3); }
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
            <div class="sup-empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <p class="sup-empty-title">No support tickets yet</p>
            <p class="sup-empty-desc">Need help? Our support team is here for you.</p>
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
