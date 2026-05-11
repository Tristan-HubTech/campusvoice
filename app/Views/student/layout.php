<?php
/**
 * STUDENT PORTAL LAYOUT
 * The main wrapper template for the student side. Includes header, navigation, and script tags.
 * 
 * CONNECTS TO:
 * - Views: home.php, my_feedback.php, submit.php, etc.
 * - Partials: portal_header_authed.php, reactions_script.php, theme_toggle.php
 * - CSS: public/css/portal.css, public/css/social.css
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->include('partials/theme_fouc') ?>
    <title><?= esc($title ?? 'CampusVoice') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;0,9..144,700;1,9..144,300;1,9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<?php
$studentPortalCss = FCPATH . 'css/portal.css';
$studentPortalCssVersion = is_file($studentPortalCss) ? (string) filemtime($studentPortalCss) : '1';
$studentIsAuthed = ! empty($studentUser['id']);
$currentTitle = (string) ($navTitle ?? $title ?? '');
$isAuthScreen = (bool) ($isAuthScreen ?? ($currentTitle === 'Student Portal Access'));
?>
    <link rel="stylesheet" href="<?= base_url('css/portal.css') . '?v=' . $studentPortalCssVersion ?>">
    <?php
    $socialCss = FCPATH . 'css/social.css';
    $socialCssVersion = is_file($socialCss) ? (string) filemtime($socialCss) : '1';
    $portalTopbarV2Css = FCPATH . 'css/portal-topbar-v2.css';
    $portalTopbarV2CssVersion = is_file($portalTopbarV2Css) ? (string) filemtime($portalTopbarV2Css) : '1';
    ?>
    <link rel="stylesheet" href="<?= base_url('css/social.css') . '?v=' . $socialCssVersion ?>">
    <link rel="stylesheet" href="<?= base_url('css/portal-topbar-v2.css') . '?v=' . $portalTopbarV2CssVersion ?>">
    <?= $this->include('partials/theme_styles') ?>
<?php if ($isAuthScreen): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
</head>
<body<?= $isAuthScreen ? ' class="is-auth-screen"' : '' ?>>

<?php if (! $isAuthScreen): ?>
<header class="portal-header">
    <div class="portal-header-inner">
        <div class="portal-brand">
            <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice" class="portal-logo">
            <span class="portal-brand-text">CampusVoice</span>
            <span class="portal-badge-beta">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                Beta
            </span>
        </div>

        <?php if ($studentIsAuthed): ?>
            <?php
            $headerUserName = (string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($studentUser['name'] ?? 'Student'));
            // Always read avatar_color fresh from the profile DB — not the stale session
            $profileForHeader = (new App\Models\SocialProfileModel())->where('user_id', (int) ($studentUser['id'] ?? 0))->first();
            $headerAvatarColor = (string) ($profileForHeader['avatar_color'] ?? 'blue');
            $this->setVar('headerUserName', $headerUserName);
            $this->setVar('headerAvatarColor', $headerAvatarColor);
            $this->setVar('currentTitle', $currentTitle);
            ?>
            <?= $this->include('partials/portal_header_authed') ?>
        <?php else: ?>
            <div class="portal-header-spacer" aria-hidden="true"></div>
            <div class="portal-header-end">
                <?= $this->include('partials/theme_toggle') ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($studentIsAuthed): ?>
    <div class="portal-nav-backdrop" id="portal-nav-backdrop" hidden></div>
    <?php endif; ?>
</header>
<?php endif; ?>

<main class="portal-main<?= $isAuthScreen ? ' portal-main--auth' : '' ?>">
    <?php if (! $isAuthScreen): ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="portal-alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="portal-alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php endif; ?>



    <?= $this->renderSection('content') ?>
</main>

<?php if (! $isAuthScreen): ?>
<footer class="portal-footer">
    <p>&copy; <?= date('Y') ?> CampusVoice — Student Portal</p>
</footer>
<?php endif; ?>

<script>
(function () {
    var alerts = document.querySelectorAll('.portal-alert');
    alerts.forEach(function (el) {
        setTimeout(function () {
            el.classList.add('is-hiding');
            setTimeout(function () { el.remove(); }, 400);
        }, 5000);
    });
})();
</script>

<?= $this->include("partials/reactions_script") ?>
<?php if (! $isAuthScreen): ?>
<?php
$portalHeaderJsPath = FCPATH . 'assets/student/portal-header.js';
$portalHeaderJsVersion = is_file($portalHeaderJsPath) ? (string) filemtime($portalHeaderJsPath) : '1';
?>
<script src="<?= base_url('assets/student/portal-header.js') ?>?v=<?= esc($portalHeaderJsVersion, 'attr') ?>"></script>
<?php endif; ?>
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
