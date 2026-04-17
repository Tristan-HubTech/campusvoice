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

    <?php
    if (! $isAuthScreen) {
        $__announcements = (new \App\Models\AnnouncementModel())
            ->where('is_published', 1)
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(5);
        if (! empty($__announcements)):
    ?>
        <section class="announcements-banner">
            <div class="announcements-header">
                <span class="announcements-icon">📢</span>
                <h3>Announcements</h3>
            </div>
            <div class="announcements-scroll">
                <?php foreach ($__announcements as $ann): ?>
                    <div class="announcement-card">
                        <h4><?= esc((string) $ann['title']) ?></h4>
                        <p><?= nl2br(esc((string) $ann['body'])) ?></p>
                        <span class="announcement-date"><?= esc(date('M d, Y', strtotime((string) $ann['created_at']))) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; } ?>

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
    /* ── Persist anonymous checkbox state ── */
    document.querySelectorAll('.anon-check').forEach(function (cb) {
        cb.checked = localStorage.getItem('comment_anon') === '1';
        cb.addEventListener('change', function () {
            localStorage.setItem('comment_anon', cb.checked ? '1' : '0');
        });
    });

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
                        item.setAttribute('data-comment-id', c.id);
                        item.innerHTML =
                            '<div class="avatar avatar-small avatar-' + (c.avatar_color || 'blue') + '">' + (c.initial || 'U') + '</div>' +
                            '<div class="comment-body-wrap">' +
                                '<div class="comment-bubble">' +
                                    '<strong>' + (c.author_name || 'You') + '</strong>' +
                                    '<p>' + (c.body || '').replace(/\n/g, '<br>') + '</p>' +
                                '</div>' +
                                '<div class="comment-actions">' +
                                    '<span class="comment-date">' + (c.created_at || 'Just now') + '</span>' +
                                    '<span class="comment-like-wrap">' +
                                        '<button type="button" class="comment-like-btn" data-comment-id="' + c.id + '" data-current="">Like</button>' +
                                        '<div class="comment-reaction-picker">' +
                                            '<button type="button" class="picker-emoji" data-reaction="like" title="Like">👍</button>' +
                                            '<button type="button" class="picker-emoji" data-reaction="love" title="Love">❤️</button>' +
                                            '<button type="button" class="picker-emoji" data-reaction="haha" title="Haha">😆</button>' +
                                            '<button type="button" class="picker-emoji" data-reaction="wow" title="Wow">😮</button>' +
                                            '<button type="button" class="picker-emoji" data-reaction="sad" title="Sad">😢</button>' +
                                            '<button type="button" class="picker-emoji" data-reaction="angry" title="Angry">😠</button>' +
                                        '</div>' +
                                    '</span>' +
                                '</div>' +
                            '</div>';
                        list.prepend(item);
                    }
                    /* Update comment count */
                    if (data.comment_total !== undefined) {
                        var summaryEls = card.querySelectorAll('.post-summary-row .summary-muted');
                        summaryEls.forEach(function (el) {
                            el.textContent = el.textContent.replace(/\d+ comments/, data.comment_total + ' comments');
                        });
                    }
                }
                form.querySelector('textarea[name="body"]').value = '';
            }
            if (btn) btn.disabled = false;
        })
        .catch(function () { if (btn) btn.disabled = false; });
    });

    /* ── Emoji map for reaction pills ── */
    /* u2500─ Emoji map for reaction pills ── */
    var emojiMap = { like: '👍', love: '❤️', deslike: '👎', shock: '😮' };

    /* ── AJAX Comment Reactions (Facebook-style) ── */
    var crEmojiMap = { like:'👍', love:'❤️', haha:'😆', wow:'😮', sad:'😢', angry:'😠' };
    var crColorMap = { like:'#2078f4', love:'#ed4956', haha:'#f7b928', wow:'#f7b928', sad:'#f7b928', angry:'#e9710f' };

    function sendCommentReaction(commentId, reaction) {
        var formData = new FormData();
        formData.append('reaction_type', reaction);
        return fetch('<?= site_url('comments/') ?>' + commentId + '/react', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        }).then(function (r) { return r.json(); });
    }

    function updateCommentUI(item, data) {
        var likeBtn = item.querySelector('.comment-like-btn');
        if (!likeBtn) return;
        if (data.viewer_reaction) {
            likeBtn.textContent = data.viewer_reaction.charAt(0).toUpperCase() + data.viewer_reaction.slice(1);
            likeBtn.style.color = crColorMap[data.viewer_reaction] || '#65676b';
            likeBtn.classList.add('reacted');
            likeBtn.setAttribute('data-current', data.viewer_reaction);
        } else {
            likeBtn.textContent = 'Like';
            likeBtn.style.color = '';
            likeBtn.classList.remove('reacted');
            likeBtn.setAttribute('data-current', '');
        }
        var bubble = item.querySelector('.comment-bubble');
        var badge = bubble.querySelector('.comment-reaction-badge');
        var bd = data.reaction_breakdown || {};
        var total = 0;
        var html = '';
        for (var t in crEmojiMap) {
            if (bd[t] && bd[t] > 0) {
                html += '<span class="badge-emoji">' + crEmojiMap[t] + '</span>';
                total += bd[t];
            }
        }
        if (total > 0) {
            html += '<span class="badge-count">' + total + '</span>';
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'comment-reaction-badge';
                bubble.appendChild(badge);
            }
            badge.innerHTML = html;
        } else if (badge) {
            badge.remove();
        }
    }

    document.addEventListener('click', function (e) {
        var emoji = e.target.closest('.picker-emoji');
        if (!emoji) return;
        var item = emoji.closest('.comment-item');
        var commentId = item.getAttribute('data-comment-id');
        var reaction = emoji.getAttribute('data-reaction');
        sendCommentReaction(commentId, reaction).then(function (data) {
            if (data.ok) updateCommentUI(item, data);
        }).catch(function (err) { console.error('Comment reaction failed:', err); });
    });

    document.addEventListener('click', function (e) {
        var likeBtn = e.target.closest('.comment-like-btn');
        if (!likeBtn) return;
        var item = likeBtn.closest('.comment-item');
        var commentId = item.getAttribute('data-comment-id');
        var current = likeBtn.getAttribute('data-current');
        var reaction = current || 'like';
        sendCommentReaction(commentId, reaction).then(function (data) {
            if (data.ok) updateCommentUI(item, data);
        }).catch(function (err) { console.error('Comment reaction failed:', err); });
    });

    /* ── AJAX Delete Post ── */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.classList.contains('delete-post-form')) return;
        e.preventDefault();
        if (!confirm('Delete this post?')) return;
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                var card = form.closest('.feed-card') || form.closest('article');
                if (card) card.remove();
            }
        })
        .catch(function () { form.submit(); });
    });

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


})();
</script>

</body>
</html>
