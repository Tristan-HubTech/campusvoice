<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page narrow">
    <div class="announce-page-head">
        <div class="announce-page-icon" aria-hidden="true">📢</div>
        <div>
            <h2 class="announce-page-title">Campus Announcements</h2>
            <p class="muted announce-page-sub">Latest updates from the administration.</p>
        </div>
    </div>

    <section class="announce-page-panel">
        <?php if (! empty($announcements)): ?>
            <ul class="announce-list-full">
                <?php foreach ($announcements as $announcement):
                    $title       = trim((string) ($announcement['title'] ?? 'Untitled'));
                    $body        = (string) ($announcement['body'] ?? '');
                    $publishTime = (string) ($announcement['publish_at'] ?? $announcement['created_at'] ?? 'now');
                    $createdAt   = (string) ($announcement['created_at'] ?? 'now');
                    $isNew       = strtotime($createdAt) > strtotime('-3 days');

                    $authorFirst = trim((string) ($announcement['author_first_name'] ?? ''));
                    $authorLast  = trim((string) ($announcement['author_last_name'] ?? ''));
                    $authorName  = trim($authorFirst . ' ' . $authorLast);
                    if ($authorName === '') {
                        $authorName = 'System Admin';
                    }
                ?>
                    <li class="announce-card">
                        <div class="announce-card__head">
                            <div class="announce-card__title-row">
                                <span class="announce-card__icon" aria-hidden="true">📢</span>
                                <h3 class="announce-card__title"><?= esc($title) ?></h3>
                            </div>
                            <div class="announce-card__badges">
                                <span class="announce-badge announce-badge--general">🏷️ General</span>
                                <?php if ($isNew): ?>
                                    <span class="announce-badge announce-badge--new">🆕 New</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="announce-card__body">
                            <p><?= nl2br(esc($body)) ?></p>
                        </div>

                        <div class="announce-card__meta">
                            <span class="announce-card__author">
                                <span aria-hidden="true">👤</span> Posted by <strong><?= esc($authorName) ?></strong>
                            </span>
                            <span class="announce-card__date">
                                <span aria-hidden="true">📅</span> <?= esc(date('M d, Y g:i A', strtotime($publishTime))) ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted empty-hint announce-empty">No announcements yet. Check back later!</p>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
