<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'CampusVoice') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
<?php
$studentPortalCss = FCPATH . 'assets/student/portal.css';
$studentPortalCssVersion = is_file($studentPortalCss) ? (string) filemtime($studentPortalCss) : '1';
$studentIsAuthed = ! empty($studentUser['id']);
$currentTitle = (string) ($title ?? '');
$isAuthScreen = (bool) ($isAuthScreen ?? ($currentTitle === 'Student Portal Access'));
?>
    <link rel="stylesheet" href="<?= base_url('assets/student/portal.css') . '?v=' . $studentPortalCssVersion ?>">
</head>
<body<?= $isAuthScreen ? ' class="is-auth-screen"' : '' ?>>

<?php if (! $isAuthScreen): ?>
<header class="portal-header">
    <div class="portal-header-inner">
        <a href="<?= site_url('users') ?>" class="portal-brand">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice" class="portal-logo">
            <span>CampusVoice</span>
        </a>

        <?php if ($studentIsAuthed): ?>
            <?php
            $navItems = [
                ['label' => 'Home', 'url' => site_url('users'), 'title' => 'My Portal'],
                ['label' => 'My Feedback', 'url' => site_url('users/feedback'), 'title' => 'My Submissions'],
                ['label' => 'Submit', 'url' => site_url('users/feedback/submit'), 'title' => 'Submit Feedback'],
                ['label' => 'Announcements', 'url' => site_url('users/announcements'), 'title' => 'Announcements'],
                ['label' => 'Settings', 'url' => site_url('settings'), 'title' => 'Settings'],
            ];
            ?>
            <nav class="portal-nav">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= $item['url'] ?>" class="<?= $currentTitle === $item['title'] ? 'active' : '' ?>"><?= esc($item['label']) ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="portal-user-info">
                <span><?= esc((string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($studentUser['name'] ?? 'Student'))) ?></span>
                <a href="<?= site_url('users/logout') ?>" class="logout-link">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</header>
<?php endif; ?>

<main class="portal-main<?= $isAuthScreen ? ' portal-main--auth' : '' ?>">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="portal-alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="portal-alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

<?php if (! $isAuthScreen): ?>
<footer class="portal-footer">
    <p>&copy; <?= date('Y') ?> CampusVoice — Student Portal</p>
</footer>
<?php endif; ?>

<script>
(function () {
    var alerts = document.querySelectorAll('.portal-alert');
    alerts.forEach(function (el) {
        setTimeout(function () {
            el.classList.add('is-hiding');
            setTimeout(function () { el.remove(); }, 400);
        }, 5000);
    });
})();
</script>

<!-- AJAX for comment / reaction / share forms (no page jump) -->
<script>
(function () {
    /* ── Comment forms ── */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.classList.contains('comment-form')) return;
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;
        var body = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: body
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok && data.comment) {
                var c = data.comment;
                var card = form.closest('.feed-card') || form.closest('article');
                if (card) {
                    var list = card.querySelector('.comment-list');
                    if (!list) {
                        var stack = card.querySelector('.comment-stack');
                        if (stack) {
                            list = document.createElement('div');
                            list.className = 'comment-list';
                            stack.insertBefore(list, stack.querySelector('.comment-form') || stack.querySelector('.summary-muted') || null);
                        }
                    }
                    if (list) {
                        var item = document.createElement('div');
                        item.className = 'comment-item';
                        item.innerHTML =
                            '<div class="avatar avatar-small avatar-' + (c.avatar_color || 'blue') + '">' + (c.initial || 'U') + '</div>' +
                            '<div><strong>' + (c.author_name || 'You') + '</strong>' +
                            '<p>' + (c.body || '').replace(/\n/g, '<br>') + '</p></div>';
                        list.appendChild(item);
                        list.scrollTop = list.scrollHeight;
                    }
                    /* Update comment count */
                    if (data.comment_total !== undefined) {
                        var summaryEls = card.querySelectorAll('.summary-muted');
                        summaryEls.forEach(function (el) {
                            el.textContent = el.textContent.replace(/\d+ comments/, data.comment_total + ' comments');
                        });
                    }
                }
                form.querySelector('textarea[name="body"]').value = '';
                var cb = form.querySelector('input[name="is_anonymous"]');
                if (cb) cb.checked = false;
            }
            if (btn) btn.disabled = false;
        })
        .catch(function () { if (btn) btn.disabled = false; });
    });

    /* ── Emoji map for reaction pills ── */
    var emojiMap = { like: '👍', love: '❤️', support: '🤝', fire: '🔥' };

    /* ── Reaction forms ── */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.action || form.action.indexOf('/react') === -1) return;
        if (form.classList.contains('comment-form')) return;
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                var card = form.closest('.feed-card') || form.closest('article');
                if (card) {
                    card.querySelectorAll('.react-bar .react-btn').forEach(function (b) {
                        b.classList.remove('active', 'reacting');
                    });
                    if (data.viewer_reaction && btn) {
                        btn.classList.add('active');
                        // Play bounce pop
                        btn.classList.remove('reacting');
                        void btn.offsetWidth; // restart animation
                        btn.classList.add('reacting');
                    }
                    var line = card.querySelector('.reaction-line');
                    if (line && data.reaction_breakdown) {
                        var html = '';
                        for (var type in data.reaction_breakdown) {
                            html += '<span class="mini-pill">' + (emojiMap[type] || '') + ' ' + data.reaction_breakdown[type] + '</span>';
                        }
                        if (data.reaction_total === 0) {
                            html = '<span class="summary-muted">No reactions yet</span>';
                        }
                        line.innerHTML = html;
                    }
                }
            }
            if (btn) btn.disabled = false;
        })
        .catch(function () { if (btn) btn.disabled = false; });
    });

    /* ── Share forms ── */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.action || form.action.indexOf('/share') === -1) return;
        if (form.classList.contains('comment-form')) return;
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                var card = form.closest('.feed-card') || form.closest('article');
                if (card && data.share_total !== undefined) {
                    var summaryEls = card.querySelectorAll('.summary-muted');
                    summaryEls.forEach(function (el) {
                        el.textContent = el.textContent.replace(/\d+ shares/, data.share_total + ' shares');
                    });
                }
                if (btn) { btn.textContent = '✓'; setTimeout(function () { btn.textContent = '↗'; }, 1200); }
            }
            if (btn) btn.disabled = false;
        })
        .catch(function () { if (btn) btn.disabled = false; });
    });

    /* ── Copy Link buttons ── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-share-url]');
        if (!btn) return;
        e.preventDefault();
        var url = btn.getAttribute('data-share-url');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(function () {
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = 'Copy Link'; }, 1500);
            });
        }
    });
})();
</script>

</body>
</html>
