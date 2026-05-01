<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<style>
body.is-auth-screen {
    background:
        radial-gradient(circle at 80% 0, rgba(47,107,255,0.18), transparent 30%),
        radial-gradient(circle at 8% 18%, rgba(11,31,77,0.55), transparent 40%),
        linear-gradient(165deg, #080e1c 0%, #0a1535 45%, #0d214e 100%);
}
</style>

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
        <div class="auth-topbar-end">
            <?= $this->include('partials/theme_toggle') ?>
        </div>
    </header>

    <div class="auth-split auth-split--center">
        <section class="auth-panel fp-panel" style="max-width:440px;text-align:center;">

            <!-- Icon -->
            <div style="margin:0 auto 20px;width:72px;height:72px;border-radius:50%;background:rgba(242,139,130,0.12);border:2px solid rgba(242,139,130,0.3);display:flex;align-items:center;justify-content:center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#f28b82" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                </svg>
            </div>

            <!-- Title -->
            <h1 style="margin:0 0 8px;font-size:1.35rem;font-family:'Fraunces',serif;color:var(--ink,#0d1b3d);">Account Deactivated</h1>
            <p style="margin:0 0 24px;font-size:0.88rem;color:var(--muted,#5f7298);line-height:1.65;">
                Your account has been deactivated by an administrator.<br>
                You no longer have access to the CampusVoice portal.
            </p>

            <!-- Info box -->
            <div style="background:rgba(242,139,130,0.08);border:1px solid rgba(242,139,130,0.25);border-radius:10px;padding:14px 16px;margin-bottom:24px;text-align:left;">
                <p style="margin:0 0 6px;font-size:0.8rem;font-weight:700;color:#f28b82;letter-spacing:0.02em;">WHAT DOES THIS MEAN?</p>
                <ul style="margin:0;padding-left:16px;font-size:0.82rem;color:var(--muted,#5f7298);line-height:1.7;">
                    <li>You cannot log in or access any content</li>
                    <li>Your submitted feedback is still on record</li>
                    <li>Contact your school administrator to appeal</li>
                </ul>
            </div>

            <!-- Contact / back -->
            <div style="display:flex;flex-direction:column;gap:10px;align-items:center;">
                <a href="mailto:admin@campusvoice.local"
                   style="display:inline-flex;align-items:center;gap:7px;padding:10px 22px;border-radius:999px;background:linear-gradient(135deg,#1e3a5f,#162d4a);color:#8ec5ff;font-size:0.83rem;font-weight:700;text-decoration:none;border:1px solid #2a4a6a;transition:opacity 0.15s;"
                   onmouseover="this.style.opacity='0.82'" onmouseout="this.style.opacity='1'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Contact Administrator
                </a>
                <a href="<?= site_url('users/login') ?>"
                   style="font-size:0.8rem;color:#6b8fd4;text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-weight:600;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Back to Login
                </a>
            </div>

        </section>
    </div>
</div>
<?= $this->endSection() ?>
