<?php
$emojiMap   = ['like' => '👍', 'love' => '❤️', 'haha' => '😆', 'wow' => '😮', 'sad' => '😢', 'angry' => '😠'];
$emojiLabel = ['like' => 'Like', 'love' => 'Love', 'haha' => 'Haha', 'wow' => 'Wow', 'sad' => 'Sad', 'angry' => 'Angry'];
$rxColors   = ['like' => '#2078f4', 'love' => '#ed4956', 'haha' => '#f7b928', 'wow' => '#f7b928', 'sad' => '#f7b928', 'angry' => '#e9710f'];
$viewerRx   = $post['viewer_reaction'] ?? null;
$fbStatus   = (string) ($post['feedback_status'] ?? '');
$fbType     = (string) ($post['feedback_type'] ?? '');
$postIsAnon = (int) ($post['is_anonymous'] ?? 0) === 1;
// Strip "TypeName\n\n" prefix added by submitFeedback when type badge is shown
$displayBody = (string) $post['body'];
if ($fbType !== '') {
    $prefix = ucfirst($fbType) . "\n\n";
    if (str_starts_with($displayBody, $prefix)) {
        $displayBody = substr($displayBody, strlen($prefix));
    }
}
?>
<article class="feed-card" id="post-<?= (int) $post['id'] ?>">

    <div class="feed-head">
        <div class="avatar avatar-<?= esc((string) $post['avatar_color']) ?>"><?= esc((string) $post['initials']) ?></div>
        <div style="flex:1; min-width:0;">
            <?php if ($postIsAnon || empty($post['profile_url']) || empty($currentUser['id'])): ?>
                <span class="feed-author"><?= esc((string) $post['author_name']) ?></span>
            <?php else: ?>
                <a href="<?= esc((string) $post['profile_url']) ?>" class="feed-author"><?= esc((string) $post['author_name']) ?></a>
            <?php endif; ?>
            <div class="feed-meta">
                <span><?= esc(date('M d, Y h:i A', strtotime((string) $post['created_at']))) ?></span>
                <?php if ($fbType !== ''): ?>
                    <span class="pill type-<?= esc($fbType) ?>" style="font-size:0.7rem; padding:2px 9px; margin-left:2px;"><?= esc(ucfirst($fbType)) ?></span>
                <?php endif; ?>
                <?php if ($fbStatus === 'reviewed'): ?>
                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;color:#0a57a1;background:#e8f2ff;border:1px solid #b8d0ff;border-radius:20px;padding:2px 8px;margin-left:2px;">&#9679; Under Review</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="feed-body-text"><?= nl2br(esc($displayBody)) ?></div>

    <?php
    $fbImage = (string) ($post['feedback_image_path'] ?? '');
    if ($fbImage !== ''):
        $fbImageUrl = \App\Libraries\FeedbackImageStorage::publicUrl($fbImage);
    ?>
    <div class="feed-attachment">
        <img src="<?= esc($fbImageUrl) ?>" alt="" class="feed-attachment__img" loading="lazy" decoding="async">
    </div>
    <?php endif; ?>

    <div class="post-summary-row">
        <div class="reaction-line">
            <?php foreach ($post['reaction_breakdown'] as $rType => $total): ?>
                <span class="mini-pill"><?= $emojiMap[$rType] ?? '' ?> <?= (int) $total ?></span>
            <?php endforeach; ?>
            <?php if ((int) $post['reaction_total'] === 0): ?>
                <span class="summary-muted">No reactions yet</span>
            <?php endif; ?>
        </div>
        <span class="summary-muted"><?= (int) $post['comment_total'] ?> comment<?= (int) $post['comment_total'] !== 1 ? 's' : '' ?></span>
    </div>

    <div class="post-action-bar">
        <?php if (! empty($currentUser['id'])): ?>
            <span class="comment-like-wrap">
                <button type="button"
                    class="comment-like-btn post-react-trigger<?= $viewerRx ? ' reacted' : '' ?>"
                    data-post-id="<?= (int) $post['id'] ?>"
                    data-current="<?= esc((string) $viewerRx) ?>"
                    <?= ($viewerRx && isset($rxColors[$viewerRx])) ? 'style="color:' . $rxColors[$viewerRx] . '"' : '' ?>
                >
                    <?= $viewerRx
                        ? (($emojiMap[$viewerRx] ?? '👍') . ' ' . ($emojiLabel[$viewerRx] ?? ucfirst($viewerRx)))
                        : '👍 React' ?>
                </button>
                <div class="comment-reaction-picker">
                    <?php foreach (['like' => '👍', 'love' => '❤️', 'deslike' => '👎', 'shock' => '😮'] as $rType => $rEmoji): ?>
                        <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/react') ?>" style="margin:0;">
                            <input type="hidden" name="reaction_type" value="<?= esc($rType) ?>">
                            <button type="submit" class="picker-emoji" title="<?= esc(ucfirst($rType)) ?>"><?= $rEmoji ?></button>
                        </form>
                    <?php endforeach; ?>
                </div>
            </span>
            <span class="post-action-divider"></span>
            <span class="summary-muted post-comment-count"><?= (int) $post['comment_total'] ?> comment<?= (int) $post['comment_total'] !== 1 ? 's' : '' ?></span>
        <?php else: ?>
            <a href="<?= site_url('users/login') ?>" class="comment-like-btn">👍 Log in to react</a>
        <?php endif; ?>
    </div>

    <div class="comment-stack">
        <?php if (! empty($post['comments'])): ?>
            <div class="comment-list">
                <?php foreach ($post['comments'] as $comment): ?>
                    <?php
                    $crBreakdown = $comment['reaction_breakdown'] ?? [];
                    $crTotal     = array_sum($crBreakdown);
                    $viewerCRx   = $comment['viewer_reaction'] ?? null;
                    $crEmojiMap  = ['like' => '👍', 'love' => '❤️', 'haha' => '😆', 'wow' => '😮', 'sad' => '😢', 'angry' => '😠'];
                    $crColors    = ['like' => '#2078f4', 'love' => '#ed4956', 'haha' => '#f7b928', 'wow' => '#f7b928', 'sad' => '#f7b928', 'angry' => '#e9710f'];
                    ?>
                    <div class="comment-item" data-comment-id="<?= (int) $comment['id'] ?>">
                        <div class="avatar avatar-small avatar-<?= esc((string) ($comment['avatar_color'] ?? 'blue')) ?>"><?= esc(strtoupper(substr((string) $comment['author_name'], 0, 1))) ?></div>
                        <div class="comment-body-wrap">
                            <?php
                            $cImgUrl = ! empty($comment['image_path'])
                                ? \App\Libraries\CommentImageStorage::publicUrl((string) $comment['image_path'])
                                : '';
                            ?>
                            <div class="comment-bubble">
                                <strong><?= esc((string) $comment['author_name']) ?></strong>
                                <?php if (trim((string) ($comment['body'] ?? '')) !== ''): ?>
                                    <p><?= nl2br(esc((string) $comment['body'])) ?></p>
                                <?php endif; ?>
                                <?php if ($cImgUrl !== ''): ?>
                                    <div class="comment-attachment">
                                        <a href="<?= esc($cImgUrl) ?>" target="_blank" rel="noopener noreferrer" class="comment-attachment__link">
                                            <img src="<?= esc($cImgUrl) ?>" alt="" class="comment-attachment__img" loading="lazy" decoding="async">
                                        </a>
                                    </div>
                                <?php endif; ?>
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
                                            class="comment-like-btn<?= $viewerCRx ? ' reacted' : '' ?>"
                                            data-comment-id="<?= (int) $comment['id'] ?>"
                                            data-current="<?= esc((string) $viewerCRx) ?>"
                                            <?php if ($viewerCRx && isset($crColors[$viewerCRx])): ?>
                                                style="color: <?= $crColors[$viewerCRx] ?>"
                                            <?php endif; ?>
                                        ><?= $viewerCRx ? esc(ucfirst($viewerCRx)) : 'Like' ?></button>
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
                                        $rrTotal     = array_sum($rrBreakdown);
                                        $rrViewerRx  = $reply['viewer_reaction'] ?? null;
                                        ?>
                                        <div class="comment-item reply-item" data-comment-id="<?= (int) $reply['id'] ?>">
                                            <div class="avatar avatar-small avatar-<?= esc((string) ($reply['avatar_color'] ?? 'blue')) ?>"><?= esc(strtoupper(substr((string) $reply['author_name'], 0, 1))) ?></div>
                                            <div class="comment-body-wrap">
                                                <?php
                                                $rImgUrl = ! empty($reply['image_path'])
                                                    ? \App\Libraries\CommentImageStorage::publicUrl((string) $reply['image_path'])
                                                    : '';
                                                ?>
                                                <div class="comment-bubble">
                                                    <strong><?= esc((string) $reply['author_name']) ?></strong>
                                                    <?php if (trim((string) ($reply['body'] ?? '')) !== ''): ?>
                                                        <p><?= nl2br(esc((string) $reply['body'])) ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($rImgUrl !== ''): ?>
                                                        <div class="comment-attachment">
                                                            <a href="<?= esc($rImgUrl) ?>" target="_blank" rel="noopener noreferrer" class="comment-attachment__link">
                                                                <img src="<?= esc($rImgUrl) ?>" alt="" class="comment-attachment__img" loading="lazy" decoding="async">
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
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
            <form method="post" action="<?= site_url('posts/' . (int) $post['id'] . '/comment') ?>" class="comment-form" enctype="multipart/form-data">
                <input type="hidden" name="parent_id" value="0" class="comment-parent-id">
                <div class="reply-indicator" style="display:none;">
                    <span class="reply-to-text"></span>
                    <button type="button" class="cancel-reply-btn" title="Cancel reply">&times;</button>
                </div>
                <textarea name="body" rows="2" class="comment-body-input" placeholder="Write a comment..."></textarea>
                <label class="comment-image-label">
                    <span class="summary-muted">Image <span class="comment-image-hint">(optional, JPG/PNG/WebP, max 5 MB)</span></span>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="comment-image-input">
                </label>
                <label class="summary-muted"><input type="checkbox" name="is_anonymous" value="1" class="anon-check"> Comment anonymously</label>
                <button type="submit" class="solid-btn">Comment</button>
            </form>
        <?php else: ?>
            <p class="summary-muted"><a href="<?= site_url('users/login') ?>">Log in</a> to comment on this post.</p>
        <?php endif; ?>
    </div>
</article>
