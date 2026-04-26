<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="panel-grid single-column-mobile">
    <section class="panel">
        <div class="panel-head">
            <h2>Feedback #<?= isset($feedback['row_num']) ? (int)$feedback['row_num'] : (int)$feedback['id'] ?></h2>
            <a href="<?= site_url('admin/feedback') ?>">Back to queue</a>
        </div>

        <div class="detail-grid">
            <div>
                <h4>Type</h4>
                <p><span class="pill type-<?= esc((string) $feedback['type']) ?>"><?= esc(ucfirst((string) $feedback['type'])) ?></span></p>
            </div>
            <div>
                <h4>Status</h4>
                <p><span class="pill status-<?= esc((string) $feedback['status']) ?>"><?= esc(ucfirst((string) $feedback['status'])) ?></span></p>
            </div>
            <div>
                <h4>Category</h4>
                <p><?= esc((string) ($feedback['category_name'] ?? 'N/A')) ?></p>
            </div>
            <div>
                <h4>Submitted</h4>
                <p><?= esc((string) date('M d, Y H:i', strtotime((string) ($feedback['created_at'] ?? 'now')))) ?></p>
            </div>
        </div>

        <h4>Subject</h4>
        <p><?= esc((string) ($feedback['subject'] ?? 'No subject')) ?></p>

        <?php
        $aimg = (string) ($feedback['image_path'] ?? '');
        if ($aimg !== ''):
            $aurl = \App\Libraries\FeedbackImageStorage::publicUrl($aimg);
        ?>
        <h4>Attachment</h4>
        <div class="admin-feedback-image">
            <a href="<?= esc($aurl) ?>" target="_blank" rel="noopener noreferrer">
                <img src="<?= esc($aurl) ?>" alt="Student upload" loading="lazy" decoding="async">
            </a>
        </div>
        <?php endif; ?>

        <h4>Message</h4>
        <article class="message-box"><?= nl2br(esc((string) $feedback['message'])) ?></article>

        <h4>Student</h4>
        <p>
            <?php if ((int) ($feedback['is_anonymous'] ?? 0) === 1): ?>
                Anonymous submission
            <?php else: ?>
                <?= esc(trim(((string) ($feedback['first_name'] ?? '')) . ' ' . ((string) ($feedback['last_name'] ?? '')))) ?>
                (<?= esc((string) ($feedback['email'] ?? 'N/A')) ?>)
            <?php endif; ?>
        </p>

        <?php $cs = (string) $feedback['status']; ?>
        <?php if ($cs === 'pending'): ?>
        <div class="fbk-detail-actions">
            <h4>Action</h4>
            <div class="fbk-action-btns">
                <form method="post" action="<?= site_url('admin/feedback/' . (int) $feedback['id'] . '/status') ?>">
                    <input type="hidden" name="status" value="approved">
                    <input type="hidden" name="admin_notes" value="">
                    <button class="fbk-act-btn fbk-act-approve" type="submit">Approve</button>
                </form>
                <form method="post" action="<?= site_url('admin/feedback/' . (int) $feedback['id'] . '/status') ?>" class="detail-status-form">
                    <input type="hidden" name="status" value="rejected">
                    <input type="hidden" name="admin_notes" value="">
                    <button class="fbk-act-btn fbk-act-reject" type="submit">Reject</button>
                </form>
            </div>
        </div>
        <?php elseif ($cs === 'approved'): ?>
        <div class="fbk-detail-actions">
            <h4>Action</h4>
            <div class="fbk-action-btns">
                <form method="post" action="<?= site_url('admin/feedback/' . (int) $feedback['id'] . '/status') ?>" class="detail-status-form">
                    <input type="hidden" name="status" value="reviewed">
                    <input type="hidden" name="admin_notes" value="">
                    <button class="fbk-act-btn fbk-act-review" type="submit">Mark Reviewed</button>
                </form>
                <form method="post" action="<?= site_url('admin/feedback/' . (int) $feedback['id'] . '/status') ?>" class="detail-status-form">
                    <input type="hidden" name="status" value="resolved">
                    <input type="hidden" name="admin_notes" value="">
                    <button class="fbk-act-btn fbk-act-resolve" type="submit">Mark Resolved</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Admin Replies</h2>
        </div>

        <?php if (! empty($replies)): ?>
            <div class="reply-thread">
                <?php foreach ($replies as $reply): ?>
                    <article class="reply-item">
                        <header>
                            <strong><?= esc(trim(((string) ($reply['first_name'] ?? '')) . ' ' . ((string) ($reply['last_name'] ?? '')))) ?></strong>
                            <small><?= esc((string) date('M d, Y H:i', strtotime((string) ($reply['created_at'] ?? 'now')))) ?></small>
                        </header>
                        <p><?= nl2br(esc((string) $reply['message'])) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">No replies yet.</p>
        <?php endif; ?>

        <form method="post" action="<?= site_url('admin/feedback/' . (int) $feedback['id'] . '/reply') ?>" class="form-grid">
            <h3>Add Reply</h3>
            <textarea name="message" rows="4" required placeholder="Type your response for this concern"></textarea>
            <button type="submit">Post Reply</button>
        </form>
    </section>
</div>
<div class="modal-overlay" id="detailStatusModal" hidden>
    <div class="modal-card" style="max-width:460px; padding:24px 28px;">
        <div class="modal-head">
            <h3 style="margin:0; font-size:1rem;" id="detailModalTitle">Confirm Action</h3>
        </div>
        <p id="detailModalDesc" style="margin:0 0 12px; font-size:0.875rem; color:#5f7298;"></p>
        <textarea id="detailModalNotes" rows="4" placeholder="e.g. Thank you for your feedback — we have taken note of this." style="width:100%; box-sizing:border-box; padding:10px 12px; border:1px solid #c8d8ff; border-radius:8px; font-size:0.875rem; resize:vertical; min-height:90px; font-family:inherit;"></textarea>
        <div style="display:flex; gap:10px; margin-top:16px; justify-content:flex-end;">
            <button type="button" id="detailModalCancel" class="mini-btn secondary">Cancel</button>
            <button type="button" id="detailModalConfirm" class="mini-btn">Confirm</button>
        </div>
    </div>
</div>
<script>
(function () {
    var modal   = document.getElementById('detailStatusModal');
    var title   = document.getElementById('detailModalTitle');
    var desc    = document.getElementById('detailModalDesc');
    var notes   = document.getElementById('detailModalNotes');
    var btnCancel  = document.getElementById('detailModalCancel');
    var btnConfirm = document.getElementById('detailModalConfirm');
    var pendingForm = null;

    var statusTitles = {
        rejected: 'Reject Feedback',
        reviewed: 'Mark as Reviewed',
        resolved: 'Mark as Resolved',
    };
    var statusDescs = {
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
            desc.textContent = statusDescs[chosen] || 'Optionally add a message for the student. Leave blank to skip.';
            notes.value = '';
            modal.removeAttribute('hidden');
            setTimeout(function () { notes.focus(); }, 50);
        });
    });

    function closeModal() { modal.setAttribute('hidden', ''); pendingForm = null; }

    btnCancel.addEventListener('click', closeModal);
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
