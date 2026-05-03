<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
$cs      = (string) $feedback['status'];
$fbType  = (string) ($feedback['type'] ?? '');
$rowNum  = isset($feedback['row_num']) ? (int)$feedback['row_num'] : (int)$feedback['id'];
$isAnon  = (int) ($feedback['is_anonymous'] ?? 0) === 1;
$aimg    = (string) ($feedback['image_path'] ?? '');
$aurl    = $aimg !== '' ? \App\Libraries\FeedbackImageStorage::publicUrl($aimg) : '';

$typeLabels   = ['complaint' => 'Complaint', 'suggestion' => 'Suggestion', 'praise' => 'Praise'];
$statusLabels = ['pending' => 'Pending', 'approved' => 'Approved', 'reviewed' => 'Under Review', 'resolved' => 'Resolved', 'rejected' => 'Rejected'];
$statusLabelMap = [
    'rejected'    => ['label' => 'Reject Feedback',   'desc' => 'Please provide a reason — this will be shared with the student.'],
    'reviewed'    => ['label' => 'Mark as Reviewed',  'desc' => 'Optionally add a message for the student about this review.'],
    'resolved'    => ['label' => 'Mark as Resolved',  'desc' => 'Optionally confirm to the student that this issue has been resolved.'],
];
?>

<div class="fd-page">

    <!-- ── Page Header ─────────────────────────────────────── -->
    <div class="fd-header">
        <a href="<?= site_url('admin/feedback') ?>" class="fd-back">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Back to Queue
        </a>
        <div class="fd-header-body">
            <div class="fd-header-left">
                <div class="fd-id-row">
                    <h1 class="fd-heading">Feedback <span class="fd-num">#<?= $rowNum ?></span></h1>
                </div>
                <p class="fd-submitted">
                    Submitted <?= esc(date('F j, Y \a\t g:i A', strtotime((string) ($feedback['created_at'] ?? 'now')))) ?>
                    <?php if (!empty($feedback['category_name'])): ?> &mdash; <span class="fd-category"><?= esc((string) $feedback['category_name']) ?></span><?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- ── Main Grid ───────────────────────────────────────── -->
    <div class="fd-grid">

        <!-- Left: Content -->
        <div class="fd-main">

            <!-- Subject + Message -->
            <div class="fd-card">
                <div class="fd-card-section">
                    <p class="fd-field-label">Subject</p>
                    <p class="fd-subject-text"><?= esc((string) ($feedback['subject'] ?? 'No subject')) ?></p>
                </div>
                <div class="fd-divider"></div>
                <div class="fd-card-section">
                    <p class="fd-field-label">Message</p>
                    <div class="fd-message-body"><?= nl2br(esc((string) $feedback['message'])) ?></div>
                </div>
                <?php if ($aurl !== ''): ?>
                <div class="fd-divider"></div>
                <div class="fd-card-section">
                    <p class="fd-field-label">Attachment</p>
                    <a href="<?= esc($aurl) ?>" target="_blank" rel="noopener noreferrer" class="fd-attachment-link">
                        <img src="<?= esc($aurl) ?>" alt="Attached image" class="fd-attachment-img" loading="lazy" decoding="async">
                        <span class="fd-attachment-hint">Click to open full size</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Reply Thread -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <h2 class="fd-card-title">Admin Replies</h2>
                    <span class="fd-reply-count"><?= count($replies ?? []) ?> <?= count($replies ?? []) === 1 ? 'reply' : 'replies' ?></span>
                </div>

                <?php if (!empty($replies)): ?>
                <div class="fd-thread">
                    <?php foreach ($replies as $reply):
                        $rName = trim(((string)($reply['first_name'] ?? '')) . ' ' . ((string)($reply['last_name'] ?? '')));
                        $rInitial = strtoupper(substr($rName ?: 'A', 0, 1));
                    ?>
                    <div class="fd-reply">
                        <div class="fd-reply-avatar"><?= esc($rInitial) ?></div>
                        <div class="fd-reply-body">
                            <div class="fd-reply-meta">
                                <span class="fd-reply-name"><?= esc($rName ?: 'Admin') ?></span>
                                <span class="fd-reply-time"><?= esc(date('M d, Y · g:i A', strtotime((string)($reply['created_at'] ?? 'now')))) ?></span>
                            </div>
                            <div class="fd-reply-text"><?= nl2br(esc((string)$reply['message'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="fd-no-replies">No replies yet. Be the first to respond.</p>
                <?php endif; ?>

                <form method="post" action="<?= site_url('admin/feedback/' . (int)$feedback['id'] . '/reply') ?>" class="fd-reply-form">
                    <p class="fd-field-label">Write a Reply</p>
                    <textarea name="message" rows="4" required placeholder="Type your response to this feedback…" class="fd-textarea"></textarea>
                    <div class="fd-reply-form-footer">
                        <button type="submit" class="fd-btn fd-btn--primary">Post Reply</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="fd-sidebar">

            <!-- Student -->
            <div class="fd-card fd-card--compact">
                <p class="fd-field-label">Submitted by</p>
                <?php if ($isAnon): ?>
                    <div class="fd-student-anon">
                        <div class="fd-student-avatar fd-student-avatar--anon">?</div>
                        <div>
                            <p class="fd-student-name">Anonymous</p>
                            <p class="fd-student-email">Identity hidden</p>
                        </div>
                    </div>
                <?php else:
                    $stuName = esc(trim(((string)($feedback['first_name'] ?? '')) . ' ' . ((string)($feedback['last_name'] ?? ''))));
                    $stuInitial = strtoupper(substr($stuName ?: 'S', 0, 1));
                ?>
                    <div class="fd-student-row">
                        <div class="fd-student-avatar"><?= $stuInitial ?></div>
                        <div>
                            <p class="fd-student-name"><?= $stuName ?></p>
                            <p class="fd-student-email"><?= esc((string)($feedback['email'] ?? 'N/A')) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Details -->
            <div class="fd-card fd-card--compact">
                <p class="fd-field-label">Details</p>
                <div class="fd-detail-list">
                    <div class="fd-detail-row">
                        <span class="fd-detail-key">Type</span>
                        <span class="fd-type-badge fd-type-<?= esc($fbType) ?>"><?= esc($typeLabels[$fbType] ?? ucfirst($fbType)) ?></span>
                    </div>
                    <div class="fd-detail-row">
                        <span class="fd-detail-key">Status</span>
                        <span class="fd-status-badge fd-status-<?= esc($cs) ?>"><?= esc($statusLabels[$cs] ?? ucfirst($cs)) ?></span>
                    </div>
                    <div class="fd-detail-row">
                        <span class="fd-detail-key">Category</span>
                        <span class="fd-detail-val"><?= esc((string)($feedback['category_name'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="fd-detail-row">
                        <span class="fd-detail-key">ID</span>
                        <span class="fd-detail-val">#<?= $rowNum ?></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <?php if ($cs === 'pending'): ?>
            <div class="fd-card fd-card--compact fd-actions-card">
                <p class="fd-field-label">Actions</p>
                <div class="fd-action-stack">
                    <form method="post" action="<?= site_url('admin/feedback/' . (int)$feedback['id'] . '/status') ?>">
                        <input type="hidden" name="status" value="approved">
                        <input type="hidden" name="admin_notes" value="">
                        <button type="submit" class="fd-btn fd-btn--approve">Approve Feedback</button>
                    </form>
                    <form method="post" action="<?= site_url('admin/feedback/' . (int)$feedback['id'] . '/status') ?>" class="detail-status-form">
                        <input type="hidden" name="status" value="rejected">
                        <input type="hidden" name="admin_notes" value="">
                        <button type="submit" class="fd-btn fd-btn--reject">Reject Feedback</button>
                    </form>
                </div>
            </div>
            <?php elseif ($cs === 'approved'): ?>
            <div class="fd-card fd-card--compact fd-actions-card">
                <p class="fd-field-label">Actions</p>
                <div class="fd-action-stack">
                    <form method="post" action="<?= site_url('admin/feedback/' . (int)$feedback['id'] . '/status') ?>" class="detail-status-form">
                        <input type="hidden" name="status" value="reviewed">
                        <input type="hidden" name="admin_notes" value="">
                        <button type="submit" class="fd-btn fd-btn--review">Mark as Reviewed</button>
                    </form>
                    <form method="post" action="<?= site_url('admin/feedback/' . (int)$feedback['id'] . '/status') ?>" class="detail-status-form">
                        <input type="hidden" name="status" value="resolved">
                        <input type="hidden" name="admin_notes" value="">
                        <button type="submit" class="fd-btn fd-btn--resolve">Mark as Resolved</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ── Modal ───────────────────────────────────────────────── -->
<div class="modal-overlay fd-modal-overlay" id="detailStatusModal" hidden>
    <div class="fd-modal">
        <div class="fd-modal-header">
            <h3 class="fd-modal-title" id="detailModalTitle">Confirm Action</h3>
            <button type="button" id="detailModalCancel" class="fd-modal-close" aria-label="Close">&times;</button>
        </div>
        <p class="fd-modal-desc" id="detailModalDesc"></p>
        <textarea id="detailModalNotes" rows="4" placeholder="e.g. Thank you for your feedback — we have taken note of this." class="fd-textarea fd-modal-textarea"></textarea>
        <div class="fd-modal-footer">
            <button type="button" id="detailModalCancelBtn" class="fd-btn fd-btn--ghost">Cancel</button>
            <button type="button" id="detailModalConfirm" class="fd-btn fd-btn--primary">Confirm</button>
        </div>
    </div>
</div>

<script>
(function () {
    var modal   = document.getElementById('detailStatusModal');
    var title   = document.getElementById('detailModalTitle');
    var desc    = document.getElementById('detailModalDesc');
    var notes   = document.getElementById('detailModalNotes');
    var btnCancel  = document.getElementById('detailModalCancelBtn');
    var btnConfirm = document.getElementById('detailModalConfirm');
    var pendingForm = null;

    var statusTitles = { rejected: 'Reject Feedback', reviewed: 'Mark as Reviewed', resolved: 'Mark as Resolved' };
    var statusDescs  = {
        rejected: 'Please provide a reason — this will be shared with the student.',
        reviewed: 'Optionally add a message for the student about this review.',
        resolved: 'Optionally confirm to the student that this issue has been resolved.',
    };

    document.querySelectorAll('.detail-status-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var si = form.querySelector('[name="status"]');
            if (!si) return;
            var chosen = si.value;
            if (chosen === 'approved') return;
            e.preventDefault();
            pendingForm = form;
            title.textContent = statusTitles[chosen] || ('Mark as ' + chosen.charAt(0).toUpperCase() + chosen.slice(1));
            desc.textContent  = statusDescs[chosen]  || 'Optionally add a message for the student. Leave blank to skip.';
            notes.value = '';
            modal.removeAttribute('hidden');
            setTimeout(function () { notes.focus(); }, 50);
        });
    });

    function closeModal() { modal.setAttribute('hidden', ''); pendingForm = null; }

    btnCancel.addEventListener('click', closeModal);
    document.getElementById('detailModalCancel').addEventListener('click', closeModal);
    btnConfirm.addEventListener('click', function () {
        if (!pendingForm) return;
        var hn = pendingForm.querySelector('[name="admin_notes"]');
        if (hn) hn.value = notes.value.trim();
        modal.setAttribute('hidden', '');
        pendingForm.submit();
        pendingForm = null;
    });
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hasAttribute('hidden')) closeModal(); });
}());
</script>
<?= $this->endSection() ?>
