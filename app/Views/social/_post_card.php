<article class="feed-card" id="post-<?= (int) $post['id'] ?>">
    <div class="feed-head">
        <div class="avatar avatar-<?= esc((string) $post['avatar_color']) ?>"><?= esc((string) $post['initials']) ?></div>
        <div>
            <?php if ((int) ($post['is_anonymous'] ?? 0) === 1 || empty($post['profile_url'])): ?>
                <span class="feed-author"><?= esc((string) $post['author_name']) ?></span>
            <?php else: ?>
                <a href="<?= esc((string) $post['profile_url']) ?>" class="feed-author"><?= esc((string) $post['author_name']) ?></a>
            <?php endif; ?>
            <div class="feed-meta">
                <span><?= esc(date('M d, Y h:i A', strtotime((string) $post['created_at']))) ?></span>
            </div>
        </div>
    </div>

    <div class="feed-body-text"><?= nl2br(esc((string) $post['body'])) ?></div>

    <div class="post-summary-row">
        <div class="reaction-line">
            <?php
            $emojiMap = ['like' => '👍', 'love' => '❤️', 'support' => '🤝', 'fire' => '🔥'];
            foreach ($post['reaction_breakdown'] as $reactionType => $total): ?>
                <span class="mini-pill"><?= $emojiMap[$reactionType] ?? '' ?> <?= (int) $total ?></span>
            <?php endforeach; ?>
            <?php if ((int) $post['reaction_total'] === 0): ?>
                <span class="summary-muted">No reactions yet</span>
            <?php endif; ?>
        </div>
        <div class="summary-muted"><?= (int) $post['comment_total'] ?> comments · <?= (int) $post['share_total'] ?> shares</div>
    </div>

    <div class="react-bar">
        <?php if (! empty($currentUser['id'])): ?>
            <?php
            $emojis = ['like' => '👍', 'love' => '❤️', 'support' => '🤝', 'fire' => '🔥'];
            foreach ($emojis as $reactionType => $emoji): ?>
                <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/react') ?>">
                    <input type="hidden" name="reaction_type" value="<?= esc($reactionType) ?>">
                    <button type="submit" class="react-btn <?= ($post['viewer_reaction'] ?? null) === $reactionType ? 'active' : '' ?>" title="<?= esc(ucfirst($reactionType)) ?>"><?= $emoji ?></button>
                </form>
            <?php endforeach; ?>
            <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/share') ?>">
                <button type="submit" class="react-btn react-share" title="Share">↗</button>
            </form>
        <?php else: ?>
            <a href="<?= site_url('users/login') ?>" class="react-btn react-login">Log in to react</a>
        <?php endif; ?>
    </div>

    <div class="comment-stack">
        <?php if (! empty($post['comments'])): ?>
            <div class="comment-list">
                <?php foreach ($post['comments'] as $comment): ?>
                    <div class="comment-item">
                        <div class="avatar avatar-small avatar-<?= esc((string) ($comment['avatar_color'] ?? 'blue')) ?>"><?= esc(strtoupper(substr((string) $comment['author_name'], 0, 1))) ?></div>
                        <div>
                            <strong><?= esc((string) $comment['author_name']) ?></strong>
                            <p><?= nl2br(esc((string) $comment['body'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (! empty($currentUser['id'])): ?>
            <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/comment') ?>" class="comment-form">
                <textarea name="body" rows="2" placeholder="Write a comment..."></textarea>
                <label class="summary-muted"><input type="checkbox" name="is_anonymous" value="1"> Comment anonymously</label>
                <button type="submit" class="solid-btn">Comment</button>
            </form>
        <?php else: ?>
            <p class="summary-muted"><a href="<?= site_url('users/login') ?>">Log in</a> to comment on this post.</p>
        <?php endif; ?>
    </div>
</article>