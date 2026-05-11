<?php
$headerUserName = (string) ($headerUserName ?? 'Student');
$currentTitle = (string) ($currentTitle ?? '');
$navItems = [
    ['label' => 'Home', 'url' => site_url('users'), 'title' => 'My Portal', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'],
    ['label' => 'My Voice', 'url' => site_url('users/feedback'), 'title' => 'My Voice', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'],
    ['label' => 'Speak Up', 'url' => site_url('users/feedback/submit'), 'title' => 'Share Feedback', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>'],
    ['label' => 'Announcements', 'url' => site_url('users/announcements'), 'title' => 'Announcements', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>'],
    ['label' => 'Settings', 'url' => site_url('settings'), 'title' => 'Settings', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'],
];
?>
<div class="portal-nav-wrap" id="portal-nav-panel" role="navigation" aria-label="Main navigation">
    <nav class="portal-nav">
        <?php foreach ($navItems as $item): ?>
            <a href="<?= $item['url'] ?>" class="<?= $currentTitle === $item['title'] ? 'active' : '' ?>">
                <span class="portal-nav-icon"><?= $item['icon'] ?></span>
                <?= esc($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="portal-user-info portal-user-info--drawer">
        <span class="avatar avatar-small avatar-<?= esc($headerAvatarColor ?? 'blue') ?>"><?= esc(strtoupper(substr($headerUserName, 0, 1))) ?></span>
        <span class="portal-user-name"><?= esc($headerUserName) ?></span>
        <?= $this->include('partials/logout_button') ?>
    </div>
</div>
<div class="portal-header-end">
    <?= $this->include('partials/theme_toggle') ?>
    <div class="portal-user-info portal-user-info--topbar">
        <span class="avatar avatar-small avatar-<?= esc($headerAvatarColor ?? 'blue') ?>"><?= esc(strtoupper(substr($headerUserName, 0, 1))) ?></span>
        <span class="portal-user-name"><?= esc($headerUserName) ?></span>
        <?= $this->include('partials/logout_button') ?>
    </div>
    <button type="button" class="portal-menu-btn" id="portal-menu-btn" aria-expanded="false" aria-controls="portal-nav-panel" aria-label="Open menu">
        <span class="portal-menu-btn__line" aria-hidden="true"></span>
        <span class="portal-menu-btn__line" aria-hidden="true"></span>
        <span class="portal-menu-btn__line" aria-hidden="true"></span>
    </button>
</div>
