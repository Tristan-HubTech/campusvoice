<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Admin Login') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/admin/control-panel.css') ?>">
</head>
<body class="login-page">
<main class="login-wrap">
    <section class="login-panel">
        <div class="login-brand">
            <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice logo" class="login-logo">
        </div>

        <div class="login-head">
            <span class="eyebrow">CampusVoice</span>
            <h1>Admin Control Panel Login</h1>
            <p>Secure access for administrators and system administrators.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('admin/login') ?>" class="login-form">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required value="<?= esc((string) old('email')) ?>" placeholder="admin@campusvoice.local">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required placeholder="Your password">

            <button type="submit">Sign In</button>
        </form>

        <div class="login-note">
            If this is your first run, seed defaults with: <code>spark db:seed InitialDataSeeder</code>
        </div>
    </section>
</main>
</body>
</html>
