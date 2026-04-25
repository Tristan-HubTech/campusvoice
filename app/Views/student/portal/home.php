<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="home-feed">

    <!-- Welcome hero -->
    <div class="home-hero">
        <div class="home-hero__left">
            <div class="home-hero__avatar">
                <?= esc(strtoupper(substr((string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($studentUser['name'] ?? 'S')), 0, 1))) ?>
            </div>
            <div>
                <?php $greeting = ! empty($studentUser['is_new_user']) ? 'Welcome,' : 'Welcome back,'; ?>
            <h2 class="home-hero__title"><?= $greeting ?> <?= esc((string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($studentUser['name'] ?? 'Student'))) ?>!</h2>
                <p class="home-hero__sub">Your campus voice matters.</p>
            </div>
        </div>
        <a href="<?= site_url('users/feedback/submit') ?>" class="btn-primary">+ Share Feedback</a>
    </div>

    <?php if (!empty($announcements)): ?>
    <section class="home-announce">
        <div class="home-section-label">
            <span>📢</span> Announcements
        </div>
        <div class="home-announce__list">
            <?php foreach ($announcements as $ann):
                $isPinned   = (int)($ann['pinned'] ?? 0) === 1;
                $annBody    = (string) ($ann['body'] ?? '');
                $annTrim    = trim(preg_replace('/\s+/', ' ', $annBody));
                $hasMore    = mb_strlen($annTrim) > 100;
                $annPreview = $hasMore ? mb_substr($annTrim, 0, 100) . '…' : $annTrim;
            ?>
            <div class="home-announcement-card<?= $isPinned ? ' is-pinned' : '' ?>">
                <div class="home-announcement-card__head">
                    <h4 class="home-announcement-card__title">
                        <?php if ($isPinned): ?>
                            <span class="home-announcement-card__badge">📌 Pinned</span>
                        <?php endif; ?>
                        <?= esc((string) $ann['title']) ?>
                    </h4>
                    <span class="home-announcement-card__arrow" aria-hidden="true">▼</span>
                </div>
                <p class="home-announcement-card__preview"><?= esc($annPreview) ?></p>
                <div class="home-announcement-card__full"><?= nl2br(esc($annBody)) ?></div>
                <span class="home-announcement-card__date"><?= esc(date('M d, Y g:i A', strtotime((string) $ann['created_at']))) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="<?= site_url('users/announcements') ?>" class="home-announce__view-all-gold">View All Announcements →</a>
    </section>
    <?php endif; ?>

    <section class="home-feed-section">
        <div class="home-section-label">
            <span>🌐</span> Community Feed
            <a href="<?= site_url('feed') ?>" class="home-feed-more">See all →</a>
        </div>
        <?php if (empty($posts)): ?>
            <section class="panel-card empty-card">
                <h2>No posts yet</h2>
                <p>The feed is ready. Publish the first update and start the conversation.</p>
            </section>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?= view('social/_post_card', ['post' => $post, 'currentUser' => $currentUser]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.home-announcement-card').forEach(card => {
        card.addEventListener('click', function() {
            this.classList.toggle('expanded');
        });
        
        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.classList.toggle('expanded');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
