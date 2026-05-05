<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | CampusVoice</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.portal-main--auth { padding: 0 !important; margin: 0 !important; }
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; padding: 0; background: var(--cv-navy-ink, #06080f); }

/* ── Design tokens ── */
:root {
    --cv-navy-ink:   #060e1f;
    --cv-navy:       #1a365d;
    --cv-navy-mid:   #0f2042;
    --cv-gold:       #c8972c;
    --cv-gold-pale:  rgba(200,151,44,0.13);
    --cv-warm-bg:    #f4f0e8;
    --cv-card:       #ffffff;
    --cv-text:       #0f1923;
    --cv-text-soft:  #4b5563;
    --cv-border:     #ddd8ce;
    --cv-pf:         'Playfair Display', Georgia, serif;
    --cv-jk:         'Plus Jakarta Sans', 'Manrope', sans-serif;
}

/* ══ OUTER SHELL ══ */
.cv-shell {
    min-height: 100vh;
    display: flex !important;
    width: 100%;
    font-family: var(--cv-jk);
    background: #06080f;
}

/* ══════════════════════════════════════
   LEFT — Illustration panel
══════════════════════════════════════ */
.cv-lp {
    display: none;
    width: 50%;
    flex-shrink: 0;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
    background: #04091a;
}
@media (min-width: 900px) { .cv-lp { display: block; } }

/* SVG fills the whole left half */
.cv-lp-art {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    display: block;
}

/* gradient vignette so text stays legible */
.cv-lp-vignette {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(to bottom, rgba(4,9,26,.65) 0%, transparent 22%, transparent 68%, rgba(4,9,26,.72) 100%),
        linear-gradient(to right, transparent 60%, rgba(4,9,26,.18) 100%);
    z-index: 1;
    pointer-events: none;
}

/* content overlay (logo + tagline) */
.cv-lp-overlay {
    position: absolute;
    inset: 0;
    z-index: 2;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 40px 44px 44px;
    pointer-events: none;
}

.cv-lp-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    pointer-events: auto;
}
.cv-lp-brand-mark {
    width: 38px; height: 38px;
    border-radius: 10px; flex-shrink: 0;
    filter: brightness(0) invert(1); opacity: .9;
}
.cv-lp-brand-name {
    font-family: var(--cv-pf);
    font-size: 1.12rem; font-weight: 700;
    color: rgba(255,255,255,.92); letter-spacing: -.01em;
}

.cv-hero-caption {
    position: absolute; top: 18%; left: 3rem; right: 3rem; z-index: 10; max-width: 580px;
    padding: 0; background: none;
    -webkit-mask-image: none;
    mask-image: none;
}
.cv-caption-rule {
    width: 40px; height: 3px; border-radius: 2px; margin-bottom: 1.25rem; opacity: 0.9;
    background: linear-gradient(90deg, var(--cv-gold), #fde68a);
}
.cv-caption-headline {
    font-family: var(--cv-pf); font-size: clamp(1.75rem, 2.5vw, 2.75rem);
    font-weight: 700; line-height: 1.15; color: #ffffff;
    margin: 0 0 0.75rem 0; letter-spacing: -0.02em;
    text-shadow: 0 2px 20px rgba(0,0,0,0.4);
    max-width: 380px;
}
.cv-caption-headline em { font-style: italic; font-weight: 300; color: rgba(200,151,44,.88); text-shadow: 0 0 20px rgba(200,151,44,0.35); }
.cv-caption-sub {
    font-size: clamp(0.85rem, 1vw, 0.95rem); font-weight: 400; line-height: 1.5;
    color: rgba(255,255,255,0.55); margin: 0 0 1.5rem 0; max-width: 380px;
}
.cv-live-badge {
    display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;
    font-size: 0.8rem; font-weight: 500; color: rgba(255,255,255,0.8);
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); border-radius: 100px;
    backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
    margin-top: 1.5rem;
}
.cv-pulse-dot {
    width: 8px; height: 8px; background: #4ade80; border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(74,222,128,0.7); animation: cvPulseDot 2s infinite;
}

/* Feature List (from admin) */
.cv-caption-eyebrow {
    font-size: .62rem; font-weight: 700; letter-spacing: .2em; text-transform: uppercase;
    color: var(--cv-gold); margin-bottom: 12px;
}
.cv-lp-feats { display:flex; flex-direction:column; gap:8px; }
.cv-lp-feat { display:flex; align-items:center; gap:12px; padding:10px 13px; background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07); border-radius:12px; }
.cv-lp-feat-icon { width:30px; height:30px; border-radius:8px; background:rgba(200,151,44,.13); display:grid; place-items:center; color:var(--cv-gold); flex-shrink:0; }
.cv-lp-feat-label { font-size:.79rem; font-weight:600; color:#f1f5f9; line-height:1.3; }
.cv-lp-feat-label small { display:block; font-weight:400; font-size:.71rem; color:rgba(255,255,255,.75); margin-top:1px; }

@keyframes cvPulseDot {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74,222,128,0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(74,222,128,0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74,222,128,0); }
}

/* ============================================
   LEFT PANEL — TREES IN FOG
   ============================================ */

.left-ground-scene {
    position: absolute; bottom: 0; left: 0; right: 0; height: 40%;
    z-index: 2; pointer-events: none; overflow: hidden;
}

.fog-floor {
    position: absolute; bottom: 0; left: 0; right: 0; height: 100%;
    background: linear-gradient(to top, rgba(10,22,40,0.98) 0%, rgba(10,22,40,0.7) 35%, rgba(10,22,40,0.2) 70%, transparent 100%);
    z-index: 3;
}

.ftree { position: absolute; bottom: 0; z-index: 2; opacity: 0.85; }
.ftree::before {
    content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
    width: 6px; background: #0c1929; border-radius: 3px 3px 0 0;
}
.ftree::after {
    content: ''; position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%);
    background: radial-gradient(circle at 35% 40%, #1a3a2a 0%, #0f1f18 60%, #0a1628 100%);
    border-radius: 50%;
}

.ftree.t1 { left: 5%; }
.ftree.t1::before { height: 35px; }
.ftree.t1::after  { width: 55px; height: 55px; box-shadow: -12px 8px 0 -8px #0f1f18, 10px 5px 0 -12px #0f1f18; }

.ftree.t2 { left: 18%; opacity: 0.6; }
.ftree.t2::before { height: 50px; width: 8px; }
.ftree.t2::after  { width: 80px; height: 75px; box-shadow: -18px 10px 0 -12px #0f1f18, 15px 8px 0 -15px #0f1f18, 0 -12px 0 -5px #152b22; }

.ftree.t3 { left: 75%; opacity: 0.7; }
.ftree.t3::before { height: 40px; }
.ftree.t3::after  { width: 65px; height: 60px; box-shadow: -14px 6px 0 -10px #0f1f18, 12px 10px 0 -14px #0f1f18; }

.ftree.t4 { left: 88%; opacity: 0.5; }
.ftree.t4::before { height: 28px; width: 5px; }
.ftree.t4::after  { width: 45px; height: 45px; box-shadow: -10px 6px 0 -8px #0f1f18; }

.tree-fireflies {
    position: absolute; bottom: 0; left: 0; right: 0; height: 50%;
    z-index: 4; pointer-events: none;
}

.tfly {
    position: absolute; width: 3px; height: 3px; border-radius: 50%;
    background: radial-gradient(circle, rgba(253,230,138,0.95) 0%, transparent 70%);
    box-shadow: 0 0 10px rgba(253,230,138,0.5), 0 0 25px rgba(253,230,138,0.15);
    animation: treeFly 8s ease-in-out infinite; opacity: 0;
}

.tfly:nth-child(1) { left: 8%;  bottom: 15%; animation-delay: 0s;   animation-duration: 7s;  width: 4px; height: 4px; }
.tfly:nth-child(2) { left: 22%; bottom: 25%; animation-delay: 1.5s; animation-duration: 9s;  }
.tfly:nth-child(3) { left: 35%; bottom: 10%; animation-delay: 3s;   animation-duration: 8s;  width: 2px; height: 2px; opacity: 0.7; }
.tfly:nth-child(4) { left: 70%; bottom: 20%; animation-delay: 2s;   animation-duration: 10s; width: 5px; height: 5px; }
.tfly:nth-child(5) { left: 82%; bottom: 12%; animation-delay: 4s;   animation-duration: 7s;  }
.tfly:nth-child(6) { left: 50%; bottom: 18%; animation-delay: 5.5s; animation-duration: 9s;  width: 3px; height: 3px; }

@keyframes treeFly {
    0% { opacity: 0; transform: translateY(0) translateX(0); }
    10% { opacity: 1; }
    50% { transform: translateY(-30px) translateX(15px); opacity: 0.8; }
    90% { opacity: 0.3; }
    100% { opacity: 0; transform: translateY(-70px) translateX(-10px); }
}

/* ============================================
   RIGHT PANEL — FIREFLIES
   ============================================ */

.right-fireflies { position: absolute; inset: 0; z-index: 4; pointer-events: none; overflow: hidden; }
.rfly {
    position: absolute; width: 3px; height: 3px; border-radius: 50%;
    background: radial-gradient(circle, rgba(253,230,138,0.95) 0%, transparent 70%);
    box-shadow: 0 0 8px rgba(253,230,138,0.4), 0 0 20px rgba(253,230,138,0.15);
    animation: rflyFloat 10s ease-in-out infinite; opacity: 0;
}

.rfly:nth-child(1)  { left: 10%; top: 15%; animation-delay: 0s;   animation-duration: 9s;  width: 4px; height: 4px; }
.rfly:nth-child(2)  { left: 25%; top: 8%;  animation-delay: 2s;   animation-duration: 12s; }
.rfly:nth-child(3)  { left: 40%; top: 20%; animation-delay: 4s;   animation-duration: 8s;  width: 2px; height: 2px; }
.rfly:nth-child(4)  { left: 55%; top: 12%; animation-delay: 1s;   animation-duration: 11s; width: 5px; height: 5px; }
.rfly:nth-child(5)  { left: 70%; top: 25%; animation-delay: 3s;   animation-duration: 10s; }
.rfly:nth-child(6)  { left: 85%; top: 10%; animation-delay: 5s;   animation-duration: 13s; width: 3px; height: 3px; }
.rfly:nth-child(7)  { left: 15%; top: 40%; animation-delay: 2.5s; animation-duration: 9s;  width: 2px; height: 2px; opacity: 0.6; }
.rfly:nth-child(8)  { left: 60%; top: 45%; animation-delay: 6s;   animation-duration: 11s; width: 4px; height: 4px; }

@keyframes rflyFloat {
    0% { opacity: 0; transform: translateY(0) translateX(0) scale(0.8); }
    15% { opacity: 1; }
    50% { transform: translateY(-40px) translateX(20px) scale(1); opacity: 0.8; }
    85% { opacity: 0.4; }
    100% { opacity: 0; transform: translateY(-90px) translateX(-15px) scale(0.6); }
}

/* ══════════════════════════════════════
   RIGHT — Form panel
══════════════════════════════════════ */
.cv-rp {
    flex: 1;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 28px;
    background: linear-gradient(to bottom, #060e1c 0%, #091628 40%, #0d1e38 70%, #132440 100%);
    position: relative;
    overflow: hidden;
}

/* stars — small scattered dots */
.cv-rp::before {
    content: '';
    position: absolute; top: 0; left: 0;
    width: 1px; height: 1px; border-radius: 50%;
    background: rgba(255,255,255,.9);
    box-shadow:
        42px 22px 0 0 rgba(255,255,255,.85),
        118px 14px 0 1px rgba(255,255,255,.70),
        195px 38px 0 0 rgba(255,255,255,.60),
        76px  60px 0 0 rgba(255,255,255,.75),
        258px 28px 0 0 rgba(255,255,255,.50),
        315px 50px 0 1px rgba(255,255,255,.65),
        22px  85px 0 0 rgba(255,255,255,.55),
        150px 80px 0 0 rgba(255,255,255,.45),
        228px 70px 0 1px rgba(255,255,255,.40),
        345px 63px 0 0 rgba(255,255,255,.70),
        92px 108px 0 0 rgba(255,255,255,.35),
        175px 118px 0 0 rgba(255,255,255,.42),
        298px 100px 0 0 rgba(255,255,255,.48),
        55px 130px 0 0 rgba(255,255,255,.30),
        380px 45px 0 0 rgba(255,255,255,.55),
        430px 18px 0 1px rgba(255,255,255,.88),
        465px 68px 0 0 rgba(255,255,255,.50),
        408px 95px 0 0 rgba(255,255,255,.40);
    pointer-events: none; z-index: 0;
}

/* stars — slightly larger, includes gold accent stars */
.cv-rp::after {
    content: '';
    position: absolute; top: 0; left: 0;
    width: 2px; height: 2px; border-radius: 50%;
    background: rgba(255,255,255,.95);
    box-shadow:
        28px  16px 0 0 rgba(255,255,255,.90),
        180px  8px 0 0 rgba(255,255,255,.80),
        348px 11px 0 0 rgba(255,255,255,.88),
        460px 24px 0 0 rgba(255,255,255,.72),
        140px 68px 0 0 rgba(200,151,44,.72),
        280px 43px 0 0 rgba(200,151,44,.52);
    pointer-events: none; z-index: 0;
}

/* ambient glows */
.cv-rp-g {
    position: absolute; border-radius: 50%;
    pointer-events: none; z-index: 0;
}
.cv-rp-g--gold {
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(200,151,44,.12) 0%, rgba(200,151,44,.04) 45%, transparent 70%);
    top: -70px; right: -70px;
}
.cv-rp-g--blue {
    width: 380px; height: 180px;
    background: radial-gradient(ellipse, rgba(26,54,93,.22) 0%, rgba(26,54,93,.07) 50%, transparent 75%);
    bottom: 0; left: 0;
}

/* faint campus silhouette */
.cv-rp-skyline {
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 220px; pointer-events: none; z-index: 0;
    opacity: 1;
}

/* ── Card (frosted glass) ── */
.cv-card {
    width: 100%;
    max-width: 430px;
    background: rgba(15, 28, 46, 0.68);
    backdrop-filter: blur(22px) saturate(1.3);
    -webkit-backdrop-filter: blur(22px) saturate(1.3);
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 20px;
    padding: 40px 36px;
    box-shadow:
        0 4px 6px rgba(0,0,0,.22),
        0 12px 32px rgba(0,0,0,.40),
        0 28px 56px rgba(0,0,0,.48),
        inset 0 1px 0 rgba(255,255,255,.06);
    position: relative; z-index: 1;
    overflow: hidden;
    animation: cvUp .55s ease both;
    color: #f1f5f9;
}
/* gradient accent bar */
.cv-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #1e3a5f 0%, #2d5a87 28%, #c8972c 65%, #fde68a 100%);
    z-index: 2; pointer-events: none;
}

/* soft gradient seam on left edge of right panel */
.cv-rp-seam {
    position: absolute; top: 0; bottom: 0; left: 0;
    width: 80px;
    background: linear-gradient(to right, rgba(6,14,28,.78) 0%, transparent 100%);
    pointer-events: none; z-index: 1;
}

/* mobile brand */
.cv-mob-brand {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 36px;
}
.cv-mob-brand img { width: 30px; height: 30px; border-radius: 7px; }
.cv-mob-brand-name {
    font-family: var(--cv-pf);
    font-size: .98rem; font-weight: 700; color: #f0f4ff;
}
@media (min-width: 900px) { .cv-mob-brand { display: none; } }

/* ── Segmented control ── */
.cv-tabs {
    display: flex; gap: 0;
    position: relative;
    background: rgba(0,0,0,.30);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 100px;
    padding: 3px;
    margin-bottom: 28px;
}
.cv-tab {
    flex: 1; padding: 8px 14px;
    border: none; border-radius: 100px;
    font-family: var(--cv-jk);
    font-size: .84rem; font-weight: 700;
    cursor: pointer; background: transparent;
    color: rgba(255,255,255,.45);
    position: relative; z-index: 1;
    transition: color .22s;
    letter-spacing: 0; white-space: nowrap;
    word-spacing: normal;
}
.cv-tab--active { color: #ffffff; }
/* sliding highlight indicator */
.cv-tab-indicator {
    position: absolute; top: 3px; left: 3px;
    width: calc(50% - 3px); height: calc(100% - 6px);
    background: rgba(26,54,93,.90);
    border-radius: 100px;
    border: 1px solid rgba(255,255,255,.10);
    box-shadow: 0 2px 10px rgba(0,0,0,.35);
    transition: transform .32s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none; z-index: 0;
}
.cv-tab-indicator--register { transform: translateX(100%); }

/* ── Flash alerts ── */
.cv-alert {
    border-radius: 10px;
    padding: 11px 14px;
    font-size: .83rem; font-weight: 500;
    margin-bottom: 22px;
    display: flex; align-items: flex-start; gap: 9px;
    line-height: 1.5;
}
.cv-alert--error   { background: rgba(239,68,68,.12); border-left: 4px solid #f87171; color: #fca5a5; }
.cv-alert--success { background: rgba(34,197,94,.12); border-left: 4px solid #4ade80; color: #86efac; }

/* ── Form head ── */
.cv-head { margin-bottom: 26px; }
.cv-eyebrow {
    font-size: .65rem; font-weight: 700;
    letter-spacing: .2em; text-transform: uppercase;
    color: var(--cv-gold); margin-bottom: 8px;
}
.cv-head h2 {
    font-family: var(--cv-pf);
    font-size: 1.85rem; font-weight: 700;
    color: #ffffff; line-height: 1.15;
    letter-spacing: -.025em; margin: 0 0 8px;
}
.cv-head p {
    font-size: .85rem; color: rgba(255,255,255,.52);
    line-height: 1.65; margin: 0;
    word-spacing: normal; letter-spacing: normal;
}

/* ── Fields ── */
.cv-form { display: grid; gap: 18px; }

.cv-form label {
    display: block; font-size: .77rem; font-weight: 600;
    color: rgba(255,255,255,.68); letter-spacing: .02em; margin-bottom: 6px;
}

.cv-form input[type="email"],
.cv-form input[type="password"],
.cv-form input[type="text"],
.cv-form input[type="tel"] {
    width: 100%; padding: 11px 14px;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 8px;
    font-family: var(--cv-jk);
    font-size: .875rem; color: #f1f5f9;
    background: rgba(0,0,0,.22);
    outline: none; appearance: none;
    transition: border-color .18s, box-shadow .18s, background .18s;
}
.cv-form input::placeholder { color: rgba(255,255,255,.28); }
.cv-form input:focus {
    border-color: var(--cv-gold);
    background: rgba(0,0,0,.30);
    box-shadow: 0 0 0 3px rgba(200,151,44,.18), 0 0 18px rgba(200,151,44,.07);
}
.cv-form input.is-invalid {
    border-color: #f87171 !important;
    box-shadow: 0 0 0 3px rgba(248,113,113,.15);
}
.cv-form input:disabled,
.cv-form input[readonly] { background: rgba(0,0,0,.18); color: rgba(255,255,255,.28); cursor: not-allowed; }

.cv-err {
    display: none; font-size: .74rem; font-weight: 500;
    color: #fca5a5; margin-top: 5px; line-height: 1.4;
}
.cv-err.show { display: block; }

.cv-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

/* recaptcha */
.cv-captcha { display: flex; justify-content: center; }

/* ── Primary CTA ── */
.cv-btn {
    width: 100%; padding: 13px 22px;
    background: linear-gradient(120deg, #1a365d 0%, #2a5298 55%, #c8972c 100%);
    color: #fff;
    font-family: var(--cv-jk); font-size: .9rem; font-weight: 700;
    letter-spacing: .03em; border: none; border-radius: 10px;
    cursor: pointer; display: flex; align-items: center;
    justify-content: center; gap: 8px;
    transition: transform .15s, box-shadow .18s, filter .18s;
    position: relative; overflow: hidden;
}
.cv-btn::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(120deg, #0f2042 0%, #1e3f7a 55%, #a87524 100%);
    opacity: 0; transition: opacity .22s;
}
.cv-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 28px rgba(26,54,93,.35); }
.cv-btn:hover::after { opacity: 1; }
.cv-btn:active { transform: none; box-shadow: none; }
.cv-btn:disabled { opacity: .45; cursor: not-allowed; transform: none; box-shadow: none; }
.cv-btn.is-loading { opacity: .75; pointer-events: none; }
.cv-btn.is-loading::before {
    content: ''; width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,.3); border-top-color: #fff;
    border-radius: 50%; animation: cvSpin .6s linear infinite; flex-shrink: 0;
}

/* ── OTP send button ── */
.cv-btn-otp {
    width: 100%; padding: 11px 18px;
    border: 1.5px solid rgba(200,151,44,.40);
    border-radius: 10px;
    background: rgba(200,151,44,.10);
    color: rgba(253,230,138,.82);
    font-family: var(--cv-jk); font-size: .875rem; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    transition: background .18s, color .18s, box-shadow .18s, border-color .18s;
}
.cv-btn-otp:disabled { opacity: .38; cursor: not-allowed; }
.cv-btn-otp.ready { background: var(--cv-gold); border-color: var(--cv-gold); color: #fff; }
.cv-btn-otp.ready:hover { background: #b07a24; box-shadow: 0 4px 14px rgba(200,151,44,.28); }

.cv-otp-reveal { animation: cvFadeUp .22s ease both; }
.cv-otp-txt { display: block; font-size: .76rem; margin-top: 5px; line-height: 1.4; }

/* footer links */
.cv-foot-row { display: flex; justify-content: center; margin-top: 8px; }
.cv-link {
    font-size: .82rem; color: rgba(255,255,255,.70);
    text-decoration: none; font-weight: 500;
    transition: color .14s;
}
.cv-link:hover { color: var(--cv-gold); }

/* pane animation */
.auth-panes-host { position: relative; }
.auth-pane { transition: opacity .18s ease, visibility .18s ease; }
.auth-pane--inactive {
    opacity: 0; visibility: hidden; pointer-events: none;
    position: absolute; top: 0; left: 0; right: 0; overflow: hidden;
}

/* ── Mobile layout ── */
@media (max-width: 899px) {
    .cv-shell { flex-direction: column !important; }
    .cv-rp { min-height: 100vh; padding: 40px 20px 60px; }
    .cv-card { max-width: 480px; padding: 32px 22px; border-radius: 20px; }
    .cv-hero-caption { left: 1.5rem; top: 12%; max-width: 280px; padding: 0; }
    .cv-caption-headline { font-size: 1.5rem; }
    .cv-caption-sub { font-size: 0.8rem; }
}

/* ── Keyframes ── */
@keyframes cvUp    { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }
@keyframes cvFadeUp{ from { opacity:0; transform: translateY(6px);  } to { opacity:1; transform: translateY(0); } }
@keyframes cvSpin  { to { transform: rotate(360deg); } }
@keyframes cvPulse { 0%,100% { box-shadow: 0 0 8px rgba(74,222,128,.6); } 50% { box-shadow: 0 0 3px rgba(74,222,128,.25); } }

</style>


<div class="cv-shell">

<!-- ══════════════════════════════════
     LEFT — Campus illustration
══════════════════════════════════════ -->
<div class="cv-lp" aria-hidden="true">

    <!-- Campus SVG illustration -->
    <svg class="cv-lp-art" viewBox="0 0 560 600" preserveAspectRatio="xMidYMax slice"
         xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <linearGradient id="cvSky" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%"   stop-color="#02060d"/>
                <stop offset="55%"  stop-color="#091530"/>
                <stop offset="100%" stop-color="#102248"/>
            </linearGradient>
            <radialGradient id="cvHalo" cx="50%" cy="46%" r="32%">
                <stop offset="0%"  stop-color="#c8972c" stop-opacity=".50"/>
                <stop offset="50%" stop-color="#c8972c" stop-opacity=".14"/>
                <stop offset="100%" stop-color="#c8972c" stop-opacity="0"/>
            </radialGradient>
            <radialGradient id="cvGround" cx="50%" cy="0%" r="80%">
                <stop offset="0%"   stop-color="#0d2040"/>
                <stop offset="100%" stop-color="#060d1c"/>
            </radialGradient>
            <radialGradient id="cvWin" cx="50%" cy="50%" r="50%">
                <stop offset="0%"  stop-color="#c8972c" stop-opacity=".85"/>
                <stop offset="100%" stop-color="#c8972c" stop-opacity=".18"/>
            </radialGradient>
        </defs>

        <!-- Sky -->
        <rect width="560" height="600" fill="url(#cvSky)"/>

        <!-- Stars -->
        <circle cx="42"  cy="26"  r="1.4" fill="#fff" opacity=".82"/>
        <circle cx="118" cy="15"  r="1"   fill="#fff" opacity=".65"/>
        <circle cx="190" cy="42"  r="1.4" fill="#fff" opacity=".54"/>
        <circle cx="80"  cy="70"  r="1"   fill="#fff" opacity=".72"/>
        <circle cx="258" cy="31"  r="1.2" fill="#fff" opacity=".6"/>
        <circle cx="325" cy="50"  r="1"   fill="#fff" opacity=".5"/>
        <circle cx="400" cy="19"  r="1.5" fill="#fff" opacity=".88"/>
        <circle cx="450" cy="52"  r="1"   fill="#fff" opacity=".56"/>
        <circle cx="512" cy="34"  r="1.8" fill="#fff" opacity=".65"/>
        <circle cx="540" cy="78"  r="1"   fill="#fff" opacity=".78"/>
        <circle cx="160" cy="90"  r="1.2" fill="#fff" opacity=".52"/>
        <circle cx="62"  cy="125" r="1"   fill="#fff" opacity=".62"/>
        <circle cx="455" cy="98"  r="1.4" fill="#fff" opacity=".7"/>
        <circle cx="508" cy="122" r="1"   fill="#fff" opacity=".5"/>
        <circle cx="236" cy="72"  r="1"   fill="#fff" opacity=".58"/>
        <circle cx="376" cy="64"  r="1.4" fill="#fff" opacity=".46"/>
        <circle cx="138" cy="52"  r="1"   fill="#fff" opacity=".5"/>
        <circle cx="480" cy="60"  r="1.4" fill="#fff" opacity=".72"/>
        <circle cx="22"  cy="92"  r="1"   fill="#fff" opacity=".6"/>
        <circle cx="310" cy="22"  r="1"   fill="#fff" opacity=".45"/>
        <!-- Gold accent stars near tower -->
        <circle cx="237" cy="146" r="2"   fill="#c8972c" opacity=".65"/>
        <circle cx="323" cy="134" r="1.5" fill="#c8972c" opacity=".50"/>
        <circle cx="280" cy="108" r="1.2" fill="#c8972c" opacity=".38"/>

        <!-- Large gold halo — behind tower -->
        <ellipse cx="280" cy="278" rx="148" ry="132" fill="url(#cvHalo)"/>

        <!-- ── FAR BACKGROUND BUILDINGS ── -->
        <!-- Far left -->
        <rect x="8"   y="390" width="72" height="30" rx="1" fill="#0b1928" opacity=".7"/>
        <rect x="8"   y="384" width="72" height="8"  rx="1" fill="#0d1f32" opacity=".7"/>
        <rect x="17"  y="397" width="10" height="12" rx="1" fill="url(#cvWin)" opacity=".18"/>
        <rect x="34"  y="397" width="10" height="12" rx="1" fill="url(#cvWin)" opacity=".14"/>
        <rect x="51"  y="397" width="10" height="12" rx="1" fill="url(#cvWin)" opacity=".11"/>
        <!-- Far right -->
        <rect x="480" y="386" width="72" height="34" rx="1" fill="#0b1928" opacity=".7"/>
        <rect x="480" y="380" width="72" height="8"  rx="1" fill="#0d1f32" opacity=".7"/>
        <rect x="489" y="393" width="10" height="13" rx="1" fill="url(#cvWin)" opacity=".18"/>
        <rect x="506" y="393" width="10" height="13" rx="1" fill="url(#cvWin)" opacity=".14"/>
        <rect x="523" y="393" width="10" height="13" rx="1" fill="url(#cvWin)" opacity=".11"/>

        <!-- ── LEFT LIBRARY (neoclassical) ── -->
        <rect x="92"  y="348" width="130" height="72" rx="2" fill="#0c1b30"/>
        <!-- Pediment -->
        <polygon points="92,348 157,320 222,348" fill="#0e2040"/>
        <!-- Frieze -->
        <rect x="92" y="345" width="130" height="5" rx="1" fill="#122540"/>
        <!-- Columns -->
        <rect x="105" y="348" width="5" height="72" fill="#0d1c32"/>
        <rect x="119" y="348" width="5" height="72" fill="#0d1c32"/>
        <rect x="133" y="348" width="5" height="72" fill="#0d1c32"/>
        <rect x="147" y="348" width="5" height="72" fill="#0d1c32"/>
        <rect x="161" y="348" width="5" height="72" fill="#0d1c32"/>
        <rect x="175" y="348" width="5" height="72" fill="#0d1c32"/>
        <rect x="189" y="348" width="5" height="72" fill="#0d1c32"/>
        <rect x="203" y="348" width="5" height="72" fill="#0d1c32"/>
        <!-- Arched windows -->
        <path d="M112,368 L112,355 Q120,347 128,355 L128,368 Z" fill="url(#cvWin)" opacity=".28"/>
        <path d="M136,368 L136,355 Q144,347 152,355 L152,368 Z" fill="url(#cvWin)" opacity=".22"/>
        <path d="M160,368 L160,355 Q168,347 176,355 L176,368 Z" fill="url(#cvWin)" opacity=".30"/>
        <path d="M184,368 L184,355 Q192,347 200,355 L200,368 Z" fill="url(#cvWin)" opacity=".20"/>
        <!-- Steps -->
        <rect x="128" y="418" width="64" height="4"  rx="1" fill="#0e2040"/>
        <rect x="122" y="422" width="76" height="4"  rx="1" fill="#0c1c34"/>

        <!-- ── CLOCK TOWER (focal point) ── -->
        <!-- Base -->
        <rect x="242" y="305" width="76" height="115" rx="2" fill="#0d1f3c"/>
        <rect x="235" y="302" width="90" height="6"   rx="2" fill="#132540"/>
        <!-- Arched entrance -->
        <path d="M262,420 L262,394 Q280,376 298,394 L298,420 Z" fill="#050c1c"/>
        <!-- Window flanking door -->
        <path d="M249,370 L249,356 Q256,349 263,356 L263,370 Z" fill="url(#cvWin)" opacity=".24"/>
        <path d="M297,370 L297,356 Q304,349 311,356 L311,370 Z" fill="url(#cvWin)" opacity=".24"/>
        <!-- Middle shaft -->
        <rect x="258" y="220" width="44" height="90" rx="2" fill="#0e2245"/>
        <rect x="252" y="218" width="56" height="6"  rx="2" fill="#132540"/>
        <!-- Clock face -->
        <circle cx="280" cy="250" r="18" fill="none" stroke="#c8972c" stroke-width="2.5" opacity=".92"/>
        <circle cx="280" cy="250" r="14" fill="#04091a" opacity=".88"/>
        <!-- Hour markers -->
        <line x1="280" y1="235" x2="280" y2="238" stroke="#c8972c" stroke-width="1.5" opacity=".7"/>
        <line x1="280" y1="262" x2="280" y2="265" stroke="#c8972c" stroke-width="1.5" opacity=".7"/>
        <line x1="265" y1="250" x2="268" y2="250" stroke="#c8972c" stroke-width="1.5" opacity=".7"/>
        <line x1="292" y1="250" x2="295" y2="250" stroke="#c8972c" stroke-width="1.5" opacity=".7"/>
        <!-- Hands -->
        <line x1="280" y1="250" x2="280" y2="238" stroke="#c8972c" stroke-width="2"   stroke-linecap="round"/>
        <line x1="280" y1="250" x2="290" y2="255" stroke="#c8972c" stroke-width="2"   stroke-linecap="round"/>
        <circle cx="280" cy="250" r="2.5" fill="#c8972c"/>
        <!-- Window below clock -->
        <rect x="270" y="272" width="20" height="26" rx="10" fill="url(#cvWin)" opacity=".32"/>
        <!-- Belfry -->
        <rect x="264" y="182" width="32" height="40" rx="2" fill="#10264a"/>
        <rect x="258" y="180" width="44" height="5"  rx="2" fill="#132540"/>
        <!-- Belfry arch -->
        <path d="M270,215 L270,200 Q280,192 290,200 L290,215 Z" fill="#04091a" opacity=".85"/>
        <!-- Spire -->
        <polygon points="280,140 263,183 297,183" fill="#132540"/>
        <!-- Lightning rod -->
        <line x1="280" y1="122" x2="280" y2="143" stroke="#c8972c" stroke-width="2.2" stroke-linecap="round"/>
        <polygon points="280,120 275,143 285,143" fill="#c8972c" opacity=".88"/>

        <!-- ── RIGHT ACADEMIC BUILDING ── -->
        <rect x="338" y="338" width="130" height="82" rx="2" fill="#0c1b30"/>
        <!-- Parapet -->
        <rect x="338" y="330" width="130" height="10" rx="1" fill="#102040"/>
        <!-- Rooftop details -->
        <rect x="348" y="324" width="18" height="8" rx="1" fill="#132040"/>
        <rect x="374" y="324" width="18" height="8" rx="1" fill="#132040"/>
        <rect x="400" y="324" width="18" height="8" rx="1" fill="#132040"/>
        <!-- Large glass facade windows -->
        <rect x="348" y="352" width="24" height="36" rx="3" fill="url(#cvWin)" opacity=".22"/>
        <rect x="380" y="352" width="24" height="36" rx="3" fill="url(#cvWin)" opacity=".28"/>
        <rect x="412" y="352" width="24" height="36" rx="3" fill="url(#cvWin)" opacity=".18"/>
        <rect x="444" y="352" width="16" height="36" rx="3" fill="url(#cvWin)" opacity=".14"/>
        <!-- Lower windows -->
        <rect x="350" y="397" width="16" height="15" rx="2" fill="url(#cvWin)" opacity=".14"/>
        <rect x="374" y="397" width="16" height="15" rx="2" fill="url(#cvWin)" opacity=".12"/>
        <rect x="398" y="397" width="16" height="15" rx="2" fill="url(#cvWin)" opacity=".10"/>
        <rect x="422" y="397" width="16" height="15" rx="2" fill="url(#cvWin)" opacity=".14"/>
        <!-- Entry door -->
        <rect x="386" y="396" width="26" height="24" rx="3" fill="url(#cvWin)" opacity=".28"/>

        <!-- ── TREES ── -->
        <!-- Far left -->
        <rect x="62"  y="390" width="6" height="28" rx="3" fill="#09182a"/>
        <ellipse cx="65"  cy="377" rx="23" ry="27" fill="#071b08" opacity=".88"/>
        <ellipse cx="65"  cy="368" rx="14" ry="18" fill="#092410" opacity=".78"/>
        <!-- Near left -->
        <rect x="173" y="383" width="6" height="35" rx="3" fill="#09182a"/>
        <ellipse cx="176" cy="369" rx="21" ry="26" fill="#071b08" opacity=".88"/>
        <ellipse cx="176" cy="360" rx="13" ry="17" fill="#092410" opacity=".78"/>
        <!-- Near right -->
        <rect x="381" y="385" width="6" height="33" rx="3" fill="#09182a"/>
        <ellipse cx="384" cy="372" rx="21" ry="25" fill="#071b08" opacity=".88"/>
        <ellipse cx="384" cy="363" rx="13" ry="16" fill="#092410" opacity=".78"/>
        <!-- Far right -->
        <rect x="489" y="388" width="6" height="28" rx="3" fill="#09182a"/>
        <ellipse cx="492" cy="376" rx="23" ry="27" fill="#071b08" opacity=".88"/>
        <ellipse cx="492" cy="367" rx="14" ry="17" fill="#092410" opacity=".78"/>

        <!-- ── GROUND ── -->
        <path d="M0 418 Q140 408 280 411 Q420 414 560 418 L560 600 L0 600 Z" fill="url(#cvGround)"/>
        <!-- Walkway -->
        <path d="M250 418 L238 475 L228 600 L332 600 L322 475 L310 418 Z" fill="#112244" opacity=".55"/>
        <line x1="250" y1="418" x2="228" y2="600" stroke="#c8972c" stroke-width=".6" opacity=".14"/>
        <line x1="310" y1="418" x2="332" y2="600" stroke="#c8972c" stroke-width=".6" opacity=".14"/>

        <!-- ── STUDENT SILHOUETTES ── -->
        <!-- Left student -->
        <circle cx="196" cy="405" r="8.5"  fill="#0e2244" opacity=".9"/>
        <path d="M187,413 Q187,434 196,434 Q205,434 205,413" fill="#0e2244" opacity=".9"/>
        <!-- Centre student (slightly taller) -->
        <circle cx="280" cy="401" r="9.5"  fill="#132b4e" opacity=".95"/>
        <path d="M270,410 Q270,434 280,434 Q290,434 290,410" fill="#132b4e" opacity=".95"/>
        <!-- Right student -->
        <circle cx="364" cy="405" r="8.5"  fill="#0e2244" opacity=".9"/>
        <path d="M355,413 Q355,434 364,434 Q373,434 373,413" fill="#0e2244" opacity=".9"/>

        <!-- ── SPEECH BUBBLES (student voices) ── -->
        <!-- Bubble from left student -->
        <rect x="150" y="370" width="48" height="26" rx="8"  fill="#1a365d" opacity=".80"/>
        <polygon points="168,396 175,396 171,406"            fill="#1a365d" opacity=".80"/>
        <circle cx="162" cy="383" r="3"   fill="#c8972c" opacity=".78"/>
        <circle cx="174" cy="383" r="3"   fill="#c8972c" opacity=".78"/>
        <circle cx="186" cy="383" r="3"   fill="#c8972c" opacity=".78"/>
        <!-- Bubble from centre student -->
        <rect x="292" y="353" width="58" height="34" rx="9"  fill="#1a365d" opacity=".88"/>
        <polygon points="306,387 314,387 310,398"            fill="#1a365d" opacity=".88"/>
        <circle cx="308" cy="370" r="3.5" fill="#c8972c" opacity=".85"/>
        <circle cx="321" cy="370" r="3.5" fill="#c8972c" opacity=".85"/>
        <circle cx="334" cy="370" r="3.5" fill="#c8972c" opacity=".85"/>
        <!-- Smaller floating bubble -->
        <rect x="82"  y="312" width="38" height="22" rx="7"  fill="#1a365d" opacity=".45"/>
        <polygon points="92,334 99,334 95,343"               fill="#1a365d" opacity=".45"/>
        <circle cx="92"  cy="323" r="2.5" fill="#c8972c" opacity=".55"/>
        <circle cx="101" cy="323" r="2.5" fill="#c8972c" opacity=".55"/>
        <circle cx="110" cy="323" r="2.5" fill="#c8972c" opacity=".55"/>
        <!-- Gold highlight bubble -->
        <rect x="336" y="308" width="44" height="24" rx="7"  fill="#c8972c" opacity=".18"/>
        <polygon points="348,332 355,332 351,341"            fill="#c8972c" opacity=".18"/>

        <!-- ── DECORATIVE ELEMENTS ── -->
        <!-- Dashed arc connecting buildings — community -->
        <path d="M157,340 Q280,286 403,332" stroke="#c8972c" stroke-width="1" fill="none"
              opacity=".16" stroke-dasharray="5 5"/>

        <!-- Book icon -->
        <rect x="99"  y="198" width="30" height="24" rx="2" fill="#1a365d" opacity=".55"/>
        <line x1="114" y1="198" x2="114" y2="222" stroke="#c8972c" stroke-width="1.2" opacity=".65"/>
        <line x1="103" y1="207" x2="112" y2="207" stroke="#c8972c" stroke-width=".9"  opacity=".55"/>
        <line x1="103" y1="213" x2="112" y2="213" stroke="#c8972c" stroke-width=".9"  opacity=".55"/>

        <!-- Graduation cap -->
        <rect x="432" y="192" width="30" height="5" rx="2" fill="#c8972c" opacity=".44"/>
        <polygon points="447,192 436,183 458,183" fill="#c8972c" opacity=".44"/>
        <line x1="456" y1="192" x2="459" y2="204" stroke="#c8972c" stroke-width="1.5" stroke-linecap="round" opacity=".44"/>
        <circle cx="459" cy="206" r="2.2" fill="#c8972c" opacity=".44"/>

        <!-- Sparkles -->
        <path d="M52,178 L54,172 L56,178 L62,180 L56,182 L54,188 L52,182 L46,180 Z"
              fill="#c8972c" opacity=".28"/>
        <path d="M508,205 L510,200 L512,205 L518,207 L512,209 L510,214 L508,209 L502,207 Z"
              fill="#c8972c" opacity=".22"/>
        <path d="M422,148 L424,143 L426,148 L431,150 L426,152 L424,157 L422,152 L417,150 Z"
              fill="#c8972c" opacity=".38"/>
    </svg>

    <!-- Left panel atmospheric ground scene -->
    <div class="left-ground-scene" aria-hidden="true">
        <div class="fog-trees">
            <div class="ftree t1"></div>
            <div class="ftree t2"></div>
            <div class="ftree t3"></div>
            <div class="ftree t4"></div>
        </div>
        <div class="fog-floor"></div>
        <div class="tree-fireflies">
            <div class="tfly"></div>
            <div class="tfly"></div>
            <div class="tfly"></div>
            <div class="tfly"></div>
            <div class="tfly"></div>
            <div class="tfly"></div>
        </div>
    </div>

    <!-- Vignette -->
    <div class="cv-lp-vignette"></div>

    <!-- Overlay content -->
    <div class="cv-lp-overlay">
        <div class="cv-lp-brand">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="" class="cv-lp-brand-mark">
            <span class="cv-lp-brand-name">CampusVoice</span>
        </div>
        
        <!-- Left Panel Hero Caption -->
        <div class="cv-hero-caption">
            <div class="cv-caption-rule"></div>
            <div class="cv-caption-eyebrow">Control Panel</div>
            <h1 class="cv-caption-headline">
                One dashboard<br>
                for every <em>voice</em>.
            </h1>
            <p class="cv-caption-sub">
                Review feedback, publish announcements, and keep your campus community running smoothly.
            </p>
            
            <div class="cv-lp-feats">
                <div class="cv-lp-feat">
                    <span class="cv-lp-feat-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
                    <span class="cv-lp-feat-label">Feedback Management<small>Review, reply, and resolve submissions</small></span>
                </div>
                <div class="cv-lp-feat">
                    <span class="cv-lp-feat-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg></span>
                    <span class="cv-lp-feat-label">Announcements<small>Publish and pin campus notices</small></span>
                </div>
                <div class="cv-lp-feat">
                    <span class="cv-lp-feat-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                    <span class="cv-lp-feat-label">Student Oversight<small>Manage roles, accounts, and activity</small></span>
                </div>
            </div>
        </div>
        
        <!-- Bottom left badge -->
        <div>
            <div class="cv-live-badge">
                <span class="cv-pulse-dot"></span>
                Portal is live
            </div>
        </div>
    </div>
</div><!-- /.cv-lp -->

<!-- ══════════════════════════════════
     RIGHT — Form panel
══════════════════════════════════════ -->
<div class="cv-rp">
    <div class="cv-rp-g cv-rp-g--gold"></div>
    <div class="cv-rp-g cv-rp-g--blue"></div>
    <div class="cv-rp-seam" aria-hidden="true"></div>

    <!-- Right panel fireflies -->
    <div class="right-fireflies" aria-hidden="true">
        <div class="rfly"></div>
        <div class="rfly"></div>
        <div class="rfly"></div>
        <div class="rfly"></div>
        <div class="rfly"></div>
        <div class="rfly"></div>
        <div class="rfly"></div>
        <div class="rfly"></div>
    </div>

    <!-- Night campus skyline silhouette -->
    <svg class="cv-rp-skyline" viewBox="0 0 800 220" preserveAspectRatio="xMidYMax meet"
         xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <radialGradient id="rpWin" cx="50%" cy="30%" r="65%">
                <stop offset="0%"  stop-color="#fde68a" stop-opacity=".95"/>
                <stop offset="100%" stop-color="#f59e0b" stop-opacity=".15"/>
            </radialGradient>
            <linearGradient id="rpGnd" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#0c1a2e"/>
                <stop offset="100%" stop-color="#040810"/>
            </linearGradient>
        </defs>

        <!-- Ground -->
        <rect x="0" y="195" width="800" height="25" fill="url(#rpGnd)"/>

        <!-- Far background buildings -->
        <rect x="14"  y="158" width="48" height="37" fill="#030810"/>
        <rect x="20"  y="153" width="28" height="10" fill="#030810"/>
        <rect x="738" y="155" width="52" height="40" fill="#030810"/>
        <rect x="745" y="150" width="30" height="10" fill="#030810"/>

        <!-- LEFT LIBRARY (neoclassical) -->
        <rect x="70" y="130" width="148" height="65" fill="#040c1e"/>
        <polygon points="70,130 144,102 218,130" fill="#040c1e"/>
        <!-- Columns -->
        <rect x="83"  y="130" width="5" height="65" fill="#030810"/>
        <rect x="97"  y="130" width="5" height="65" fill="#030810"/>
        <rect x="111" y="130" width="5" height="65" fill="#030810"/>
        <rect x="125" y="130" width="5" height="65" fill="#030810"/>
        <rect x="139" y="130" width="5" height="65" fill="#030810"/>
        <rect x="153" y="130" width="5" height="65" fill="#030810"/>
        <rect x="167" y="130" width="5" height="65" fill="#030810"/>
        <rect x="181" y="130" width="5" height="65" fill="#030810"/>
        <!-- Arched windows (lit) -->
        <path d="M88,152 L88,140 Q96,132 104,140 L104,152 Z"   fill="url(#rpWin)" opacity=".65"/>
        <path d="M112,152 L112,140 Q120,132 128,140 L128,152 Z" fill="url(#rpWin)" opacity=".50"/>
        <path d="M136,152 L136,140 Q144,132 152,140 L152,152 Z" fill="url(#rpWin)" opacity=".72"/>
        <path d="M160,152 L160,140 Q168,132 176,140 L176,152 Z" fill="url(#rpWin)" opacity=".45"/>
        <!-- Lower windows -->
        <rect x="90"  y="163" width="12" height="14" rx="2" fill="url(#rpWin)" opacity=".42"/>
        <rect x="114" y="163" width="12" height="14" rx="2" fill="url(#rpWin)" opacity=".30"/>
        <rect x="138" y="163" width="12" height="14" rx="2" fill="url(#rpWin)" opacity=".50"/>
        <rect x="162" y="163" width="12" height="14" rx="2" fill="url(#rpWin)" opacity=".36"/>
        <!-- Steps -->
        <rect x="120" y="193" width="68" height="3" fill="#04090f"/>
        <rect x="114" y="195" width="80" height="2" fill="#03070e"/>

        <!-- Mid-left building -->
        <rect x="228" y="150" width="58" height="45" fill="#030c1c"/>
        <rect x="234" y="158" width="10" height="13" rx="2" fill="url(#rpWin)" opacity=".58"/>
        <rect x="250" y="158" width="10" height="13" rx="2" fill="url(#rpWin)" opacity=".38"/>
        <rect x="266" y="158" width="10" height="13" rx="2" fill="url(#rpWin)" opacity=".65"/>
        <rect x="234" y="175" width="10" height="10" rx="2" fill="url(#rpWin)" opacity=".30"/>
        <rect x="266" y="175" width="10" height="10" rx="2" fill="url(#rpWin)" opacity=".44"/>

        <!-- CLOCK TOWER (centred at x=400) -->
        <rect x="362" y="118" width="76" height="77" fill="#040c1e"/>
        <rect x="354" y="114" width="92" height="7"  fill="#030a18"/>
        <!-- Arched door -->
        <path d="M385,195 L385,178 Q400,164 415,178 L415,195 Z" fill="#020608"/>
        <!-- Door-flanking windows -->
        <rect x="365" y="136" width="14" height="20" rx="7" fill="url(#rpWin)" opacity=".58"/>
        <rect x="421" y="136" width="14" height="20" rx="7" fill="url(#rpWin)" opacity=".58"/>
        <!-- Mid shaft -->
        <rect x="378" y="58" width="44" height="62" fill="#050e1e"/>
        <rect x="372" y="54" width="56" height="7"  fill="#030a18"/>
        <!-- Clock face -->
        <circle cx="400" cy="88" r="18" fill="none" stroke="#c8972c" stroke-width="2" opacity=".78"/>
        <circle cx="400" cy="88" r="13" fill="#030812" opacity=".9"/>
        <line x1="400" y1="88" x2="400" y2="77" stroke="#c8972c" stroke-width="1.8" stroke-linecap="round" opacity=".88"/>
        <line x1="400" y1="88" x2="409" y2="93" stroke="#c8972c" stroke-width="1.8" stroke-linecap="round" opacity=".88"/>
        <circle cx="400" cy="88" r="2.5" fill="#c8972c" opacity=".95"/>
        <line x1="400" y1="73" x2="400" y2="76" stroke="#c8972c" stroke-width="1.4" opacity=".65"/>
        <line x1="400" y1="100" x2="400" y2="103" stroke="#c8972c" stroke-width="1.4" opacity=".65"/>
        <line x1="384" y1="88" x2="387" y2="88" stroke="#c8972c" stroke-width="1.4" opacity=".65"/>
        <line x1="413" y1="88" x2="416" y2="88" stroke="#c8972c" stroke-width="1.4" opacity=".65"/>
        <!-- Window below clock -->
        <rect x="391" y="110" width="18" height="26" rx="9" fill="url(#rpWin)" opacity=".52"/>
        <!-- Belfry -->
        <rect x="382" y="26" width="36" height="34" fill="#050d1c"/>
        <rect x="376" y="23" width="48" height="6"  fill="#030a18"/>
        <path d="M390,58 L390,44 Q400,36 410,44 L410,58 Z" fill="#020608"/>
        <!-- Spire -->
        <polygon points="400,2 383,26 417,26" fill="#040b1a"/>
        <line x1="400" y1="0" x2="400" y2="4" stroke="#c8972c" stroke-width="2" stroke-linecap="round" opacity=".82"/>

        <!-- Mid-right building -->
        <rect x="516" y="148" width="60" height="47" fill="#030c1c"/>
        <rect x="522" y="156" width="10" height="13" rx="2" fill="url(#rpWin)" opacity=".62"/>
        <rect x="538" y="156" width="10" height="13" rx="2" fill="url(#rpWin)" opacity=".40"/>
        <rect x="554" y="156" width="10" height="13" rx="2" fill="url(#rpWin)" opacity=".55"/>
        <rect x="522" y="173" width="10" height="11" rx="2" fill="url(#rpWin)" opacity=".35"/>
        <rect x="554" y="173" width="10" height="11" rx="2" fill="url(#rpWin)" opacity=".42"/>

        <!-- RIGHT ACADEMIC BUILDING -->
        <rect x="582" y="120" width="148" height="75" fill="#040c1e"/>
        <rect x="582" y="112" width="148" height="10" fill="#030a18"/>
        <rect x="592" y="106" width="18" height="8" fill="#030810"/>
        <rect x="620" y="106" width="18" height="8" fill="#030810"/>
        <rect x="648" y="106" width="18" height="8" fill="#030810"/>
        <!-- Large windows -->
        <rect x="593" y="132" width="22" height="32" rx="3" fill="url(#rpWin)" opacity=".60"/>
        <rect x="624" y="132" width="22" height="32" rx="3" fill="url(#rpWin)" opacity=".70"/>
        <rect x="655" y="132" width="22" height="32" rx="3" fill="url(#rpWin)" opacity=".50"/>
        <rect x="686" y="132" width="16" height="32" rx="3" fill="url(#rpWin)" opacity=".42"/>
        <!-- Lower windows -->
        <rect x="594" y="170" width="14" height="13" rx="2" fill="url(#rpWin)" opacity=".36"/>
        <rect x="617" y="170" width="14" height="13" rx="2" fill="url(#rpWin)" opacity=".44"/>
        <rect x="640" y="170" width="14" height="13" rx="2" fill="url(#rpWin)" opacity=".30"/>
        <rect x="663" y="170" width="14" height="13" rx="2" fill="url(#rpWin)" opacity=".40"/>
        <!-- Entry door -->
        <rect x="630" y="172" width="24" height="23" rx="3" fill="url(#rpWin)" opacity=".58"/>

        <!-- TREES -->
        <rect x="44"  y="158" width="5" height="38" rx="2" fill="#030810"/>
        <ellipse cx="47"  cy="147" rx="19" ry="22" fill="#020c04"/>
        <ellipse cx="47"  cy="140" rx="11" ry="14" fill="#031005"/>
        <rect x="296" y="162" width="5" height="34" rx="2" fill="#030810"/>
        <ellipse cx="299" cy="151" rx="18" ry="21" fill="#020c04"/>
        <ellipse cx="299" cy="144" rx="10" ry="13" fill="#031005"/>
        <rect x="486" y="160" width="5" height="35" rx="2" fill="#030810"/>
        <ellipse cx="489" cy="149" rx="19" ry="22" fill="#020c04"/>
        <ellipse cx="489" cy="142" rx="11" ry="13" fill="#031005"/>
        <rect x="756" y="158" width="5" height="36" rx="2" fill="#030810"/>
        <ellipse cx="759" cy="147" rx="19" ry="22" fill="#020c04"/>
        <ellipse cx="759" cy="140" rx="11" ry="14" fill="#031005"/>

        <!-- Walkway from tower -->
        <path d="M385,195 L374,220 L426,220 L415,195 Z" fill="#0a1628" opacity=".55"/>
    </svg>

    <div class="cv-card" style="max-width: 440px;">
        <!-- Mobile brand (hidden on desktop) -->
        <div class="cv-mob-brand">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice">
            <span class="cv-mob-brand-name">CampusVoice</span>
        </div>

        <div class="cv-head" style="margin-bottom: 34px;">
            <div class="cv-eyebrow" style="margin-bottom: 11px;">Secure Access</div>
            <h2 style="font-size: 2rem; margin: 0 0 10px;">Welcome back</h2>
            <p>Sign in with your admin credentials to access the control panel.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="cv-alert cv-alert--error" role="alert">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <span><?= esc((string) session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="cv-alert cv-alert--success" role="alert">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span><?= esc((string) session()->getFlashdata('success')) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('admin/login') ?>" class="cv-form">
            <div>
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" required
                       autocomplete="off" placeholder="admin@campus.edu"
                       value="<?= esc((string) old('email', '')) ?>">
            </div>

            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required
                       autocomplete="off" placeholder="••••••••••">
            </div>

            <button type="submit" class="cv-btn" style="margin-top: 1rem;">
                Sign In <span class="cv-arrow">→</span>
            </button>
        </form>
        
        <p style="margin-top: 30px; text-align: center; font-size: 0.75rem; color: rgba(255,255,255,0.4);">
            CampusVoice &copy; <?= date('Y') ?> &nbsp;·&nbsp; Admin Portal
        </p>
    </div>

</div><!-- /.cv-rp -->
</div><!-- /.cv-shell -->

</body>
</html>