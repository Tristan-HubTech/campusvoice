<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page">
    <div class="portal-welcome">
        <div>
            <h2>Welcome back, <?= esc((string) ($studentUser['name'] ?? 'Student')) ?>!</h2>
            <p class="muted">Here's an overview of your CampusVoice activity.</p>
        </div>
        <a href="<?= site_url('portal/feedback/submit') ?>" class="btn-primary">+ Submit Feedback</a>
    </div>

    <div class="stats-row">
        <div class="stat-chip">
            <span class="stat-label">Total Submitted</span>
            <strong class="stat-value"><?= (int) ($stats['total'] ?? 0) ?></strong>
        </div>
        <div class="stat-chip">
            <span class="stat-label">Pending Review</span>
            <strong class="stat-value"><?= (int) ($stats['new'] ?? 0) ?></strong>
        </div>
        <div class="stat-chip">
            <span class="stat-label">Reviewed</span>
            <strong class="stat-value"><?= (int) ($stats['reviewed'] ?? 0) ?></strong>
        </div>
        <div class="stat-chip">
            <span class="stat-label">Resolved</span>
            <strong class="stat-value"><?= (int) ($stats['resolved'] ?? 0) ?></strong>
        </div>
    </div>

    <div class="home-grid">
        <section class="portal-card">
            <div class="card-head">
                <h3>Recent Submissions</h3>
                <a href="<?= site_url('portal/feedback') ?>" class="link-more">View all</a>
            </div>
            <?php if (! empty($myFeedback)): ?>
                <ul class="feedback-mini-list">
                    <?php foreach ($myFeedback as $item): ?>
                        <li>
                            <a href="<?= site_url('portal/feedback/' . (int) $item['id']) ?>">
                                <span class="fb-subject"><?= esc(strlen((string) $item['subject']) > 60 ? substr((string) $item['subject'], 0, 60) . '…' : (string) $item['subject']) ?></span>
                                <span class="fb-meta">
                                    <?= esc((string) ($item['category_name'] ?? 'General')) ?>
                                    &middot;
                                    <span class="pill status-<?= esc((string) $item['status']) ?>"><?= esc(ucfirst((string) $item['status'])) ?></span>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="muted empty-hint">No submissions yet. <a href="<?= site_url('portal/feedback/submit') ?>">Submit your first feedback.</a></p>
            <?php endif; ?>
        </section>

        <section class="portal-card">
            <div class="card-head">
                <h3>Latest Announcements</h3>
                <a href="<?= site_url('portal/announcements') ?>" class="link-more">View all</a>
            </div>
            <?php if (! empty($announcements)): ?>
                <ul class="announce-mini-list">
                    <?php foreach ($announcements as $item): ?>
                        <li>
                            <strong><?= esc((string) $item['title']) ?></strong>
                            <p><?= esc(strlen((string) $item['body']) > 100 ? substr((string) $item['body'], 0, 100) . '…' : (string) $item['body']) ?></p>
                            <small class="muted"><?= esc(date('M d, Y', strtotime((string) ($item['created_at'] ?? 'now')))) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="muted empty-hint">No announcements yet.</p>
            <?php endif; ?>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
