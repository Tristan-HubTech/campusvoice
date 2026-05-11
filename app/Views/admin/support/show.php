<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<?php
$status   = (string) $ticket['status'];
$priority = (string) $ticket['priority'];
$name     = trim(esc($ticket['first_name'] ?? '') . ' ' . esc($ticket['last_name'] ?? ''));
$perms    = $adminUser['permissions'] ?? [];

$statusCfg = [
    'open'        => ['cls' => 'open',   'label' => 'Open'],
    'in_progress' => ['cls' => 'prog',   'label' => 'In Progress'],
    'resolved'    => ['cls' => 'res',    'label' => 'Resolved'],
    'closed'      => ['cls' => 'closed', 'label' => 'Closed'],
];
$priCfg = [
    'low'    => ['cls' => 'low',    'label' => 'Low'],
    'normal' => ['cls' => 'normal', 'label' => 'Normal'],
    'high'   => ['cls' => 'high',   'label' => 'High'],
    'urgent' => ['cls' => 'urgent', 'label' => 'Urgent'],
];
$sc = $statusCfg[$status]   ?? $statusCfg['closed'];
$pc = $priCfg[$priority]    ?? $priCfg['normal'];
?>
<style>
/* ── Admin Support Detail ── */
.adsh-back {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .8rem; color: var(--cv-muted); text-decoration: none;
    margin-bottom: 1.25rem; transition: color .15s;
}
.adsh-back:hover { color: var(--cv-ink); text-decoration: none; }

.adsh-layout {
    display: grid;
    grid-template-columns: 1fr 255px;
    gap: 1.1rem; align-items: start;
}
@media (max-width: 780px) { .adsh-layout { grid-template-columns: 1fr; } }

/* Ticket header */
.adsh-ticket-card {
    background: var(--cv-surface); border: 1px solid var(--cv-border);
    border-radius: 12px; padding: 1.35rem 1.5rem;
    margin-bottom: .9rem; position: relative; overflow: hidden;
    box-shadow: var(--cv-shadow-sm);
}
.adsh-ticket-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
}
.adsh-tc-open::before   { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.adsh-tc-prog::before   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.adsh-tc-res::before    { background: linear-gradient(90deg, #10b981, #34d399); }
.adsh-tc-closed::before { background: linear-gradient(90deg, #94a3b8, #cbd5e1); }

.adsh-tc-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap; margin-bottom: .55rem;
}
.adsh-tc-subject { font-size: 1rem; font-weight: 700; color: var(--cv-ink); margin: 0; flex: 1; min-width: 0; }
.adsh-tc-badges  { display: flex; gap: .35rem; flex-wrap: wrap; flex-shrink: 0; }
.adsh-tc-meta    { font-size: .76rem; color: var(--cv-muted); margin-bottom: .9rem; }
.adsh-tc-body {
    font-size: .88rem; color: var(--cv-ink); line-height: 1.7;
    white-space: pre-wrap; margin: 0;
    padding: .85rem 1rem;
    background: var(--cv-surface-2); border-radius: 8px;
    border: 1px solid var(--cv-border);
}

.adsh-pill {
    display: inline-flex; align-items: center;
    padding: .2rem .65rem; border-radius: 999px;
    font-size: .7rem; font-weight: 600; white-space: nowrap;
}
.adsh-pill--open   { background: #fef3c7; color: #92400e; }
.adsh-pill--prog   { background: #dbeafe; color: #1e3a8a; }
.adsh-pill--res    { background: #d1fae5; color: #065f46; }
.adsh-pill--closed { background: #f1f5f9; color: #475569; }
.adsh-pill--low    { background: #f1f5f9; color: #475569; }
.adsh-pill--normal { background: #dbeafe; color: #1e3a8a; }
.adsh-pill--high   { background: #ffedd5; color: #9a3412; }
.adsh-pill--urgent { background: #fee2e2; color: #991b1b; }

/* Thread */
.adsh-thread { display: flex; flex-direction: column; gap: .65rem; margin-bottom: .9rem; }
.adsh-reply {
    background: var(--cv-surface); border: 1px solid var(--cv-border);
    border-radius: 10px; padding: 1rem 1.25rem;
    box-shadow: var(--cv-shadow-xs);
}
.adsh-reply--admin {
    border-left: 3px solid var(--cv-navy-600, #235096);
    background: var(--cv-surface-3);
}
.adsh-reply-meta {
    display: flex; align-items: center; gap: .45rem; flex-wrap: wrap;
    margin-bottom: .5rem;
}
.adsh-reply-author { font-size: .8rem; font-weight: 600; color: var(--cv-ink); }
.adsh-reply-team {
    display: inline-flex; align-items: center; gap: .2rem;
    padding: .08rem .5rem; border-radius: 999px;
    font-size: .67rem; font-weight: 600;
    background: var(--cv-blue-bg, #e6efff); color: var(--cv-blue, #1a4ab8);
}
.adsh-reply-time { font-size: .72rem; color: var(--cv-muted); margin-left: auto; }
.adsh-reply-body { font-size: .86rem; color: var(--cv-ink); line-height: 1.65; white-space: pre-wrap; margin: 0; }

/* Reply form */
.adsh-reply-form {
    background: var(--cv-surface); border: 1px solid var(--cv-border);
    border-radius: 12px; padding: 1.25rem 1.5rem;
    box-shadow: var(--cv-shadow-sm);
}
.adsh-reply-form-title { font-size: .88rem; font-weight: 600; color: var(--cv-ink); margin: 0 0 .75rem; }
.adsh-textarea {
    width: 100%; padding: .6rem .85rem;
    border: 1.5px solid var(--cv-border, #dce6f5);
    border-radius: 8px;
    background: #ffffff; color: var(--cv-ink);
    font-size: .86rem; resize: vertical; box-sizing: border-box;
    font-family: inherit; outline: none; line-height: 1.6;
    transition: border-color .15s, box-shadow .15s;
    margin-bottom: .75rem;
}
.adsh-textarea:focus {
    border-color: var(--cv-navy-600, #235096);
    box-shadow: 0 0 0 3px rgba(35,80,150,.1);
}
.adsh-send-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem 1.2rem;
    background: var(--cv-navy-800, #1a365d); color: #fff;
    border: none; border-radius: 8px;
    font-size: .82rem; font-weight: 600; cursor: pointer;
    font-family: inherit; transition: opacity .15s, transform .12s;
}
.adsh-send-btn:hover { opacity: .88; transform: translateY(-1px); }

/* Sidebar */
.adsh-sidebar { display: flex; flex-direction: column; gap: .85rem; }
.adsh-sidebar-card {
    background: var(--cv-surface); border: 1px solid var(--cv-border);
    border-radius: 12px; padding: 1rem 1.15rem;
    box-shadow: var(--cv-shadow-xs);
}
.adsh-sidebar-label {
    font-size: .7rem; font-weight: 700; color: var(--cv-muted);
    letter-spacing: .05em; text-transform: uppercase; margin: 0 0 .7rem;
}
.adsh-sidebar-select {
    width: 100%; padding: .45rem .7rem;
    border: 1.5px solid var(--cv-border, #dce6f5);
    border-radius: 8px;
    background: #ffffff; color: var(--cv-ink);
    font-size: .82rem; margin-bottom: .6rem; box-sizing: border-box;
    font-family: inherit; outline: none; cursor: pointer;
    transition: border-color .15s;
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237888a8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right .6rem center;
    padding-right: 1.8rem;
}
.adsh-sidebar-select:focus { border-color: var(--cv-navy-600, #235096); }
.adsh-info-row { display: flex; justify-content: space-between; align-items: center; font-size: .8rem; margin-bottom: .45rem; }
.adsh-info-row:last-child { margin-bottom: 0; }
.adsh-info-key { color: var(--cv-muted); font-weight: 500; }
.adsh-info-val { color: var(--cv-ink); font-weight: 600; text-align: right; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.adsh-autosave-hint { font-size: .62rem; font-weight: 500; color: var(--cv-muted); text-transform: none; letter-spacing: 0; opacity: .7; }

/* ── Dark mode overrides ── */
[data-theme="dark"] .adsh-textarea,
[data-theme="dark"] .adsh-sidebar-select {
    background: #0f1c30; border-color: rgba(255,255,255,.14); color: #e4ecff;
}
[data-theme="dark"] .adsh-textarea:focus { border-color: #638cff; box-shadow: 0 0 0 3px rgba(99,140,255,.15); }
[data-theme="dark"] .adsh-sidebar-select:focus { border-color: #638cff; }
[data-theme="dark"] .adsh-sidebar-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236f82a6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
}
[data-theme="dark"] .adsh-pill--open   { background: rgba(245,158,11,.18); color: #fcd34d; }
[data-theme="dark"] .adsh-pill--prog   { background: rgba(99,140,255,.18); color: #93c5fd; }
[data-theme="dark"] .adsh-pill--res    { background: rgba(52,211,153,.18); color: #6ee7b7; }
[data-theme="dark"] .adsh-pill--closed { background: rgba(148,163,184,.13); color: #94a3b8; }
[data-theme="dark"] .adsh-pill--high   { background: rgba(249,115,22,.18); color: #fb923c; }
[data-theme="dark"] .adsh-pill--urgent { background: rgba(239,68,68,.18); color: #fca5a5; }
[data-theme="dark"] .adsh-reply-team   { background: rgba(99,140,255,.2); color: #93c5fd; }
</style>

<a href="<?= site_url('admin/support') ?>" class="adsh-back">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    All Tickets
</a>

<div class="adsh-layout">

    <!-- Main column -->
    <div>
        <div class="adsh-ticket-card adsh-tc-<?= esc($sc['cls']) ?>">
            <div class="adsh-tc-top">
                <h2 class="adsh-tc-subject"><?= esc($ticket['subject']) ?></h2>
                <div class="adsh-tc-badges">
                    <span class="adsh-pill adsh-pill--<?= esc($pc['cls']) ?>"><?= esc($pc['label']) ?></span>
                    <span class="adsh-pill adsh-pill--<?= esc($sc['cls']) ?>"><?= esc($sc['label']) ?></span>
                </div>
            </div>
            <div class="adsh-tc-meta">
                By <?= $name ?: esc($ticket['user_email'] ?? '') ?>
                &middot; <?= date('M j, Y g:i A', strtotime($ticket['created_at'])) ?>
                &middot; <?= esc(ucfirst($ticket['category'])) ?>
            </div>
            <p class="adsh-tc-body"><?= esc($ticket['message']) ?></p>
        </div>

        <?php if (! empty($replies)): ?>
        <div class="adsh-thread">
            <?php foreach ($replies as $reply):
                $isAdmin = ! empty($reply['admin_name']);
                $rName   = $isAdmin
                    ? esc($reply['admin_name'])
                    : esc(trim(($reply['first_name'] ?? '') . ' ' . ($reply['last_name'] ?? '')) ?: 'Student');
            ?>
            <div class="adsh-reply <?= $isAdmin ? 'adsh-reply--admin' : '' ?>">
                <div class="adsh-reply-meta">
                    <span class="adsh-reply-author"><?= $rName ?></span>
                    <?php if ($isAdmin): ?>
                    <span class="adsh-reply-team">
                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Support Team
                    </span>
                    <?php endif; ?>
                    <span class="adsh-reply-time"><?= date('M j, Y g:i A', strtotime($reply['created_at'])) ?></span>
                </div>
                <p class="adsh-reply-body"><?= esc($reply['message']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (! in_array($status, ['closed', 'resolved'], true) && ! empty($perms['support.reply'])): ?>
        <div class="adsh-reply-form">
            <h3 class="adsh-reply-form-title">Post Reply</h3>
            <form action="<?= site_url('admin/support/' . $ticket['id'] . '/reply') ?>" method="post">
                <?= csrf_field() ?>
                <textarea name="message" class="adsh-textarea" required rows="4"
                    placeholder="Write your reply to the student..."></textarea>
                <button type="submit" class="adsh-send-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Send Reply
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="adsh-sidebar">

        <div class="adsh-sidebar-card">
            <p class="adsh-sidebar-label">Ticket Info</p>
            <div class="adsh-info-row">
                <span class="adsh-info-key">ID</span>
                <span class="adsh-info-val">#<?= (int) $ticket['id'] ?></span>
            </div>
            <div class="adsh-info-row">
                <span class="adsh-info-key">Student</span>
                <span class="adsh-info-val"><?= $name ?: esc($ticket['user_email'] ?? '') ?></span>
            </div>
            <div class="adsh-info-row">
                <span class="adsh-info-key">Category</span>
                <span class="adsh-info-val"><?= esc(ucfirst($ticket['category'])) ?></span>
            </div>
            <div class="adsh-info-row">
                <span class="adsh-info-key">Opened</span>
                <span class="adsh-info-val"><?= date('M j, Y', strtotime($ticket['created_at'])) ?></span>
            </div>
        </div>

        <?php if (! empty($perms['support.change_status'])): ?>
        <div class="adsh-sidebar-card">
            <p class="adsh-sidebar-label">Status <span class="adsh-autosave-hint">auto-saves</span></p>
            <form action="<?= site_url('admin/support/' . $ticket['id'] . '/status') ?>" method="post">
                <?= csrf_field() ?>
                <select name="status" class="adsh-sidebar-select" onchange="this.form.submit()">
                    <?php foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $status === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="adsh-sidebar-card">
            <p class="adsh-sidebar-label">Priority <span class="adsh-autosave-hint">auto-saves</span></p>
            <form action="<?= site_url('admin/support/' . $ticket['id'] . '/priority') ?>" method="post">
                <?= csrf_field() ?>
                <select name="priority" class="adsh-sidebar-select" onchange="this.form.submit()">
                    <?php foreach (['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $priority === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>


    </div>
</div>

<?= $this->endSection() ?>
