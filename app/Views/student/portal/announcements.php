<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php use App\Libraries\FeedbackImageStorage; ?>
<div class="portal-page narrow">

    <!-- Hero Header -->
    <div class="announce-hero">
        <div class="announce-hero__icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 8.5c0 2.5-3 4-3 4v3l-5-2H7a4 4 0 0 1 0-8h7c0 0 8 0.5 8 3z"/>
                <path d="M19 12.5v2.5l-5-2"/>
                <circle cx="7" cy="16" r="2"/>
            </svg>
        </div>
        <div class="announce-hero__text">
            <h1 class="announce-hero__title">Campus Announcements</h1>
            <p class="announce-hero__sub">Official updates and notices from the administration</p>
        </div>
    </div>

    <!-- Card List -->
    <div class="announce-stack">
        <?php if (! empty($announcements)): ?>
            <?php foreach ($announcements as $ann):
                $title       = trim((string) ($ann['title'] ?? 'Untitled'));
                $body        = (string) ($ann['body'] ?? '');
                $publishTime = (string) ($ann['publish_at'] ?? $ann['created_at'] ?? 'now');
                $createdAt   = (string) ($ann['created_at'] ?? 'now');
                $isNew       = strtotime($createdAt) > strtotime('-3 days');

                $authorFirst = trim((string) ($ann['author_first_name'] ?? ''));
                $authorLast  = trim((string) ($ann['author_last_name'] ?? ''));
                $authorName  = trim($authorFirst . ' ' . $authorLast);
                if ($authorName === '') { $authorName = 'System Admin'; }

                $initials  = strtoupper(substr($authorName, 0, 1));
                $annId     = (int) ($ann['id'] ?? 0);
                $isLongBody = mb_strlen($body) > 200 || substr_count($body, "\n") > 3;
            ?>
            <article class="ann-card">
                <div class="ann-card__stripe"></div>
                <div class="ann-card__body">
                    <div class="ann-card__top">
                        <div class="ann-card__meta-left">
                            <div class="ann-card__avatar"><?= esc($initials) ?></div>
                            <div>
                                <div class="ann-card__author"><?= esc($authorName) ?></div>
                                <div class="ann-card__date">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    <?= esc(date('M d, Y · g:i A', strtotime($publishTime))) ?>
                                </div>
                            </div>
                        </div>
                        <div class="ann-card__badges">
                            <span class="ann-badge ann-badge--general">General</span>
                            <?php if ($isNew): ?>
                                <span class="ann-badge ann-badge--new">New</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h2 class="ann-card__title" style="word-break:break-word; overflow-wrap:anywhere;"><?= esc($title) ?></h2>

                    <?php
                    $annImgPath = (string) ($ann['image_path'] ?? '');
                    $annImgUrl  = $annImgPath !== '' ? FeedbackImageStorage::publicUrl($annImgPath) : '';
                    if ($annImgUrl !== ''): ?>
                    <div class="ann-card__image">
                        <img src="<?= esc($annImgUrl) ?>" alt="<?= esc($title) ?>" loading="lazy">
                    </div>
                    <?php endif; ?>

                    <div class="ann-card__content ann-body-clamp<?= $isLongBody ? ' is-clamped' : '' ?>"
                         id="ann-body-<?= $annId ?>"
                         style="word-break:break-word; overflow-wrap:anywhere;">
                        <?= nl2br(esc($body)) ?>
                    </div>

                    <?php if ($isLongBody): ?>
                    <button type="button"
                            class="ann-read-more-btn"
                            data-target="ann-body-<?= $annId ?>"
                            onclick="
                                var el = document.getElementById(this.dataset.target);
                                var collapsed = el.classList.toggle('is-clamped');
                                this.textContent = collapsed ? 'Read more ▾' : 'Collapse ▴';
                            ">Read more ▾</button>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ann-empty">
                <div class="ann-empty__icon">📭</div>
                <h3>No announcements yet</h3>
                <p>Check back later for updates from the administration.</p>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
