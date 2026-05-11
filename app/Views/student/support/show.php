<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>

<?php
$status      = (string) $ticket['status'];
$statusLabel = ucfirst(str_replace('_', ' ', $status));
$canClose    = ! in_array($status, ['closed', 'resolved'], true);
?>
<style>
/* ── Student Support Show ── */
.sup-show-page { max-width: 740px; margin: 0 auto; padding: 2rem 1.25rem; }

.sup-back-link {
    display: inline-flex; align-items: center; gap: .35rem;
    color: var(--ink-soft); font-size: .82rem; text-decoration: none;
    margin-bottom: 1.5rem; transition: color .15s;
}
.sup-back-link:hover { color: var(--ink); text-decoration: none; }

/* Ticket header card */
.sup-ticket-header {
    background: var(--surface); border: 1px solid var(--line);
    border-radius: 14px; padding: 1.5rem;
    margin-bottom: 1rem; position: relative; overflow: hidden;
    box-shadow: 0 2px 12px rgba(10,27,66,.06);
}
.sup-ticket-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
}
.sup-th-open::before        { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.sup-th-in_progress::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.sup-th-resolved::before    { background: linear-gradient(90deg, #10b981, #34d399); }
.sup-th-closed::before      { background: linear-gradient(90deg, #94a3b8, #cbd5e1); }

.sup-th-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap; margin-bottom: .6rem;
}
.sup-th-subject { font-size: 1.1rem; font-weight: 700; color: var(--ink); margin: 0; flex: 1; min-width: 0; }
.sup-th-meta { font-size: .76rem; color: var(--ink-soft); margin-bottom: .9rem; }
.sup-th-body { font-size: .9rem; color: var(--ink); line-height: 1.7; white-space: pre-wrap; margin: 0; }

.sup-status-badge {
    display: inline-flex; align-items: center;
    padding: .22rem .75rem; border-radius: 999px;
    font-size: .72rem; font-weight: 600; white-space: nowrap;
    flex-shrink: 0;
}
.sup-badge-open        { background: rgba(245,158,11,.15); color: #b45309; border: 1px solid rgba(245,158,11,.3); }
.sup-badge-in_progress { background: rgba(59,130,246,.15); color: #1d4ed8; border: 1px solid rgba(59,130,246,.3); }
.sup-badge-resolved    { background: rgba(16,185,129,.15); color: #047857; border: 1px solid rgba(16,185,129,.3); }
.sup-badge-closed      { background: rgba(100,116,139,.12); color: #475569; border: 1px solid rgba(100,116,139,.25); }

/* Thread */
.sup-thread { display: flex; flex-direction: column; gap: .65rem; margin-bottom: 1rem; }
.sup-reply {
    background: var(--surface); border: 1px solid var(--line);
    border-radius: 12px; padding: 1rem 1.25rem;
    box-shadow: 0 1px 6px rgba(10,27,66,.04);
}
.sup-reply--admin { border-left: 3px solid #3b82f6; background: rgba(59,130,246,.03); }
.sup-reply-meta {
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
    margin-bottom: .55rem;
}
.sup-reply-author { font-size: .81rem; font-weight: 600; color: var(--ink); }
.sup-reply-team-badge {
    display: inline-flex; align-items: center;
    padding: .1rem .52rem; border-radius: 999px;
    font-size: .68rem; font-weight: 600;
    background: rgba(59,130,246,.12); color: #1d4ed8;
}
.sup-reply-time { font-size: .73rem; color: var(--ink-soft); margin-left: auto; }
.sup-reply-body { font-size: .88rem; color: var(--ink); line-height: 1.65; white-space: pre-wrap; margin: 0; }

/* Waiting state */
.sup-waiting {
    text-align: center; padding: 2.5rem 1rem;
    background: var(--surface); border: 1px dashed var(--line);
    border-radius: 12px; margin-bottom: 1rem;
}
.sup-waiting-icon {
    width: 48px; height: 48px; border-radius: 14px;
    margin: 0 auto 1rem;
    background: rgba(59,130,246,.1);
    border: 1px solid rgba(59,130,246,.2);
    display: flex; align-items: center; justify-content: center;
    color: #3b82f6;
}
.sup-waiting p { font-size: .88rem; color: var(--ink-soft); margin: 0; }

/* Close button */
.sup-actions { display: flex; justify-content: flex-end; }
.sup-close-btn {
    background: none; border: 1px solid var(--line);
    color: var(--ink-soft); padding: .42rem .95rem;
    border-radius: 8px; font-size: .8rem; cursor: pointer;
    font-family: inherit; transition: border-color .15s, color .15s;
}
.sup-close-btn:hover { border-color: #ef4444; color: #dc2626; }

/* ── Dark mode overrides ── */
html[data-theme="dark"] .sup-back-link { color: #8aaad0; }
html[data-theme="dark"] .sup-back-link:hover { color: #e0eaff; }
html[data-theme="dark"] .sup-ticket-header { background: #131b2e; border-color: #1e2d4a; box-shadow: 0 4px 24px rgba(0,0,0,.3); }
html[data-theme="dark"] .sup-th-subject { color: #e0eaff; }
html[data-theme="dark"] .sup-th-meta { color: #8aaad0; }
html[data-theme="dark"] .sup-th-body { color: #c8d8f0; }
html[data-theme="dark"] .sup-badge-open        { background: rgba(245,158,11,.2); color: #fcd34d; border-color: rgba(245,158,11,.4); }
html[data-theme="dark"] .sup-badge-in_progress { background: rgba(99,140,255,.2); color: #93c5fd; border-color: rgba(99,140,255,.4); }
html[data-theme="dark"] .sup-badge-resolved    { background: rgba(52,211,153,.2); color: #6ee7b7; border-color: rgba(52,211,153,.4); }
html[data-theme="dark"] .sup-badge-closed      { background: rgba(148,163,184,.12); color: #94a3b8; border-color: rgba(148,163,184,.25); }
html[data-theme="dark"] .sup-reply { background: #131b2e; border-color: #1e2d4a; box-shadow: none; }
html[data-theme="dark"] .sup-reply--admin { background: rgba(80,128,255,.07); border-left-color: #5080ff; }
html[data-theme="dark"] .sup-reply-author { color: #e0eaff; }
html[data-theme="dark"] .sup-reply-team-badge { background: rgba(99,140,255,.2); color: #93c5fd; }
html[data-theme="dark"] .sup-reply-time { color: #6f82a6; }
html[data-theme="dark"] .sup-reply-body { color: #c8d8f0; }
html[data-theme="dark"] .sup-waiting { background: #0f1a30; border-color: #1e2d4a; }
html[data-theme="dark"] .sup-waiting p { color: #8aaad0; }
html[data-theme="dark"] .sup-close-btn { border-color: #1e2d4a; color: #6f82a6; }
html[data-theme="dark"] .sup-close-btn:hover { border-color: #ef4444; color: #fca5a5; }
</style>

<div class="sup-show-page">
    <a href="<?= site_url('users/support') ?>" class="sup-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        My Tickets
    </a>

    <div class="sup-ticket-header sup-th-<?= esc($status) ?>">
        <div class="sup-th-top">
            <h1 class="sup-th-subject"><?= esc($ticket['subject']) ?></h1>
            <span class="sup-status-badge sup-badge-<?= esc($status) ?>"><?= esc($statusLabel) ?></span>
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
                <span class="sup-reply-team-badge">Support Team</span>
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
