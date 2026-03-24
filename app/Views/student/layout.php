<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'CampusVoice') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
<?php
$studentPortalCss = FCPATH . 'assets/student/portal.css';
$studentPortalCssVersion = is_file($studentPortalCss) ? (string) filemtime($studentPortalCss) : '1';
$studentIsAuthed = ! empty($studentUser['id']);
$currentTitle = (string) ($title ?? '');
$isAuthScreen = (bool) ($isAuthScreen ?? ($currentTitle === 'Student Portal Access'));
?>
    <link rel="stylesheet" href="<?= base_url('assets/student/portal.css') . '?v=' . $studentPortalCssVersion ?>">
</head>
<body<?= $isAuthScreen ? ' class="is-auth-screen"' : '' ?>>

<?php if (! $isAuthScreen): ?>
<header class="portal-header">
    <div class="portal-header-inner">
        <a href="<?= site_url('users') ?>" class="portal-brand">
            <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice" class="portal-logo">
            <span>CampusVoice</span>
        </a>

        <?php if ($studentIsAuthed): ?>
            <nav class="portal-nav">
                <a href="<?= site_url('users') ?>" class="<?= $currentTitle === 'My Portal' ? 'active' : '' ?>">Home</a>
                <a href="<?= site_url('users/feedback') ?>" class="<?= $currentTitle === 'My Submissions' ? 'active' : '' ?>">My Feedback</a>
                <a href="<?= site_url('users/feedback/submit') ?>" class="<?= $currentTitle === 'Submit Feedback' ? 'active' : '' ?>">Submit</a>
                <a href="<?= site_url('users/announcements') ?>" class="<?= $currentTitle === 'Announcements' ? 'active' : '' ?>">Announcements</a>
            </nav>
            <div class="portal-user-info">
                <span><?= esc((string) ($studentUser['name'] ?? 'Student')) ?></span>
                <a href="<?= site_url('users/logout') ?>" class="logout-link">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</header>
<?php endif; ?>

<main class="portal-main<?= $isAuthScreen ? ' portal-main--auth' : '' ?>">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="portal-alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="portal-alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
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

</body>
</html>
