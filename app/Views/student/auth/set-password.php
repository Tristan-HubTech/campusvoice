<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php $valid = !empty($valid); ?>
<div class="auth-shell">
    <header class="auth-topbar">
        <div aria-hidden="true"></div>
        <div class="portal-brand portal-brand--hero">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice" class="portal-logo">
            <div class="brand-hero-text">
                <span class="brand-hero-name">CampusVoice</span>
                <span class="brand-hero-sub">Your campus, your voice</span>
            </div>
        </div>

    <div class="auth-split auth-split--center">
        <section class="auth-panel fp-panel">

            <div class="fp-hero">
                <div class="fp-hero__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <h1 class="fp-hero__title">Set New Password</h1>
                    <p class="fp-hero__sub"><?= $valid ? 'Choose a strong password for your account.' : 'This reset link is no longer valid.' ?></p>
                </div>
            </div>

            <?php if (session()->has('error')): ?>
                <div class="auth-alert auth-alert--error"><?= esc(session('error')) ?></div>
            <?php endif ?>
            <?php if (session()->has('success')): ?>
                <div class="auth-alert auth-alert--success"><?= esc(session('success')) ?></div>
            <?php endif ?>

            <?php if ($valid): ?>
                <form method="post" action="<?= site_url('users/set-password/' . esc($token ?? '', 'attr')) ?>" class="auth-form" novalidate autocomplete="off" id="set-pw-form">

                    <div class="fp-field-group">
                        <label for="sp-password">New Password <small>(min 8 characters)</small></label>
                        <input id="sp-password" name="password" type="password" required
                               minlength="8" maxlength="255" autocomplete="new-password"
                               placeholder="Create a new password">
                        <small id="sp-len-warn" class="fp-warn" style="display:none;"></small>
                    </div>

                    <div class="fp-field-group">
                        <label for="sp-confirm">Confirm New Password</label>
                        <input id="sp-confirm" name="password_confirm" type="password" required
                               maxlength="255" autocomplete="new-password"
                               placeholder="Repeat your new password">
                        <small id="sp-match-warn" class="fp-warn" style="display:none;"></small>
                    </div>

                    <button type="submit" class="btn-primary" id="sp-submit" disabled style="opacity:0.5;cursor:not-allowed;">
                        Save Password
                    </button>
                </form>

            <?php else: ?>
                <div style="text-align:center;padding:24px 0 8px;">
                    <p style="color:var(--muted,#5f7298);font-size:0.9rem;line-height:1.6;margin:0 0 20px;">
                        This link has expired or already been used. Ask your administrator to send a new reset link, or reset your password yourself below.
                    </p>
                    <a href="<?= site_url('users/forgot-password') ?>" class="btn-primary" style="display:inline-block;text-decoration:none;">
                        Forgot Password
                    </a>
                </div>
            <?php endif ?>

        </section>
    </div>
</div>

<?php if ($valid): ?>
<script>
(function () {
    var pw      = document.getElementById('sp-password');
    var confirm = document.getElementById('sp-confirm');
    var submit  = document.getElementById('sp-submit');
    var lenWarn = document.getElementById('sp-len-warn');
    var matchWarn = document.getElementById('sp-match-warn');

    function validate() {
        var p = pw ? pw.value : '';
        var c = confirm ? confirm.value : '';
        var ok = p.length >= 8 && p === c;

        if (lenWarn) {
            lenWarn.style.display = (p.length > 0 && p.length < 8) ? 'block' : 'none';
            lenWarn.textContent = '⚠ Password must be at least 8 characters.';
        }
        if (matchWarn) {
            matchWarn.style.display = (c.length > 0 && p !== c) ? 'block' : 'none';
            matchWarn.textContent = '⚠ Passwords do not match.';
        }

        submit.disabled = !ok;
        submit.style.opacity = ok ? '1' : '0.5';
        submit.style.cursor  = ok ? 'pointer' : 'not-allowed';
    }

    if (pw)      pw.addEventListener('input', validate);
    if (confirm) confirm.addEventListener('input', validate);
})();
</script>
<?php endif ?>
<?= $this->endSection() ?>
