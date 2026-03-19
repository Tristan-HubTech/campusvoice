<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php $authMode = (string) ($authMode ?? 'login'); ?>
<div class="auth-card auth-card--wide">
    <div class="auth-brand">
        <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice" class="auth-logo">
        <h1>CampusVoice</h1>
<<<<<<< HEAD
        <p>Welcome</p>
=======
        <p>User Access</p>
>>>>>>> 8f683a475b049c70f2e46bdc1a59b56eb5b110f1
    </div>

    <div class="auth-tab-switch" id="auth-tab-switch">
        <button type="button" class="auth-tab-btn" data-auth-tab="login">Login</button>
        <button type="button" class="auth-tab-btn" data-auth-tab="register">Register</button>
    </div>

    <section class="auth-pane" data-auth-pane="login">
        <form method="post" action="<?= site_url('portal/login') ?>" class="auth-form" novalidate>
            <input type="hidden" name="auth_mode" value="login">

            <label for="login-email">Email Address</label>
            <input
                id="login-email"
                name="email"
                type="email"
                required
                autocomplete="email"
                placeholder="you@example.com"
                value="<?= esc((string) (old('email') ?? '')) ?>"
            >

            <label for="login-password">Password</label>
            <input
                id="login-password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Your password"
            >

            <button type="submit" class="btn-primary">Log In</button>
        </form>
    </section>

    <section class="auth-pane" data-auth-pane="register" hidden>
        <form method="post" action="<?= site_url('portal/login') ?>" class="auth-form" novalidate>
            <input type="hidden" name="auth_mode" value="register">

            <div class="form-row-half">
                <div>
                    <label for="reg-first">First Name</label>
                    <input id="reg-first" name="first_name" type="text" required maxlength="100" placeholder="First name" value="<?= esc((string) (old('first_name') ?? '')) ?>">
                </div>
                <div>
                    <label for="reg-last">Last Name</label>
                    <input id="reg-last" name="last_name" type="text" required maxlength="100" placeholder="Last name" value="<?= esc((string) (old('last_name') ?? '')) ?>">
                </div>
            </div>

            <label for="reg-email">Email Address</label>
            <input id="reg-email" name="email" type="email" required maxlength="150" placeholder="you@example.com" value="<?= esc((string) (old('email') ?? '')) ?>">

<<<<<<< HEAD
=======
            <label for="reg-student-no">Student Number <small>(optional)</small></label>
            <input id="reg-student-no" name="student_no" type="text" maxlength="50" placeholder="e.g. 2023-00001" value="<?= esc((string) (old('student_no') ?? '')) ?>">

>>>>>>> 8f683a475b049c70f2e46bdc1a59b56eb5b110f1
            <label for="reg-phone">Phone <small>(optional)</small></label>
            <input id="reg-phone" name="phone" type="tel" maxlength="30" placeholder="e.g. 09xx-xxx-xxxx" value="<?= esc((string) (old('phone') ?? '')) ?>">

            <label for="reg-password">Password <small>(min 8 characters)</small></label>
            <input id="reg-password" name="password" type="password" required minlength="8" maxlength="255" autocomplete="new-password" placeholder="Create a password">

            <label for="reg-confirm">Confirm Password</label>
            <input id="reg-confirm" name="password_confirm" type="password" required autocomplete="new-password" placeholder="Repeat password">

            <button type="submit" class="btn-primary">Create Account</button>
        </form>
    </section>

<<<<<<< HEAD

=======
    <p class="auth-footer-text">Admin access is on a separate page: <a href="<?= site_url('admin/login') ?>">Admin Login</a></p>
>>>>>>> 8f683a475b049c70f2e46bdc1a59b56eb5b110f1
</div>

<script>
    (function () {
        var authMode = <?= json_encode($authMode) ?>;
        if (authMode !== 'register') {
            authMode = 'login';
        }

        var tabButtons = document.querySelectorAll('[data-auth-tab]');
        var panes = document.querySelectorAll('[data-auth-pane]');

        function setMode(mode, updateUrl) {
            panes.forEach(function (pane) {
                var active = pane.getAttribute('data-auth-pane') === mode;
                if (active) {
                    pane.removeAttribute('hidden');
                } else {
                    pane.setAttribute('hidden', 'hidden');
                }
            });

            tabButtons.forEach(function (btn) {
                btn.classList.toggle('active', btn.getAttribute('data-auth-tab') === mode);
            });

            if (updateUrl) {
                var nextUrl = mode === 'register'
                    ? <?= json_encode(site_url('portal/login?mode=register')) ?>
                    : <?= json_encode(site_url('portal/login?mode=login')) ?>;
                window.history.replaceState({}, '', nextUrl);
            }
        }

        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var mode = btn.getAttribute('data-auth-tab');
                setMode(mode, true);
            });
        });

        setMode(authMode, false);
    })();
</script>
<?= $this->endSection() ?>
