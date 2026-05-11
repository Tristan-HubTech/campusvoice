<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->include('partials/theme_fouc') ?>
    <title><?= esc($title ?? 'CampusVoice') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
<?php
$socialCss = FCPATH . 'css/social.css';
$socialCssVersion = is_file($socialCss) ? (string) filemtime($socialCss) : '1';
$portalCss = FCPATH . 'css/portal.css';
$portalCssVersion = is_file($portalCss) ? (string) filemtime($portalCss) : '1';
$portalTopbarV2Css = FCPATH . 'css/portal-topbar-v2.css';
$portalTopbarV2CssVersion = is_file($portalTopbarV2Css) ? (string) filemtime($portalTopbarV2Css) : '1';
?>
    <link rel="stylesheet" href="<?= base_url('css/portal.css') . '?v=' . $portalCssVersion ?>">
    <link rel="stylesheet" href="<?= base_url('css/social.css') . '?v=' . $socialCssVersion ?>">
    <link rel="stylesheet" href="<?= base_url('css/portal-topbar-v2.css') . '?v=' . $portalTopbarV2CssVersion ?>">
    <?= $this->include('partials/theme_styles') ?>
</head>
<body>
<?php $currentUser = (array) ($currentUser ?? []); ?>
<?php $currentTitle = (string) ($title ?? ''); ?>

<header class="portal-header">
    <div class="portal-header-inner">
        <?php if (! empty($currentUser['id'])): ?>
            <div class="portal-brand">
                <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice" class="portal-logo">
                <span class="portal-brand-text">CampusVoice</span>
                <span class="portal-badge-beta">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    Beta
                </span>
            </div>
            <?php
            $headerUserName = (string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($currentUser['name'] ?? 'User'));
            // Always read avatar_color fresh from the profile DB — not the stale session
            $profileForHeader = (new App\Models\SocialProfileModel())->where('user_id', (int) ($currentUser['id'] ?? 0))->first();
            $headerAvatarColor = (string) ($profileForHeader['avatar_color'] ?? 'blue');
            $this->setVar('headerUserName', $headerUserName);
            $this->setVar('headerAvatarColor', $headerAvatarColor);
            $this->setVar('currentTitle', $currentTitle);
            ?>
            <?= $this->include('partials/portal_header_authed') ?>
        <?php else: ?>
            <div aria-hidden="true"></div>
            <div class="portal-brand portal-brand--hero">
                <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice" class="portal-logo">
                <div class="brand-hero-text">
                    <span class="brand-hero-name">CampusVoice</span>
                    <span class="brand-hero-sub">Your campus, your voice</span>
                </div>
            </div>
            <div class="topbar-actions portal-header-end">
                <?= $this->include('partials/theme_toggle') ?>
                <a href="<?= site_url('users/login?mode=register') ?>" class="ghost-btn">Join now</a>
                <a href="<?= site_url('users/login') ?>" class="solid-btn">Log in</a>
            </div>
        <?php endif; ?>
    </div>
    <?php if (! empty($currentUser['id'])): ?>
    <div class="portal-nav-backdrop" id="portal-nav-backdrop" hidden></div>
    <?php endif; ?>
</header>

<div class="social-shell">
    <div class="social-main">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="toast-alert toast-success" id="toastAlert">
                <span class="toast-icon">✅</span>
                <span><?= esc((string) session()->getFlashdata('success')) ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="toast-alert toast-error" id="toastAlert">
                <span class="toast-icon">❌</span>
                <span><?= esc((string) session()->getFlashdata('error')) ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
            </div>
        <?php endif; ?>

        <main class="social-content">
            <?= $this->renderSection('content') ?>
        </main>

        <footer class="social-footer">
            <p>&copy; <?= date('Y') ?> CampusVoice — Student Portal</p>
        </footer>
    </div>
</div>

<?= $this->include("partials/reactions_script") ?>
<?php
$portalHeaderJsPath = FCPATH . 'assets/student/portal-header.js';
$portalHeaderJsVersion = is_file($portalHeaderJsPath) ? (string) filemtime($portalHeaderJsPath) : '1';
?>
<script src="<?= base_url('assets/student/portal-header.js') ?>?v=<?= esc($portalHeaderJsVersion, 'attr') ?>"></script>
<script>
(function () {
    var minZoom = 1;
    var maxZoom = 1.5;
    var step = 0.1;
    var currentZoom = 1;

    function clampZoom(value) {
        return Math.min(maxZoom, Math.max(minZoom, value));
    }

    function applyZoom(value) {
        currentZoom = clampZoom(Math.round(value * 100) / 100);
        document.documentElement.style.zoom = String(currentZoom * 100) + '%';
    }

    window.addEventListener('wheel', function (event) {
        if (!event.ctrlKey && !event.metaKey) return;
        event.preventDefault();
        applyZoom(currentZoom + (event.deltaY < 0 ? step : -step));
    }, { passive: false });

    window.addEventListener('keydown', function (event) {
        if (!event.ctrlKey && !event.metaKey) return;
        if (event.key === '+' || event.key === '=' || event.key === 'Add') {
            event.preventDefault();
            applyZoom(currentZoom + step);
        } else if (event.key === '-' || event.key === '_' || event.key === 'Subtract') {
            event.preventDefault();
            applyZoom(currentZoom - step);
        } else if (event.key === '0') {
            event.preventDefault();
            applyZoom(1);
        }
    });
})();
</script>
<?= $this->include('partials/theme_script') ?>
</body>
</html>
