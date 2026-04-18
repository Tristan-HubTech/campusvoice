<article class="feed-card" id="post-<?= (int) $post['id'] ?>">
    <div class="feed-head">
        <div class="avatar avatar-<?= esc((string) $post['avatar_color']) ?>"><?= esc((string) $post['initials']) ?></div>
        <div>
            <?php if ((int) ($post['is_anonymous'] ?? 0) === 1 || empty($post['profile_url']) || empty($currentUser['id'])): ?>
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
            $emojiMap = ['like' => '👍', 'love' => '❤️', 'deslike' => '👎', 'shock' => '😮'];
            foreach ($post['reaction_breakdown'] as $reactionType => $total): ?>
                <span class="mini-pill"><?= $emojiMap[$reactionType] ?? '' ?> <?= (int) $total ?></span>
            <?php endforeach; ?>
            <?php if ((int) $post['reaction_total'] === 0): ?>
                <span class="summary-muted">No reactions yet</span>
            <?php endif; ?>
        </div>
        <div class="summary-muted"><?= (int) $post['comment_total'] ?> comments</div>
    </div>

    <div class="react-bar">
        <?php if (! empty($currentUser['id'])): ?>
            <?php
            $emojis = ['like' => '👍', 'love' => '❤️', 'deslike' => '👎', 'shock' => '😮'];
            foreach ($emojis as $reactionType => $emoji): ?>
                <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/react') ?>">
                    <input type="hidden" name="reaction_type" value="<?= esc($reactionType) ?>">
                    <button type="submit" class="react-btn <?= ($post['viewer_reaction'] ?? null) === $reactionType ? 'active' : '' ?>" title="<?= esc(ucfirst($reactionType)) ?>"><?= $emoji ?></button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <a href="<?= site_url('users/login') ?>" class="react-btn react-login">Log in to react</a>
        <?php endif; ?>
    </div>

    <div class="comment-stack">
        <?php if (! empty($post['comments'])): ?>
            <div class="comment-list">
                <?php foreach ($post['comments'] as $comment): ?>
                    <?php
                    $crBreakdown = $comment['reaction_breakdown'] ?? [];
                    $crTotal = array_sum($crBreakdown);
                    $viewerRx = $comment['viewer_reaction'] ?? null;
                    $crEmojiMap = ['like'=>'👍','love'=>'❤️','haha'=>'😆','wow'=>'😮','sad'=>'😢','angry'=>'😠'];
                    $crColors  = ['like'=>'#2078f4','love'=>'#ed4956','haha'=>'#f7b928','wow'=>'#f7b928','sad'=>'#f7b928','angry'=>'#e9710f'];
                    ?>
                    <div class="comment-item" data-comment-id="<?= (int) $comment['id'] ?>">
                        <div class="avatar avatar-small avatar-<?= esc((string) ($comment['avatar_color'] ?? 'blue')) ?>"><?= esc(strtoupper(substr((string) $comment['author_name'], 0, 1))) ?></div>
                        <div class="comment-body-wrap">
                            <div class="comment-bubble">
                                <strong><?= esc((string) $comment['author_name']) ?></strong>
                                <p><?= nl2br(esc((string) $comment['body'])) ?></p>
                                <?php if ($crTotal > 0): ?>
                                    <span class="comment-reaction-badge">
                                        <?php foreach ($crBreakdown as $bType => $bCount):
                                            if ($bCount > 0 && isset($crEmojiMap[$bType])): ?>
                                                <span class="badge-emoji"><?= $crEmojiMap[$bType] ?></span>
                                            <?php endif;
                                        endforeach; ?>
                                        <span class="badge-count"><?= $crTotal ?></span>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="comment-actions">
                                <span class="comment-date"><?= esc(date('M d, Y h:i A', strtotime((string) $comment['created_at']))) ?></span>
                                <?php if (! empty($currentUser['id'])): ?>
                                    <span class="comment-like-wrap">
                                        <button type="button"
                                            class="comment-like-btn<?= $viewerRx ? ' reacted' : '' ?>"
                                            data-comment-id="<?= (int) $comment['id'] ?>"
                                            data-current="<?= esc((string) $viewerRx) ?>"
                                            <?php if ($viewerRx && isset($crColors[$viewerRx])): ?>
                                                style="color: <?= $crColors[$viewerRx] ?>"
                                            <?php endif; ?>
                                        ><?= $viewerRx ? esc(ucfirst($viewerRx)) : 'Like' ?></button>
                                        <div class="comment-reaction-picker">
                                            <?php foreach ($crEmojiMap as $rType => $rEmoji): ?>
                                                <button type="button" class="picker-emoji" data-reaction="<?= esc($rType) ?>" title="<?= esc(ucfirst($rType)) ?>"><?= $rEmoji ?></button>
                                            <?php endforeach; ?>
                                        </div>
                                    </span>
                                    <button type="button" class="comment-reply-btn" data-comment-id="<?= (int) $comment['id'] ?>" data-author="<?= esc((string) $comment['author_name']) ?>">Reply</button>
                                <?php endif; ?>
                            </div>

                            <?php if (! empty($comment['replies'])): ?>
                                <div class="reply-list">
                                    <?php foreach ($comment['replies'] as $reply): ?>
                                        <?php
                                        $rrBreakdown = $reply['reaction_breakdown'] ?? [];
                                        $rrTotal = array_sum($rrBreakdown);
                                        $rrViewerRx = $reply['viewer_reaction'] ?? null;
                                        ?>
                                        <div class="comment-item reply-item" data-comment-id="<?= (int) $reply['id'] ?>">
                                            <div class="avatar avatar-small avatar-<?= esc((string) ($reply['avatar_color'] ?? 'blue')) ?>"><?= esc(strtoupper(substr((string) $reply['author_name'], 0, 1))) ?></div>
                                            <div class="comment-body-wrap">
                                                <div class="comment-bubble">
                                                    <strong><?= esc((string) $reply['author_name']) ?></strong>
                                                    <p><?= nl2br(esc((string) $reply['body'])) ?></p>
                                                    <?php if ($rrTotal > 0): ?>
                                                        <span class="comment-reaction-badge">
                                                            <?php foreach ($rrBreakdown as $bType => $bCount):
                                                                if ($bCount > 0 && isset($crEmojiMap[$bType])): ?>
                                                                    <span class="badge-emoji"><?= $crEmojiMap[$bType] ?></span>
                                                                <?php endif;
                                                            endforeach; ?>
                                                            <span class="badge-count"><?= $rrTotal ?></span>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="comment-actions">
                                                    <span class="comment-date"><?= esc(date('M d, Y h:i A', strtotime((string) $reply['created_at']))) ?></span>
                                                    <?php if (! empty($currentUser['id'])): ?>
                                                        <span class="comment-like-wrap">
                                                            <button type="button"
                                                                class="comment-like-btn<?= $rrViewerRx ? ' reacted' : '' ?>"
                                                                data-comment-id="<?= (int) $reply['id'] ?>"
                                                                data-current="<?= esc((string) $rrViewerRx) ?>"
                                                                <?php if ($rrViewerRx && isset($crColors[$rrViewerRx])): ?>
                                                                    style="color: <?= $crColors[$rrViewerRx] ?>"
                                                                <?php endif; ?>
                                                            ><?= $rrViewerRx ? esc(ucfirst($rrViewerRx)) : 'Like' ?></button>
                                                            <div class="comment-reaction-picker">
                                                                <?php foreach ($crEmojiMap as $rType => $rEmoji): ?>
                                                                    <button type="button" class="picker-emoji" data-reaction="<?= esc($rType) ?>" title="<?= esc(ucfirst($rType)) ?>"><?= $rEmoji ?></button>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (! empty($currentUser['id'])): ?>
            <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/comment') ?>" class="comment-form">
                <input type="hidden" name="parent_id" value="0" class="comment-parent-id">
                <div class="reply-indicator" style="display:none;">
                    <span class="reply-to-text"></span>
                    <button type="button" class="cancel-reply-btn" title="Cancel reply">&times;</button>
                </div>
                <textarea name="body" rows="2" placeholder="Write a comment..."></textarea>
                <label class="summary-muted"><input type="checkbox" name="is_anonymous" value="1" class="anon-check"> Comment anonymously</label>
                <button type="submit" class="solid-btn">Comment</button>
            </form>
        <?php else: ?>
            <p class="summary-muted"><a href="<?= site_url('users/login') ?>">Log in</a> to comment on this post.</p>
        <?php endif; ?>
    </div>
</article>