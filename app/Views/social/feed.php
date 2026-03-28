<?= $this->extend('social/layout') ?>

<?= $this->section('content') ?>
<div class="social-grid">
    <section class="feed-column">
        <?php if (! empty($currentUser['id'])): ?>
            <section class="panel-card composer-card">
                <div class="panel-head">
                    <h2>Share with the community</h2>
                    <span class="summary-muted">Public post</span>
                </div>
                <form method="post" action="<?= site_url('feed/post') ?>" class="composer-form">
                    <textarea name="body" rows="4" placeholder="What is happening on campus today?"><?= esc((string) old('body')) ?></textarea>
                    <div class="composer-actions">
                        <span class="summary-muted">Your post will be visible to everyone.</span>
                        <button type="submit" class="solid-btn">Post Update</button>
                    </div>
                </form>
            </section>
        <?php else: ?>
            <section class="panel-card composer-card guest-card">
                <div class="panel-head">
                    <h2>Campus feed is live</h2>
                    <span class="summary-muted">Public view</span>
                </div>
                <p>Browse what people are posting. Log in or create an account to publish, react, comment, and share.</p>
                <div class="inline-actions">
                    <a href="<?= site_url('users/login') ?>" class="solid-btn">Log in</a>
                    <a href="<?= site_url('users/login?mode=register') ?>" class="ghost-btn">Register</a>
                </div>
            </section>
        <?php endif; ?>

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
<?= $this->endSection() ?>