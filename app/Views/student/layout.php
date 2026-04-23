<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->include('partials/theme_fouc') ?>
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
    <?= $this->include('partials/theme_styles') ?>
<?php if ($isAuthScreen): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
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
            $headerUserName = (string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($studentUser['name'] ?? 'Student'));
            $this->setVar('headerUserName', $headerUserName);
            $this->setVar('currentTitle', $currentTitle);
            ?>
            <?= $this->include('partials/portal_header_authed') ?>
        <?php else: ?>
            <div class="portal-header-spacer" aria-hidden="true"></div>
            <div class="portal-header-end">
                <?= $this->include('partials/theme_toggle') ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($studentIsAuthed): ?>
    <div class="portal-nav-backdrop" id="portal-nav-backdrop" hidden></div>
    <?php endif; ?>
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
    /* ── Persist anonymous checkbox state ── */
    document.querySelectorAll('.anon-check').forEach(function (cb) {
        cb.checked = localStorage.getItem('comment_anon') === '1';
        cb.addEventListener('change', function () {
            localStorage.setItem('comment_anon', cb.checked ? '1' : '0');
        });
    });

    /* ── Helper: find the parent .feed-card for any element (matches social/feed) ── */
    function findCard(el) {
        return el.closest('.feed-card');
    }

    function buildCommentHTML(c, isReply) {
        var cls = 'comment-item' + (isReply ? ' reply-item' : '');
        var replyBtn = isReply ? '' :
            '<button type="button" class="comment-reply-btn" data-comment-id="' + c.id + '" data-author="' + (c.author_name || '') + '">Reply</button>';
        var bodyText = (c.body != null) ? String(c.body) : '';
        var hasBody = bodyText.replace(/^\s+|\s+$/g, '') !== '';
        var bodyHtml = hasBody ? ('<p>' + bodyText.replace(/\n/g, '<br>') + '</p>') : '';
        var imgU = c.image_url ? String(c.image_url) : '';
        var safeImg = imgU.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
        var imgHtml = imgU
            ? ('<div class="comment-attachment"><a href="' + safeImg + '" target="_blank" rel="noopener noreferrer" class="comment-attachment__link">' +
                '<img src="' + safeImg + '" alt="" class="comment-attachment__img" loading="lazy" decoding="async"></a></div>')
            : '';
        return '<div class="' + cls + '" data-comment-id="' + c.id + '">' +
            '<div class="avatar avatar-small avatar-' + (c.avatar_color || 'blue') + '">' + (c.initial || 'U') + '</div>' +
            '<div class="comment-body-wrap">' +
                '<div class="comment-bubble">' +
                    '<strong>' + (c.author_name || 'You') + '</strong>' +
                    bodyHtml +
                    imgHtml +
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
                    replyBtn +
                '</div>' +
            '</div>' +
        '</div>';
    }

    /* ── AJAX Comment Submission (with nested replies) ── */
    document.querySelectorAll('.comment-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            var origText = btn ? btn.textContent : '';
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Posting...';
            }

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.error && !data.ok) {
                    if (window.alert) { window.alert(data.error); }
                }
                if (data.ok && data.comment) {
                    var c = data.comment;
                    var parentId = c.parent_id ? parseInt(c.parent_id, 10) : 0;
                    var stack = form.closest('.comment-stack');
                    if (stack) {
                        if (parentId > 0) {
                            var parentItem = stack.querySelector('.comment-item[data-comment-id="' + parentId + '"]');
                            if (parentItem) {
                                var replyList = parentItem.querySelector('.reply-list');
                                if (!replyList) {
                                    replyList = document.createElement('div');
                                    replyList.className = 'reply-list';
                                    var bodyWrap = parentItem.querySelector('.comment-body-wrap');
                                    if (bodyWrap) { bodyWrap.appendChild(replyList); }
                                }
                                if (replyList) {
                                    replyList.insertAdjacentHTML('beforeend', buildCommentHTML(c, true));
                                }
                            }
                            var parentInput = form.querySelector('.comment-parent-id');
                            if (parentInput) { parentInput.value = '0'; }
                            var indicator = form.querySelector('.reply-indicator');
                            if (indicator) { indicator.style.display = 'none'; }
                            var ta0 = form.querySelector('textarea');
                            if (ta0) { ta0.placeholder = 'Write a comment...'; }
                        } else {
                            var commentList = stack.querySelector('.comment-list');
                            if (!commentList) {
                                commentList = document.createElement('div');
                                commentList.className = 'comment-list';
                                stack.insertBefore(commentList, form);
                            }
                            commentList.insertAdjacentHTML('afterbegin', buildCommentHTML(c, false));
                        }
                    }
                    var card = findCard(form) || form.closest('article');
                    if (card && data.comment_total !== undefined) {
                        var summaryEls = card.querySelectorAll('.post-summary-row .summary-muted');
                        summaryEls.forEach(function (el) {
                            el.textContent = el.textContent.replace(
                                /\d+ comments/,
                                data.comment_total + ' comments'
                            );
                        });
                    }
                    var clearTa = form.querySelector('textarea');
                    if (clearTa) { clearTa.value = ''; }
                    var fileIn = form.querySelector('.comment-image-input');
                    if (fileIn) { fileIn.value = ''; }
                }
            })
            .catch(function (err) {
                console.error('Comment failed:', err);
                form.submit();
            })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.textContent = origText; }
            });
        });
    });

    /* ── Reply: set parent_id and show indicator ── */
    document.addEventListener('click', function (e) {
        var replyBtn = e.target.closest('.comment-reply-btn');
        if (!replyBtn) { return; }
        var commentId = replyBtn.getAttribute('data-comment-id');
        var author = replyBtn.getAttribute('data-author') || '';
        var card = findCard(replyBtn) || replyBtn.closest('article');
        if (!card) { return; }
        var cform = card.querySelector('.comment-form');
        if (!cform) { return; }
        var parentInput = cform.querySelector('.comment-parent-id');
        var indicator = cform.querySelector('.reply-indicator');
        var toText = indicator ? indicator.querySelector('.reply-to-text') : null;
        var textarea = cform.querySelector('textarea');
        if (parentInput) { parentInput.value = commentId; }
        if (toText) { toText.textContent = 'Replying to ' + author; }
        if (indicator) { indicator.style.display = 'flex'; }
        if (textarea) {
            textarea.placeholder = 'Write a reply...';
            textarea.focus();
        }
    });

    /* ── Cancel reply ── */
    document.addEventListener('click', function (e) {
        var cancelBtn = e.target.closest('.cancel-reply-btn');
        if (!cancelBtn) { return; }
        var cform = cancelBtn.closest('.comment-form');
        if (!cform) { return; }
        var parentInput = cform.querySelector('.comment-parent-id');
        var indicator = cform.querySelector('.reply-indicator');
        var textarea = cform.querySelector('textarea');
        if (parentInput) { parentInput.value = '0'; }
        if (indicator) { indicator.style.display = 'none'; }
        if (textarea) { textarea.placeholder = 'Write a comment...'; }
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
<?php if (! $isAuthScreen): ?>
<?php
$portalHeaderJsPath = FCPATH . 'assets/student/portal-header.js';
$portalHeaderJsVersion = is_file($portalHeaderJsPath) ? (string) filemtime($portalHeaderJsPath) : '1';
?>
<script src="<?= base_url('assets/student/portal-header.js') ?>?v=<?= esc($portalHeaderJsVersion, 'attr') ?>"></script>
<?php endif; ?>
<?= $this->include('partials/theme_script') ?>

</body>
</html>
