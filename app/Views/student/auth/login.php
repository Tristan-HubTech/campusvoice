<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>
<?php $authMode = (string) ($authMode ?? 'login'); ?>
<style>
    /* ── reset portal-main so shell can go full-screen ── */
    .portal-main--auth { padding: 0 !important; margin: 0 !important; }

    /* ── shared reset ── */
    *, *::before, *::after { box-sizing: border-box; }

    /* ══ PAGE SHELL ══ */
    .auth-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: row;
        font-family: 'Manrope', sans-serif;
    }

    /* ══ LEFT — Brand Panel ══ */
    .auth-lp {
        display: none;
        width: 50%;
        flex-shrink: 0;
        min-height: 100vh;
        position: relative;
        background: linear-gradient(158deg, #060d22 0%, #0a1b42 52%, #0b2056 100%);
        overflow: hidden;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 48px 44px;
    }

    @media (min-width: 860px) { .auth-lp { display: flex; } }

    .auth-lp::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
        background-size: 30px 30px;
        pointer-events: none;
    }

    .auth-lp::after {
        content: '';
        position: absolute;
        width: 480px; height: 480px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(200,151,44,0.20) 0%, transparent 65%);
        top: -140px; right: -140px;
        pointer-events: none;
    }

    .auth-lp-orb {
        position: absolute;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
        bottom: -80px; left: -60px;
        pointer-events: none;
    }

    .auth-lp-top {
        display: flex; align-items: center; gap: 11px;
        position: relative; z-index: 2;
        animation: saUp .6s ease both;
    }

    .auth-lp-logo { width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0; }

    .auth-lp-name {
        font-family: 'Fraunces', serif;
        font-size: 1rem; font-weight: 600;
        color: rgba(255,255,255,0.88);
    }

    .auth-lp-mid {
        position: relative; z-index: 2;
        animation: saUp .7s .1s ease both;
    }

    .auth-lp-eyebrow {
        display: flex; align-items: center; gap: 10px;
        font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.2em; text-transform: uppercase;
        color: #c8972c; margin-bottom: 20px;
    }

    .auth-lp-eyebrow::before {
        content: ''; display: block;
        width: 26px; height: 1.5px;
        background: #c8972c; flex-shrink: 0;
    }

    .auth-lp-headline {
        font-family: 'Fraunces', serif;
        font-size: clamp(1.9rem, 3vw, 2.9rem);
        font-weight: 700; color: #fff;
        line-height: 1.12; letter-spacing: -0.025em;
        margin: 0 0 18px;
    }

    .auth-lp-headline em {
        font-style: italic; font-weight: 300;
        color: rgba(200,151,44,0.88);
    }

    .auth-lp-desc {
        font-size: 0.86rem;
        color: rgba(255,255,255,0.38);
        line-height: 1.75; margin: 0 0 32px;
        max-width: 290px;
    }

    .auth-lp-features { display: flex; flex-direction: column; gap: 10px; }

    .auth-lp-feat {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 12px;
    }

    .auth-lp-feat-icon {
        width: 32px; height: 32px; border-radius: 8px;
        background: rgba(200,151,44,0.13);
        display: grid; place-items: center;
        color: #c8972c; flex-shrink: 0;
    }

    .auth-lp-feat-label {
        font-size: 0.8rem; font-weight: 600;
        color: rgba(255,255,255,0.52); line-height: 1.3;
    }

    .auth-lp-feat-label small {
        display: block; font-weight: 400; font-size: 0.72rem;
        color: rgba(255,255,255,0.26); margin-top: 1px;
    }

    .auth-lp-bottom {
        position: relative; z-index: 2;
        animation: saUp .7s .25s ease both;
    }

    .auth-lp-status {
        display: inline-flex; align-items: center; gap: 9px;
        padding: 9px 18px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 100px;
        font-size: 0.74rem; color: rgba(255,255,255,0.42);
    }

    .auth-lp-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #4ade80;
        box-shadow: 0 0 8px rgba(74,222,128,0.6);
        flex-shrink: 0;
        animation: saPulse 2.5s ease-in-out infinite;
    }

    /* ══ RIGHT — Form Panel ══ */
    .auth-rp {
        flex: 1;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px 28px;
        background: #e8e4dc;
        position: relative;
        overflow: hidden;
    }

    .auth-rp::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(circle, rgba(10,27,66,0.12) 1px, transparent 1px);
        background-size: 24px 24px;
        pointer-events: none; z-index: 0;
    }

    .rp-blob-g {
        position: absolute; width: 500px; height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(200,151,44,0.28) 0%, rgba(200,151,44,0.06) 50%, transparent 70%);
        top: -160px; right: -160px;
        pointer-events: none; z-index: 0;
    }

    .rp-blob-b {
        position: absolute; width: 400px; height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59,130,246,0.18) 0%, rgba(59,130,246,0.05) 50%, transparent 70%);
        bottom: -120px; left: -120px;
        pointer-events: none; z-index: 0;
    }

    .rp-ring {
        position: absolute; width: 280px; height: 280px;
        border-radius: 50%;
        border: 2px solid rgba(200,151,44,0.28);
        bottom: 40px; right: -90px;
        pointer-events: none; z-index: 0;
    }

    .rp-ring::after {
        content: ''; position: absolute; inset: 24px;
        border-radius: 50%;
        border: 1px solid rgba(200,151,44,0.14);
    }

    .rp-ring2 {
        position: absolute; width: 200px; height: 200px;
        border-radius: 50%;
        border: 1.5px solid rgba(10,27,66,0.13);
        top: 50px; left: -70px;
        pointer-events: none; z-index: 0;
    }

    /* ── Form card ── */
    .auth-card {
        width: 100%; max-width: 420px;
        position: relative; z-index: 1;
        animation: saUp .6s .05s ease both;
    }

    /* mobile logo */
    .auth-mobile-brand {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 36px;
    }

    .auth-mobile-brand img { width: 30px; height: 30px; border-radius: 7px; }

    .auth-mobile-brand-name {
        font-family: 'Fraunces', serif;
        font-size: 0.95rem; font-weight: 600; color: #09111f;
    }

    @media (min-width: 860px) { .auth-mobile-brand { display: none; } }

    /* Tab switcher */
    .auth-tab-switch {
        display: flex; gap: 0;
        background: rgba(10,27,66,0.07);
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 28px;
        border: 1px solid rgba(10,27,66,0.1);
    }

    .auth-tab-btn {
        flex: 1; padding: 9px 16px;
        border: none; border-radius: 9px;
        font-family: 'Manrope', sans-serif;
        font-size: 0.84rem; font-weight: 700;
        cursor: pointer;
        background: transparent;
        color: rgba(10,27,66,0.45);
        transition: background .18s, color .18s, box-shadow .18s;
    }

    .auth-tab-btn.active {
        background: #ffffff;
        color: #0a1b42;
        box-shadow: 0 1px 6px rgba(10,27,66,0.12);
    }

    /* form head */
    .auth-head { margin-bottom: 24px; }

    .auth-eyebrow {
        font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.2em; text-transform: uppercase;
        color: #c8972c; margin-bottom: 10px;
    }

    .auth-head h2 {
        font-family: 'Fraunces', serif;
        font-size: 1.7rem; font-weight: 700;
        color: #09111f; line-height: 1.15;
        letter-spacing: -0.02em; margin: 0 0 8px;
    }

    .auth-head p {
        font-size: 0.84rem; color: #7a8698;
        line-height: 1.6; margin: 0;
    }

    /* alerts */
    .auth-alert, .portal-alert {
        border-radius: 10px; padding: 11px 15px;
        font-size: 0.83rem; font-weight: 500;
        margin-bottom: 18px; border: 1px solid transparent;
        line-height: 1.5;
    }

    .auth-alert--error, .portal-alert.error {
        background: #fef2f2; border-color: #fecaca; color: #991b1b;
    }

    .auth-alert--success, .portal-alert.success {
        background: #f0fdf4; border-color: #bbf7d0; color: #166534;
    }

    /* Fields */
    .auth-form { display: grid; gap: 15px; }

    .auth-form label {
        display: block; font-size: 0.77rem; font-weight: 700;
        color: #4b5563; letter-spacing: 0.02em; margin-bottom: 6px;
    }

    .auth-form input[type="email"],
    .auth-form input[type="password"],
    .auth-form input[type="text"],
    .auth-form input[type="tel"] {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid #e0e2e7;
        border-radius: 10px;
        font-family: 'Manrope', sans-serif;
        font-size: 0.88rem; color: #111827;
        background: #ffffff;
        outline: none; appearance: none;
        transition: border-color .18s, box-shadow .18s;
    }

    .auth-form input::placeholder { color: #c4cad4; }

    .auth-form input:focus {
        border-color: #c8972c;
        box-shadow: 0 0 0 3px rgba(200,151,44,0.13);
    }

    .auth-form input[readonly],
    .auth-form input:disabled {
        background: #f4f4f4;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .form-row-half {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }

    .auth-otp-row { display: flex; gap: 8px; }
    .auth-otp-row input { flex: 1; }

    /* OTP send/verify button */
    .auth-otp-btn {
        padding: 10px 14px;
        border: 1.5px solid #c8972c;
        border-radius: 10px;
        background: transparent;
        color: #c8972c;
        font-family: 'Manrope', sans-serif;
        font-size: 0.78rem; font-weight: 700;
        cursor: pointer; white-space: nowrap;
        transition: background .18s, color .18s;
        flex-shrink: 0;
    }

    .auth-otp-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    .auth-otp-btn.ready {
        background: #c8972c; color: #ffffff;
    }

    .otp-status-text {
        display: block; font-size: 0.77rem;
        margin-top: 5px; line-height: 1.4;
    }

    /* reCAPTCHA nudge */
    .g-recaptcha { transform-origin: left top; }

    /* Primary button */
    .btn-primary {
        width: 100%;
        padding: 12px 22px;
        background: #0a1b42; color: #fff;
        font-family: 'Manrope', sans-serif;
        font-size: 0.88rem; font-weight: 700;
        letter-spacing: 0.03em;
        border: none; border-radius: 10px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background .2s, transform .15s, box-shadow .18s;
        position: relative; overflow: hidden;
        margin-top: 4px;
    }

    .btn-primary::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(130deg, rgba(200,151,44,0.18), transparent 55%);
        opacity: 0; transition: opacity .2s;
    }

    .btn-primary:hover {
        background: #0e2152;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(10,27,66,0.24);
    }

    .btn-primary:hover::after { opacity: 1; }
    .btn-primary:active { transform: translateY(0); box-shadow: none; }
    .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

    .auth-form-footer {
        text-align: center; margin-top: 4px;
    }

    .auth-link-subtle {
        font-size: 0.8rem; color: #8a9ab8;
        text-decoration: none; font-weight: 500;
        transition: color .15s;
    }

    .auth-link-subtle:hover { color: #c8972c; }

    .auth-divider {
        border: none;
        border-top: 1px solid rgba(10,27,66,0.1);
        margin: 4px 0;
    }

    /* Theme toggle */
    .auth-theme-wrap {
        position: fixed; top: 16px; right: 16px; z-index: 50;
    }

    html:not([data-theme="dark"]) .auth-theme-wrap .theme-toggle {
        background: rgba(10,27,66,0.08) !important;
        border: 1.5px solid rgba(10,27,66,0.18) !important;
        color: #0a1b42 !important;
        box-shadow: 0 1px 4px rgba(10,27,66,0.1) !important;
    }

    html:not([data-theme="dark"]) .auth-theme-wrap .theme-toggle:hover {
        background: rgba(10,27,66,0.14) !important;
    }

    /* ── keyframes ── */
    @keyframes saUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes saPulse {
        0%,100% { box-shadow: 0 0 8px rgba(74,222,128,.6); }
        50%      { box-shadow: 0 0 3px rgba(74,222,128,.25); }
    }

    /* ── Dark mode ── */
    html[data-theme="dark"] .auth-rp { background: #080f1e; }
    html[data-theme="dark"] .auth-rp::before {
        background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
    }
    html[data-theme="dark"] .rp-blob-g {
        background: radial-gradient(circle, rgba(200,151,44,0.22) 0%, rgba(200,151,44,0.06) 50%, transparent 70%);
    }
    html[data-theme="dark"] .rp-blob-b {
        background: radial-gradient(circle, rgba(59,130,246,0.20) 0%, rgba(59,130,246,0.05) 50%, transparent 70%);
    }
    html[data-theme="dark"] .rp-ring { border-color: rgba(200,151,44,0.22); }
    html[data-theme="dark"] .rp-ring2 { border-color: rgba(255,255,255,0.07); }
    html[data-theme="dark"] .auth-lp {
        background: linear-gradient(158deg, #02060f 0%, #050d22 52%, #060f28 100%);
    }
    html[data-theme="dark"] .auth-mobile-brand-name { color: #e6ebf5; }
    html[data-theme="dark"] .auth-tab-switch {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.07);
    }
    html[data-theme="dark"] .auth-tab-btn { color: rgba(255,255,255,0.28); }
    html[data-theme="dark"] .auth-tab-btn.active {
        background: #0d1e3a; color: #e6ebf5;
        box-shadow: 0 1px 6px rgba(0,0,0,0.3);
    }
    html[data-theme="dark"] .auth-head h2 { color: #e8edf8; }
    html[data-theme="dark"] .auth-head p  { color: #4e5e72; }
    html[data-theme="dark"] .auth-form label { color: #6a7a90; }
    html[data-theme="dark"] .auth-form input[type="email"],
    html[data-theme="dark"] .auth-form input[type="password"],
    html[data-theme="dark"] .auth-form input[type="text"],
    html[data-theme="dark"] .auth-form input[type="tel"] {
        background: #0c1626; border-color: #1c2b40; color: #e6ebf5;
    }
    html[data-theme="dark"] .auth-form input::placeholder { color: #283848; }
    html[data-theme="dark"] .auth-form input:focus {
        border-color: #c8972c; box-shadow: 0 0 0 3px rgba(200,151,44,0.12);
    }
    html[data-theme="dark"] .auth-form input[readonly],
    html[data-theme="dark"] .auth-form input:disabled {
        background: #0a1220; color: #3a4a5e;
    }
    html[data-theme="dark"] .btn-primary { background: #0c2050; }
    html[data-theme="dark"] .btn-primary:hover { background: #0f2660; }
    html[data-theme="dark"] .auth-divider { border-top-color: rgba(255,255,255,0.07); }
    html[data-theme="dark"] .auth-eyebrow { color: #c8972c; }
</style>

<div class="auth-theme-wrap">
    <?= $this->include('partials/theme_toggle') ?>
</div>

<div class="auth-shell">

    <!-- ══ LEFT: Brand Panel ══ -->
    <div class="auth-lp" aria-hidden="true">
        <div class="auth-lp-orb"></div>

        <div class="auth-lp-top">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="" class="auth-lp-logo">
            <span class="auth-lp-name">CampusVoice</span>
        </div>

        <div class="auth-lp-mid">
            <div class="auth-lp-eyebrow">Student Portal</div>
            <h2 class="auth-lp-headline">Your campus,<br>your <em>voice.</em></h2>
            <p class="auth-lp-desc">Submit feedback, read announcements, and stay connected with everything happening on campus.</p>

            <div class="auth-lp-features">
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Share Feedback
                        <small>Submit suggestions, reports, and ideas</small>
                    </span>
                </div>
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Campus Announcements
                        <small>Stay up to date with official notices</small>
                    </span>
                </div>
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Community Feed
                        <small>See what your fellow students are saying</small>
                    </span>
                </div>
            </div>
        </div>

        <div class="auth-lp-bottom">
            <div class="auth-lp-status">
                <span class="auth-lp-dot"></span>
                Portal is open
            </div>
        </div>
    </div>

    <!-- ══ RIGHT: Form Panel ══ -->
    <div class="auth-rp">
        <div class="rp-blob-g"></div>
        <div class="rp-blob-b"></div>
        <div class="rp-ring"></div>
        <div class="rp-ring2"></div>

        <div class="auth-card">

            <!-- Mobile logo -->
            <div class="auth-mobile-brand">
                <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice">
                <span class="auth-mobile-brand-name">CampusVoice</span>
            </div>

            <!-- Tab switcher -->
            <div class="auth-tab-switch" id="auth-tab-switch">
                <button type="button" class="auth-tab-btn" data-auth-tab="login">Sign In</button>
                <button type="button" class="auth-tab-btn" data-auth-tab="register">Register</button>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="auth-alert auth-alert--error"><?= esc((string) session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="auth-alert auth-alert--success"><?= esc((string) session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <!-- ── LOGIN PANE ── -->
            <section class="auth-pane" data-auth-pane="login">
                <div class="auth-head">
                    <div class="auth-eyebrow">Welcome Back</div>
                    <h2>Sign in to your account</h2>
                    <p>Access your campus portal and stay connected.</p>
                </div>

                <form method="post" action="<?= site_url('users/login') ?>" class="auth-form" novalidate autocomplete="off">
                    <input type="hidden" name="auth_mode" value="login">

                    <div>
                        <label for="login-email">Email Address</label>
                        <input id="login-email" name="email" type="email" required
                               autocomplete="off" placeholder="you@example.com">
                    </div>

                    <div>
                        <label for="login-password">Password</label>
                        <input id="login-password" name="password" type="password" required
                               autocomplete="off" placeholder="Your password">
                    </div>

                    <div class="g-recaptcha" data-sitekey="<?= esc(RECAPTCHA_SITE_KEY) ?>"></div>

                    <button type="submit" class="btn-primary">Sign In &nbsp;→</button>

                    <div class="auth-form-footer">
                        <a href="<?= site_url('users/forgot-password') ?>" class="auth-link-subtle">Forgot password?</a>
                    </div>
                </form>
            </section>

            <!-- ── REGISTER PANE ── -->
            <section class="auth-pane" data-auth-pane="register" hidden>
                <div class="auth-head">
                    <div class="auth-eyebrow">Join CampusVoice</div>
                    <h2>Create your account</h2>
                    <p>Fill in your details and verify your email to get started.</p>
                </div>

                <form method="post" action="<?= site_url('users/login') ?>" class="auth-form" novalidate autocomplete="off">
                    <input type="hidden" name="auth_mode" value="register">

                    <div class="form-row-half">
                        <div>
                            <label for="reg-first">First Name</label>
                            <input id="reg-first" name="first_name" type="text" required maxlength="100" placeholder="First name">
                        </div>
                        <div>
                            <label for="reg-last">Last Name</label>
                            <input id="reg-last" name="last_name" type="text" required maxlength="100" placeholder="Last name">
                        </div>
                    </div>

                    <div>
                        <label for="reg-email">Email Address</label>
                        <input id="reg-email" name="email" type="email" required maxlength="150" autocomplete="off" placeholder="you@example.com">
                        <small id="reg-email-hint" class="otp-status-text" aria-live="polite"></small>
                    </div>

                    <div>
                        <label for="reg-password">Password <small style="font-weight:400;color:#9ca3af;">(min 8 chars)</small></label>
                        <input id="reg-password" name="password" type="password" required minlength="8" maxlength="255" autocomplete="new-password" placeholder="Create a password">
                    </div>

                    <div>
                        <label for="reg-confirm">Confirm Password</label>
                        <input id="reg-confirm" name="password_confirm" type="password" required autocomplete="new-password" placeholder="Repeat password">
                    </div>

                    <div>
                        <label for="reg-otp">OTP Verification</label>
                        <div class="auth-otp-row">
                            <input id="reg-otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*"
                                   required maxlength="6" placeholder="Send OTP first" disabled>
                            <button type="button" class="auth-otp-btn" id="send-register-otp-btn">Send OTP</button>
                        </div>
                        <small id="register-otp-status" class="otp-status-text" aria-live="polite"></small>
                    </div>

                    <button type="submit" class="btn-primary">Create Account &nbsp;→</button>
                </form>
            </section>

        </div><!-- /.auth-card -->
    </div><!-- /.auth-rp -->

</div><!-- /.auth-shell -->

<script>
(function () {
    var authMode = <?= json_encode($authMode) ?>;
    if (authMode !== 'register') authMode = 'login';

    var tabButtons = document.querySelectorAll('[data-auth-tab]');
    var panes = document.querySelectorAll('[data-auth-pane]');
    var sendOtpBtn = document.getElementById('send-register-otp-btn');
    var otpStatus  = document.getElementById('register-otp-status');
    var otpInput   = document.getElementById('reg-otp');

    function showOtpStatus(message, isError) {
        if (!otpStatus) return;
        otpStatus.textContent = message;
        otpStatus.style.color = isError ? '#8a251a' : '#1f8f5f';
    }

    function setMode(mode, updateUrl) {
        panes.forEach(function (pane) {
            var active = pane.getAttribute('data-auth-pane') === mode;
            if (active) pane.removeAttribute('hidden');
            else pane.setAttribute('hidden', 'hidden');
        });
        tabButtons.forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-auth-tab') === mode);
        });
        if (updateUrl) {
            var nextUrl = mode === 'register'
                ? <?= json_encode(site_url('users/login?mode=register')) ?>
                : <?= json_encode(site_url('users/login?mode=login')) ?>;
            window.history.replaceState({}, '', nextUrl);
        }
    }

    tabButtons.forEach(function (btn) {
        btn.addEventListener('click', function () { setMode(btn.getAttribute('data-auth-tab'), true); });
    });

    if (sendOtpBtn) {
        sendOtpBtn.disabled = true;

        function gatherRegisterFields() {
            return {
                firstName: (document.getElementById('reg-first') || {}).value || '',
                lastName:  (document.getElementById('reg-last')  || {}).value || '',
                email:     (document.getElementById('reg-email') || {}).value || '',
                password:  (document.getElementById('reg-password') || {}).value || '',
                confirm:   (document.getElementById('reg-confirm')  || {}).value || '',
            };
        }

        function areRegisterRequirementsComplete(v) {
            if (!v.firstName || !v.lastName || !v.email || !v.password || !v.confirm) return false;
            if (!/^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/.test(v.email)) return false;
            if (v.password.length < 8) return false;
            if (v.password !== v.confirm) return false;
            return true;
        }

        function requestOtp() {
            var v = gatherRegisterFields();
            if (!v.firstName || !v.lastName || !v.email || !v.password || !v.confirm) { showOtpStatus('Please complete all fields first.', true); return; }
            if (v.password.length < 8) { showOtpStatus('Password must be at least 8 characters.', true); return; }
            if (v.password !== v.confirm) { showOtpStatus('Passwords do not match.', true); return; }

            var body = new URLSearchParams();
            body.set('first_name', v.firstName); body.set('last_name', v.lastName);
            body.set('email', v.email); body.set('password', v.password); body.set('password_confirm', v.confirm);
            sendOtpBtn.disabled = true;
            showOtpStatus('Sending OTP to your email...', false);

            fetch(<?= json_encode(site_url('users/register/send-otp')) ?>, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest' },
                body: body.toString()
            })
            .then(function (r) { return r.json().catch(function () { return { ok: false, message: 'Unexpected response.' }; }); })
            .then(function (data) {
                var ok = !!(data && data.ok);
                showOtpStatus((data && data.message) ? data.message : (ok ? 'OTP sent.' : 'Unable to send OTP.'), !ok);
                if (ok && otpInput) { otpInput.disabled = false; otpInput.placeholder = 'Enter 6-digit OTP'; otpInput.focus(); }
            })
            .catch(function () { showOtpStatus('Failed to send OTP. Please try again.', true); })
            .finally(function () { sendOtpBtn.disabled = false; });
        }

        var emailHintEl = document.getElementById('reg-email-hint');

        function updateOtpButtonState() {
            var v = gatherRegisterFields();
            var isReady = areRegisterRequirementsComplete(v);
            sendOtpBtn.classList.toggle('ready', isReady);
            sendOtpBtn.disabled = !isReady;
            if (emailHintEl) {
                var hint = '';
                if (isReady) { hint = ''; }
                else if (/^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/.test(v.email)) {
                    if (!v.firstName || !v.lastName) hint = '👆 Fill in your name to continue.';
                    else if (!v.password) hint = '🔒 Create a password, then we\'ll send an OTP.';
                    else if (v.password.length < 8) hint = '🔒 Password needs at least 8 characters.';
                    else if (v.password !== v.confirm) hint = '⚠ Passwords don\'t match yet.';
                }
                emailHintEl.textContent = hint;
                emailHintEl.style.color = hint ? '#92400e' : '';
            }
        }

        sendOtpBtn.addEventListener('click', requestOtp);
        ['reg-first', 'reg-last', 'reg-email', 'reg-password', 'reg-confirm'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) { el.addEventListener('input', updateOtpButtonState); el.addEventListener('change', updateOtpButtonState); }
        });
    }

    setMode(authMode, false);

    (function () {
        var errorEl = document.querySelector('.auth-alert--error, .portal-alert.error');
        var otpField = document.getElementById('reg-otp');
        if (otpField && otpField.value !== '' && errorEl) {
            otpField.value = ''; otpField.disabled = false;
            otpField.placeholder = 'Enter 6-digit OTP';
            otpField.style.transition = 'border-color 0.2s, box-shadow 0.2s';
            otpField.style.borderColor = '#e53e3e';
            otpField.style.boxShadow = '0 0 0 3px rgba(229,62,62,0.18)';
            setTimeout(function () { otpField.style.borderColor = ''; otpField.style.boxShadow = ''; }, 900);
            otpField.focus();
        }
    })();
})();
</script>
<?= $this->endSection() ?>
