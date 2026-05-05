<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->include('partials/theme_fouc') ?>
    <title><?= esc($title ?? 'Admin Login') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;0,9..144,700;1,9..144,400;1,9..144,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/admin/control-panel.css') ?>">
    <?= $this->include('partials/theme_styles') ?>
    <style>
        /* ── hard-reset conflicting control-panel rules ── */
        .login-page  { display: block !important; place-items: unset !important; }
        .login-wrap  { width: 100% !important; max-width: none !important; }
        .login-panel { border: none !important; border-radius: 0 !important;
                       background: transparent !important; box-shadow: none !important;
                       padding: 0 !important; }

        /* ══════════════════════════════════════
           PAGE SHELL
        ══════════════════════════════════════ */
        html, body { margin: 0; padding: 0; }

        body.login-page {
            min-height: 100vh;
            display: flex !important;
            flex-direction: row;
            background: #f2f1ee;
            font-family: 'Manrope', sans-serif;
        }

        /* ══════════════════════════════════════
           LEFT — BRAND PANEL
        ══════════════════════════════════════ */
        .lp-brand {
            display: none;
            width: 50%;
            flex-shrink: 0;
            min-height: 100vh;
            position: relative;
            background: linear-gradient(160deg, #060d22 0%, #0a1b42 50%, #0b2056 100%);
            overflow: hidden;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 48px 44px;
            box-sizing: border-box;
        }

        @media (min-width: 860px) {
            .lp-brand { display: flex; }
        }

        /* dot-grid */
        .lp-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
        }

        /* gold glow */
        .lp-brand::after {
            content: '';
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(200,151,44,0.20) 0%, transparent 65%);
            top: -140px;
            right: -140px;
            pointer-events: none;
        }

        /* blue glow bottom */
        .lp-orb {
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
            bottom: -80px;
            left: -60px;
            pointer-events: none;
        }

        /* ── Top: logo row ── */
        .lp-top {
            display: flex;
            align-items: center;
            gap: 11px;
            position: relative;
            z-index: 2;
            animation: cvUp .6s ease both;
        }

        .lp-logo {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            flex-shrink: 0;
        }

        .lp-logo-name {
            font-family: 'Fraunces', serif;
            font-size: 1rem;
            font-weight: 600;
            color: rgba(255,255,255,0.88);
        }

        /* ── Mid: headline block ── */
        .lp-mid {
            position: relative;
            z-index: 2;
            animation: cvUp .7s .1s ease both;
        }

        .lp-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #c8972c;
            margin-bottom: 20px;
        }

        .lp-eyebrow::before {
            content: '';
            display: block;
            width: 26px;
            height: 1.5px;
            background: #c8972c;
            flex-shrink: 0;
        }

        .lp-headline {
            font-family: 'Fraunces', serif;
            font-size: clamp(1.9rem, 3vw, 2.9rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.12;
            letter-spacing: -0.025em;
            margin: 0 0 18px;
        }

        .lp-headline em {
            font-style: italic;
            font-weight: 300;
            color: rgba(200,151,44,0.88);
        }

        .lp-desc {
            font-size: 0.86rem;
            color: rgba(255,255,255,0.38);
            line-height: 1.75;
            margin: 0 0 32px;
            max-width: 290px;
        }

        /* ── Feature list ── */
        .lp-features {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .lp-feat {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px;
        }

        .lp-feat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(200,151,44,0.13);
            display: grid;
            place-items: center;
            color: #c8972c;
            flex-shrink: 0;
        }

        .lp-feat-icon svg { display: block; }

        .lp-feat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255,255,255,0.52);
            line-height: 1.3;
        }

        .lp-feat-label small {
            display: block;
            font-weight: 400;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.26);
            margin-top: 1px;
        }

        /* ── Bottom: status badge ── */
        .lp-bottom {
            position: relative;
            z-index: 2;
            animation: cvUp .7s .25s ease both;
        }

        .lp-status {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 9px 18px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 100px;
            font-size: 0.74rem;
            color: rgba(255,255,255,0.42);
        }

        .lp-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 8px rgba(74,222,128,0.6);
            flex-shrink: 0;
            animation: cvPulse 2.5s ease-in-out infinite;
        }

        /* ══════════════════════════════════════
           RIGHT — FORM SIDE
        ══════════════════════════════════════ */
        .login-wrap {
            flex: 1;
            min-height: 100vh;
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 48px 28px;
            background: #e8e4dc;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        /* dot grid */
        .login-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(10,27,66,0.12) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
            z-index: 0;
        }

        /* gold blob — top right */
        .rp-blob-gold {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(200,151,44,0.28) 0%, rgba(200,151,44,0.06) 50%, transparent 70%);
            top: -160px;
            right: -160px;
            pointer-events: none;
            z-index: 0;
        }

        /* blue blob — bottom left */
        .rp-blob-blue {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.18) 0%, rgba(59,130,246,0.05) 50%, transparent 70%);
            bottom: -120px;
            left: -120px;
            pointer-events: none;
            z-index: 0;
        }

        /* decorative ring — bottom right */
        .rp-ring {
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            border: 2px solid rgba(200,151,44,0.28);
            bottom: 40px;
            right: -90px;
            pointer-events: none;
            z-index: 0;
        }

        .rp-ring::after {
            content: '';
            position: absolute;
            inset: 24px;
            border-radius: 50%;
            border: 1px solid rgba(200,151,44,0.14);
        }

        /* decorative ring — top left */
        .rp-ring2 {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 1.5px solid rgba(10,27,66,0.13);
            top: 50px;
            left: -70px;
            pointer-events: none;
            z-index: 0;
        }

        .login-panel {
            width: 100%;
            max-width: 390px;
            position: relative;
            z-index: 1;
            animation: cvUp .6s .05s ease both;
        }

        /* mobile logo */
        .lp-mobile-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 44px;
        }

        .lp-mobile-brand img { width: 30px; height: 30px; border-radius: 7px; }

        .lp-mobile-name {
            font-family: 'Fraunces', serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: #09111f;
        }

        @media (min-width: 860px) { .lp-mobile-brand { display: none; } }

        /* form head */
        .login-head { margin-bottom: 34px; }

        .lp-form-eyebrow {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #c8972c;
            margin-bottom: 11px;
        }

        .login-head h1 {
            font-family: 'Fraunces', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #09111f;
            line-height: 1.12;
            letter-spacing: -0.025em;
            margin: 0 0 10px;
        }

        .login-head p {
            font-size: 0.86rem;
            color: #7a8698;
            line-height: 1.65;
            margin: 0;
        }

        /* alerts */
        .alert {
            border-radius: 10px;
            padding: 11px 15px;
            font-size: 0.83rem;
            font-weight: 500;
            margin-bottom: 22px;
            border: 1px solid transparent;
            line-height: 1.5;
        }

        .alert.error   { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
        .alert.success { background:#f0fdf4; border-color:#bbf7d0; color:#166534; }

        /* form fields */
        .login-form { display: grid; gap: 18px; }

        .field-group { display: grid; gap: 7px; }

        .field-group label {
            font-size: 0.77rem;
            font-weight: 700;
            color: #4b5563;
            letter-spacing: 0.02em;
        }

        .field-wrap { position: relative; }

        .field-wrap .fi {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #c4cad4;
            pointer-events: none;
            transition: color .18s;
            display: flex;
        }

        .field-group input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #e0e2e7;
            border-radius: 11px;
            font-family: 'Manrope', sans-serif;
            font-size: 0.9rem;
            color: #111827;
            background: #ffffff;
            outline: none;
            box-sizing: border-box;
            transition: border-color .18s, box-shadow .18s;
            appearance: none;
        }

        .field-group input::placeholder { color: #c4cad4; }

        .field-group input:focus {
            border-color: #c8972c;
            box-shadow: 0 0 0 3px rgba(200,151,44,0.13);
        }

        .field-group input:focus ~ .fi { color: #c8972c; }

        /* submit */
        .cv-btn {
            width: 100%;
            margin-top: 6px;
            padding: 13px 22px;
            background: #0a1b42;
            color: #fff;
            font-family: 'Manrope', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            border: none;
            border-radius: 11px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .2s, transform .15s, box-shadow .18s;
            position: relative;
            overflow: hidden;
        }

        .cv-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(130deg, rgba(200,151,44,0.18), transparent 55%);
            opacity: 0;
            transition: opacity .2s;
        }

        .cv-btn:hover {
            background: #0e2152;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(10,27,66,0.24);
        }

        .cv-btn:hover::after { opacity: 1; }
        .cv-btn:active { transform: translateY(0); box-shadow: none; }

        .cv-arrow { display: inline-block; transition: transform .18s; }
        .cv-btn:hover .cv-arrow { transform: translateX(4px); }

        .login-note {
            margin-top: 30px;
            text-align: center;
            font-size: 0.73rem;
            color: #b0b8c4;
        }

        /* theme toggle — always visible on any background */
        .theme-wrap {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 50;
        }

        /* Light mode: force the button to be visible on cream/white */
        html:not([data-theme="dark"]) .theme-wrap .theme-toggle {
            background: rgba(10,27,66,0.08) !important;
            border: 1.5px solid rgba(10,27,66,0.18) !important;
            color: #0a1b42 !important;
            box-shadow: 0 1px 4px rgba(10,27,66,0.1) !important;
        }

        html:not([data-theme="dark"]) .theme-wrap .theme-toggle:hover {
            background: rgba(10,27,66,0.14) !important;
        }

        /* ── keyframes ── */
        @keyframes cvUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes cvPulse {
            0%,100% { box-shadow: 0 0 8px rgba(74,222,128,.6); }
            50%      { box-shadow: 0 0 3px rgba(74,222,128,.25); }
        }

        /* ── dark mode ── */
        html[data-theme="dark"] body.login-page { background: #060c18; }

        /* right side — deep navy, not black */
        html[data-theme="dark"] .login-wrap {
            background: #080f1e;
        }
        html[data-theme="dark"] .login-wrap::before {
            background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
        }
        html[data-theme="dark"] .rp-blob-gold {
            background: radial-gradient(circle, rgba(200,151,44,0.22) 0%, rgba(200,151,44,0.06) 50%, transparent 70%);
        }
        html[data-theme="dark"] .rp-blob-blue {
            background: radial-gradient(circle, rgba(59,130,246,0.20) 0%, rgba(59,130,246,0.05) 50%, transparent 70%);
        }
        html[data-theme="dark"] .rp-ring {
            border-color: rgba(200,151,44,0.22);
        }
        html[data-theme="dark"] .rp-ring::after {
            border-color: rgba(200,151,44,0.10);
        }
        html[data-theme="dark"] .rp-ring2 {
            border-color: rgba(255,255,255,0.07);
        }

        /* left side — noticeably deeper in dark mode */
        html[data-theme="dark"] .lp-brand {
            background: linear-gradient(160deg, #02060f 0%, #050d22 50%, #060f28 100%);
        }

        /* form text & inputs */
        html[data-theme="dark"] .lp-mobile-name { color: #e6ebf5; }
        html[data-theme="dark"] .lp-form-eyebrow { color: #c8972c; }
        html[data-theme="dark"] .login-head h1  { color: #e8edf8; }
        html[data-theme="dark"] .login-head p   { color: #4e5e72; }
        html[data-theme="dark"] .field-group label { color: #6a7a90; }
        html[data-theme="dark"] .field-group input {
            background: #0c1626;
            border-color: #1c2b40;
            color: #e6ebf5;
        }
        html[data-theme="dark"] .field-group input::placeholder { color: #283848; }
        html[data-theme="dark"] .field-group input:focus {
            border-color: #c8972c;
            box-shadow: 0 0 0 3px rgba(200,151,44,0.12);
        }
        html[data-theme="dark"] .fi { color: #2e4060; }
        html[data-theme="dark"] .cv-btn { background: #0c2050; }
        html[data-theme="dark"] .cv-btn:hover {
            background: #0f2660;
            box-shadow: 0 8px 24px rgba(10,27,66,0.5);
        }
        html[data-theme="dark"] .login-note { color: #2e3e52; }
    </style>
</head>
<body class="login-page">

<div class="theme-wrap">
    <?= $this->include('partials/theme_toggle', ['toggleClass' => 'theme-toggle--on-light']) ?>
</div>

<!-- ══ LEFT: Brand Panel ══ -->
<div class="lp-brand" aria-hidden="true">
    <div class="lp-orb"></div>

    <!-- Logo row -->
    <div class="lp-top">
        <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="" class="lp-logo">
        <span class="lp-logo-name">CampusVoice</span>
    </div>

    <!-- Headline + features -->
    <div class="lp-mid">
        <div class="lp-eyebrow">Control Panel</div>
        <h2 class="lp-headline">One dashboard<br>for every <em>voice.</em></h2>
        <p class="lp-desc">Review feedback, publish announcements, and keep your campus community running smoothly.</p>

        <div class="lp-features">
            <div class="lp-feat">
                <span class="lp-feat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </span>
                <span class="lp-feat-label">
                    Feedback Management
                    <small>Review, reply, and resolve submissions</small>
                </span>
            </div>
            <div class="lp-feat">
                <span class="lp-feat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg>
                </span>
                <span class="lp-feat-label">
                    Announcements
                    <small>Publish and pin campus notices</small>
                </span>
            </div>
            <div class="lp-feat">
                <span class="lp-feat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <span class="lp-feat-label">
                    Student Oversight
                    <small>Manage roles, accounts, and activity</small>
                </span>
            </div>
        </div>
    </div>

    <!-- Status badge -->
    <div class="lp-bottom">
        <div class="lp-status">
            <span class="lp-dot"></span>
            All systems operational
        </div>
    </div>
</div>

<!-- ══ RIGHT: Form Panel ══ -->
<main class="login-wrap">
    <div class="rp-blob-gold"></div>
    <div class="rp-blob-blue"></div>
    <div class="rp-ring"></div>
    <div class="rp-ring2"></div>
    <div class="login-panel">

        <!-- Mobile logo (hidden on desktop) -->
        <div class="lp-mobile-brand">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice">
            <span class="lp-mobile-name">CampusVoice</span>
        </div>

        <div class="login-head">
            <div class="lp-form-eyebrow">Secure Access</div>
            <h1>Welcome back</h1>
            <p>Sign in with your admin credentials to access the control panel.</p>
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
                <div class="field-wrap">
                    <input id="email" name="email" type="email" required
                           placeholder="admin@campus.edu"
                           autocomplete="username" autofocus
                           value="<?= esc((string) old('email', '')) ?>">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </span>
                </div>
            </div>

            <div class="field-group">
                <label for="password">Password</label>
                <div class="field-wrap">
                    <input id="password" name="password" type="password" required
                           placeholder="••••••••••"
                           autocomplete="current-password">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                </div>
            </div>

            <button type="submit" class="cv-btn">
                Sign In <span class="cv-arrow">→</span>
            </button>

        </form>

        <p class="login-note">CampusVoice &copy; <?= date('Y') ?> &nbsp;·&nbsp; Admin Portal</p>
    </div>
</main>

<?= $this->include('partials/theme_script') ?>
</body>
</html>
