<?php
$headerUserName = (string) ($headerUserName ?? 'Student');
$currentTitle = (string) ($currentTitle ?? '');
$navItems = [
    ['label' => 'Home', 'url' => site_url('users'), 'title' => 'My Portal'],
    ['label' => 'My Feedback', 'url' => site_url('users/feedback'), 'title' => 'My Submissions'],
    ['label' => 'Submit', 'url' => site_url('users/feedback/submit'), 'title' => 'Submit Feedback'],
    ['label' => 'Announcements', 'url' => site_url('users/announcements'), 'title' => 'Announcements'],
    ['label' => 'Settings', 'url' => site_url('settings'), 'title' => 'Settings'],
];
?>
<div class="portal-nav-wrap" id="portal-nav-panel" role="navigation" aria-label="Main navigation">
    <nav class="portal-nav">
        <?php foreach ($navItems as $item): ?>
            <a href="<?= $item['url'] ?>" class="<?= $currentTitle === $item['title'] ? 'active' : '' ?>"><?= esc($item['label']) ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="portal-user-info portal-user-info--drawer">
        <span class="portal-user-name"><?= esc($headerUserName) ?></span>
        <?= $this->include('partials/logout_button') ?>
    </div>
</div>
<div class="portal-header-end">
    <?= $this->include('partials/theme_toggle') ?>
    <div class="portal-user-info portal-user-info--topbar">
        <span class="portal-user-name"><?= esc($headerUserName) ?></span>
        <?= $this->include('partials/logout_button') ?>
    </div>
    <button type="button" class="portal-menu-btn" id="portal-menu-btn" aria-expanded="false" aria-controls="portal-nav-panel" aria-label="Open menu">
        <span class="portal-menu-btn__line" aria-hidden="true"></span>
        <span class="portal-menu-btn__line" aria-hidden="true"></span>
        <span class="portal-menu-btn__line" aria-hidden="true"></span>
    </button>
</div>
