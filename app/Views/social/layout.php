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
?>
    <link rel="stylesheet" href="<?= base_url('assets/student/social.css') . '?v=' . $socialCssVersion ?>">
</head>
<body>
<?php $currentUser = (array) ($currentUser ?? []); ?>
<div class="social-shell">
    <div class="social-main">
        <header class="social-topbar">
            <div>
                <h1><?= esc($title ?? 'CampusVoice') ?></h1>
                <p>Post updates, react, comment, and share what matters on campus.</p>
            </div>
            <div class="topbar-actions">
                <?php if (! empty($currentUser['id'])): ?>
                    <a href="<?= site_url('users') ?>" class="ghost-btn">Back</a>
                    <div class="admin-user">
                        <strong><?= esc((string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($currentUser['name'] ?? 'User'))) ?></strong>
                        <small><?= esc((string) ($currentUser['email'] ?? '')) ?></small>
                    </div>
                <?php else: ?>
                    <a href="<?= site_url('users/login?mode=register') ?>" class="ghost-btn">Join now</a>
                    <a href="<?= site_url('users/login') ?>" class="solid-btn">Log in</a>
                <?php endif; ?>
            </div>
        </header>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert success"><?= esc((string) session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert error"><?= esc((string) session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <main class="social-content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script>
    /* ── Copy Link buttons ── */
    document.querySelectorAll('[data-share-url]').forEach(function (button) {
        button.addEventListener('click', async function () {
            const url = button.getAttribute('data-share-url');
            try {
                await navigator.clipboard.writeText(url);
                button.textContent = 'Link Copied';
                setTimeout(function () {
                    button.textContent = 'Copy Link';
                }, 1600);
            } catch (error) {
                window.prompt('Copy this link:', url);
            }
        });
    });

    /* ── Helper: find the parent .feed-card for any element ── */
    function findCard(el) {
        return el.closest('.feed-card');
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
                    const item = document.createElement('div');
                    item.className = 'comment-item';
                    item.innerHTML =
                        '<div class="avatar avatar-small avatar-' + c.avatar_color + '">' + c.initial + '</div>' +
                        '<div><strong>' + c.author_name + '</strong>' +
                        '<p>' + c.body.replace(/\n/g, '<br>') + '</p></div>';

                    const stack = form.closest('.comment-stack');
                    let commentList = stack.querySelector('.comment-list');
                    if (!commentList) {
                        commentList = document.createElement('div');
                        commentList.className = 'comment-list';
                        stack.insertBefore(commentList, form);
                    }
                    commentList.appendChild(item);
                    commentList.scrollTop = commentList.scrollHeight;

                    // Update comment count in the summary row
                    const card = findCard(form);
                    if (card && data.comment_total !== undefined) {
                        const summaryEls = card.querySelectorAll('.summary-muted');
                        summaryEls.forEach(function (el) {
                            el.textContent = el.textContent.replace(
                                /\d+ comments/,
                                data.comment_total + ' comments'
                            );
                        });
                    }

                    form.querySelector('textarea').value = '';
                    const anonCheck = form.querySelector('input[name="is_anonymous"]');
                    if (anonCheck) anonCheck.checked = false;
                }
            } catch (err) {
                console.error('Comment failed:', err);
                form.submit(); // fallback to normal submit
            } finally {
                btn.disabled = false;
                btn.textContent = origText;
            }
        });
    });

    /* ── Emoji map for reaction pills ── */
    var emojiMap = { like: '👍', love: '❤️', support: '🤝', fire: '🔥' };

    /* ── AJAX Reaction Submission ── */
    document.querySelectorAll('.react-bar form').forEach(function (form) {
        const reactionInput = form.querySelector('input[name="reaction_type"]');
        if (!reactionInput) return; // skip share forms

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

    /* ── AJAX Share Submission ── */
    document.querySelectorAll('.react-bar form').forEach(function (form) {
        const reactionInput = form.querySelector('input[name="reaction_type"]');
        if (reactionInput) return; // skip reaction forms
        const shareBtn = form.querySelector('button[type="submit"]');
        if (!shareBtn || shareBtn.hasAttribute('data-share-url')) return;
        // Only target the Share button form (action contains /share)
        if (!form.action || !form.action.includes('/share')) return;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            shareBtn.disabled = true;
            const origText = shareBtn.textContent;
            shareBtn.textContent = 'Shared!';

            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
                });
                const data = await resp.json();

                if (data.ok) {
                    const card = findCard(form);
                    if (card && data.share_total !== undefined) {
                        const summaryEls = card.querySelectorAll('.summary-muted');
                        summaryEls.forEach(function (el) {
                            el.textContent = el.textContent.replace(
                                /\d+ shares/,
                                data.share_total + ' shares'
                            );
                        });
                    }
                }
            } catch (err) {
                console.error('Share failed:', err);
                form.submit();
            } finally {
                setTimeout(function () {
                    shareBtn.disabled = false;
                    shareBtn.textContent = origText;
                }, 1200);
            }
        });
    });
</script>
</body>
</html>