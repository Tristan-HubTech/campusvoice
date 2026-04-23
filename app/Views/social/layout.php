<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'CampusVoice') ?> | CampusVoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
<?php
$socialCss = FCPATH . 'assets/student/social.css';
$socialCssVersion = is_file($socialCss) ? (string) filemtime($socialCss) : '1';
$portalCss = FCPATH . 'assets/student/portal.css';
$portalCssVersion = is_file($portalCss) ? (string) filemtime($portalCss) : '1';
?>
    <link rel="stylesheet" href="<?= base_url('assets/student/portal.css') . '?v=' . $portalCssVersion ?>">
    <link rel="stylesheet" href="<?= base_url('assets/student/social.css') . '?v=' . $socialCssVersion ?>">
</head>
<body>
<?php $currentUser = (array) ($currentUser ?? []); ?>
<?php $currentTitle = (string) ($title ?? ''); ?>

<header class="portal-header">
    <div class="portal-header-inner">
        <a href="<?= site_url('users') ?>" class="portal-brand">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice" class="portal-logo">
            <span>CampusVoice</span>
        </a>

        <?php if (! empty($currentUser['id'])): ?>
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
                <span><?= esc((string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($currentUser['name'] ?? 'User'))) ?></span>
                <a href="<?= site_url('users/logout') ?>" class="logout-link">Logout</a>
            </div>
        <?php else: ?>
            <div class="topbar-actions">
                <a href="<?= site_url('users/login?mode=register') ?>" class="ghost-btn">Join now</a>
                <a href="<?= site_url('users/login') ?>" class="solid-btn">Log in</a>
            </div>
        <?php endif; ?>
    </div>
</header>

<div class="social-shell">
    <div class="social-main">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="toast-alert toast-success" id="toastAlert">
                <span class="toast-icon">✅</span>
                <span><?= esc((string) session()->getFlashdata('success')) ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="toast-alert toast-error" id="toastAlert">
                <span class="toast-icon">❌</span>
                <span><?= esc((string) session()->getFlashdata('error')) ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
            </div>
        <?php endif; ?>

        <main class="social-content">
            <?= $this->renderSection('content') ?>
        </main>

        <footer class="social-footer">
            <p>&copy; <?= date('Y') ?> CampusVoice — Student Portal</p>
        </footer>
    </div>
</div>

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
        return '<div class="' + cls + '" data-comment-id="' + c.id + '">' +
            '<div class="avatar avatar-small avatar-' + c.avatar_color + '">' + c.initial + '</div>' +
            '<div class="comment-body-wrap">' +
                '<div class="comment-bubble">' +
                    '<strong>' + c.author_name + '</strong>' +
                    '<p>' + c.body.replace(/\n/g, '<br>') + '</p>' +
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
    function updatePostReactUI(card, data) {
        var btn = card.querySelector('.post-react-btn');
        if (btn) {
            if (data.viewer_reaction) {
                btn.textContent = (emojiMap[data.viewer_reaction] || '👍') + ' ' + (rxLabelMap[data.viewer_reaction] || data.viewer_reaction);
                btn.style.color = rxColorMap[data.viewer_reaction] || '';
                btn.classList.add('reacted');
                btn.setAttribute('data-current', data.viewer_reaction);
            } else {
                btn.textContent = '👍 React';
                btn.style.color = '';
                btn.classList.remove('reacted');
                btn.setAttribute('data-current', '');
            }
        }
        var reactionLine = card.querySelector('.reaction-line');
        if (reactionLine) {
            var pills = '';
            var bd = data.reaction_breakdown || {};
            for (var t in bd) {
                if (bd[t] > 0) pills += '<span class="mini-pill">' + (emojiMap[t] || '') + ' ' + bd[t] + '</span>';
            }
            reactionLine.innerHTML = pills || '<span class="summary-muted">No reactions yet</span>';
        }
    }

    /* Emoji picker buttons — bound directly, no delegation */
    document.querySelectorAll('.post-emoji-btn').forEach(function(btn) {
        btn.addEventListener('click', async function() {
            var postId = btn.getAttribute('data-post-id');
            var reaction = btn.getAttribute('data-reaction');
            var card = document.getElementById('post-' + postId);
            if (!card) return;
            var fd = new FormData();
            fd.append('reaction_type', reaction);
            try {
                var resp = await fetch('<?= site_url('posts/') ?>' + postId + '/react', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd,
                });
                var data = await resp.json();
                if (data.ok) updatePostReactUI(card, data);
            } catch (err) { console.error('Post reaction error:', err); }
        });
    });

    /* Clicking the React button itself un-reacts (when already reacted) */
    document.querySelectorAll('.post-react-btn').forEach(function(btn) {
        btn.addEventListener('click', async function() {
            var current = btn.getAttribute('data-current');
            if (!current) return;
            var postId = btn.getAttribute('data-post-id');
            var card = document.getElementById('post-' + postId);
            if (!card) return;
            var fd = new FormData();
            fd.append('reaction_type', current);
            try {
                var resp = await fetch('<?= site_url('posts/') ?>' + postId + '/react', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd,
                });
                var data = await resp.json();
                if (data.ok) updatePostReactUI(card, data);
            } catch (err) { console.error('Post un-react error:', err); }
        });
    });

    /* ── AJAX Comment Reactions ── */
    var crEmojiMap = { like:'👍', love:'❤️', haha:'😆', wow:'😮', sad:'😢', angry:'😠' };
    var crColorMap = { like:'#2078f4', love:'#ed4956', haha:'#f7b928', wow:'#f7b928', sad:'#f7b928', angry:'#e9710f' };

    function sendCommentReaction(commentId, reaction) {
        var fd = new FormData();
        fd.append('reaction_type', reaction);
        return fetch('<?= site_url('comments/') ?>' + commentId + '/react', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
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

    /* Click a picker emoji → comment react */
    document.addEventListener('click', async function (e) {
        var emoji = e.target.closest('.picker-emoji');
        if (!emoji) return;
        e.preventDefault();
        var item = emoji.closest('.comment-item');
        if (!item) return;
        var commentId = item.getAttribute('data-comment-id');
        var reaction = emoji.getAttribute('data-reaction');
        try {
            var data = await sendCommentReaction(commentId, reaction);
            if (data.ok) updateCommentUI(item, data);
        } catch (err) { console.error('Comment reaction error:', err); }
    });

    /* Click the Like/reaction text on a comment → toggle */
    document.addEventListener('click', async function (e) {
        var likeBtn = e.target.closest('.comment-like-btn');
        if (!likeBtn) return;
        var item = likeBtn.closest('.comment-item');
        if (!item) return;
        var commentId = item.getAttribute('data-comment-id');
        var current = likeBtn.getAttribute('data-current');
        var reaction = current || 'like';
        try {
            var data = await sendCommentReaction(commentId, reaction);
            if (data.ok) updateCommentUI(item, data);
        } catch (err) { console.error('Comment like error:', err); }
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
</body>
</html>