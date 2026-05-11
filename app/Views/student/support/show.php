<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>

<?php
$status      = (string) $ticket['status'];
$statusLabel = ucfirst(str_replace('_', ' ', $status));
$canClose    = ! in_array($status, ['closed', 'resolved'], true);
$statusColors = [
    'open'        => ['glow' => '#f59e0b', 'pill_bg' => 'rgba(245,158,11,.18)', 'pill_text' => '#fbbf24', 'pill_border' => 'rgba(245,158,11,.32)', 'bar' => 'linear-gradient(90deg,#f59e0b,#fbbf24)'],
    'in_progress' => ['glow' => '#3b82f6', 'pill_bg' => 'rgba(59,130,246,.18)',  'pill_text' => '#93c5fd', 'pill_border' => 'rgba(59,130,246,.32)',  'bar' => 'linear-gradient(90deg,#3b82f6,#60a5fa)'],
    'resolved'    => ['glow' => '#10b981', 'pill_bg' => 'rgba(16,185,129,.18)',  'pill_text' => '#6ee7b7', 'pill_border' => 'rgba(16,185,129,.32)',  'bar' => 'linear-gradient(90deg,#10b981,#34d399)'],
    'closed'      => ['glow' => '#64748b', 'pill_bg' => 'rgba(100,116,139,.12)', 'pill_text' => '#94a3b8', 'pill_border' => 'rgba(100,116,139,.25)', 'bar' => 'linear-gradient(90deg,#475569,#64748b)'],
];
$sc = $statusColors[$status] ?? $statusColors['closed'];
?>
<style>
/* ── Student Support Show ── */
.sup-show-page { max-width: 720px; margin: 0 auto; padding: 2rem 1.25rem 3rem; }

.sup-back-link {
    display: inline-flex; align-items: center; gap: .35rem;
    color: var(--ink-soft); font-size: .82rem; text-decoration: none;
    margin-bottom: 1.75rem; transition: color .15s;
}
.sup-back-link:hover { color: var(--ink); text-decoration: none; }

/* Ticket header */
.sup-ticket-header {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 18px; padding: 1.6rem 1.75rem;
    margin-bottom: .85rem; position: relative; overflow: hidden;
    box-shadow: 0 8px 32px rgba(5,15,50,.3);
}
.sup-ticket-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2.5px;
    background: <?= $sc['bar'] ?>;
}
.sup-ticket-header::after {
    content: ''; position: absolute; top: -60px; right: -60px;
    width: 180px; height: 180px; border-radius: 50%;
    background: radial-gradient(circle, <?= $sc['glow'] ?>22 0%, transparent 70%);
    pointer-events: none;
}
.sup-th-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap; margin-bottom: .55rem;
}
.sup-th-subject {
    font-size: 1.15rem; font-weight: 700; color: var(--ink);
    margin: 0; flex: 1; min-width: 0; line-height: 1.3;
    font-family: 'Fraunces', Georgia, serif;
}
.sup-th-meta { font-size: .76rem; color: var(--ink-soft); margin-bottom: 1rem; }
.sup-th-body {
    font-size: .9rem; color: var(--ink); line-height: 1.72; white-space: pre-wrap; margin: 0;
    padding: 1rem 1.1rem;
    background: rgba(255,255,255,.04);
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,.07);
}
.sup-status-badge {
    display: inline-flex; align-items: center;
    padding: .24rem .78rem; border-radius: 999px;
    font-size: .7rem; font-weight: 700; white-space: nowrap; flex-shrink: 0;
    background: <?= $sc['pill_bg'] ?>; color: <?= $sc['pill_text'] ?>;
    border: 1px solid <?= $sc['pill_border'] ?>;
    letter-spacing: .03em;
}

/* Thread */
.sup-thread { display: flex; flex-direction: column; gap: .65rem; margin-bottom: .85rem; }
.sup-reply {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 13px; padding: 1rem 1.25rem;
}
.sup-reply--admin {
    border-left: 3px solid #3b82f6;
    background: rgba(59,130,246,.05);
    border-color: rgba(59,130,246,.22);
}
.sup-reply-meta {
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
    margin-bottom: .5rem;
}
.sup-reply-author { font-size: .81rem; font-weight: 700; color: var(--ink); }
.sup-reply-team-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    padding: .1rem .52rem; border-radius: 999px;
    font-size: .66rem; font-weight: 700;
    background: rgba(59,130,246,.18); color: #93c5fd;
    border: 1px solid rgba(59,130,246,.28);
}
.sup-reply-time { font-size: .72rem; color: var(--ink-soft); margin-left: auto; }
.sup-reply-body { font-size: .88rem; color: var(--ink); line-height: 1.68; white-space: pre-wrap; margin: 0; }

/* Waiting state */
.sup-waiting {
    text-align: center; padding: 2.5rem 1rem;
    background: rgba(255,255,255,.025);
    border: 1px dashed rgba(255,255,255,.12);
    border-radius: 13px; margin-bottom: .85rem;
}
.sup-waiting-icon {
    width: 50px; height: 50px; border-radius: 14px;
    margin: 0 auto 1rem;
    background: rgba(59,130,246,.12);
    border: 1px solid rgba(59,130,246,.22);
    display: flex; align-items: center; justify-content: center;
    color: #7aaeff;
}
.sup-waiting p { font-size: .88rem; color: var(--ink-soft); margin: 0; }

/* Close button */
.sup-actions { display: flex; justify-content: flex-end; }
.sup-close-btn {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.12);
    color: var(--ink-soft); padding: .44rem 1.1rem;
    border-radius: 9px; font-size: .8rem; cursor: pointer;
    font-family: inherit; transition: border-color .15s, color .15s, background .15s;
}
.sup-close-btn:hover { border-color: rgba(239,68,68,.5); color: #fca5a5; background: rgba(239,68,68,.08); }

/* Light mode */
html:not([data-theme="dark"]) .sup-ticket-header { background: #fff; border-color: #dde8f8; box-shadow: 0 4px 20px rgba(14,32,80,.08); }
html:not([data-theme="dark"]) .sup-th-subject { color: #0d1e42; }
html:not([data-theme="dark"]) .sup-th-meta { color: #5a6a82; }
html:not([data-theme="dark"]) .sup-th-body { background: #f8faff; border-color: #dde8f8; color: #0d1e42; }
html:not([data-theme="dark"]) .sup-status-badge { /* inline styles from PHP handle this */ }
html:not([data-theme="dark"]) .sup-reply { background: #fff; border-color: #dde8f8; }
html:not([data-theme="dark"]) .sup-reply--admin { background: #eff6ff; border-left-color: #3b82f6; border-color: #bfdbfe; }
html:not([data-theme="dark"]) .sup-reply-author { color: #0d1e42; }
html:not([data-theme="dark"]) .sup-reply-team-badge { background: #dbeafe; color: #1e3a8a; border-color: rgba(59,130,246,.35); }
html:not([data-theme="dark"]) .sup-reply-time { color: #5a6a82; }
html:not([data-theme="dark"]) .sup-reply-body { color: #1e2d50; }
html:not([data-theme="dark"]) .sup-waiting { background: #f8faff; border-color: #c8d8f0; }
html:not([data-theme="dark"]) .sup-waiting p { color: #5a6a82; }
html:not([data-theme="dark"]) .sup-close-btn { background: #f8faff; border-color: #c8d8f0; color: #5a6a82; }
html:not([data-theme="dark"]) .sup-close-btn:hover { border-color: #ef4444; color: #dc2626; background: #fef2f2; }
html:not([data-theme="dark"]) .sup-status-badge {
    background: <?= ['open'=>'#fef3c7','in_progress'=>'#dbeafe','resolved'=>'#d1fae5','closed'=>'#f1f5f9'][$status] ?? '#f1f5f9' ?>;
    color: <?= ['open'=>'#92400e','in_progress'=>'#1e3a8a','resolved'=>'#065f46','closed'=>'#475569'][$status] ?? '#475569' ?>;
    border-color: <?= ['open'=>'rgba(245,158,11,.4)','in_progress'=>'rgba(59,130,246,.4)','resolved'=>'rgba(16,185,129,.4)','closed'=>'rgba(100,116,139,.3)'][$status] ?? 'rgba(100,116,139,.3)' ?>;
}
</style>

<div class="sup-show-page">
    <a href="<?= site_url('users/support') ?>" class="sup-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        My Tickets
    </a>

    <div class="sup-ticket-header">
        <div class="sup-th-top">
            <h1 class="sup-th-subject"><?= esc($ticket['subject']) ?></h1>
            <span class="sup-status-badge"><?= esc($statusLabel) ?></span>
        </div>
        <div class="sup-th-meta">
            <?= esc(ucfirst($ticket['category'])) ?> &middot; Opened <?= date('M j, Y g:i A', strtotime($ticket['created_at'])) ?>
        </div>
        <p class="sup-th-body"><?= esc($ticket['message']) ?></p>
    </div>

    <?php if (! empty($replies)): ?>
    <div class="sup-thread">
        <?php foreach ($replies as $reply):
            $isAdmin = ! empty($reply['admin_name']);
            $rName   = $isAdmin ? esc($reply['admin_name']) : 'You';
        ?>
        <div class="sup-reply <?= $isAdmin ? 'sup-reply--admin' : '' ?>">
            <div class="sup-reply-meta">
                <span class="sup-reply-author"><?= $rName ?></span>
                <?php if ($isAdmin): ?>
                <span class="sup-reply-team-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Support Team
                </span>
                <?php endif; ?>
                <span class="sup-reply-time"><?= date('M j, Y g:i A', strtotime($reply['created_at'])) ?></span>
            </div>
            <p class="sup-reply-body"><?= esc($reply['message']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php elseif ($canClose): ?>
    <div class="sup-waiting">
        <div class="sup-waiting-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <p>Ticket is open &mdash; our support team will reply shortly.</p>
    </div>
    <?php endif; ?>

    <?php if ($canClose): ?>
    <div class="sup-actions">
        <form action="<?= site_url('users/support/' . $ticket['id'] . '/close') ?>" method="post"
              onsubmit="return confirm('Close this ticket?');">
            <?= csrf_field() ?>
            <button type="submit" class="sup-close-btn">Close Ticket</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
