<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php
$fbStatus  = (string) ($feedback['status']        ?? 'pending');
$fbType    = (string) ($feedback['type']           ?? 'suggestion');
$fbCat     = (string) ($feedback['category_name'] ?? 'General');
$fbSubject = (string) ($feedback['subject']        ?? 'Feedback Detail');
$fbMsg     = (string) ($feedback['message']        ?? '');
$fbDate    = (string) ($feedback['created_at']     ?? 'now');
$vip       = trim((string) ($feedback['image_path'] ?? ''));
$vurl      = $vip !== '' ? \App\Libraries\FeedbackImageStorage::publicUrl($vip) : '';

$typeIcon = match($fbType) { 'complaint' => '⚡', 'praise' => '⭐', default => '💡' };
$typeColor = match($fbType) { 'complaint' => '#ef4444', 'praise' => '#22c55e', default => '#f59e0b' };
$typeColorRgb = match($fbType) { 'complaint' => '239,68,68', 'praise' => '34,197,94', default => '245,158,11' };

$statusConfig = [
    'pending'  => ['label' => 'Pending',      'icon' => '🕐', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'border' => '#fcd34d', 'msg' => 'Your feedback has been received and is awaiting admin review.'],
    'approved' => ['label' => 'Approved',     'icon' => '✅', 'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#86efac', 'msg' => 'Your feedback has been approved and is currently being processed by the administration.'],
    'reviewed' => ['label' => 'Under Review', 'icon' => '🔍', 'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#93c5fd', 'msg' => 'Your feedback is currently under review by the administration.'],
    'resolved' => ['label' => 'Resolved',     'icon' => '🎉', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#6ee7b7', 'msg' => 'Your feedback has been resolved. Thank you for helping improve the campus!'],
    'rejected' => ['label' => 'Rejected',     'icon' => '❌', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fca5a5', 'msg' => 'Your feedback has been declined. See admin replies for more details.'],
];
$sc = $statusConfig[$fbStatus] ?? $statusConfig['pending'];

$steps = [
    ['key' => 'pending',  'label' => 'Submitted',    'icon' => '📤'],
    ['key' => 'reviewed', 'label' => 'Under Review', 'icon' => '🔍'],
    ['key' => 'approved', 'label' => 'Approved',     'icon' => '✅'],
    ['key' => 'resolved', 'label' => 'Resolved',     'icon' => '🎉'],
];
$stepOrder = ['pending' => 0, 'reviewed' => 1, 'approved' => 2, 'resolved' => 3, 'rejected' => 99];
$currentStep = $stepOrder[$fbStatus] ?? 0;
?>

<style>
/* ── View Feedback Premium ── */
.vfb-wrap {
    max-width: 820px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding-bottom: 48px;
}

/* Hero */
.vfb-hero {
    background: linear-gradient(135deg, #0b1f4d 0%, #102a62 55%, #1a3a80 100%);
    border-radius: 22px;
    padding: 28px 30px 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.vfb-hero::before {
    content: '';
    position: absolute;
    right: -60px; top: -60px;
    width: 240px; height: 240px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    pointer-events: none;
}
.vfb-hero::after {
    content: '';
    position: absolute;
    right: 80px; bottom: -80px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,0.03);
    pointer-events: none;
}
.vfb-hero__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
}
.vfb-hero__back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: rgba(255,255,255,0.7);
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    padding: 7px 14px;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 10px;
    background: rgba(255,255,255,0.07);
    transition: background 0.15s, color 0.15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.vfb-hero__back:hover { background: rgba(255,255,255,0.15); color: #fff; text-decoration: none; }
.vfb-hero__title {
    margin: 0;
    font-size: 1.55rem;
    font-weight: 800;
    color: #fff;
    line-height: 1.25;
    letter-spacing: -0.02em;
    font-family: 'Playfair Display', serif;
    max-width: 580px;
}
.vfb-hero__pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
}
.vfb-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    letter-spacing: 0.03em;
    white-space: nowrap;
}
.vfb-pill--cat  { background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.18); }
.vfb-pill--type { border: 1px solid rgba(255,255,255,0.25); color: #fff; }
.vfb-pill--status {
    background: <?= esc($sc['bg']) ?>;
    color: <?= esc($sc['color']) ?>;
    border: 1px solid <?= esc($sc['border']) ?>;
}
.vfb-ref {
    font-size: 0.72rem;
    font-weight: 700;
    color: rgba(255,255,255,0.45);
    letter-spacing: 0.06em;
    font-family: 'SFMono-Regular', Consolas, monospace;
}

/* Timeline */
.vfb-timeline {
    display: flex;
    align-items: center;
    gap: 0;
    background: #fff;
    border: 1px solid #e8eef8;
    border-radius: 16px;
    padding: 18px 24px;
    overflow-x: auto;
}
.vfb-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 72px;
    position: relative;
}
.vfb-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 16px;
    left: calc(50% + 16px);
    right: calc(-50% + 16px);
    height: 2px;
    background: #e2eaf8;
    z-index: 0;
}
.vfb-step--done:not(:last-child)::after,
.vfb-step--active:not(:last-child)::after {
    background: linear-gradient(90deg, #c8972c, #e8eef8);
}
.vfb-step__dot {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    font-size: 0.9rem;
    background: #f0f4ff;
    border: 2px solid #dde6f8;
    position: relative;
    z-index: 1;
    transition: all 0.2s;
}
.vfb-step--done .vfb-step__dot {
    background: linear-gradient(135deg, #c8972c, #a8771c);
    border-color: #c8972c;
    box-shadow: 0 0 0 4px rgba(200,151,44,0.15);
}
.vfb-step--active .vfb-step__dot {
    background: linear-gradient(135deg, #0b1f4d, #1a3f8f);
    border-color: #1a3f8f;
    box-shadow: 0 0 0 4px rgba(26,63,143,0.18);
    animation: vfb-pulse 2s infinite;
}
.vfb-step--rejected .vfb-step__dot {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border-color: #ef4444;
    box-shadow: 0 0 0 4px rgba(239,68,68,0.15);
}
@keyframes vfb-pulse {
    0%, 100% { box-shadow: 0 0 0 4px rgba(26,63,143,0.18); }
    50%       { box-shadow: 0 0 0 8px rgba(26,63,143,0.08); }
}
.vfb-step__label {
    font-size: 0.68rem;
    font-weight: 700;
    color: #9aa5be;
    text-align: center;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    line-height: 1.2;
}
.vfb-step--done .vfb-step__label,
.vfb-step--active .vfb-step__label { color: #0b1f4d; }

/* Cards */
.vfb-card {
    background: #fff;
    border: 1px solid #e8eef8;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 2px 16px rgba(11,31,77,0.06);
}
.vfb-card__head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 22px 14px;
    border-bottom: 1px solid #f0f4ff;
}
.vfb-card__head-icon {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0b1f4d, #1a3a80);
    display: grid;
    place-items: center;
    color: #fff;
    flex-shrink: 0;
}
.vfb-card__head-title {
    font-size: 0.88rem;
    font-weight: 800;
    color: #0b1f4d;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}
.vfb-card__body { padding: 20px 22px; }

/* Message */
.vfb-message {
    font-size: 0.97rem;
    line-height: 1.75;
    color: #2d3a52;
    margin: 0;
    white-space: pre-wrap;
    word-break: break-word;
}

/* Date chip */
.vfb-date-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.76rem;
    color: #8a9ab8;
    font-weight: 500;
    margin-top: 14px;
}

/* Image viewer */
.vfb-image-wrap {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e8eef8;
    background: #0a0f1a;
    display: flex;
    align-items: center;
    justify-content: center;
    max-height: 480px;
}
.vfb-image-wrap img {
    max-width: 100%;
    max-height: 480px;
    object-fit: contain;
    display: block;
    transition: transform 0.3s ease;
    cursor: zoom-in;
}
.vfb-image-wrap img:hover { transform: scale(1.02); }
.vfb-img-open {
    display: block;
    text-align: right;
    margin-top: 8px;
    font-size: 0.77rem;
    color: #c8972c;
    font-weight: 600;
    text-decoration: none;
}
.vfb-img-open:hover { text-decoration: underline; }

/* Status banner */
.vfb-status-banner {
    border-radius: 16px;
    border: 1px solid <?= esc($sc['border']) ?>;
    background: <?= esc($sc['bg']) ?>;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.vfb-status-banner__icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    background: <?= esc($sc['color']) ?>1a;
    border: 1px solid <?= esc($sc['border']) ?>;
    display: grid;
    place-items: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.vfb-status-banner__label {
    font-size: 0.78rem;
    font-weight: 800;
    color: <?= esc($sc['color']) ?>;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 2px;
}
.vfb-status-banner__msg {
    font-size: 0.88rem;
    color: <?= esc($sc['color']) ?>cc;
    margin: 0;
    line-height: 1.5;
}

/* Admin reply bubbles */
.vfb-reply-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.vfb-reply {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.vfb-reply__avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0b1f4d, #1a3f8f);
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 800;
    flex-shrink: 0;
    letter-spacing: 0.02em;
    box-shadow: 0 2px 8px rgba(11,31,77,0.2);
}
.vfb-reply__bubble {
    flex: 1;
    background: #f7f9ff;
    border: 1px solid #e2eaf8;
    border-radius: 4px 16px 16px 16px;
    padding: 12px 16px;
}
.vfb-reply__meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
}
.vfb-reply__name { font-size: 0.82rem; font-weight: 800; color: #0b1f4d; }
.vfb-reply__date { font-size: 0.72rem; color: #9aa5be; }
.vfb-reply__badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(11,31,77,0.08);
    color: #0b1f4d;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.vfb-reply__text { font-size: 0.9rem; color: #2d3a52; line-height: 1.65; margin: 0; word-break: break-word; }

/* Empty replies */
.vfb-empty-replies {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 32px 16px;
    text-align: center;
}
.vfb-empty-replies__icon { font-size: 2rem; opacity: 0.4; }
.vfb-empty-replies__text { font-size: 0.88rem; color: #9aa5be; margin: 0; }
</style>

<div class="vfb-wrap">

    <!-- ── Hero Header ── -->
    <div class="vfb-hero">
        <div class="vfb-hero__top">
            <div>
                <div class="vfb-ref">#FBK-<?= str_pad((string)(int)($feedback['id'] ?? 0), 4, '0', STR_PAD_LEFT) ?></div>
                <h1 class="vfb-hero__title"><?= esc($fbSubject) ?></h1>
            </div>
            <a href="<?= site_url('users/feedback') ?>" class="vfb-hero__back">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                My Voice
            </a>
        </div>
        <div class="vfb-hero__pills">
            <span class="vfb-pill vfb-pill--cat">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                <?= esc($fbCat) ?>
            </span>
            <span class="vfb-pill vfb-pill--type" style="background:rgba(<?= $typeColorRgb ?>,0.18);border-color:rgba(<?= $typeColorRgb ?>,0.4);">
                <?= $typeIcon ?> <?= esc(ucfirst($fbType)) ?>
            </span>
            <span class="vfb-pill vfb-pill--status"><?= $sc['icon'] ?> <?= esc($sc['label']) ?></span>
        </div>
    </div>

    <!-- ── Status Timeline ── -->
    <?php if ($fbStatus !== 'rejected'): ?>
    <div class="vfb-timeline">
        <?php foreach ($steps as $i => $step):
            $stepIdx = $stepOrder[$step['key']] ?? 0;
            $cls = '';
            if ($stepIdx < $currentStep)       $cls = 'vfb-step--done';
            elseif ($stepIdx === $currentStep) $cls = 'vfb-step--active';
        ?>
        <div class="vfb-step <?= $cls ?>">
            <div class="vfb-step__dot"><?= $stepIdx < $currentStep ? '✓' : $step['icon'] ?></div>
            <div class="vfb-step__label"><?= esc($step['label']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ── Status Banner ── -->
    <div class="vfb-status-banner">
        <div class="vfb-status-banner__icon"><?= $sc['icon'] ?></div>
        <div>
            <div class="vfb-status-banner__label"><?= esc($sc['label']) ?></div>
            <p class="vfb-status-banner__msg"><?= esc($sc['msg']) ?></p>
        </div>
    </div>

    <!-- ── Your Message ── -->
    <div class="vfb-card">
        <div class="vfb-card__head">
            <div class="vfb-card__head-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <span class="vfb-card__head-title">Your Message</span>
        </div>
        <div class="vfb-card__body">
            <p class="vfb-message"><?= nl2br(esc($fbMsg)) ?></p>
            <div class="vfb-date-chip">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Submitted <?= esc(date('F d, Y · g:i A', strtotime($fbDate))) ?>
            </div>

            <?php if ($vurl !== ''): ?>
            <div style="margin-top: 18px;">
                <div style="font-size:0.78rem; font-weight:700; color:#8a9ab8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:10px;">
                    📎 Photo Attachment
                </div>
                <div class="vfb-image-wrap">
                    <img src="<?= esc($vurl) ?>" alt="Feedback attachment" loading="lazy" decoding="async">
                </div>
                <a href="<?= esc($vurl) ?>" target="_blank" rel="noopener noreferrer" class="vfb-img-open">
                    Open full size ↗
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Admin Replies ── -->
    <div class="vfb-card">
        <div class="vfb-card__head">
            <div class="vfb-card__head-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2v4l-4-4H9a2 2 0 0 1-2-2v-1"/><path d="M15 6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2v4l4-4h2a2 2 0 0 0 2-2v-1"/></svg>
            </div>
            <span class="vfb-card__head-title">Admin Replies</span>
            <?php if (!empty($replies)): ?>
            <span style="margin-left:auto; font-size:0.72rem; font-weight:700; color:#9aa5be;"><?= count($replies) ?> reply<?= count($replies) !== 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </div>
        <div class="vfb-card__body">
            <?php if (!empty($replies)): ?>
            <div class="vfb-reply-list">
                <?php foreach ($replies as $reply):
                    $adminName = trim(((string)($reply['first_name'] ?? '')) . ' ' . ((string)($reply['last_name'] ?? ''))) ?: 'Administrator';
                    $initials = strtoupper(substr($adminName, 0, 1) . (strpos($adminName, ' ') !== false ? substr($adminName, strpos($adminName, ' ') + 1, 1) : ''));
                ?>
                <div class="vfb-reply">
                    <div class="vfb-reply__avatar"><?= esc($initials) ?></div>
                    <div class="vfb-reply__bubble">
                        <div class="vfb-reply__meta">
                            <span class="vfb-reply__name"><?= esc($adminName) ?></span>
                            <span class="vfb-reply__badge">Admin</span>
                            <span class="vfb-reply__date"><?= esc(date('M d, Y · g:i A', strtotime((string)($reply['created_at'] ?? 'now')))) ?></span>
                        </div>
                        <p class="vfb-reply__text"><?= nl2br(esc((string)($reply['message'] ?? ''))) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="vfb-empty-replies">
                <div class="vfb-empty-replies__icon">💬</div>
                <p class="vfb-empty-replies__text">No admin replies yet.<br>We'll respond as soon as possible.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
