<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->include('partials/theme_fouc') ?>
    <title>CampusVoice — Student Feedback Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f5f9a;
            --primary-strong: #0a4a78;
            --accent: #f28b30;
            --ink: #17324a;
            --ink-soft: #4a6a84;
            --bg: #f4f8fb;
            --bg-alt: #e6f0f8;
            --line: #d6e3ef;
            --surface: #ffffff;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 8% -10%, rgba(242,139,48,.18), transparent 34%),
                radial-gradient(circle at 95% 5%, rgba(15,95,154,.22), transparent 42%),
                linear-gradient(180deg, var(--bg-alt), var(--bg) 26%, #f9fbfd);
            min-height: 100vh;
        }
        a { color: var(--primary); text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* NAV */
        nav {
            position: sticky; top: 0; z-index: 10;
            border-bottom: 1px solid rgba(214,227,239,.75);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,.78);
        }
        .nav-inner {
            max-width: 1100px; margin: 0 auto;
            padding: 12px 20px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
        }
        .brand {
            display: inline-flex; align-items: center; gap: 10px;
            color: var(--ink); font-weight: 800; font-size: 1.15rem; letter-spacing: .2px;
        }
        .brand img { width: 34px; height: 34px; }
        .nav-links { display: flex; gap: 10px; align-items: center; }
        .btn-outline {
            border: 2px solid var(--primary); border-radius: 12px;
            padding: 9px 18px; font-weight: 700; color: var(--primary);
            background: transparent; cursor: pointer;
        }
        .btn-outline:hover { background: var(--primary); color: #fff; text-decoration: none; }
        .btn-fill {
            border: none; border-radius: 12px;
            padding: 9px 18px; font-weight: 800;
            background: linear-gradient(130deg, var(--primary), #1982c4);
            color: #fff; cursor: pointer;
            box-shadow: 0 8px 16px rgba(15,95,154,.24);
        }
        .btn-fill:hover { background: linear-gradient(130deg, var(--primary-strong), var(--primary)); text-decoration: none; }

        /* HERO */
        .hero {
            max-width: 1100px; margin: 0 auto;
            padding: 70px 20px 60px;
            display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center;
        }
        .hero-text h1 {
            font-size: clamp(2rem, 4vw, 3rem); line-height: 1.15;
            font-weight: 800; margin-bottom: 18px;
        }
        .hero-text h1 span { color: var(--primary); }
        .hero-text p {
            font-size: 1.1rem; line-height: 1.7;
            color: var(--ink-soft); margin-bottom: 28px;
        }
        .hero-cta { display: flex; gap: 12px; flex-wrap: wrap; }
        .hero-cta a { font-size: 1rem; padding: 13px 26px; }
        .hero-card {
            background: var(--surface); border: 1px solid var(--line);
            border-radius: 22px; padding: 28px;
            box-shadow: 0 20px 50px rgba(15,62,94,.12);
        }
        .hero-card h3 { margin-bottom: 14px; color: var(--ink-soft); font-size: .9rem; text-transform: uppercase; letter-spacing: .06em; }
        .mock-stat-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
        .mock-stat {
            background: var(--bg); border: 1px solid var(--line);
            border-radius: 12px; padding: 12px;
        }
        .mock-stat span { display: block; font-size: .8rem; color: var(--ink-soft); margin-bottom: 4px; }
        .mock-stat strong { font-size: 1.4rem; }
        .mock-row {
            border: 1px solid var(--line); background: #fbfdff;
            border-radius: 10px; padding: 10px 12px; margin-bottom: 8px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .mock-row span { font-size: .9rem; font-weight: 700; }
        .mock-pill {
            border-radius: 999px; font-size: .78rem; padding: 3px 9px; font-weight: 700;
        }
        .pill-new { background: #fff4e9; color: #bf5f0a; }
        .pill-resolved { background: #e8f8ef; color: #1f8f5f; }
        .pill-reviewed { background: #e8f2ff; color: #0a57a1; }

        /* FEATURES */
        .features {
            max-width: 1100px; margin: 0 auto;
            padding: 20px 20px 70px;
        }
        .features h2 { text-align: center; font-size: 1.7rem; margin-bottom: 30px; }
        .feature-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .feature-card {
            background: var(--surface); border: 1px solid var(--line);
            border-radius: 16px; padding: 22px;
            box-shadow: 0 6px 20px rgba(15,62,94,.06);
        }
        .feature-icon { font-size: 1.9rem; margin-bottom: 10px; }
        .feature-card h3 { margin-bottom: 7px; font-size: 1rem; }
        .feature-card p { color: var(--ink-soft); font-size: .93rem; line-height: 1.6; }

        /* FOOTER */
        footer {
            text-align: center; padding: 20px;
            color: var(--ink-soft); font-size: .9rem;
            border-top: 1px solid var(--line);
        }

        @media (max-width: 760px) {
            .hero { grid-template-columns: 1fr; padding: 40px 16px; }
            .feature-grid { grid-template-columns: 1fr; }
            .mock-stat-row { grid-template-columns: 1fr 1fr; }
        }
    </style>
    <?= $this->include('partials/theme_styles') ?>
</head>
<body class="landing-page">

<nav>
    <div class="nav-inner">
        <a class="brand" href="<?= site_url('/') ?>">
            <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice">
            CampusVoice
        </a>
        <div class="nav-links landing-nav-actions">
            <?= $this->include('partials/theme_toggle', ['toggleClass' => 'theme-toggle--on-light']) ?>
            <a href="<?= site_url('users/login?mode=register') ?>" class="btn-outline">Register</a>
            <a href="<?= site_url('users/login') ?>" class="btn-fill">Log In</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-text">
        <h1>Your voice shapes a <span>better campus</span></h1>
        <p>Submit feedback, track progress, and stay informed with CampusVoice — the student portal that keeps you connected to your school.</p>
        <div class="hero-cta">
            <a href="<?= site_url('users/login') ?>" class="btn-fill">Get Started</a>
            <a href="<?= site_url('users/login?mode=register') ?>" class="btn-outline">Create Account</a>
        </div>
    </div>
    <div class="hero-card">
        <h3>Your Dashboard</h3>
        <div class="mock-stat-row">
            <div class="mock-stat"><span>Total Submitted</span><strong>12</strong></div>
            <div class="mock-stat"><span>Resolved</span><strong>8</strong></div>
            <div class="mock-stat"><span>Pending Review</span><strong>3</strong></div>
            <div class="mock-stat"><span>Reviewed</span><strong>1</strong></div>
        </div>
        <div class="mock-row">
            <span>Broken lights in Lab 3</span>
            <span class="mock-pill pill-resolved">Resolved</span>
        </div>
        <div class="mock-row">
            <span>Cafeteria menu update</span>
            <span class="mock-pill pill-reviewed">Reviewed</span>
        </div>
        <div class="mock-row">
            <span>Wi-Fi issues in Block B</span>
            <span class="mock-pill pill-new">Pending</span>
        </div>
    </div>
</section>

<section class="features">
    <h2>Everything you need</h2>
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">📝</div>
            <h3>Submit Feedback</h3>
            <p>Easily submit complaints, suggestions, or praise about any aspect of campus life.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Track Progress</h3>
            <p>See the real-time status of every submission — pending, reviewed, or resolved.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📢</div>
            <h3>Stay Informed</h3>
            <p>Get the latest school announcements directly in your portal dashboard.</p>
        </div>
    </div>
</section>

<footer>
    <p>&copy; <?= date('Y') ?> CampusVoice. All rights reserved.</p>
</footer>
<?= $this->include('partials/theme_script') ?>

</body>
</html>
