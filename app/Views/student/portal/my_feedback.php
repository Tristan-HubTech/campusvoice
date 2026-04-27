<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page narrow">

    <!-- Hero Header -->
    <div class="fb-hero">
        <div class="fb-hero__icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </div>
        <div class="fb-hero__text">
            <h1 class="fb-hero__title">My Voice</h1>
            <p class="fb-hero__sub">Track statuses and read admin replies on each submission.</p>
        </div>
        <a href="<?= site_url('users/feedback/submit') ?>" class="fb-hero__cta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Share Feedback
        </a>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="fb-alert fb-alert--success"><?= esc(session('success')) ?></div>
    <?php endif ?>
    <?php if (session()->has('error')): ?>
        <div class="fb-alert fb-alert--error"><?= esc(session('error')) ?></div>
    <?php endif ?>

    <?php if (! empty($feedbackList)): ?>
        <div class="myfb-list">
            <?php $rowNum = 0; foreach ($feedbackList as $item):
                $rowNum++;
                $rowStatus = (string) ($item['status'] ?? 'pending');
                $ip        = (string) ($item['image_path'] ?? '');
                $iurl      = $ip !== '' ? \App\Libraries\FeedbackImageStorage::publicUrl($ip) : '';
                $rc        = (int) ($item['reply_count'] ?? 0);
                $typeMap   = ['complaint' => ['⚡','myfb-pill--complaint'], 'suggestion' => ['💡','myfb-pill--suggestion'], 'praise' => ['⭐','myfb-pill--praise']];
                $typeKey   = (string) ($item['type'] ?? 'suggestion');
                [$typeIcon, $typeClass] = $typeMap[$typeKey] ?? ['📝',''];
                $statusMap = [
                    'pending'  => ['Pending',      'myfb-status--pending'],
                    'approved' => ['Approved',     'myfb-status--approved'],
                    'rejected' => ['Rejected',     'myfb-status--rejected'],
                    'reviewed' => ['Under Review', 'myfb-status--reviewed'],
                    'resolved' => ['Resolved',     'myfb-status--resolved'],
                ];
                [$statusLabel, $statusClass] = $statusMap[$rowStatus] ?? [ucfirst($rowStatus), ''];
                $refNum = '#FBK-' . str_pad((string) (int) $item['id'], 4, '0', STR_PAD_LEFT);
            ?>
            <article class="myfb-card">
                <!-- Left stripe by type -->
                <div class="myfb-card__stripe myfb-stripe--<?= esc($typeKey) ?>"></div>

                <div class="myfb-card__inner">
                    <!-- Top row -->
                    <div class="myfb-card__top">
                        <div class="myfb-card__meta">
                            <span class="myfb-ref"><?= esc($refNum) ?></span>
                            <span class="myfb-pill <?= $typeClass ?>">
                                <?= $typeIcon ?> <?= esc(ucfirst($typeKey)) ?>
                            </span>
                            <span class="myfb-category"><?= esc((string) ($item['category_name'] ?? 'General')) ?></span>
                        </div>
                        <div class="myfb-card__right">
                            <span class="myfb-status <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
                        </div>
                    </div>

                    <!-- Message preview -->
                    <p class="myfb-card__message"><?= esc(mb_strimwidth((string) ($item['message'] ?? ''), 0, 180, '…')) ?></p>

                    <?php if ($rowStatus === 'rejected' && ! empty($item['rejection_reason'])): ?>
                        <div class="myfb-rejection">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            <span><strong>Reason:</strong> <?= esc((string) $item['rejection_reason']) ?></span>
                        </div>
                    <?php endif ?>

                    <!-- Footer row -->
                    <div class="myfb-card__footer">
                        <div class="myfb-card__foot-left">
                            <?php if ($iurl !== ''): ?>
                                <a href="<?= esc($iurl) ?>" target="_blank" rel="noopener noreferrer" class="myfb-thumb-link">
                                    <img src="<?= esc($iurl) ?>" alt="Attachment" class="myfb-thumb" loading="lazy">
                                </a>
                            <?php endif ?>
                            <span class="myfb-date">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <?= esc(date('M d, Y', strtotime((string) ($item['created_at'] ?? 'now')))) ?>
                            </span>
                            <?php if ($rc > 0): ?>
                                <span class="myfb-replies">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    <?= $rc ?> <?= $rc === 1 ? 'reply' : 'replies' ?>
                                </span>
                            <?php endif ?>
                        </div>
                        <div class="myfb-card__actions">
                            <a href="<?= site_url('users/feedback/' . (int) $item['id']) ?>" class="myfb-btn myfb-btn--view">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                            <button type="button" class="myfb-btn myfb-btn--delete"
                                data-delete-url="<?= site_url('users/feedback/' . (int) $item['id'] . '/delete') ?>">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <!-- Empty state -->
        <div class="myfb-empty">
            <div class="myfb-empty__icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <h3 class="myfb-empty__title">Nothing shared yet</h3>
            <p class="myfb-empty__sub">Your voice matters. Share your first feedback and help improve the campus.</p>
        </div>
    <?php endif ?>

</div>

<!-- Delete Confirmation Modal -->
<div class="myfb-modal-overlay" id="deleteModal">
    <div class="myfb-modal">
        <div class="myfb-modal__icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <h3 class="myfb-modal__title">Delete Feedback</h3>
        <p class="myfb-modal__text">Are you sure you want to delete this submission? This action cannot be undone.</p>
        <div class="myfb-modal__actions">
            <button type="button" class="myfb-modal__btn myfb-modal__btn--cancel" id="modalCancel">Cancel</button>
            <form method="post" id="deleteForm" style="margin:0;">
                <button type="submit" class="myfb-modal__btn myfb-modal__btn--confirm">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('deleteModal');
    var form    = document.getElementById('deleteForm');
    var cancel  = document.getElementById('modalCancel');

    document.querySelectorAll('.myfb-btn--delete[data-delete-url]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.action = btn.getAttribute('data-delete-url');
            overlay.classList.add('is-visible');
        });
    });

    cancel.addEventListener('click', function () { overlay.classList.remove('is-visible'); });
    overlay.addEventListener('click', function (e) { if (e.target === overlay) overlay.classList.remove('is-visible'); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') overlay.classList.remove('is-visible'); });
})();
</script>
<?= $this->endSection() ?>
