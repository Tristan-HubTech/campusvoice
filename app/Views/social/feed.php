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

    <aside class="rail-column">
        <section class="panel-card stats-card">
            <div class="panel-head">
                <h2>Community Stats</h2>
            </div>
            <div class="stats-grid compact">
                <div class="stat-card"><span>Posts</span><strong><?= (int) ($stats['posts'] ?? 0) ?></strong></div>
                <div class="stat-card"><span>People</span><strong><?= (int) ($stats['people'] ?? 0) ?></strong></div>
                <div class="stat-card"><span>Comments</span><strong><?= (int) ($stats['comments'] ?? 0) ?></strong></div>
                <div class="stat-card"><span>Reactions</span><strong><?= (int) ($stats['reactions'] ?? 0) ?></strong></div>
                <div class="stat-card"><span>Shares</span><strong><?= (int) ($stats['shares'] ?? 0) ?></strong></div>
                <div class="stat-card"><span>My Feedback</span><strong><?= (int) ($stats['my_feedback'] ?? 0) ?></strong></div>
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <h2>Top Creators</h2>
            </div>
            <div class="user-list">
                <?php foreach ($topCreators as $creator): ?>
                    <a href="<?= site_url('profile/' . (int) $creator['id']) ?>" class="user-list-item">
                        <div class="avatar avatar-small avatar-<?= esc((string) ($creator['avatar_color'] ?? 'blue')) ?>"><?= esc(strtoupper(substr((string) $creator['first_name'], 0, 1) . substr((string) $creator['last_name'], 0, 1))) ?></div>
                        <div>
                            <strong><?= esc(trim((string) $creator['first_name'] . ' ' . (string) $creator['last_name'])) ?></strong>
                            <small><?= (int) $creator['post_total'] ?> posts</small>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <h2>Announcements</h2>
            </div>
            <div class="rail-list">
                <?php if (! empty($announcements)): ?>
                    <?php foreach ($announcements as $item): ?>
                        <article class="rail-item">
                            <strong><?= esc((string) $item['title']) ?></strong>
                            <p><?= esc(strlen((string) $item['body']) > 90 ? substr((string) $item['body'], 0, 90) . '...' : (string) $item['body']) ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="summary-muted">No announcements published yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </aside>
</div>
<?= $this->endSection() ?>