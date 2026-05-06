<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page narrow">
    <div class="portal-welcome">
        <div>
            <h2><?= esc((string) ($feedback['subject'] ?? 'Feedback Detail')) ?></h2>
            <p class="muted">
                Category: <strong><?= esc((string) ($feedback['category_name'] ?? 'N/A')) ?></strong>
                &middot;
                Type: <strong><?= esc(ucfirst((string) ($feedback['type'] ?? 'suggestion'))) ?></strong>
                &middot;
                Status: <span class="pill status-<?= esc((string) ($feedback['status'] ?? 'new')) ?>"><?= esc(ucfirst((string) ($feedback['status'] ?? 'new'))) ?></span>
            </p>
        </div>
        <a href="<?= site_url('users/feedback') ?>" class="link-more">Back to My Voice</a>
    </div>

    <section class="portal-card">
        <h3>Your Message</h3>
        <p class="message-box"><?= nl2br(esc((string) ($feedback['message'] ?? ''))) ?></p>
        <?php
        $vip = trim((string) ($feedback['image_path'] ?? ''));
        if ($vip !== ''):
            $vurl = \App\Libraries\FeedbackImageStorage::publicUrl($vip);
        ?>
            <h4 class="feedback-attachment-heading">Photo attachment</h4>
            <div class="feedback-detail-image">
                <a href="<?= esc($vurl) ?>" target="_blank" rel="noopener noreferrer" title="Open full size">
                    <img src="<?= esc($vurl) ?>" alt="Feedback attachment" loading="lazy" decoding="async">
                </a>
            </div>
        <?php endif; ?>
        <p class="muted">Submitted: <?= esc(date('M d, Y H:i', strtotime((string) ($feedback['created_at'] ?? 'now')))) ?></p>
    </section>

    <?php $fbStatus = (string) ($feedback['status'] ?? 'new'); ?>
    <?php if ($fbStatus !== 'new'): ?>
    <section class="portal-card fb-status-card fb-status-card--<?= esc($fbStatus) ?>">
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <span class="pill status-<?= esc($fbStatus) ?>"><?= esc(ucfirst($fbStatus)) ?></span>
            <p style="margin:0; font-size:0.9rem;">
                <?php if ($fbStatus === 'resolved'): ?>
                    Your feedback has been resolved.<?php if (! empty($feedback['resolved_at'])): ?> <span class="muted">Resolved on <?= esc(date('M d, Y', strtotime((string) $feedback['resolved_at']))) ?>.</span><?php endif; ?>
                <?php elseif ($fbStatus === 'approved'): ?>
                    Your feedback has been approved and is currently being processed by the administration.
                <?php elseif ($fbStatus === 'rejected'): ?>
                    Your feedback has been declined. Please check admin replies for more details.
                <?php elseif ($fbStatus === 'reviewed'): ?>
                    Your feedback is currently under review by the administration.
                <?php else: ?>
                    Your feedback has been received and is waiting for review.
                <?php endif; ?>
            </p>
        </div>
    </section>
    <?php endif; ?>

    <section class="portal-card">
        <h3>Admin Replies</h3>
        <?php if (! empty($replies)): ?>
            <ul class="reply-list">
                <?php foreach ($replies as $reply): ?>
                    <li class="reply-item">
                        <div class="reply-head">
                            <strong><?= esc(trim(((string) ($reply['first_name'] ?? '')) . ' ' . ((string) ($reply['last_name'] ?? ''))) ?: 'Administrator') ?></strong>
                            <small class="muted"><?= esc(date('M d, Y H:i', strtotime((string) ($reply['created_at'] ?? 'now')))) ?></small>
                        </div>
                        <p><?= nl2br(esc((string) ($reply['message'] ?? ''))) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted empty-hint">No admin replies yet. Please check back soon.</p>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
