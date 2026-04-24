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
    <?php
    $socialCss = FCPATH . 'assets/student/social.css';
    $socialCssVersion = is_file($socialCss) ? (string) filemtime($socialCss) : '1';
    ?>
    <link rel="stylesheet" href="<?= base_url('assets/student/social.css') . '?v=' . $socialCssVersion ?>">
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

<script>
    /* ── Persist anonymous checkbox state ── */
    document.querySelectorAll('.anon-check').forEach(function (cb) {
        cb.checked = localStorage.getItem('comment_anon') === '1';
        cb.addEventListener('change', function () {
            localStorage.setItem('comment_anon', cb.checked ? '1' : '0');
        });
    });

    /* ── Helper: find the parent .feed-card for any element ── */
    function findCard(el) {
        return el.closest('.feed-card');
    }

    function buildCommentHTML(c, isReply) {
        var cls = 'comment-item' + (isReply ? ' reply-item' : '');
        var safeAuthor = (c.author_name || 'User').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        var replyBtn = isReply ? '' :
            '<button type="button" class="comment-reply-btn" data-comment-id="' + c.id + '" data-author="' + safeAuthor + '">↩ Reply</button>';
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
            '<div class="avatar avatar-small avatar-' + c.avatar_color + '">' + c.initial + '</div>' +
            '<div class="comment-body-wrap">' +
                '<div class="comment-bubble">' +
                    '<strong>' + c.author_name + '</strong>' +
                    bodyHtml +
                    imgHtml +
                    '<span class="comment-reaction-badge" data-counts="{}"></span>' +
                '</div>' +
                '<div class="comment-actions">' +
                    '<span class="comment-date">' + (c.created_at || 'Just now') + '</span>' +
                    '<span class="comment-like-wrap">' +
                        '<div class="comment-reaction-picker">' +
                            '<button type="button" class="picker-emoji" data-reaction="like" title="Like">👍</button>' +
                            '<button type="button" class="picker-emoji" data-reaction="love" title="Love">❤️</button>' +
                            '<button type="button" class="picker-emoji" data-reaction="haha" title="Haha">😆</button>' +
                            '<button type="button" class="picker-emoji" data-reaction="wow" title="Wow">😮</button>' +
                            '<button type="button" class="picker-emoji" data-reaction="sad" title="Sad">😢</button>' +
                            '<button type="button" class="picker-emoji" data-reaction="angry" title="Angry">😠</button>' +
                        '</div>' +
                        '<button type="button" class="comment-like-btn" data-comment-id="' + c.id + '" data-current="">' +
                            '<span class="fb-like-icon">👍</span><span class="fb-like-label">Like</span>' +
                        '</button>' +
                    '</span>' +
                    replyBtn +
                '</div>' +
            '</div>' +
        '</div>';
    }

    /* ── AJAX Comment Submission ── */
    document.querySelectorAll('.comment-form').forEach(function (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const origText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Posting...';

            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
                });
                const data = await resp.json();

                if (data.error && !data.ok) {
                    if (window.alert) { window.alert(data.error); }
                }
                if (data.ok && data.comment) {
                    const c = data.comment;
                    const parentId = c.parent_id ? parseInt(c.parent_id) : 0;
                    const stack = form.closest('.comment-stack');

                    var newEl = null;
                    if (parentId > 0) {
                        // It's a reply — append under the parent comment
                        var parentItem = stack.querySelector('.comment-item[data-comment-id="' + parentId + '"]');
                        if (parentItem) {
                            var bodyWrap = parentItem.querySelector('.comment-body-wrap');
                            var replyList = parentItem.querySelector('.reply-list');
                            if (!replyList) {
                                replyList = document.createElement('div');
                                replyList.className = 'reply-list';
                                bodyWrap.appendChild(replyList);
                            }
                            replyList.style.display = '';
                            replyList.insertAdjacentHTML('beforeend', buildCommentHTML(c, true));
                            newEl = replyList.lastElementChild;
                            // Update or create the reply count toggle
                            var replyCount = replyList.querySelectorAll(':scope > .comment-item').length;
                            var countLabel = '↩ ' + replyCount + ' ' + (replyCount === 1 ? 'reply' : 'replies');
                            var existingToggle = parentItem.querySelector('.reply-count-btn');
                            if (existingToggle) {
                                existingToggle.textContent = countLabel;
                                existingToggle.setAttribute('data-expanded', '1');
                            } else {
                                var newToggle = document.createElement('button');
                                newToggle.type = 'button';
                                newToggle.className = 'reply-count-btn';
                                newToggle.setAttribute('data-expanded', '1');
                                newToggle.textContent = countLabel;
                                bodyWrap.insertBefore(newToggle, replyList);
                            }
                        }
                        // Reset reply state
                        var parentInput = form.querySelector('.comment-parent-id');
                        if (parentInput) parentInput.value = '0';
                        var indicator = form.querySelector('.reply-indicator');
                        if (indicator) indicator.style.display = 'none';
                        var ta = form.querySelector('textarea');
                        if (ta) { ta.placeholder = 'Write a comment...'; ta.value = ''; }
                        form.classList.remove('replying');
                    } else {
                        // Top-level comment — append at bottom so it's visible near the textarea
                        let commentList = stack.querySelector('.comment-list');
                        if (!commentList) {
                            commentList = document.createElement('div');
                            commentList.className = 'comment-list';
                            stack.insertBefore(commentList, form);
                        }
                        commentList.insertAdjacentHTML('beforeend', buildCommentHTML(c, false));
                        newEl = commentList.lastElementChild;
                    }
                    // Animate and scroll newly inserted comment/reply into view
                    if (newEl) {
                        newEl.classList.add('newly-added');
                        newEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        setTimeout(function() { newEl.classList.remove('newly-added'); }, 400);
                    }

                    // Update comment counts everywhere on the card
                    const card = findCard(form);
                    if (card && data.comment_total !== undefined) {
                        var total = data.comment_total;
                        var label = total + ' comment' + (total !== 1 ? 's' : '');
                        card.querySelectorAll('.post-comment-count, .post-summary-row .summary-muted').forEach(function (el) {
                            if (/\d+ comments?/.test(el.textContent)) el.textContent = label;
                        });
                    }

                    form.querySelector('textarea').value = '';
                    const fileIn = form.querySelector('.comment-image-input');
                    if (fileIn) { fileIn.value = ''; }
                }
            } catch (err) {
                console.error('Comment failed:', err);
            } finally {
                btn.disabled = false;
                btn.textContent = origText;
            }
        });
    });

    /* ── Reply Button Handler ── */
    document.addEventListener('click', function (e) {
        var replyBtn = e.target.closest('.comment-reply-btn');
        if (!replyBtn) return;
        var commentId = replyBtn.getAttribute('data-comment-id');
        var author = replyBtn.getAttribute('data-author');
        var card = findCard(replyBtn);
        if (!card) return;
        var form = card.querySelector('.comment-form');
        if (!form) return;
        var parentInput = form.querySelector('.comment-parent-id');
        var indicator = form.querySelector('.reply-indicator');
        var toText = indicator ? indicator.querySelector('.reply-to-text') : null;
        var textarea = form.querySelector('textarea');
        if (parentInput) parentInput.value = commentId;
        if (toText) toText.textContent = 'Replying to ' + author;
        if (indicator) indicator.style.display = 'flex';
        if (textarea) {
            textarea.placeholder = 'Write a reply to ' + author + '...';
            var mention = '@' + author + ' ';
            if (!textarea.value || /^@\S.*\s/.test(textarea.value)) {
                textarea.value = mention;
            }
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }
        form.classList.add('replying');
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    /* ── Cancel Reply Handler ── */
    document.addEventListener('click', function (e) {
        var cancelBtn = e.target.closest('.cancel-reply-btn');
        if (!cancelBtn) return;
        var form = cancelBtn.closest('.comment-form');
        if (!form) return;
        var parentInput = form.querySelector('.comment-parent-id');
        var indicator = form.querySelector('.reply-indicator');
        var textarea = form.querySelector('textarea');
        if (parentInput) parentInput.value = '0';
        if (indicator) indicator.style.display = 'none';
        if (textarea) {
            textarea.placeholder = 'Write a comment...';
            if (/^@.+ $/.test(textarea.value)) textarea.value = '';
        }
        form.classList.remove('replying');
    });

    /* ── Reply Count Toggle ── */
    document.addEventListener('click', function (e) {
        var toggleBtn = e.target.closest('.reply-count-btn');
        if (!toggleBtn) return;
        var commentItem = toggleBtn.closest('.comment-item');
        if (!commentItem) return;
        var replyList = commentItem.querySelector('.reply-list');
        if (!replyList) return;
        var expanded = toggleBtn.getAttribute('data-expanded') === '1';
        replyList.style.display = expanded ? 'none' : '';
        toggleBtn.setAttribute('data-expanded', expanded ? '0' : '1');
    });

    /* ── Emoji maps ── */
    var emojiMap  = { like:'👍', love:'❤️', haha:'😆', wow:'😮', sad:'😢', angry:'😠' };
    var rxColorMap = { like:'#2078f4', love:'#ed4956', haha:'#f7b928', wow:'#f7b928', sad:'#f7b928', angry:'#e9710f' };
    var rxLabelMap = { like:'Like', love:'Love', haha:'Haha', wow:'Wow', sad:'Sad', angry:'Angry' };

    /* ── Post Reactions ── */
    function updatePostSummary(postEl, breakdown) {
        var summary = postEl.querySelector('.post-reaction-summary .reaction-content');
        if (!summary) return;
        var total = Object.keys(breakdown).reduce(function(a, k) { return a + parseInt(breakdown[k] || 0, 10); }, 0);
        if (total === 0) { summary.textContent = 'No reactions yet'; return; }
        var top = Object.keys(breakdown)
            .filter(function(k) { return breakdown[k] > 0; })
            .sort(function(a, b) { return breakdown[b] - breakdown[a]; })
            .slice(0, 3);
        summary.innerHTML = top.map(function(k) {
            return '<span class="top-emoji">' + (emojiMap[k] || '') + '</span>';
        }).join('') + '<span class="top-count">' + total + '</span>';
    }

    function updatePostReactUI(card, data) {
        var btn   = card.querySelector('.post-action-bar .comment-like-btn');
        var icon  = btn ? btn.querySelector('.like-icon') : null;
        var label = btn ? btn.querySelector('.like-label') : null;
        var rx    = data.viewer_reaction;

        if (btn) {
            btn.setAttribute('data-current', rx || '');
            btn.style.color = rx ? (rxColorMap[rx] || '') : '';
            btn.classList.toggle('reacted', !!rx);
        }
        if (icon) {
            icon.textContent = rx ? (emojiMap[rx] || '👍') : '👍';
            if (rx) {
                icon.classList.remove('pop-trigger');
                void icon.offsetWidth;
                icon.classList.add('pop-trigger');
            }
        }
        if (label) label.textContent = rx ? (' ' + (rxLabelMap[rx] || rx)) : ' Like';

        updatePostSummary(card, data.breakdown || data.reaction_breakdown || {});
    }

    /* ── Comment Reaction System ── */
    var crEmojiMap = { like:'👍', love:'❤️', haha:'😆', wow:'😮', sad:'😢', angry:'😠' };
    var crColorMap = { like:'#2078f4', love:'#ed4956', haha:'#f7b928', wow:'#f7b928', sad:'#f7b928', angry:'#e9710f' };

    function getBadge(commentEl) {
        var bubble = commentEl.querySelector('.comment-bubble');
        return bubble ? bubble.querySelector('.comment-reaction-badge') : null;
    }

    function renderBadge(badge, counts) {
        var entries = Object.keys(counts).map(function(k) { return [k, parseInt(counts[k] || 0, 10)]; })
            .filter(function(e) { return e[1] > 0; })
            .sort(function(a, b) { return b[1] - a[1]; })
            .slice(0, 3);
        var total = Object.keys(counts).reduce(function(s, k) { return s + parseInt(counts[k] || 0, 10); }, 0);
        if (total === 0) { badge.innerHTML = ''; badge.dataset.counts = '{}'; return; }
        var html = entries.map(function(e) { return '<span class="badge-emoji">' + (crEmojiMap[e[0]] || '') + '</span>'; }).join('');
        html += '<span class="badge-count">' + total + '</span>';
        badge.innerHTML = html;
        badge.dataset.counts = JSON.stringify(counts);
    }

    function updateCommentUI(commentEl, likeBtn, newRx, prevRx) {
        var icon  = likeBtn.querySelector('.fb-like-icon');
        var label = likeBtn.querySelector('.fb-like-label');
        if (newRx) {
            likeBtn.dataset.current = newRx;
            likeBtn.style.color = crColorMap[newRx] || '';
            likeBtn.classList.add('reacted');
            if (icon)  icon.textContent  = crEmojiMap[newRx] || '👍';
            if (label) label.textContent = rxLabelMap[newRx] || newRx;
        } else {
            likeBtn.dataset.current = '';
            likeBtn.style.color = '';
            likeBtn.classList.remove('reacted');
            if (icon)  icon.textContent  = '👍';
            if (label) label.textContent = 'Like';
        }
        var badge = getBadge(commentEl);
        if (badge) {
            var counts = {};
            try { counts = JSON.parse(badge.dataset.counts || '{}'); } catch(e2) {}
            if (prevRx) counts[prevRx] = Math.max(0, parseInt(counts[prevRx] || 0, 10) - 1);
            if (newRx)  counts[newRx]  = parseInt(counts[newRx] || 0, 10) + 1;
            renderBadge(badge, counts);
        }
    }

    function renderCommentFromServer(commentEl, likeBtn, data) {
        var rx     = data.viewer_reaction;
        var counts = data.breakdown || data.reaction_breakdown || {};
        var icon   = likeBtn.querySelector('.fb-like-icon');
        var label  = likeBtn.querySelector('.fb-like-label');
        likeBtn.dataset.current = rx || '';
        likeBtn.style.color = rx ? (crColorMap[rx] || '') : '';
        likeBtn.classList.toggle('reacted', !!rx);
        if (icon)  icon.textContent  = rx ? (crEmojiMap[rx] || '👍') : '👍';
        if (label) label.textContent = rx ? (rxLabelMap[rx] || rx) : 'Like';
        var badge = getBadge(commentEl);
        if (badge) renderBadge(badge, counts);
    }

    function sendCommentReact(commentId, reactionType) {
        var fd = new FormData();
        fd.append('reaction_type', reactionType);
        return fetch('<?= site_url('comments/') ?>' + commentId + '/react', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
        }).then(function(r) { return r.json(); });
    }

    /* Unified .picker-emoji click — routes to post or comment based on data-post-id */
    document.addEventListener('click', function(e) {
        var emoji = e.target.closest('.picker-emoji');
        if (!emoji) return;
        e.stopPropagation();
        var reaction = emoji.getAttribute('data-reaction');
        var postId   = emoji.getAttribute('data-post-id');

        if (postId) {
            var card = document.getElementById('post-' + postId);
            if (!card) return;
            var fd = new FormData();
            fd.append('reaction_type', reaction);
            fetch('<?= site_url('posts/') ?>' + postId + '/react', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            }).then(function(r) { return r.json(); })
            .then(function(data) { if (data.ok) updatePostReactUI(card, data); })
            .catch(function(err) { console.error('Post reaction error:', err); });
        } else {
            var wrap    = emoji.closest('.comment-like-wrap');
            var likeBtn = wrap ? wrap.querySelector('.comment-like-btn') : null;
            var item    = emoji.closest('.comment-item');
            if (!item || !likeBtn) return;
            var commentId = likeBtn.dataset.commentId || likeBtn.getAttribute('data-comment-id');
            var prev      = likeBtn.getAttribute('data-current') || null;
            var newRx     = (prev === reaction) ? null : reaction;
            updateCommentUI(item, likeBtn, newRx, prev);
            sendCommentReact(commentId, reaction)
                .then(function(data) {
                    if (!data.ok) throw new Error('Server error');
                    renderCommentFromServer(item, likeBtn, data);
                })
                .catch(function(err) {
                    console.error('Comment reaction error:', err);
                    updateCommentUI(item, likeBtn, prev, newRx);
                });
        }
    });

    /* Unified .comment-like-btn click — routes to post or comment based on data-post-id */
    document.addEventListener('click', function(e) {
        if (e.target.closest('.picker-emoji')) return;
        var likeBtn = e.target.closest('.comment-like-btn');
        if (!likeBtn) return;
        var postId  = likeBtn.getAttribute('data-post-id');
        var current = likeBtn.getAttribute('data-current') || null;

        if (postId) {
            var reaction = current || 'like';
            var card     = document.getElementById('post-' + postId);
            if (!card) return;
            var fd = new FormData();
            fd.append('reaction_type', reaction);
            fetch('<?= site_url('posts/') ?>' + postId + '/react', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            }).then(function(r) { return r.json(); })
            .then(function(data) { if (data.ok) updatePostReactUI(card, data); })
            .catch(function(err) { console.error('Post like error:', err); });
        } else {
            var item = likeBtn.closest('.comment-item');
            if (!item) return;
            var commentId = likeBtn.dataset.commentId || likeBtn.getAttribute('data-comment-id');
            var reaction  = current || 'like';
            var newRx     = current ? null : 'like';
            updateCommentUI(item, likeBtn, newRx, current);
            sendCommentReact(commentId, reaction)
                .then(function(data) {
                    if (!data.ok) throw new Error('Server error');
                    renderCommentFromServer(item, likeBtn, data);
                })
                .catch(function(err) {
                    console.error('Comment like error:', err);
                    updateCommentUI(item, likeBtn, current, newRx);
                });
        }
    });

    /* ── AJAX Delete Post ── */
    document.querySelectorAll('.delete-post-form').forEach(function (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!confirm('Delete this post?')) return;
            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
                });
                const data = await resp.json();
                if (data.ok) {
                    const card = findCard(form);
                    if (card) card.remove();
                }
            } catch (err) {
                form.submit();
            }
        });
    });

    /* ── AJAX Reaction Submission ── */
    document.querySelectorAll('.react-bar form').forEach(function (form) {
        const reactionInput = form.querySelector('input[name="reaction_type"]');
        if (!reactionInput) return;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;

            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
                });
                const data = await resp.json();

                if (data.ok) {
                    const card = findCard(form);
                    if (!card) return;

                    // Update reaction pills with emojis
                    const reactionLine = card.querySelector('.reaction-line');
                    if (reactionLine) {
                        let pillsHtml = '';
                        const breakdown = data.reaction_breakdown || {};
                        for (const type in breakdown) {
                            pillsHtml += '<span class="mini-pill">' +
                                (emojiMap[type] || '') + ' ' + breakdown[type] + '</span>';
                        }
                        if (data.reaction_total === 0) {
                            pillsHtml = '<span class="summary-muted">No reactions yet</span>';
                        }
                        reactionLine.innerHTML = pillsHtml;
                    }

                    // Update active state on emoji buttons + bounce animation
                    const allReactForms = card.querySelectorAll('.react-bar form');
                    allReactForms.forEach(function (f) {
                        const ri = f.querySelector('input[name="reaction_type"]');
                        if (!ri) return;
                        const b = f.querySelector('button[type="submit"]');
                        if (data.viewer_reaction === ri.value) {
                            b.classList.add('active');
                            // Play bounce pop
                            b.classList.remove('reacting');
                            void b.offsetWidth; // restart animation
                            b.classList.add('reacting');
                        } else {
                            b.classList.remove('active', 'reacting');
                        }
                    });
                }
            } catch (err) {
                console.error('Reaction failed:', err);
                form.submit();
            } finally {
                btn.disabled = false;
            }
        });
    });
    /* ── Toast auto-dismiss ── */
    (function() {
        var toast = document.getElementById('toastAlert');
        if (toast) {
            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(function() { toast.remove(); }, 400);
            }, 5000);
        }
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
