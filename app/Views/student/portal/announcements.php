<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page narrow">
    <div class="portal-welcome">
        <div>
            <h2>Campus Announcements</h2>
            <p class="muted">Latest updates from the administration.</p>
        </div>
    </div>

    <section class="portal-card">
        <?php if (! empty($announcements)): ?>
            <ul class="announce-list-full">
                <?php foreach ($announcements as $announcement): ?>
                    <li class="announce-item-full">
                        <h3><?= esc((string) ($announcement['title'] ?? 'Untitled')) ?></h3>
                        <p><?= nl2br(esc((string) ($announcement['body'] ?? ''))) ?></p>
                        <small class="muted">
                            Published: <?= esc(date('M d, Y H:i', strtotime((string) ($announcement['publish_at'] ?? $announcement['created_at'] ?? 'now')))) ?>
                        </small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted empty-hint">No announcements found.</p>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
