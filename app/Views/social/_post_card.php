<?php
/**
 * POST CARD PARTIAL
 * Displays a single feedback post with author, category, type, content, and reactions.
 * 
 * CONNECTS TO:
 * - Controller: Student\PortalController, SocialController
 * - CSS: public/css/social.css
 * - JS: reactions_script.php
 * - Database: feedback, comments, and reactions tables
 */

$emojiMap   = ['like' => "👍\u{FE0F}", 'love' => "❤\u{FE0F}", 'haha' => "😆\u{FE0F}", 'wow' => "😮\u{FE0F}", 'sad' => "😢\u{FE0F}", 'angry' => "😠\u{FE0F}"];
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

    <!-- ── Author & Meta ── Avatar, name, date, and status badges -->
    <?php $fbCategory = trim((string) ($post['category_name'] ?? '')); ?>
    <?php
    $statusLabelMap = [
        'reviewed'    => ['label' => 'Under Review', 'icon' => '🔍', 'cls' => 'fb-status-badge--reviewed'],
        'in_progress' => ['label' => 'In Progress',  'icon' => '🔧', 'cls' => 'fb-status-badge--progress'],
        'resolved'    => ['label' => 'Resolved',     'icon' => '✅', 'cls' => 'fb-status-badge--resolved'],
    ];
    ?>
    <div class="feed-head">
        <div class="avatar avatar-<?= esc((string) $post['avatar_color']) ?>"><?= esc((string) $post['initials']) ?></div>
        <div class="feed-info">
            <div class="feed-info-text">
                <?php if ($postIsAnon || empty($post['profile_url']) || empty($currentUser['id'])): ?>
                    <span class="feed-author"><?= esc((string) $post['author_name']) ?></span>
                <?php else: ?>
                    <a href="<?= esc((string) $post['profile_url']) ?>" class="feed-author"><?= esc((string) $post['author_name']) ?></a>
                <?php endif; ?>
                <span class="feed-date"><?= esc(date('M d, Y', strtotime((string) $post['created_at']))) ?></span>
            </div>
            <div class="feed-pills-col">
                <?php if ($fbCategory !== ''): ?>
                    <span class="pill pill-category"><?= esc($fbCategory) ?></span>
                <?php endif; ?>
                <?php if ($fbType !== ''): ?>
                    <span class="pill pill-type-<?= esc($fbType) ?>"><?= esc(ucfirst($fbType)) ?></span>
                <?php endif; ?>
                <?php if (isset($statusLabelMap[$fbStatus])): $sb = $statusLabelMap[$fbStatus]; ?>
                    <span class="fb-status-badge <?= $sb['cls'] ?>"><?= $sb['icon'] ?> <?= esc($sb['label']) ?></span>
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

    <!-- ── Reactions Summary ── Shows total counts and top emojis -->
    <div class="post-summary-row">
        <div class="post-reaction-summary">
            <span class="reaction-content" data-counts="<?= esc(json_encode($post['reaction_breakdown'])) ?>"><?php if ((int) $post['reaction_total'] === 0): ?>No reactions yet<?php else:
                $sortedRx = $post['reaction_breakdown']; arsort($sortedRx);
                foreach (array_slice($sortedRx, 0, 3, true) as $rType => $rCount):
                    if ($rCount > 0 && isset($emojiMap[$rType])): ?><span class="top-emoji"><?= $emojiMap[$rType] ?></span><?php endif;
                endforeach; ?><span class="top-count"><?= (int) $post['reaction_total'] ?></span><?php endif; ?></span>
        </div>
    </div>

    <div class="post-action-bar">
        <?php if (! empty($currentUser['id'])): ?>
            <div class="comment-like-wrap">
                <div class="comment-reaction-picker" role="tooltip" aria-label="React">
                    <?php foreach ($emojiMap as $rType => $rEmoji): ?>
                        <button type="button" class="picker-emoji"
                            data-post-id="<?= (int) $post['id'] ?>"
                            data-reaction="<?= esc($rType) ?>"
                            title="<?= esc($emojiLabel[$rType]) ?>"><?= $rEmoji ?></button>
                    <?php endforeach; ?>
                </div>
                <button type="button"
                    class="comment-like-btn<?= $viewerRx ? ' reacted' : '' ?>"
                    data-post-id="<?= (int) $post['id'] ?>"
                    data-current="<?= esc((string) $viewerRx) ?>"
                    <?= ($viewerRx && isset($rxColors[$viewerRx])) ? 'style="color:' . $rxColors[$viewerRx] . '"' : '' ?>
                ><span class="like-icon"><?= $viewerRx ? ($emojiMap[$viewerRx] ?? '👍') : '👍' ?></span
                ><span class="like-label"> <?= $viewerRx ? esc(ucfirst((string) $viewerRx)) : 'Like' ?></span></button>
            </div>
            <span class="summary-muted post-comment-count">💬 <?= (int) $post['comment_total'] ?> comment<?= (int) $post['comment_total'] !== 1 ? 's' : '' ?></span>
        <?php else: ?>
            <a href="<?= site_url('users/login') ?>" class="summary-muted">👍 Log in to react</a>
        <?php endif; ?>
    </div>

    <!-- ── Comment Section ── Shows list of comments and replies -->
    <div class="comment-stack">
        <?php if (! empty($post['comments'])): ?>
            <div class="comment-list">
                <?php foreach ($post['comments'] as $comment): ?>
                    <?php
                    $crBreakdown = $comment['reaction_breakdown'] ?? [];
                    $crTotal     = array_sum($crBreakdown);
                    $viewerCRx   = $comment['viewer_reaction'] ?? null;
                    $crEmojiMap  = ['like' => "👍\u{FE0F}", 'love' => "❤\u{FE0F}", 'haha' => "😆\u{FE0F}", 'wow' => "😮\u{FE0F}", 'sad' => "😢\u{FE0F}", 'angry' => "😠\u{FE0F}"];
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
                                    <span class="comment-reaction-badge" data-counts="<?= esc(json_encode($crBreakdown)) ?>"><?php if ($crTotal > 0): ?><?php foreach ($crBreakdown as $bType => $bCount): if ($bCount > 0 && isset($crEmojiMap[$bType])): ?><span class="badge-emoji"><?= $crEmojiMap[$bType] ?></span><?php endif; endforeach; ?><span class="badge-count"><?= $crTotal ?></span><?php endif; ?></span>
                            </div>
                            <div class="comment-actions">
                                <span class="comment-date"><?= esc(date('M d, Y h:i A', strtotime((string) $comment['created_at']))) ?></span>
                                <?php if (! empty($currentUser['id'])): ?>
                                    <span class="comment-like-wrap">
                                        <div class="comment-reaction-picker">
                                            <?php foreach ($crEmojiMap as $rType => $rEmoji): ?>
                                                <button type="button" class="picker-emoji" data-reaction="<?= esc($rType) ?>" title="<?= esc(ucfirst($rType)) ?>"><?= $rEmoji ?></button>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button"
                                            class="comment-like-btn<?= $viewerCRx ? ' reacted' : '' ?>"
                                            data-comment-id="<?= (int) $comment['id'] ?>"
                                            data-current="<?= esc((string) $viewerCRx) ?>"
                                            <?php if ($viewerCRx && isset($crColors[$viewerCRx])): ?>
                                                style="color: <?= $crColors[$viewerCRx] ?>"
                                            <?php endif; ?>
                                        ><span class="fb-like-icon"><?= $viewerCRx ? ($crEmojiMap[$viewerCRx] ?? '👍') : '👍' ?></span><span class="fb-like-label"><?= $viewerCRx ? esc(ucfirst((string) $viewerCRx)) : 'Like' ?></span></button>
                                    </span>
                                    <button type="button" class="comment-reply-btn" data-comment-id="<?= (int) $comment['id'] ?>" data-author="<?= esc((string) $comment['author_name']) ?>">↩ Reply</button>
                                <?php endif; ?>
                            </div>

                            <?php if (! empty($comment['replies'])): ?>
                                <?php $rCount = count($comment['replies']); ?>
                                <button type="button" class="reply-toggle-btn" data-count="<?= $rCount ?>" data-expanded="0">&mdash;&mdash; View <?= $rCount ?> <?= $rCount === 1 ? 'reply' : 'replies' ?></button>
                                <div class="reply-list" style="display:none">
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
                                                        <span class="comment-reaction-badge" data-counts="<?= esc(json_encode($rrBreakdown)) ?>"><?php if ($rrTotal > 0): ?><?php foreach ($rrBreakdown as $bType => $bCount): if ($bCount > 0 && isset($crEmojiMap[$bType])): ?><span class="badge-emoji"><?= $crEmojiMap[$bType] ?></span><?php endif; endforeach; ?><span class="badge-count"><?= $rrTotal ?></span><?php endif; ?></span>
                                                </div>
                                                <div class="comment-actions">
                                                    <span class="comment-date"><?= esc(date('M d, Y h:i A', strtotime((string) $reply['created_at']))) ?></span>
                                                    <?php if (! empty($currentUser['id'])): ?>
                                                        <span class="comment-like-wrap">
                                                            <div class="comment-reaction-picker">
                                                                <?php foreach ($crEmojiMap as $rType => $rEmoji): ?>
                                                                    <button type="button" class="picker-emoji" data-reaction="<?= esc($rType) ?>" title="<?= esc(ucfirst($rType)) ?>"><?= $rEmoji ?></button>
                                                                <?php endforeach; ?>
                                                            </div>
                                                            <button type="button"
                                                                class="comment-like-btn<?= $rrViewerRx ? ' reacted' : '' ?>"
                                                                data-comment-id="<?= (int) $reply['id'] ?>"
                                                                data-current="<?= esc((string) $rrViewerRx) ?>"
                                                                <?php if ($rrViewerRx && isset($crColors[$rrViewerRx])): ?>
                                                                    style="color: <?= $crColors[$rrViewerRx] ?>"
                                                                <?php endif; ?>
                                                            ><span class="fb-like-icon"><?= $rrViewerRx ? ($crEmojiMap[$rrViewerRx] ?? '👍') : '👍' ?></span><span class="fb-like-label"><?= $rrViewerRx ? esc(ucfirst((string) $rrViewerRx)) : 'Like' ?></span></button>
                                                        </span>
                                                        <button type="button" class="comment-reply-btn" data-comment-id="<?= (int) $comment['id'] ?>" data-author="<?= esc((string) $reply['author_name']) ?>">↩ Reply</button>
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

        <!-- ── Add Comment Form ── Input for new comments and images -->
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
