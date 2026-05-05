<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>
<?php
$forgotOtpVerified = ! empty($forgotOtpVerified);
$verifiedEmail = (string) ($forgotOtpVerifiedEmail ?? '');
$emailValue = (string) (old('email') ?? ($forgotOtpVerified ? $verifiedEmail : ''));
$sessionOtp  = (string) ($forgotSessionOtp ?? '');
?>
<style>
    .portal-main--auth { padding: 0 !important; margin: 0 !important; }
    *, *::before, *::after { box-sizing: border-box; }

    .auth-shell { min-height: 100vh; display: flex; flex-direction: row; font-family: 'Manrope', sans-serif; }

    /* ══ LEFT PANEL ══ */
    .auth-lp {
        display: none; width: 50%; flex-shrink: 0; min-height: 100vh;
        position: relative; overflow: hidden;
        background: linear-gradient(158deg, #060d22 0%, #0a1b42 52%, #0b2056 100%);
        flex-direction: column; justify-content: space-between; padding: 48px 48px 44px;
    }
    @media (min-width: 860px) { .auth-lp { display: flex; } }
    .auth-lp::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
        background-size: 30px 30px; pointer-events: none;
    }
    .auth-lp::after {
        content: ''; position: absolute; width: 480px; height: 480px; border-radius: 50%;
        background: radial-gradient(circle, rgba(200,151,44,0.20) 0%, transparent 65%);
        top: -140px; right: -140px; pointer-events: none;
    }
    .auth-lp-orb {
        position: absolute; width: 320px; height: 320px; border-radius: 50%;
        background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
        bottom: -80px; left: -60px; pointer-events: none;
    }
    .auth-lp-top { display: flex; align-items: center; gap: 11px; position: relative; z-index: 2; animation: saUp .6s ease both; }
    .auth-lp-logo { width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0; }
    .auth-lp-name { font-family: 'Fraunces', serif; font-size: 1rem; font-weight: 600; color: rgba(255,255,255,0.88); }
    .auth-lp-mid { position: relative; z-index: 2; animation: saUp .7s .1s ease both; }
    .auth-lp-eyebrow {
        display: flex; align-items: center; gap: 10px;
        font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase;
        color: #c8972c; margin-bottom: 20px;
    }
    .auth-lp-eyebrow::before { content: ''; display: block; width: 26px; height: 1.5px; background: #c8972c; flex-shrink: 0; }
    .auth-lp-headline {
        font-family: 'Fraunces', serif; font-size: clamp(1.9rem, 3vw, 2.9rem);
        font-weight: 700; color: #fff; line-height: 1.12; letter-spacing: -0.025em; margin: 0 0 18px;
    }
    .auth-lp-headline em { font-style: italic; font-weight: 300; color: rgba(200,151,44,0.88); }
    .auth-lp-desc { font-size: 0.86rem; color: rgba(255,255,255,0.38); line-height: 1.75; margin: 0 0 32px; max-width: 290px; }
    .auth-lp-features { display: flex; flex-direction: column; gap: 10px; }
    .auth-lp-feat {
        display: flex; align-items: center; gap: 12px; padding: 11px 14px;
        background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); border-radius: 12px;
    }
    .auth-lp-feat-icon {
        width: 32px; height: 32px; border-radius: 8px; background: rgba(200,151,44,0.13);
        display: grid; place-items: center; color: #c8972c; flex-shrink: 0;
    }
    .auth-lp-feat-label { font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.52); line-height: 1.3; }
    .auth-lp-feat-label small { display: block; font-weight: 400; font-size: 0.72rem; color: rgba(255,255,255,0.26); margin-top: 1px; }
    .auth-lp-bottom { position: relative; z-index: 2; animation: saUp .7s .25s ease both; }
    .auth-lp-status {
        display: inline-flex; align-items: center; gap: 9px; padding: 9px 18px;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
        border-radius: 100px; font-size: 0.74rem; color: rgba(255,255,255,0.42);
    }
    .auth-lp-dot {
        width: 7px; height: 7px; border-radius: 50%; background: #4ade80;
        box-shadow: 0 0 8px rgba(74,222,128,0.6); flex-shrink: 0;
        animation: saPulse 2.5s ease-in-out infinite;
    }

    /* ══ RIGHT PANEL ══ */
    .auth-rp {
        flex: 1; min-height: 100vh; display: flex; align-items: center; justify-content: center;
        padding: 48px 28px; background: #e8e4dc; position: relative; overflow: hidden;
    }
    .auth-rp::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(circle, rgba(10,27,66,0.12) 1px, transparent 1px);
        background-size: 24px 24px; pointer-events: none; z-index: 0;
    }
    .rp-blob-g {
        position: absolute; width: 500px; height: 500px; border-radius: 50%;
        background: radial-gradient(circle, rgba(200,151,44,0.28) 0%, rgba(200,151,44,0.06) 50%, transparent 70%);
        top: -160px; right: -160px; pointer-events: none; z-index: 0;
    }
    .rp-blob-b {
        position: absolute; width: 400px; height: 400px; border-radius: 50%;
        background: radial-gradient(circle, rgba(59,130,246,0.18) 0%, rgba(59,130,246,0.05) 50%, transparent 70%);
        bottom: -120px; left: -120px; pointer-events: none; z-index: 0;
    }
    .rp-ring {
        position: absolute; width: 280px; height: 280px; border-radius: 50%;
        border: 2px solid rgba(200,151,44,0.28); bottom: 40px; right: -90px;
        pointer-events: none; z-index: 0;
    }
    .rp-ring::after {
        content: ''; position: absolute; inset: 24px; border-radius: 50%;
        border: 1px solid rgba(200,151,44,0.14);
    }
    .rp-ring2 {
        position: absolute; width: 200px; height: 200px; border-radius: 50%;
        border: 1.5px solid rgba(10,27,66,0.13); top: 50px; left: -70px;
        pointer-events: none; z-index: 0;
    }

    .auth-card { width: 100%; max-width: 420px; position: relative; z-index: 1; animation: saUp .6s .05s ease both; }

    .auth-mobile-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
    .auth-mobile-brand img { width: 30px; height: 30px; border-radius: 7px; }
    .auth-mobile-brand-name { font-family: 'Fraunces', serif; font-size: 0.95rem; font-weight: 600; color: #09111f; }
    @media (min-width: 860px) { .auth-mobile-brand { display: none; } }

    .auth-head { margin-bottom: 20px; }
    .auth-eyebrow { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #c8972c; margin-bottom: 10px; }
    .auth-head h2 { font-family: 'Fraunces', serif; font-size: 1.7rem; font-weight: 700; color: #09111f; line-height: 1.15; letter-spacing: -0.02em; margin: 0 0 7px; }
    .auth-head p { font-size: 0.84rem; color: #7a8698; line-height: 1.6; margin: 0; }

    /* alerts */
    .auth-alert { border-radius: 10px; padding: 11px 15px; font-size: 0.83rem; font-weight: 500; margin-bottom: 16px; border: 1px solid transparent; line-height: 1.5; }
    .auth-alert--error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
    .auth-alert--success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }

    /* Step tracker */
    .fp-steps {
        display: flex; align-items: center;
        background: rgba(10,27,66,0.05); border: 1px solid rgba(10,27,66,0.08);
        border-radius: 14px; padding: 14px 16px; margin-bottom: 20px;
    }
    .fp-step { display: flex; flex-direction: column; align-items: center; gap: 5px; flex: 1; }
    .fp-step__num {
        width: 28px; height: 28px; border-radius: 50%;
        display: grid; place-items: center;
        font-size: 0.72rem; font-weight: 700;
        background: rgba(10,27,66,0.07); color: rgba(10,27,66,0.35);
        border: 1.5px solid rgba(10,27,66,0.12); transition: all .2s;
    }
    .fp-step__label { font-size: 0.65rem; font-weight: 600; color: rgba(10,27,66,0.35); letter-spacing: 0.03em; }
    .fp-step--active .fp-step__num { background: #c8972c; color: #fff; border-color: #c8972c; box-shadow: 0 0 0 3px rgba(200,151,44,0.2); }
    .fp-step--active .fp-step__label { color: #c8972c; }
    .fp-step--done .fp-step__num { background: #22c55e; color: #fff; border-color: #22c55e; }
    .fp-step--done .fp-step__label { color: #15803d; }
    .fp-step__line { flex: 1; height: 1.5px; background: rgba(10,27,66,0.12); margin: 0 4px; margin-bottom: 18px; }

    /* Form */
    .auth-form { display: grid; gap: 16px; }
    .fp-field-group { display: grid; }
    .fp-field-group label {
        display: block; font-size: 0.77rem; font-weight: 600;
        color: #4b5563; letter-spacing: 0.02em; margin-bottom: 6px;
    }
    .fp-field-group label small { font-weight: 400; color: #9ca3af; }
    .auth-form input[type="email"],
    .auth-form input[type="password"],
    .auth-form input[type="text"] {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid #d1d5db; border-radius: 10px;
        font-family: 'Manrope', sans-serif; font-size: 0.875rem; color: #111827;
        background: #ffffff; outline: none; appearance: none;
        transition: border-color .18s, box-shadow .18s;
    }
    .auth-form input::placeholder { color: #9ca3af; }
    .auth-form input:focus { border-color: #c8972c; box-shadow: 0 0 0 3px rgba(200,151,44,0.13); }
    .auth-form input[readonly], .auth-form input:disabled { background: #f4f4f4; color: #9ca3af; cursor: not-allowed; }

    /* Progressive reveal sections */
    .fp-section { display: grid; gap: 16px; }
    .fp-section--hidden { display: none !important; }
    .fp-section-reveal { animation: fpReveal .25s ease both; }
    @keyframes fpReveal { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

    .auth-otp-row { display: flex; gap: 8px; }
    .auth-otp-row input { flex: 1; }
    .auth-otp-btn {
        padding: 11px 14px; border: 1.5px solid #c8972c; border-radius: 10px;
        background: transparent; color: #8a6218;
        font-family: 'Manrope', sans-serif; font-size: 0.78rem; font-weight: 700;
        cursor: pointer; white-space: nowrap; transition: background .18s, color .18s; flex-shrink: 0;
    }
    .auth-otp-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .auth-otp-btn.ready { background: #c8972c; color: #ffffff; }
    .otp-status-text { display: block; font-size: 0.77rem; margin-top: 5px; line-height: 1.4; }
    .fp-warn { display: block; font-size: 0.77rem; margin-top: 5px; color: #b91c1c; }

    /* Standalone step action button */
    .btn-step {
        width: 100%; padding: 11px 22px; background: #0a1b42; color: #fff;
        font-family: 'Manrope', sans-serif; font-size: 0.875rem; font-weight: 700; letter-spacing: 0.02em;
        border: none; border-radius: 10px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background .2s, transform .15s, box-shadow .18s;
        position: relative; overflow: hidden;
    }
    .btn-step::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(130deg, rgba(200,151,44,0.18), transparent 55%);
        opacity: 0; transition: opacity .2s;
    }
    .btn-step:hover { background: #0e2152; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(10,27,66,0.24); }
    .btn-step:hover::after { opacity: 1; }
    .btn-step:active { transform: translateY(0); box-shadow: none; }
    .btn-step:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

    .btn-primary {
        width: 100%; padding: 12px 22px; background: #0a1b42; color: #fff;
        font-family: 'Manrope', sans-serif; font-size: 0.88rem; font-weight: 700; letter-spacing: 0.03em;
        border: none; border-radius: 10px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background .2s, transform .15s, box-shadow .18s;
        position: relative; overflow: hidden; margin-top: 4px;
    }
    .btn-primary::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(130deg, rgba(200,151,44,0.18), transparent 55%);
        opacity: 0; transition: opacity .2s;
    }
    .btn-primary:hover { background: #0e2152; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(10,27,66,0.24); }
    .btn-primary:hover::after { opacity: 1; }
    .btn-primary:active { transform: translateY(0); box-shadow: none; }
    .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

    .auth-divider { border: none; border-top: 1px solid rgba(10,27,66,0.1); margin: 8px 0; }
    .auth-link-subtle { font-size: 0.8rem; color: #8a9ab8; text-decoration: none; font-weight: 500; transition: color .15s; }
    .auth-link-subtle:hover { color: #c8972c; }

    .auth-theme-wrap { position: fixed; top: 16px; right: 16px; z-index: 50; }
    html:not([data-theme="dark"]) .auth-theme-wrap .theme-toggle {
        background: rgba(10,27,66,0.08) !important; border: 1.5px solid rgba(10,27,66,0.18) !important;
        color: #0a1b42 !important; box-shadow: 0 1px 4px rgba(10,27,66,0.1) !important;
    }
    html:not([data-theme="dark"]) .auth-theme-wrap .theme-toggle:hover { background: rgba(10,27,66,0.14) !important; }

    @keyframes saUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes saPulse { 0%,100% { box-shadow: 0 0 8px rgba(74,222,128,.6); } 50% { box-shadow: 0 0 3px rgba(74,222,128,.25); } }

    /* ── Dark mode ── */
    html[data-theme="dark"] .auth-rp { background: #080f1e; }
    html[data-theme="dark"] .auth-rp::before { background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px); }
    html[data-theme="dark"] .rp-blob-g { background: radial-gradient(circle, rgba(200,151,44,0.22) 0%, rgba(200,151,44,0.06) 50%, transparent 70%); }
    html[data-theme="dark"] .rp-blob-b { background: radial-gradient(circle, rgba(59,130,246,0.20) 0%, rgba(59,130,246,0.05) 50%, transparent 70%); }
    html[data-theme="dark"] .rp-ring { border-color: rgba(200,151,44,0.22); }
    html[data-theme="dark"] .rp-ring2 { border-color: rgba(255,255,255,0.07); }
    html[data-theme="dark"] .auth-lp { background: linear-gradient(158deg, #02060f 0%, #050d22 52%, #060f28 100%); }
    html[data-theme="dark"] .auth-mobile-brand-name { color: #e6ebf5; }
    html[data-theme="dark"] .auth-head h2 { color: #e8edf8; }
    html[data-theme="dark"] .auth-head p { color: #4e5e72; }
    html[data-theme="dark"] .fp-steps { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.07); }
    html[data-theme="dark"] .fp-step__num { background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.1); }
    html[data-theme="dark"] .fp-step__label { color: rgba(255,255,255,0.25); }
    html[data-theme="dark"] .fp-step__line { background: rgba(255,255,255,0.1); }
    html[data-theme="dark"] .fp-field-group label { color: #6a7a90; }
    html[data-theme="dark"] .auth-form input[type="email"],
    html[data-theme="dark"] .auth-form input[type="password"],
    html[data-theme="dark"] .auth-form input[type="text"] { background: #0c1626; border-color: #1c2b40; color: #e6ebf5; }
    html[data-theme="dark"] .auth-form input::placeholder { color: #283848; }
    html[data-theme="dark"] .auth-form input:focus { border-color: #c8972c; box-shadow: 0 0 0 3px rgba(200,151,44,0.12); }
    html[data-theme="dark"] .auth-form input[readonly], html[data-theme="dark"] .auth-form input:disabled { background: #0a1220; color: #3a4a5e; }
    html[data-theme="dark"] .btn-primary { background: #0c2050; }
    html[data-theme="dark"] .btn-primary:hover { background: #0f2660; }
    html[data-theme="dark"] .btn-step { background: #0c2050; }
    html[data-theme="dark"] .btn-step:hover { background: #0f2660; }
    html[data-theme="dark"] .auth-divider { border-top-color: rgba(255,255,255,0.07); }
    html[data-theme="dark"] .auth-eyebrow { color: #c8972c; }
    html[data-theme="dark"] .fp-field-group label { color: #6b7280; }
    html[data-theme="dark"] .auth-otp-btn { color: #c8972c; }
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
            <div class="auth-lp-eyebrow">Account Recovery</div>
            <h2 class="auth-lp-headline">Back in 3<br>simple <em>steps.</em></h2>
            <p class="auth-lp-desc">Enter your email, verify with a one-time code, and set a new password — you'll be back in seconds.</p>

            <div class="auth-lp-features">
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Step 1 — Enter Email
                        <small>We'll send a verification code to your inbox</small>
                    </span>
                </div>
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Step 2 — Verify OTP
                        <small>Confirm your identity with the 6-digit code</small>
                    </span>
                </div>
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Step 3 — New Password
                        <small>Set a strong password and get back in</small>
                    </span>
                </div>
            </div>
        </div>

        <div class="auth-lp-bottom">
            <div class="auth-lp-status">
                <span class="auth-lp-dot"></span>
                Secure password reset
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

            <!-- Heading -->
            <div class="auth-head">
                <div class="auth-eyebrow">Password Reset</div>
                <h2>Reset your password</h2>
                <p>Follow the steps below to regain access to your account.</p>
            </div>

            <!-- Step tracker -->
            <div class="fp-steps">
                <div class="fp-step<?= !$forgotOtpVerified ? ' fp-step--active' : ' fp-step--done' ?>">
                    <span class="fp-step__num"><?= !$forgotOtpVerified ? '1' : '✓' ?></span>
                    <span class="fp-step__label">Email</span>
                </div>
                <div class="fp-step__line"></div>
                <div class="fp-step<?= !$forgotOtpVerified ? '' : ' fp-step--done' ?>">
                    <span class="fp-step__num">2</span>
                    <span class="fp-step__label">Verify OTP</span>
                </div>
                <div class="fp-step__line"></div>
                <div class="fp-step<?= $forgotOtpVerified ? ' fp-step--active' : '' ?>">
                    <span class="fp-step__num">3</span>
                    <span class="fp-step__label">New Password</span>
                </div>
            </div>

            <?php if (session()->has('error')): ?>
                <div class="auth-alert auth-alert--error"><?= esc(session('error')) ?></div>
            <?php endif ?>
            <?php if (session()->has('success')): ?>
                <div class="auth-alert auth-alert--success"><?= esc(session('success')) ?></div>
            <?php endif ?>

            <form method="post" action="<?= site_url('users/forgot-password') ?>" class="auth-form" novalidate autocomplete="off" id="forgot-form">

                <!-- ── STEP 1: Email + Send OTP ── -->
                <div id="fp-step1" class="fp-section<?= $forgotOtpVerified ? ' fp-section--hidden' : '' ?>">
                    <div class="fp-field-group">
                        <label for="fp-email">Email Address</label>
                        <input id="fp-email" name="email" type="email" required maxlength="150"
                               autocomplete="off" placeholder="you@example.com"
                               value="<?= esc($emailValue) ?>"<?= $forgotOtpVerified ? ' readonly' : '' ?>>
                        <small id="forgot-otp-status" class="otp-status-text" aria-live="polite"></small>
                    </div>
                    <button type="button" class="btn-step" id="send-forgot-otp-btn" disabled>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        Send OTP to Email
                    </button>
                </div>

                <!-- ── STEP 2: Verify OTP ── -->
                <div id="fp-step2" class="fp-section<?= $forgotOtpVerified ? ' fp-section--hidden' : ' fp-section--hidden' ?>">
                    <?php if ($forgotOtpVerified && $sessionOtp !== ''): ?>
                        <input type="hidden" name="otp" value="<?= esc($sessionOtp) ?>">
                    <?php endif ?>
                    <div class="fp-field-group">
                        <label for="fp-otp">Verification Code</label>
                        <?php if ($forgotOtpVerified && $sessionOtp !== ''): ?>
                            <input id="fp-otp" type="text" placeholder="✓ OTP verified" readonly
                                   style="background:rgba(34,197,94,0.06);border-color:#86efac;color:#15803d;">
                        <?php else: ?>
                            <input id="fp-otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*"
                                   required maxlength="6" placeholder="Enter 6-digit code"
                                   value="<?= esc((string) (old('otp') ?? '')) ?>" disabled>
                        <?php endif ?>
                    </div>
                    <button type="button" class="btn-step" id="verify-forgot-otp-btn" disabled>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Verify Code
                    </button>
                </div>

                <!-- ── STEP 3: New Password + Submit ── -->
                <div id="fp-step3" class="fp-section<?= $forgotOtpVerified ? '' : ' fp-section--hidden' ?>">
                    <div class="fp-field-group">
                        <label for="fp-password">New Password <small>(min 8 characters)</small></label>
                        <input id="fp-password" name="password" type="password" required
                               minlength="8" maxlength="255" autocomplete="new-password"
                               placeholder="Create a strong password">
                        <small id="fp-pw-len-warn" class="fp-warn" style="display:none;"></small>
                    </div>
                    <div class="fp-field-group">
                        <label for="fp-confirm">Confirm New Password</label>
                        <input id="fp-confirm" name="password_confirm" type="password" required
                               maxlength="255" autocomplete="new-password"
                               placeholder="Repeat your new password">
                        <small id="fp-pw-match-warn" class="fp-warn" style="display:none;"></small>
                    </div>
                    <button type="submit" class="btn-primary" id="fp-submit-btn" disabled style="opacity:0.5;cursor:not-allowed;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Reset Password
                    </button>
                    <?php if ($forgotOtpVerified): ?>
                    <div style="text-align:center;margin-top:6px;">
                        <a href="<?= site_url('users/forgot-password?restart=1') ?>" class="auth-link-subtle">↩ Use a different email?</a>
                    </div>
                    <?php endif ?>
                </div>

                <hr class="auth-divider" style="margin-top:4px;">
                <div style="text-align:center;">
                    <a href="<?= site_url('users/login') ?>" class="auth-link-subtle">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px;"><polyline points="15 18 9 12 15 6"/></svg>
                        Back to Login
                    </a>
                </div>

            </form>
        </div><!-- /.auth-card -->
    </div><!-- /.auth-rp -->

</div><!-- /.auth-shell -->

<script>
(function () {
    var emailEl    = document.getElementById('fp-email');
    var otpEl      = document.getElementById('fp-otp');
    var passwordEl = document.getElementById('fp-password');
    var confirmEl  = document.getElementById('fp-confirm');
    var sendBtn    = document.getElementById('send-forgot-otp-btn');
    var verifyBtn  = document.getElementById('verify-forgot-otp-btn');
    var submitBtn  = document.getElementById('fp-submit-btn');
    var statusEl   = document.getElementById('forgot-otp-status');
    var step1      = document.getElementById('fp-step1');
    var step2      = document.getElementById('fp-step2');
    var step3      = document.getElementById('fp-step3');
    var otpVerified = <?= $forgotOtpVerified ? 'true' : 'false' ?>;

    function isValidEmail(val) {
        return /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/.test(val.trim());
    }

    function showStatus(msg, isError) {
        if (!statusEl) return;
        statusEl.textContent = msg;
        statusEl.style.color = isError ? '#8a251a' : '#1f8f5f';
    }

    function showSection(el) {
        if (!el) return;
        el.classList.remove('fp-section--hidden');
        el.classList.add('fp-section-reveal');
    }

    function updateSendBtn() {
        if (!sendBtn) return;
        var ready = emailEl && isValidEmail(emailEl.value) && !emailEl.readOnly;
        sendBtn.disabled = !ready;
    }

    function updateVerifyBtn() {
        if (!verifyBtn) return;
        var ready = !otpVerified && otpEl && !otpEl.disabled && otpEl.value.trim().length === 6;
        verifyBtn.disabled = !ready;
    }

    function revealStep3() {
        otpVerified = true;
        showSection(step3);
        if (passwordEl) { passwordEl.disabled = false; passwordEl.focus(); }
        if (confirmEl)  { confirmEl.disabled = false; }
        updateSubmitBtn();
    }

    function updateSubmitBtn() {
        if (!submitBtn) return;
        var password = passwordEl ? passwordEl.value : '';
        var confirm  = confirmEl  ? confirmEl.value  : '';
        var ready    = otpVerified && password.length >= 8 && password === confirm;
        submitBtn.disabled = !ready;
        submitBtn.style.opacity = ready ? '1' : '0.5';
        submitBtn.style.cursor  = ready ? 'pointer' : 'not-allowed';

        var warnEl = document.getElementById('fp-pw-match-warn');
        if (warnEl) {
            warnEl.style.display = (confirm.length > 0 && password !== confirm) ? 'block' : 'none';
            warnEl.textContent = '⚠ Passwords do not match.';
        }
        var lenWarnEl = document.getElementById('fp-pw-len-warn');
        if (lenWarnEl) {
            lenWarnEl.style.display = (password.length > 0 && password.length < 8) ? 'block' : 'none';
            lenWarnEl.textContent = '⚠ Password must be at least 8 characters.';
        }
    }

    if (emailEl) {
        emailEl.addEventListener('input', function () {
            updateSendBtn();
            /* If email changes after OTP sent, hide step 2 again */
            if (step2 && !step2.classList.contains('fp-section--hidden')) {
                step2.classList.add('fp-section--hidden');
                if (otpEl) { otpEl.disabled = true; otpEl.value = ''; }
                otpVerified = false;
            }
        });
    }

    if (otpEl) {
        otpEl.addEventListener('input', function () {
            updateVerifyBtn();
        });
    }
    if (passwordEl) { passwordEl.addEventListener('input', updateSubmitBtn); }
    if (confirmEl)  { confirmEl.addEventListener('input', updateSubmitBtn); }

    if (sendBtn) {
        sendBtn.addEventListener('click', function () {
            if (!emailEl || !isValidEmail(emailEl.value)) return;
            sendBtn.disabled = true;
            showStatus('Sending OTP to your email…', false);

            var body = new URLSearchParams();
            body.set('email', emailEl.value.trim());

            fetch(<?= json_encode(site_url('users/forgot-password/send-otp')) ?>, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest' },
                body: body.toString()
            })
            .then(function (res) { return res.json().catch(function () { return { ok: false, message: 'Unexpected server response.' }; }); })
            .then(function (data) {
                var ok = !!(data && data.ok);
                var message = (data && data.message) ? data.message : (ok ? 'OTP sent to your email.' : 'Failed to send OTP.');
                showStatus(message, !ok);
                if (ok) {
                    emailEl.readOnly = true;
                    showSection(step2);
                    if (otpEl) { otpEl.disabled = false; otpEl.value = ''; otpEl.focus(); }
                    updateVerifyBtn();
                } else {
                    sendBtn.disabled = false;
                    updateSendBtn();
                }
            })
            .catch(function () {
                showStatus('Failed to send OTP. Please try again.', true);
                sendBtn.disabled = false;
                updateSendBtn();
            });
        });
    }

    if (verifyBtn) {
        verifyBtn.addEventListener('click', function () {
            if (!emailEl || !otpEl || !isValidEmail(emailEl.value) || otpEl.value.trim().length !== 6) return;
            verifyBtn.disabled = true;
            showStatus('Verifying code…', false);

            var body = new URLSearchParams();
            body.set('email', emailEl.value.trim());
            body.set('otp', otpEl.value.trim());

            fetch(<?= json_encode(site_url('users/forgot-password/verify-otp')) ?>, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest' },
                body: body.toString()
            })
            .then(function (res) { return res.json().catch(function () { return { ok: false, message: 'Unexpected server response.' }; }); })
            .then(function (data) {
                var ok = !!(data && data.ok);
                var message = (data && data.message) ? data.message : (ok ? 'Code verified.' : 'Verification failed.');
                showStatus(message, !ok);
                if (ok) {
                    revealStep3();
                } else {
                    verifyBtn.disabled = false;
                    updateVerifyBtn();
                }
            })
            .catch(function () {
                showStatus('Failed to verify. Please try again.', true);
                verifyBtn.disabled = false;
                updateVerifyBtn();
            });
        });
    }

    /* Server-side: OTP already verified — go straight to Step 3 */
    if (otpVerified) {
        updateSubmitBtn();
    }

    updateSendBtn();
    updateVerifyBtn();
})();
</script>
<?= $this->endSection() ?>
