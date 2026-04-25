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
    <?= $this->include('partials/theme_styles') ?>
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <?php $panelUrl = site_url('admin'); ?>
        <div class="brand">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice logo" class="brand-mark">
            <div>
                <strong>CampusVoice</strong>
                <small>Control Panel</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="<?= $panelUrl . '#overview' ?>" data-nav-tab="overview">Overview</a>
            <a href="<?= $panelUrl . '#feedback' ?>" data-nav-tab="feedback">Feedback</a>
            <a href="<?= $panelUrl . '#announcements' ?>" data-nav-tab="announcements">Announcements</a>
            <a href="<?= $panelUrl . '#users' ?>" data-nav-tab="users">Students</a>
            <a href="<?= $panelUrl . '#categories' ?>" data-nav-tab="categories">Categories</a>
            <?php if (! empty($canViewActivity)): ?>
                <a href="<?= $panelUrl . '#activity' ?>" data-nav-tab="activity">Activity</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-foot">
            <span class="role-chip"><?= esc(strtoupper((string) ($adminUser['role'] ?? 'ADMIN'))) ?></span>
            <?= $this->include('partials/logout_button', ['logoutUrl' => site_url('admin/logout')]) ?>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar" style="background:linear-gradient(175deg,#0a1535 0%,#0d214e 58%,#102a62 100%);color:#fff;border:1px solid rgba(133,172,255,.32);">
            <button type="button" class="menu-btn" id="menuBtn">Menu</button>
            <div>
                <h1 style="color:#fff;"><?= esc($title ?? 'Control Panel') ?></h1>
                <p style="color:rgba(255,255,255,.65);">Manage feedback, announcements, and response workflows.</p>
            </div>
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
    function syncSideNavWithHash() {
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
