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
$socialCss = FCPATH . 'assets/student/social.css';
$socialCssVersion = is_file($socialCss) ? (string) filemtime($socialCss) : '1';
$portalCss = FCPATH . 'assets/student/portal.css';
$portalCssVersion = is_file($portalCss) ? (string) filemtime($portalCss) : '1';
?>
    <link rel="stylesheet" href="<?= base_url('assets/student/portal.css') . '?v=' . $portalCssVersion ?>">
    <link rel="stylesheet" href="<?= base_url('assets/student/social.css') . '?v=' . $socialCssVersion ?>">
    <?= $this->include('partials/theme_styles') ?>
</head>
<body>
<?php $currentUser = (array) ($currentUser ?? []); ?>
<?php $currentTitle = (string) ($title ?? ''); ?>

<header class="portal-header">
    <div class="portal-header-inner">
        <?php if (! empty($currentUser['id'])): ?>
            <a href="<?= site_url('users') ?>" class="portal-brand">
                <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice" class="portal-logo">
                <span>CampusVoice</span>
            </a>
            <?php
            $headerUserName = (string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($currentUser['name'] ?? 'User'));
            $this->setVar('headerUserName', $headerUserName);
            $this->setVar('currentTitle', $currentTitle);
            ?>
            <?= $this->include('partials/portal_header_authed') ?>
        <?php else: ?>
            <div aria-hidden="true"></div>
            <a href="<?= site_url('users') ?>" class="portal-brand portal-brand--hero">
                <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice" class="portal-logo">
                <div class="brand-hero-text">
                    <span class="brand-hero-name">CampusVoice</span>
                    <span class="brand-hero-sub">Your campus, your voice</span>
                </div>
            </a>
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

<?= $this->include('partials/reactions_script') ?>
<?php
$portalHeaderJsPath = FCPATH . 'assets/student/portal-header.js';
$portalHeaderJsVersion = is_file($portalHeaderJsPath) ? (string) filemtime($portalHeaderJsPath) : '1';
?>
<script src="<?= base_url('assets/student/portal-header.js') ?>?v=<?= esc($portalHeaderJsVersion, 'attr') ?>"></script>