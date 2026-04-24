<?= $this->extend('social/layout') ?>

<?= $this->section('content') ?>
<?php if (! empty($announcements)): ?>
    <section class="announcements-banner">
        <div class="announcements-header">
            <span class="announcements-icon">📢</span>
            <h3>Announcements</h3>
        </div>
        <div class="announcements-scroll">
            <?php foreach ($announcements as $ann): ?>
                <div class="announcement-card">
                    <h4><?= esc((string) $ann['title']) ?></h4>
                    <p><?= nl2br(esc((string) $ann['body'])) ?></p>
                    <span class="announcement-date"><?= esc(date('M d, Y', strtotime((string) $ann['created_at']))) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
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