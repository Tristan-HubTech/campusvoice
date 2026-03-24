<article class="feed-card" id="post-<?= (int) $post['id'] ?>">
    <div class="feed-head">
        <div class="avatar avatar-<?= esc((string) $post['avatar_color']) ?>"><?= esc((string) $post['initials']) ?></div>
        <div>
            <a href="<?= esc((string) $post['profile_url']) ?>" class="feed-author"><?= esc((string) $post['author_name']) ?></a>
            <div class="feed-meta">
                <span><?= esc(date('M d, Y h:i A', strtotime((string) $post['created_at']))) ?></span>
                <span>&middot;</span>
                <a href="<?= esc((string) $post['permalink']) ?>">View post</a>
            </div>
        </div>
    </div>

    <div class="feed-body-text"><?= nl2br(esc((string) $post['body'])) ?></div>

    <div class="post-summary-row">
        <div class="reaction-line">
            <?php foreach ($post['reaction_breakdown'] as $reactionType => $total): ?>
                <span class="mini-pill"><?= esc(ucfirst((string) $reactionType)) ?> <?= (int) $total ?></span>
            <?php endforeach; ?>
            <?php if ((int) $post['reaction_total'] === 0): ?>
                <span class="summary-muted">No reactions yet</span>
            <?php endif; ?>
        </div>
        <div class="summary-muted"><?= (int) $post['comment_total'] ?> comments · <?= (int) $post['share_total'] ?> shares</div>
    </div>

    <div class="post-actions-grid">
        <?php if (! empty($currentUser['id'])): ?>
            <?php foreach (['like', 'love', 'support', 'fire'] as $reactionType): ?>
                <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/react') ?>">
                    <input type="hidden" name="reaction_type" value="<?= esc($reactionType) ?>">
                    <button type="submit" class="action-btn <?= ($post['viewer_reaction'] ?? null) === $reactionType ? 'active' : '' ?>"><?= esc(ucfirst($reactionType)) ?></button>
                </form>
            <?php endforeach; ?>
            <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/share') ?>">
                <button type="submit" class="action-btn">Share</button>
            </form>
            <button type="button" class="action-btn" data-share-url="<?= esc((string) $post['permalink']) ?>">Copy Link</button>
        <?php else: ?>
            <a href="<?= site_url('users/login') ?>" class="action-btn link-btn">Log in to react</a>
            <button type="button" class="action-btn" data-share-url="<?= esc((string) $post['permalink']) ?>">Copy Link</button>
        <?php endif; ?>
    </div>

    <div class="comment-stack">
        <?php if (! empty($post['comments'])): ?>
            <?php foreach ($post['comments'] as $comment): ?>
                <div class="comment-item">
                    <div class="avatar avatar-small avatar-<?= esc((string) ($comment['avatar_color'] ?? 'blue')) ?>"><?= esc(strtoupper(substr((string) $comment['author_name'], 0, 1))) ?></div>
                    <div>
                        <strong><?= esc((string) $comment['author_name']) ?></strong>
                        <p><?= nl2br(esc((string) $comment['body'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ((int) $post['comment_total'] > count($post['comments'])): ?>
            <p class="summary-muted more-comments">Showing latest comments only.</p>
        <?php endif; ?>

        <?php if (! empty($currentUser['id'])): ?>
            <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/comment') ?>" class="comment-form">
                <textarea name="body" rows="2" placeholder="Write a comment..."></textarea>
                <button type="submit" class="solid-btn">Comment</button>
            </form>
        <?php else: ?>
            <p class="summary-muted"><a href="<?= site_url('users/login') ?>">Log in</a> to comment on this post.</p>
        <?php endif; ?>
    </div>
</article>