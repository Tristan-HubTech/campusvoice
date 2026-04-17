<?= $this->extend('social/layout') ?>

<?= $this->section('content') ?>
<?php
$fullName = trim((string) $profileUser['first_name'] . ' ' . (string) $profileUser['last_name']);
$avatarColor = (string) ($profileDetails['avatar_color'] ?? 'blue');
$initials = strtoupper(substr((string) $profileUser['first_name'], 0, 1) . substr((string) $profileUser['last_name'], 0, 1));
?>
<div class="profile-shell">
    <section class="profile-cover">
        <div class="profile-cover-inner">
            <div class="profile-avatar-wrap">
                <div class="avatar avatar-profile avatar-<?= esc($avatarColor) ?>"><?= esc($initials) ?></div>
            </div>
            <div class="profile-info">
                <h2><?= esc($fullName) ?></h2>
                <p class="profile-bio"><?= esc((string) ($profileDetails['bio'] ?: 'No bio yet.')) ?></p>
            </div>
            <div class="profile-stats-row">
                <div class="profile-stat"><strong><?= (int) ($profileStats['posts'] ?? 0) ?></strong><span>Posts</span></div>
                <div class="profile-stat"><strong><?= (int) ($profileStats['comments'] ?? 0) ?></strong><span>Comments</span></div>
                <div class="profile-stat"><strong><?= (int) ($profileStats['reactions'] ?? 0) ?></strong><span>Reactions</span></div>
            </div>
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