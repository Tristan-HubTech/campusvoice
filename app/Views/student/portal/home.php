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
        <a href="<?= site_url('users/feedback/submit') ?>" class="btn-primary">+ Submit Feedback</a>
    </div>

    <?php if (!empty($announcements)): ?>
    <section class="home-announce">
        <div class="home-section-label">
            <span>📢</span> Announcements
        </div>
        <div class="home-announce__list">
            <?php foreach ($announcements as $ann): ?>
            <div class="home-announce__card">
                <h4 class="home-announce__title"><?= esc((string) $ann['title']) ?></h4>
                <p class="home-announce__body"><?= nl2br(esc((string) $ann['body'])) ?></p>
                <span class="home-announce__date"><?= esc(date('M d, Y', strtotime((string) $ann['created_at']))) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="home-feed-section">
        <div class="home-section-label">
            <span>🌐</span> Community Feed
            <a href="<?= site_url('feed') ?>" class="home-feed-more">See all →</a>
        </div>

        <?php if (empty($posts)): ?>
            <div class="home-empty">
                <div class="home-empty__icon">💬</div>
                <p>No posts yet. Be the first to start the conversation.</p>
                <a href="<?= site_url('feed') ?>" class="btn-primary" style="margin-top:10px;">Go to Feed</a>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?= view('social/_post_card', ['post' => $post, 'currentUser' => $currentUser]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</div>
<?= $this->endSection() ?>
