<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'CampusVoice') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/student/portal.css') ?>">
</head>
<body>

<header class="portal-header">
    <div class="portal-header-inner">
        <a href="<?= site_url('portal') ?>" class="portal-brand">
            <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice" class="portal-logo">
            <span>CampusVoice</span>
        </a>

        <?php if (! empty($studentUser['id'])): ?>
            <nav class="portal-nav">
                <a href="<?= site_url('portal') ?>" class="<?= ($title ?? '') === 'My Portal' ? 'active' : '' ?>">Home</a>
                <a href="<?= site_url('portal/feedback') ?>" class="<?= ($title ?? '') === 'My Submissions' ? 'active' : '' ?>">My Feedback</a>
                <a href="<?= site_url('portal/feedback/submit') ?>" class="<?= ($title ?? '') === 'Submit Feedback' ? 'active' : '' ?>">Submit</a>
                <a href="<?= site_url('portal/announcements') ?>" class="<?= ($title ?? '') === 'Announcements' ? 'active' : '' ?>">Announcements</a>
            </nav>
            <div class="portal-user-info">
                <span><?= esc((string) ($studentUser['name'] ?? 'Student')) ?></span>
                <a href="<?= site_url('portal/logout') ?>" class="logout-link">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="portal-main">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="portal-alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="portal-alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

<footer class="portal-footer">
    <p>&copy; <?= date('Y') ?> CampusVoice — Student Portal</p>
</footer>

</body>
</html>
