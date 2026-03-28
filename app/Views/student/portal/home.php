<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page">
    <div class="portal-welcome">
        <div>
            <h2>Welcome back, <?= esc((string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($studentUser['name'] ?? 'Student'))) ?>!</h2>
            <p class="muted">Here's your portal overview.</p>
        </div>
        <a href="<?= site_url('users/feedback/submit') ?>" class="btn-primary">+ Submit Feedback</a>
    </div>

    <section class="portal-card feed-home-shell" id="composer">
        <div class="card-head">
            <h3>Community Feed</h3>
            <a href="<?= site_url('feed') ?>" class="link-more">Open full page</a>
        </div>

        <?php if (empty($posts)): ?>
            <section class="panel-card empty-card">
                <h2>No posts yet</h2>
                <p>The feed is ready. Head to the full page to start the conversation.</p>
            </section>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?= view('social/_post_card', ['post' => $post, 'currentUser' => $currentUser]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</div>
<?= $this->endSection() ?>
