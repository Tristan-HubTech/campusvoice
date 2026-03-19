<?= $this->extend('social/layout') ?>

<?= $this->section('content') ?>
<?php
$fullName = trim((string) $profileUser['first_name'] . ' ' . (string) $profileUser['last_name']);
$avatarColor = (string) ($profileDetails['avatar_color'] ?? 'blue');
$initials = strtoupper(substr((string) $profileUser['first_name'], 0, 1) . substr((string) $profileUser['last_name'], 0, 1));
?>
<div class="profile-shell">
    <section class="panel-card profile-hero">
        <div class="avatar avatar-large avatar-<?= esc($avatarColor) ?>"><?= esc($initials) ?></div>
        <div class="profile-copy">
            <h2><?= esc($fullName) ?></h2>
            <p><?= esc((string) ($profileDetails['bio'] ?: 'No bio yet.')) ?></p>
        </div>
        <div class="stats-grid compact profile-stats">
            <div class="stat-card"><span>Posts</span><strong><?= (int) ($profileStats['posts'] ?? 0) ?></strong></div>
            <div class="stat-card"><span>Comments</span><strong><?= (int) ($profileStats['comments'] ?? 0) ?></strong></div>
            <div class="stat-card"><span>Reactions</span><strong><?= (int) ($profileStats['reactions'] ?? 0) ?></strong></div>
            <div class="stat-card"><span>Shares</span><strong><?= (int) ($profileStats['shares'] ?? 0) ?></strong></div>
        </div>
    </section>

    <section class="feed-column">
        <?php if (empty($posts)): ?>
            <section class="panel-card empty-card">
                <h2>No posts yet</h2>
                <p>This profile has not posted to the community feed yet.</p>
            </section>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?= view('social/_post_card', ['post' => $post, 'currentUser' => $currentUser]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>