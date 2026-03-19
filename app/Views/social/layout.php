<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'CampusVoice') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/student/social.css') ?>">
</head>
<body>
<?php $currentUser = (array) ($currentUser ?? []); ?>
<div class="social-shell">
    <aside class="social-sidebar" id="socialSidebar">
        <a href="<?= site_url('feed') ?>" class="brand">
            <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice logo" class="brand-mark">
            <div>
                <strong>CampusVoice</strong>
                <small>Main Website</small>
            </div>
        </a>

        <nav class="sidebar-nav">
            <a href="<?= site_url('feed') ?>" class="<?= ($pageKey ?? '') === 'feed' ? 'active' : '' ?>">Feed</a>
            <?php if (! empty($currentUser['id'])): ?>
                <a href="<?= site_url('users/' . (int) $currentUser['id']) ?>" class="<?= ($pageKey ?? '') === 'profile' ? 'active' : '' ?>">My Profile</a>
                <a href="<?= site_url('settings') ?>" class="<?= ($pageKey ?? '') === 'settings' ? 'active' : '' ?>">Settings</a>
                <a href="<?= site_url('portal/feedback') ?>">My Feedback</a>
                <a href="<?= site_url('portal/announcements') ?>">Announcements</a>
            <?php else: ?>
                <a href="<?= site_url('portal/login') ?>">Log In</a>
                <a href="<?= site_url('portal/login?mode=register') ?>">Register</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-foot">
            <?php if (! empty($currentUser['id'])): ?>
                <span class="role-chip">USER SPACE</span>
                <a class="logout-link" href="<?= site_url('portal/logout') ?>">Logout</a>
            <?php else: ?>
                <span class="role-chip">PUBLIC FEED</span>
            <?php endif; ?>
        </div>
    </aside>
    <div class="social-overlay" id="socialOverlay"></div>

    <div class="social-main">
        <header class="social-topbar">
            <button type="button" class="menu-btn" id="menuBtn">Menu</button>
            <div>
                <h1><?= esc($title ?? 'CampusVoice') ?></h1>
                <p>Post updates, react, comment, and share what matters on campus.</p>
            </div>
            <div class="topbar-actions">
                <?php if (! empty($currentUser['id'])): ?>
                    <a href="<?= site_url('settings') ?>" class="ghost-btn">Settings</a>
                    <div class="admin-user">
                        <strong><?= esc((string) ($currentUser['name'] ?? 'User')) ?></strong>
                        <small><?= esc((string) ($currentUser['email'] ?? '')) ?></small>
                    </div>
                <?php else: ?>
                    <a href="<?= site_url('portal/login?mode=register') ?>" class="ghost-btn">Join now</a>
                    <a href="<?= site_url('portal/login') ?>" class="solid-btn">Log in</a>
                <?php endif; ?>
            </div>
        </header>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <main class="social-content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script>
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('socialSidebar');
    const overlay = document.getElementById('socialOverlay');

    function setSidebarOpen(isOpen) {
        if (!sidebar) {
            return;
        }

        sidebar.classList.toggle('open', isOpen);
        if (overlay) {
            overlay.classList.toggle('open', isOpen);
        }
        document.body.classList.toggle('menu-open', isOpen);
    }

    if (menuBtn && sidebar && overlay) {
        menuBtn.addEventListener('click', function () {
            setSidebarOpen(!sidebar.classList.contains('open'));
        });

        overlay.addEventListener('click', function () {
            setSidebarOpen(false);
        });

        document.querySelectorAll('.sidebar-nav a, .logout-link, .brand').forEach(function (link) {
            link.addEventListener('click', function () {
                setSidebarOpen(false);
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                setSidebarOpen(false);
            }
        });
    }

    document.querySelectorAll('[data-share-url]').forEach(function (button) {
        button.addEventListener('click', async function () {
            const url = button.getAttribute('data-share-url');
            try {
                await navigator.clipboard.writeText(url);
                button.textContent = 'Link Copied';
                setTimeout(function () {
                    button.textContent = 'Copy Link';
                }, 1600);
            } catch (error) {
                window.prompt('Copy this link:', url);
            }
        });
    });
</script>
</body>
</html>