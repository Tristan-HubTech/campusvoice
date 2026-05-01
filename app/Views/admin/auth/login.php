<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->include('partials/theme_fouc') ?>
    <title><?= esc($title ?? 'Admin Login') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/admin/control-panel.css') ?>">
    <?= $this->include('partials/theme_styles') ?>
</head>
<body class="login-page">
<div class="theme-wrap">
    <?= $this->include('partials/theme_toggle', ['toggleClass' => 'theme-toggle--on-light']) ?>
</div>
<main class="login-wrap">
    <section class="login-panel">
        <div class="login-brand">
            <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice" class="login-logo">
        </div>

        <div class="login-head">
            <h1>Admin Sign In</h1>
            <p>Enter your admin credentials to access the control panel.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('admin/login') ?>" class="login-form">
            <div class="field-group">
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" required placeholder="admin@example.com" autocomplete="username" autofocus
                       value="<?= esc((string) old('email', '')) ?>">
            </div>

            <div class="field-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required placeholder="Your password" autocomplete="current-password">
            </div>

            <button type="submit">Sign In &rarr;</button>
        </form>

        <div class="login-note">
            CampusVoice &copy; <?= date('Y') ?>
        </div>
    </section>
</main>
<?= $this->include('partials/theme_script') ?>
</body>
</html>
