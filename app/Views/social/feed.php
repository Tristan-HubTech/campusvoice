<?= $this->extend('social/layout') ?>

<?= $this->section('content') ?>
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
<?php endif; ?>

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
<?= $this->endSection() ?>