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
        <a href="<?= site_url('portal/feedback') ?>" class="link-more">Back to My Feedback</a>
    </div>

    <section class="portal-card">
        <h3>Your Message</h3>
        <p class="message-box"><?= nl2br(esc((string) ($feedback['message'] ?? ''))) ?></p>
        <p class="muted">Submitted: <?= esc(date('M d, Y H:i', strtotime((string) ($feedback['created_at'] ?? 'now')))) ?></p>
    </section>

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
