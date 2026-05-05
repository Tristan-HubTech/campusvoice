<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>
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

    .auth-card { width: 100%; max-width: 440px; position: relative; z-index: 1; animation: saUp .6s .05s ease both; }

    .auth-mobile-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
    .auth-mobile-brand img { width: 30px; height: 30px; border-radius: 7px; }
    .auth-mobile-brand-name { font-family: 'Fraunces', serif; font-size: 0.95rem; font-weight: 600; color: #09111f; }
    @media (min-width: 860px) { .auth-mobile-brand { display: none; } }

    .auth-head { margin-bottom: 24px; }
    .auth-eyebrow { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #c8972c; margin-bottom: 10px; }
    .auth-head h2 { font-family: 'Fraunces', serif; font-size: 1.7rem; font-weight: 700; color: #09111f; line-height: 1.15; letter-spacing: -0.02em; margin: 0 0 7px; }
    .auth-head p { font-size: 0.84rem; color: #7a8698; line-height: 1.6; margin: 0; }

    .auth-form { display: grid; gap: 14px; }

    .auth-form label {
        display: block; font-size: 0.77rem; font-weight: 700;
        color: #4b5563; letter-spacing: 0.02em; margin-bottom: 6px;
    }
    .auth-form label small { font-weight: 400; color: #9ca3af; }

    .auth-form input[type="email"],
    .auth-form input[type="password"],
    .auth-form input[type="text"],
    .auth-form input[type="tel"] {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid #e0e2e7; border-radius: 10px;
        font-family: 'Manrope', sans-serif; font-size: 0.88rem; color: #111827;
        background: #ffffff; outline: none; appearance: none;
        transition: border-color .18s, box-shadow .18s;
    }
    .auth-form input::placeholder { color: #c4cad4; }
    .auth-form input:focus { border-color: #c8972c; box-shadow: 0 0 0 3px rgba(200,151,44,0.13); }

    .form-row-half { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

    .btn-primary {
        width: 100%; padding: 12px 22px; background: #0a1b42; color: #fff;
        font-family: 'Manrope', sans-serif; font-size: 0.88rem; font-weight: 700; letter-spacing: 0.03em;
        border: none; border-radius: 10px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background .2s, transform .15s, box-shadow .18s;
        position: relative; overflow: hidden; margin-top: 6px;
    }
    .btn-primary::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(130deg, rgba(200,151,44,0.18), transparent 55%);
        opacity: 0; transition: opacity .2s;
    }
    .btn-primary:hover { background: #0e2152; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(10,27,66,0.24); }
    .btn-primary:hover::after { opacity: 1; }
    .btn-primary:active { transform: translateY(0); box-shadow: none; }

    .auth-divider { border: none; border-top: 1px solid rgba(10,27,66,0.1); margin: 8px 0; }
    .auth-link-subtle { font-size: 0.8rem; color: #8a9ab8; text-decoration: none; font-weight: 500; transition: color .15s; }
    .auth-link-subtle:hover { color: #c8972c; }
    .auth-link-subtle strong { color: #0a1b42; }

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
    html[data-theme="dark"] .auth-form label { color: #6a7a90; }
    html[data-theme="dark"] .auth-form input[type="email"],
    html[data-theme="dark"] .auth-form input[type="password"],
    html[data-theme="dark"] .auth-form input[type="text"],
    html[data-theme="dark"] .auth-form input[type="tel"] { background: #0c1626; border-color: #1c2b40; color: #e6ebf5; }
    html[data-theme="dark"] .auth-form input::placeholder { color: #283848; }
    html[data-theme="dark"] .auth-form input:focus { border-color: #c8972c; box-shadow: 0 0 0 3px rgba(200,151,44,0.12); }
    html[data-theme="dark"] .btn-primary { background: #0c2050; }
    html[data-theme="dark"] .btn-primary:hover { background: #0f2660; }
    html[data-theme="dark"] .auth-divider { border-top-color: rgba(255,255,255,0.07); }
    html[data-theme="dark"] .auth-eyebrow { color: #c8972c; }
    html[data-theme="dark"] .auth-link-subtle strong { color: #8ab4f8; }
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
            <div class="auth-lp-eyebrow">New Account</div>
            <h2 class="auth-lp-headline">Join the<br><em>conversation.</em></h2>
            <p class="auth-lp-desc">Create your student account and start shaping campus life — your feedback, your voice, your community.</p>

            <div class="auth-lp-features">
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Submit Feedback
                        <small>Your ideas and concerns reach the right people</small>
                    </span>
                </div>
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Campus Announcements
                        <small>Stay informed with official campus updates</small>
                    </span>
                </div>
                <div class="auth-lp-feat">
                    <span class="auth-lp-feat-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </span>
                    <span class="auth-lp-feat-label">
                        Community Feed
                        <small>Connect with fellow students on campus</small>
                    </span>
                </div>
            </div>
        </div>

        <div class="auth-lp-bottom">
            <div class="auth-lp-status">
                <span class="auth-lp-dot"></span>
                Registration is open
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
                <div class="auth-eyebrow">Create Account</div>
                <h2>Student registration</h2>
                <p>Fill in your details below to create your campus portal account.</p>
            </div>

            <form method="post" action="<?= site_url('users/register') ?>" class="auth-form" novalidate>

                <div class="form-row-half">
                    <div>
                        <label for="reg-first">First Name</label>
                        <input id="reg-first" name="first_name" type="text" required maxlength="100"
                            placeholder="First name"
                            value="<?= esc((string) (old('first_name') ?? '')) ?>">
                    </div>
                    <div>
                        <label for="reg-last">Last Name</label>
                        <input id="reg-last" name="last_name" type="text" required maxlength="100"
                            placeholder="Last name"
                            value="<?= esc((string) (old('last_name') ?? '')) ?>">
                    </div>
                </div>

                <div>
                    <label for="reg-email">Email Address</label>
                    <input id="reg-email" name="email" type="email" required maxlength="150"
                        placeholder="you@example.com"
                        value="<?= esc((string) (old('email') ?? '')) ?>">
                </div>

                <div>
                    <label for="reg-phone">Phone <small>(optional)</small></label>
                    <input id="reg-phone" name="phone" type="tel" maxlength="30"
                        placeholder="e.g. 09xx-xxx-xxxx"
                        value="<?= esc((string) (old('phone') ?? '')) ?>">
                </div>

                <div>
                    <label for="reg-password">Password <small>(min 8 characters)</small></label>
                    <input id="reg-password" name="password" type="password" required minlength="8" maxlength="255"
                        autocomplete="new-password" placeholder="Create a password">
                </div>

                <div>
                    <label for="reg-confirm">Confirm Password</label>
                    <input id="reg-confirm" name="password_confirm" type="password" required
                        autocomplete="new-password" placeholder="Repeat password">
                </div>

                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    Create Account
                </button>

                <hr class="auth-divider">
                <div style="text-align:center;">
                    <a href="<?= site_url('users/login') ?>" class="auth-link-subtle">
                        Already have an account? <strong>Sign in →</strong>
                    </a>
                </div>

            </form>
        </div><!-- /.auth-card -->
    </div><!-- /.auth-rp -->

</div><!-- /.auth-shell -->
<?= $this->endSection() ?>
