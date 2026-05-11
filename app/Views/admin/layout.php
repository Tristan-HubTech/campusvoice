<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.5, user-scalable=yes">
    <?= $this->include('partials/theme_fouc') ?>
    <title><?= esc($title ?? 'Admin Panel') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <?php $cpCssV = is_file(FCPATH . 'assets/admin/control-panel.css') ? filemtime(FCPATH . 'assets/admin/control-panel.css') : '1'; ?>
    <link rel="stylesheet" href="<?= base_url('assets/admin/control-panel.css') . '?v=' . $cpCssV ?>">
    <?php $modCssV = is_file(FCPATH . 'assets/admin/admin-modern.css') ? filemtime(FCPATH . 'assets/admin/admin-modern.css') : '1'; ?>
    <link rel="stylesheet" href="<?= base_url('assets/admin/admin-modern.css') . '?v=' . $modCssV ?>">
    <?php $topbarCssV = is_file(FCPATH . 'assets/admin/topbar-redesign.css') ? filemtime(FCPATH . 'assets/admin/topbar-redesign.css') : '1'; ?>
    <link rel="stylesheet" href="<?= base_url('assets/admin/topbar-redesign.css') . '?v=' . $topbarCssV ?>">
    <?= $this->include('partials/theme_styles') ?>
</head>
<body>
<div class="admin-shell">
    <?php
    /* Sidebar computed vars — must come before the aside */
    $navPerms    = $adminUser['permissions'] ?? [];
    $panelUrl    = site_url('admin');
    $activeMenu  = $activeMenu ?? '';
    $pName       = (string) ($adminUser['name']  ?? 'Admin');
    $pEmail      = (string) ($adminUser['email'] ?? '');
    $pInitials   = strtoupper(implode('', array_map(fn($w) => $w[0], array_filter(explode(' ', $pName)))));
    $pInitials   = substr($pInitials, 0, 2) ?: 'AD';
    ?>
    <aside class="admin-sidebar" id="adminSidebar">

        <!-- 1 · Brand ──────────────────────────────────────── -->
        <div class="brand">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice logo" class="brand-mark">
            <div>
                <strong>CampusVoice</strong>
                <small>Control Panel</small>
            </div>
        </div>

        <!-- 2 · Navigation ─────────────────────────────────── -->
        <nav class="sidebar-nav">
            <?php if (! empty($navPerms['dashboard.view'])): ?>
            <a href="<?= $panelUrl ?>#overview" data-nav-tab="overview">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Overview
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['feedback.view'])): ?>
            <a href="<?= $panelUrl ?>#feedback" data-nav-tab="feedback">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Feedback
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['announcements.create']) || ! empty($navPerms['announcements.edit']) || ! empty($navPerms['announcements.delete']) || ! empty($navPerms['announcements.pin'])): ?>
            <a href="<?= $panelUrl ?>#announcements" data-nav-tab="announcements">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg>
                Announcements
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['users.view'])): ?>
            <a href="<?= $panelUrl ?>#users" data-nav-tab="users">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Students
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['categories.view'])): ?>
            <a href="<?= $panelUrl ?>#categories" data-nav-tab="categories">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Categories
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['activity.view'])): ?>
            <a href="<?= $panelUrl ?>#activity" data-nav-tab="activity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Admin Activity
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['student_activity.view'])): ?>
            <a href="<?= $panelUrl ?>#student-activity" data-nav-tab="student-activity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Student Activity
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['admin.view']) || ! empty($navPerms['roles.view'])): ?>
            <div class="cv-nav-divider"></div>
            <?php endif; ?>
            <?php if (! empty($navPerms['admin.view'])): ?>
            <a href="<?= site_url('admin/admins') ?>" data-nav-page="admins" class="<?= $activeMenu === 'admins' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Admin Accounts
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['roles.view'])): ?>
            <a href="<?= site_url('admin/roles') ?>" data-nav-page="roles" class="<?= $activeMenu === 'roles' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
                Roles
            </a>
            <?php endif; ?>
            <?php if (! empty($navPerms['support.view'])): ?>
            <a href="<?= site_url('admin/support') ?>" data-nav-page="support" class="<?= $activeMenu === 'support' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Support
            </a>
            <?php endif; ?>
        </nav>

        <!-- 3 · Sign out ──────────────────────────────────── -->
        <a href="<?= site_url('admin/logout') ?>" class="cv-sidebar-logout">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span class="cv-logout-label">Sign Out</span>
        </a>

    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button type="button" class="menu-btn" id="menuBtn" aria-label="Toggle navigation">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="admin-topbar-brand">
                <div class="admin-topbar-graphic" aria-hidden="true">
                    <svg viewBox="0 0 148 52" xmlns="http://www.w3.org/2000/svg" role="presentation" focusable="false">
                        <defs>
                            <linearGradient id="cvSky" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%"   stop-color="#050c1a"/>
                                <stop offset="100%" stop-color="#0c1b34"/>
                            </linearGradient>
                            <linearGradient id="cvGl" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%"   stop-color="#1a3f9f" stop-opacity="0"/>
                                <stop offset="30%"  stop-color="#2c60d4"/>
                                <stop offset="68%"  stop-color="#d4a83a"/>
                                <stop offset="100%" stop-color="#d4a83a" stop-opacity="0"/>
                            </linearGradient>
                            <radialGradient id="cvTg" cx="50%" cy="80%" r="60%">
                                <stop offset="0%"   stop-color="#d4a83a" stop-opacity=".28"/>
                                <stop offset="100%" stop-color="#d4a83a" stop-opacity="0"/>
                            </radialGradient>
                            <radialGradient id="cvMoon" cx="50%" cy="50%" r="50%">
                                <stop offset="0%"   stop-color="#3464cc" stop-opacity=".22"/>
                                <stop offset="100%" stop-color="#3464cc" stop-opacity="0"/>
                            </radialGradient>
                        </defs>
                        <!-- Night sky -->
                        <rect x="0" y="0" width="148" height="52" rx="10" fill="url(#cvSky)"/>
                        <!-- Moon bloom top-left -->
                        <ellipse cx="16" cy="7" rx="22" ry="13" fill="url(#cvMoon)"/>
                        <!-- Stars -->
                        <circle cx="6"   cy="5"  r=".75" fill="#9bbff6" opacity=".9"/>
                        <circle cx="19"  cy="3"  r=".55" fill="#fff"    opacity=".8"/>
                        <circle cx="31"  cy="8"  r=".65" fill="#9bbff6" opacity=".72"/>
                        <circle cx="46"  cy="4"  r=".5"  fill="#fff"    opacity=".68"/>
                        <circle cx="58"  cy="10" r=".5"  fill="#9bbff6" opacity=".6"/>
                        <circle cx="67"  cy="6"  r=".4"  fill="#fff"    opacity=".55"/>
                        <circle cx="83"  cy="9"  r=".4"  fill="#9bbff6" opacity=".5"/>
                        <circle cx="96"  cy="5"  r=".65" fill="#fff"    opacity=".82"/>
                        <circle cx="110" cy="9"  r=".5"  fill="#9bbff6" opacity=".65"/>
                        <circle cx="121" cy="4"  r=".75" fill="#fff"    opacity=".88"/>
                        <circle cx="135" cy="7"  r=".6"  fill="#9bbff6" opacity=".75"/>
                        <circle cx="144" cy="3"  r=".65" fill="#fff"    opacity=".72"/>
                        <!-- Gold sparkle top-right -->
                        <path d="M139 11l.55 1.85 1.85.55-1.85.55-.55 1.85-.55-1.85-1.85-.55 1.85-.55z" fill="#d4a83a" opacity=".82"/>
                        <!-- Pine tree far left -->
                        <polygon points="2,41 5.5,30 9,41"             fill="#0c2818" stroke="#0e3020" stroke-width=".4"/>
                        <rect    x="5"   y="40" width="1.2" height="1.5" fill="#091a10"/>
                        <!-- House left -->
                        <rect    x="11"  y="32" width="10" height="9"  fill="#102248" stroke="#1e3d74" stroke-width=".8"/>
                        <polygon points="11,32 16,26.5 21,32"          fill="#152b58" stroke="#1e3d74" stroke-width=".8"/>
                        <rect    x="13"  y="34" width="2.5" height="3.5" fill="#d4a83a" opacity=".2"/>
                        <rect    x="17"  y="34" width="2.5" height="3.5" fill="#3060d0" opacity=".3"/>
                        <!-- Building 2 -->
                        <rect    x="23"  y="25" width="10" height="16" rx=".4" fill="#0f2042" stroke="#1c3870" stroke-width=".8"/>
                        <rect    x="25"  y="27.5" width="2.5" height="2.5" fill="#3060d0" opacity=".48"/>
                        <rect    x="28.5" y="27.5" width="2.5" height="2.5" fill="#3060d0" opacity=".32"/>
                        <rect    x="25"  y="32"   width="2.5" height="2.5" fill="#d4a83a" opacity=".34"/>
                        <rect    x="28.5" y="32"   width="2.5" height="2.5" fill="#3060d0" opacity=".28"/>
                        <!-- Building 3 arched -->
                        <rect    x="35"  y="18" width="10" height="23" rx=".4" fill="#0d1e3e" stroke="#1c3870" stroke-width=".8"/>
                        <path    d="M35 18.5Q40 13 45 18.5"            fill="#0d1e3e" stroke="#1c3870" stroke-width=".8"/>
                        <rect    x="37"  y="21"   width="2.5" height="2.5" fill="#3060d0" opacity=".44"/>
                        <rect    x="40.5" y="21"   width="2.5" height="2.5" fill="#3060d0" opacity=".3"/>
                        <rect    x="37"  y="26"   width="2.5" height="2.5" fill="#d4a83a" opacity=".34"/>
                        <rect    x="40.5" y="26"   width="2.5" height="2.5" fill="#3060d0" opacity=".28"/>
                        <rect    x="37"  y="31"   width="2.5" height="2.5" fill="#3060d0" opacity=".28"/>
                        <!-- Small connector -->
                        <rect    x="47"  y="29" width="8"  height="12" rx=".4" fill="#102248" stroke="#1c3870" stroke-width=".7"/>
                        <rect    x="49"  y="31.5" width="2" height="2" fill="#3060d0" opacity=".44"/>
                        <rect    x="52"  y="31.5" width="2" height="2" fill="#d4a83a" opacity=".34"/>
                        <!-- Tower glow halo -->
                        <ellipse cx="74" cy="31" rx="20" ry="14" fill="url(#cvTg)"/>
                        <!-- Clock tower spire -->
                        <polygon points="74,2 76.5,12.5 71.5,12.5"     fill="#d4a83a" opacity=".97"/>
                        <!-- Belfry -->
                        <rect    x="70"  y="12.5" width="8" height="7" rx=".6" fill="#0d1c38" stroke="#2c60d4" stroke-width="1.2"/>
                        <!-- Clock face -->
                        <circle  cx="74" cy="16.5" r="3.2"             fill="none"  stroke="#d4a83a" stroke-width="1.3"/>
                        <line    x1="74" y1="16.5" x2="74"   y2="14"   stroke="#d4a83a" stroke-width=".95" stroke-linecap="round"/>
                        <line    x1="74" y1="16.5" x2="76.3" y2="17.6" stroke="#d4a83a" stroke-width=".85" stroke-linecap="round"/>
                        <!-- Tower body -->
                        <rect    x="67"  y="19.5" width="14" height="21.5" rx=".7" fill="#0d1c38" stroke="#2c60d4" stroke-width="1.2"/>
                        <!-- Arch windows -->
                        <path    d="M69.5 22.5v5.5Q71.5 28.8 73.5 22.5" fill="#2c60d4" opacity=".38"/>
                        <path    d="M74.5 22.5v5.5Q76.5 28.8 78.5 22.5" fill="#2c60d4" opacity=".38"/>
                        <!-- Side windows -->
                        <rect    x="69"  y="30.5" width="3"  height="2.5" rx=".3" fill="#3060d0" opacity=".32"/>
                        <rect    x="76"  y="30.5" width="3"  height="2.5" rx=".3" fill="#3060d0" opacity=".32"/>
                        <!-- Door arch -->
                        <path    d="M72.5 41v-6.5Q74 33 75.5 34.5v6.5"  fill="#d4a83a" opacity=".18"/>
                        <!-- Building dome right -->
                        <rect    x="83"  y="22" width="10" height="19"  rx=".4" fill="#0f2042" stroke="#1c3870" stroke-width=".8"/>
                        <path    d="M83 22Q88 16.5 93 22"                fill="#0f2042" stroke="#1c3870" stroke-width=".8"/>
                        <rect    x="85"  y="24.5" width="2.5" height="2.5" fill="#3060d0" opacity=".44"/>
                        <rect    x="88.5" y="24.5" width="2.5" height="2.5" fill="#d4a83a" opacity=".32"/>
                        <rect    x="85"  y="29.5" width="2.5" height="2.5" fill="#3060d0" opacity=".34"/>
                        <rect    x="88.5" y="29.5" width="2.5" height="2.5" fill="#3060d0" opacity=".28"/>
                        <!-- Building 5 -->
                        <rect    x="95"  y="26" width="10" height="15"  rx=".4" fill="#102248" stroke="#1c3870" stroke-width=".8"/>
                        <rect    x="97"  y="28.5" width="2.5" height="2.5" fill="#3060d0" opacity=".44"/>
                        <rect    x="100.5" y="28.5" width="2.5" height="2.5" fill="#3060d0" opacity=".3"/>
                        <rect    x="97"  y="33.5" width="2.5" height="2.5" fill="#d4a83a" opacity=".34"/>
                        <!-- House right -->
                        <rect    x="107" y="31" width="10" height="10"  fill="#102248" stroke="#1c3870" stroke-width=".8"/>
                        <polygon points="107,31 112,25.5 117,31"         fill="#152b58" stroke="#1c3870" stroke-width=".8"/>
                        <rect    x="109" y="33.5" width="2.5" height="3.5" fill="#3060d0" opacity=".32"/>
                        <rect    x="113" y="33.5" width="2.5" height="3.5" fill="#d4a83a" opacity=".22"/>
                        <!-- Building 6 far right -->
                        <rect    x="119" y="27" width="9"  height="14"  rx=".4" fill="#0d1e3e" stroke="#1c3870" stroke-width=".8"/>
                        <rect    x="121" y="29.5" width="2.5" height="2.5" fill="#3060d0" opacity=".44"/>
                        <rect    x="124.5" y="29.5" width="2.5" height="2.5" fill="#3060d0" opacity=".3"/>
                        <rect    x="121" y="34.5" width="2.5" height="2.5" fill="#d4a83a" opacity=".34"/>
                        <!-- Pine trees right -->
                        <polygon points="130,41 133.5,31 137,41"         fill="#0c2818" stroke="#0e3020" stroke-width=".4"/>
                        <rect    x="133" y="40" width="1.2" height="1.5" fill="#091a10"/>
                        <polygon points="135,41 138,33.5 141,41"         fill="#0c2818" stroke="#0e3020" stroke-width=".4"/>
                        <rect    x="137.5" y="40" width="1" height="1.5" fill="#091a10"/>
                        <!-- Ground line -->
                        <line x1="0" y1="41" x2="148" y2="41"           stroke="url(#cvGl)" stroke-width="1.8"/>
                        <!-- Ground base -->
                        <rect x="0" y="41.5" width="148" height="10.5"   fill="#030810"/>
                        <!-- Cobblestone path strip (center walkway) -->
                        <rect x="58" y="41.5" width="32" height="10.5"   fill="#050e1c" opacity=".7"/>
                        <line x1="58" y1="41.5" x2="58" y2="52"         stroke="#0c1e38" stroke-width=".6" opacity=".5"/>
                        <line x1="90" y1="41.5" x2="90" y2="52"         stroke="#0c1e38" stroke-width=".6" opacity=".5"/>
                        <!-- Subtle ground grid (path tiles) -->
                        <line x1="0"  y1="45" x2="148" y2="45"          stroke="#070f20" stroke-width=".5" opacity=".6"/>
                        <line x1="0"  y1="48.5" x2="148" y2="48.5"      stroke="#070f20" stroke-width=".4" opacity=".45"/>
                        <line x1="20" y1="41.5" x2="20" y2="52"         stroke="#070f20" stroke-width=".4" opacity=".4"/>
                        <line x1="40" y1="41.5" x2="40" y2="52"         stroke="#070f20" stroke-width=".4" opacity=".4"/>
                        <line x1="74" y1="41.5" x2="74" y2="52"         stroke="#080f22" stroke-width=".5" opacity=".5"/>
                        <line x1="108" y1="41.5" x2="108" y2="52"       stroke="#070f20" stroke-width=".4" opacity=".4"/>
                        <line x1="128" y1="41.5" x2="128" y2="52"       stroke="#070f20" stroke-width=".4" opacity=".4"/>
                        <!-- Gold tower glow pool on ground -->
                        <ellipse cx="74" cy="52" rx="16" ry="5"          fill="#d4a83a" opacity=".09"/>
                        <ellipse cx="74" cy="50" rx="9"  ry="3"          fill="#d4a83a" opacity=".13"/>
                        <!-- Window light spills (blue) -->
                        <ellipse cx="26" cy="52" rx="6"  ry="2"          fill="#3060d0" opacity=".08"/>
                        <ellipse cx="40" cy="52" rx="7"  ry="2.5"        fill="#d4a83a" opacity=".07"/>
                        <ellipse cx="51" cy="52" rx="4"  ry="1.8"        fill="#3060d0" opacity=".08"/>
                        <ellipse cx="88" cy="52" rx="7"  ry="2.5"        fill="#3060d0" opacity=".08"/>
                        <ellipse cx="100" cy="52" rx="5" ry="2"          fill="#d4a83a" opacity=".07"/>
                        <ellipse cx="122" cy="52" rx="5" ry="2"          fill="#3060d0" opacity=".07"/>
                        <!-- Streetlamp posts left -->
                        <line x1="19" y1="36" x2="19" y2="41.5"         stroke="#1a3060" stroke-width=".7"/>
                        <circle cx="19" cy="35.5" r="1.2"                fill="#d4a83a" opacity=".7"/>
                        <ellipse cx="19" cy="41.5" rx="3.5" ry="1.2"     fill="#d4a83a" opacity=".12"/>
                        <!-- Streetlamp post right -->
                        <line x1="129" y1="36" x2="129" y2="41.5"       stroke="#1a3060" stroke-width=".7"/>
                        <circle cx="129" cy="35.5" r="1.2"               fill="#d4a83a" opacity=".7"/>
                        <ellipse cx="129" cy="41.5" rx="3.5" ry="1.2"    fill="#d4a83a" opacity=".12"/>
                    </svg>
                </div>
                <div class="admin-topbar-heading">
                    <span class="admin-topbar-kicker">CampusVoice Admin Portal</span>
                    <h1 class="admin-topbar-title"><?= esc($title ?? 'Control Panel') ?></h1>
                </div>
            </div>
            <div class="admin-topbar-actions">
                <?= $this->include('partials/theme_toggle', ['toggleClass' => 'theme-toggle--on-light']) ?>
                <div class="admin-topbar-sep"></div>
                <div class="admin-user">
                    <div class="admin-topbar-avatar"><?= esc($pInitials) ?></div>
                    <div class="admin-user-info">
                        <strong><?= esc((string) ($adminUser['name'] ?? 'Admin')) ?></strong>
                        <small><?= esc((string) ($adminUser['email'] ?? '')) ?></small>
                    </div>
                </div>
            </div>
        </header>


        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <main class="admin-content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script>
    (function () {
        var minZoom = 1;
        var maxZoom = 1.5;
        var step = 0.1;
        var currentZoom = 1;
        function clampZoom(value) {
            return Math.min(maxZoom, Math.max(minZoom, value));
        }
        function applyZoom(value) {
            currentZoom = clampZoom(Math.round(value * 100) / 100);
            document.documentElement.style.zoom = String(currentZoom * 100) + '%';
        }
        window.addEventListener('wheel', function (event) {
            if (!event.ctrlKey && !event.metaKey) return;
            event.preventDefault();
            applyZoom(currentZoom + (event.deltaY < 0 ? step : -step));
        }, { passive: false });
        window.addEventListener('keydown', function (event) {
            if (!event.ctrlKey && !event.metaKey) return;
            if (event.key === '+' || event.key === '=' || event.key === 'Add') {
                event.preventDefault();
                applyZoom(currentZoom + step);
            } else if (event.key === '-' || event.key === '_' || event.key === 'Subtract') {
                event.preventDefault();
                applyZoom(currentZoom - step);
            } else if (event.key === '0') {
                event.preventDefault();
                applyZoom(1);
            }
        });
    })();

    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('adminSidebar');
    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    const tabNavLinks = document.querySelectorAll('[data-nav-tab]');
    const pageNavActive = document.querySelector('[data-nav-page].active');

    function syncSideNavWithHash() {
        // If we're on a dedicated page (admins, roles), don't highlight any hash tab
        if (pageNavActive) {
            tabNavLinks.forEach(function (link) { link.classList.remove('active'); });
            return;
        }
        const tab = (window.location.hash || '#overview').replace('#', '');
        tabNavLinks.forEach(function (link) {
            link.classList.toggle('active', link.getAttribute('data-nav-tab') === tab);
        });
    }

    window.addEventListener('hashchange', syncSideNavWithHash);
    syncSideNavWithHash();
</script>
<?= $this->include('partials/theme_script') ?>
</body>
</html>
