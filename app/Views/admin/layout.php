<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->include('partials/theme_fouc') ?>
    <title><?= esc($title ?? 'Admin Panel') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <?php $cpCssV = is_file(FCPATH . 'assets/admin/control-panel.css') ? filemtime(FCPATH . 'assets/admin/control-panel.css') : '1'; ?>
    <link rel="stylesheet" href="<?= base_url('assets/admin/control-panel.css') . '?v=' . $cpCssV ?>">
    <?php $modCssV = is_file(FCPATH . 'assets/admin/admin-modern.css') ? filemtime(FCPATH . 'assets/admin/admin-modern.css') : '1'; ?>
    <link rel="stylesheet" href="<?= base_url('assets/admin/admin-modern.css') . '?v=' . $modCssV ?>">
    <?= $this->include('partials/theme_styles') ?>
</head>
<body>
<div class="admin-shell">
    <?php
    /* Sidebar computed vars — must come before the aside */
    $navPerms    = $adminUser['permissions'] ?? [];
    $panelUrl    = site_url('admin');
    $activeMenu  = $activeMenu ?? '';
    $pName       = (string) ($adminUser['name']  ?? 'Admin');
    $pEmail      = (string) ($adminUser['email'] ?? '');
    $pInitials   = strtoupper(implode('', array_map(fn($w) => $w[0], array_filter(explode(' ', $pName)))));
    $pInitials   = substr($pInitials, 0, 2) ?: 'AD';
    ?>
    <aside class="admin-sidebar" id="adminSidebar">

        <!-- 1 · Brand ──────────────────────────────────────── -->
        <div class="brand">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice logo" class="brand-mark">
            <div>
                <strong>CampusVoice</strong>
                <small>Control Panel</small>
            </div>
        </div>

        <!-- 2 · Navigation ─────────────────────────────────── -->
        <nav class="sidebar-nav">
            <a href="<?= $panelUrl ?>#overview" data-nav-tab="overview">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Overview
            </a>
            <a href="<?= $panelUrl ?>#feedback" data-nav-tab="feedback">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Feedback
            </a>
            <a href="<?= $panelUrl ?>#announcements" data-nav-tab="announcements">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg>
                Announcements
            </a>
            <a href="<?= $panelUrl ?>#users" data-nav-tab="users">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Students
            </a>
            <a href="<?= $panelUrl ?>#categories" data-nav-tab="categories">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Categories
            </a>
            <a href="<?= $panelUrl ?>#activity" data-nav-tab="activity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Admin Activity
            </a>
            <a href="<?= $panelUrl ?>#student-activity" data-nav-tab="student-activity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Student Activity
            </a>
            <div class="cv-nav-divider"></div>
            <a href="<?= site_url('admin/admins') ?>" data-nav-page="admins" class="<?= $activeMenu === 'admins' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Admin Accounts
            </a>
            <a href="<?= site_url('admin/roles') ?>" data-nav-page="roles" class="<?= $activeMenu === 'roles' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
                Roles
            </a>
        </nav>

        <!-- 3 · Profile (sticks to bottom) ─────────────────── -->
        <div class="cv-sidebar-profile">
            <div class="cv-profile-avatar"><?= esc($pInitials) ?></div>
            <div class="cv-profile-info">
                <strong><?= esc($pName) ?></strong>
                <small><?= esc($pEmail) ?></small>
            </div>
            <a href="<?= site_url('admin/logout') ?>" class="cv-profile-logout" title="Sign out">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
        </div>

    </aside>

    <div class="admin-main">
        <header class="admin-topbar" style="background:linear-gradient(175deg,#0a1535 0%,#0d214e 58%,#102a62 100%);color:#fff;border:1px solid rgba(133,172,255,.32);">
            <button type="button" class="menu-btn" id="menuBtn">Menu</button>
            <h1 style="color:#fff;margin:0;"><?= esc($title ?? 'Control Panel') ?></h1>
            <div class="admin-topbar-actions">
                <?= $this->include('partials/theme_toggle', ['toggleClass' => 'theme-toggle--on-light']) ?>
                <div class="admin-user">
                    <strong style="color:#fff;"><?= esc((string) ($adminUser['name'] ?? 'Admin')) ?></strong>
                    <small style="color:rgba(255,255,255,.6);"><?= esc((string) ($adminUser['email'] ?? '')) ?></small>
                </div>
            </div>
        </header>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <main class="admin-content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script>
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('adminSidebar');
    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    const tabNavLinks = document.querySelectorAll('[data-nav-tab]');
    const pageNavActive = document.querySelector('[data-nav-page].active');

    function syncSideNavWithHash() {
        // If we're on a dedicated page (admins, roles), don't highlight any hash tab
        if (pageNavActive) {
            tabNavLinks.forEach(function (link) { link.classList.remove('active'); });
            return;
        }
        const tab = (window.location.hash || '#overview').replace('#', '');
        tabNavLinks.forEach(function (link) {
            link.classList.toggle('active', link.getAttribute('data-nav-tab') === tab);
        });
    }

    window.addEventListener('hashchange', syncSideNavWithHash);
    syncSideNavWithHash();
</script>
<?= $this->include('partials/theme_script') ?>
</body>
</html>
