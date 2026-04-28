<?php
/**
 * HOME PAGE
 * Shows welcome banner, announcements list, and recent community feed.
 * 
 * CONNECTS TO:
 * - Controller: Student\PortalController
 * - View Partials: social/_post_card.php, student/layout.php
 * - CSS: portal.css, social.css
 */
?>
<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="home-feed">

    <!-- ── Welcome hero ── Shows user name and 'Share Feedback' button -->
    <div class="home-hero">
        <div class="home-hero__left">
            <div class="home-hero__avatar">
                <?= esc(strtoupper(substr((string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($studentUser['name'] ?? 'S')), 0, 1))) ?>
            </div>
            <div>
                <?php $greeting = ! empty($studentUser['is_new_user']) ? 'Welcome,' : 'Welcome back,'; ?>
            <h2 class="home-hero__title"><?= $greeting ?> <?= esc((string) (! empty($isAnonymous) ? ($anonAlias ?? 'Anonymous') : ($studentUser['name'] ?? 'Student'))) ?>!</h2>
                <p class="home-hero__sub">Your campus voice matters.</p>
            </div>
        </div>
        <a href="<?= site_url('users/feedback/submit') ?>" class="fb-hero__cta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Share Feedback
        </a>
    </div>

    <?php if (!empty($announcements)): ?>
    <section class="home-announce">
        <div class="home-section-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 8.5c0 2.5-3 4-3 4v3l-5-2H7a4 4 0 0 1 0-8h7c0 0 8 .5 8 3z"/></svg>
            Announcements
        </div>
        <div class="home-announce__list">
            <?php foreach ($announcements as $ann):
                $isPinned = (int)($ann['pinned'] ?? 0) === 1;
                $annBody  = (string) ($ann['body'] ?? '');
                $annTitle = (string) ($ann['title'] ?? 'Untitled');
                $annDate  = (string) ($ann['created_at'] ?? 'now');
            ?>
            <div class="h-ann-card<?= $isPinned ? ' is-pinned' : '' ?>">
                <div class="h-ann-card__stripe"></div>
                <div class="h-ann-card__body">
                    <div class="h-ann-card__head">
                        <h4 class="h-ann-card__title">
                            <?= esc($annTitle) ?>
                        </h4>
                        <?php if ($isPinned): ?>
                            <span class="h-ann-badge h-ann-badge--pinned">📌 Pinned</span>
                        <?php endif; ?>
                    </div>
                    <div style="display: flex; gap: 12px; margin-bottom: 8px;">
                        <div class="h-ann-card__content h-ann-body-clamp" style="flex: 1; margin-bottom: 0;"><?= nl2br(esc($annBody)) ?></div>
                        <?php
                        $annImgPath = (string) ($ann['image_path'] ?? '');
                        if ($annImgPath !== ''): 
                            $annImgUrl = App\Libraries\FeedbackImageStorage::publicUrl($annImgPath);
                        ?>
                            <div class="h-ann-card__thumb">
                                <img src="<?= esc($annImgUrl) ?>" alt="Attachment" loading="lazy">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="h-ann-card__footer">
                        <div class="h-ann-card__meta-left" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <span class="h-ann-card__date">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                <?= esc(date('M d, Y g:i A', strtotime($annDate))) ?>
                            </span>
                        </div>
                        <a href="<?= site_url('users/announcements') ?>" class="h-ann-read-link">Read more →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="<?= site_url('users/announcements') ?>" class="home-announce__view-all-gold">View All Announcements →</a>
    </section>
    <?php endif; ?>

    <section class="home-feed-section">
        <div class="home-section-label">
            <span>🌐</span> Community Feed
        </div>
        <?php if (empty($posts)): ?>
            <section class="panel-card empty-card">
                <h2>No posts yet</h2>
                <p>The feed is ready. Publish the first update and start the conversation.</p>
            </section>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?= view('social/_post_card', ['post' => $post, 'currentUser' => $currentUser]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</div>
<?= $this->endSection() ?>
