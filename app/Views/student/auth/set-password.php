<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php $valid = !empty($valid); ?>
<style>
body.is-auth-screen {
    background:
        radial-gradient(circle at 80% 0, rgba(47,107,255,0.18), transparent 30%),
        radial-gradient(circle at 8% 18%, rgba(11,31,77,0.55), transparent 40%),
        linear-gradient(165deg, #080e1c 0%, #0a1535 45%, #0d214e 100%);
    color: #e6ebf5;
}
</style>

<!-- One-tab-only overlay -->
<div id="sp-tab-block" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(6,10,22,0.97);display:none;align-items:center;justify-content:center;flex-direction:column;gap:16px;text-align:center;padding:32px;">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#f28b82" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    <h2 style="margin:0;color:#f0f4ff;font-size:1.2rem;">Link Already Open</h2>
    <p style="margin:0;color:#8b9cc4;font-size:0.9rem;max-width:340px;">This password reset link is already open in another tab.<br>Close that tab first, then refresh this one.</p>
    <button onclick="window.location.reload()" style="margin-top:8px;padding:10px 24px;border-radius:999px;background:#1e3a5f;color:#8ec5ff;border:1px solid #2a4a6a;font-size:0.85rem;font-weight:700;cursor:pointer;font-family:inherit;">Retry</button>
</div>

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
    </header>

    <div class="auth-split auth-split--center">
        <section class="auth-panel fp-panel">

            <div class="fp-hero">
                <div class="fp-hero__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <h1 class="fp-hero__title"><?= ! empty($stolen) ? 'Link Already In Use' : 'Set New Password' ?></h1>
                    <p class="fp-hero__sub">
                        <?php if (! empty($stolen)): ?>
                            This link was already opened in another browser or device.
                        <?php elseif ($valid): ?>
                            Choose a strong password for your account.
                        <?php else: ?>
                            This reset link is no longer valid.
                        <?php endif; ?>
                    </p>
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

            <?php elseif (! empty($stolen)): ?>
                <div style="text-align:center;padding:20px 0 8px;">
                    <div style="font-size:2rem;margin-bottom:12px;">🔒</div>
                    <p style="color:#f28b82;font-size:0.9rem;font-weight:700;margin:0 0 8px;">Security Notice</p>
                    <p style="color:var(--muted,#8b9cc4);font-size:0.85rem;line-height:1.6;margin:0 0 20px;">
                        This reset link is already open in another browser or device.<br>
                        For your security, only one session may use this link.<br><br>
                        If this was not you, contact your administrator immediately.
                    </p>
                    <a href="<?= site_url('users/forgot-password') ?>" class="btn-primary" style="display:inline-block;text-decoration:none;">
                        Request a New Link
                    </a>
                </div>
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
/* ── One-tab lock ── */
(function () {
    var TOKEN    = <?= json_encode($token ?? '') ?>;
    var CHANNEL  = 'sp_tab_' + TOKEN;
    var tabId    = Math.random().toString(36).slice(2);
    var blocked  = false;
    var overlay  = document.getElementById('sp-tab-block');
    var form     = document.getElementById('set-pw-form');

    function block() {
        blocked = true;
        if (overlay) { overlay.style.display = 'flex'; }
        if (form)    { form.style.pointerEvents = 'none'; form.style.opacity = '0.3'; }
    }

    if (!window.BroadcastChannel) {
        /* Fallback: localStorage ping */
        var LS_KEY = 'sp_lock_' + TOKEN;
        var existing = localStorage.getItem(LS_KEY);
        if (existing && existing !== tabId) { block(); }
        else { localStorage.setItem(LS_KEY, tabId); }
        window.addEventListener('storage', function (e) {
            if (e.key === LS_KEY && e.newValue && e.newValue !== tabId) block();
        });
        window.addEventListener('beforeunload', function () {
            if (localStorage.getItem(LS_KEY) === tabId) localStorage.removeItem(LS_KEY);
        });
        return;
    }

    var bc = new BroadcastChannel(CHANNEL);

    bc.addEventListener('message', function (e) {
        if (e.data && e.data.type === 'CLAIM' && e.data.tabId !== tabId) {
            /* Another tab claimed this link — block this one */
            block();
            bc.postMessage({ type: 'TAKEN', tabId: tabId });
        }
        if (e.data && e.data.type === 'TAKEN' && e.data.tabId !== tabId && !blocked) {
            /* We sent CLAIM and another tab responded TAKEN — we are the duplicate */
            block();
        }
    });

    /* Broadcast claim immediately */
    bc.postMessage({ type: 'CLAIM', tabId: tabId });

    window.addEventListener('beforeunload', function () {
        bc.postMessage({ type: 'RELEASE', tabId: tabId });
        bc.close();
    });
}());
</script>
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
