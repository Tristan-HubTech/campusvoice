<?php
/**
 * ADMIN DASHBOARD
 * Main control panel interface with tabs for Overview, Feedback, Announcements, Students, Categories, and Activity.
 * 
 * CONNECTS TO:
 * - Controller: Admin\DashboardController, Admin\FeedbackController, Admin\AnnouncementController, Admin\UserManagementController, Admin\CategoryController
 * - CSS: public/css/admin.css
 * - Layout: app/Views/admin/layout.php
 */
?>
<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
use App\Libraries\FeedbackImageStorage;
$canViewActivity = ! empty($canViewActivity);
$allowedTabs = ['overview', 'feedback', 'announcements', 'users', 'categories'];
if ($canViewActivity) {
    $allowedTabs[] = 'activity';
    $allowedTabs[] = 'student-activity';
}

$safePanelTab = in_array($panelTab ?? 'overview', $allowedTabs, true)
    ? (string) $panelTab
    : 'overview';
?>

<div class="tab-strip" id="adminTabStrip">
    <button class="tab-btn active" type="button" data-tab-trigger="overview">Overview</button>
    <button class="tab-btn" type="button" data-tab-trigger="feedback">Feedback</button>
    <button class="tab-btn" type="button" data-tab-trigger="announcements">Announcements</button>
    <button class="tab-btn" type="button" data-tab-trigger="users">Students</button>
    <button class="tab-btn" type="button" data-tab-trigger="categories">Categories</button>
    <?php if ($canViewActivity): ?>
        <button class="tab-btn" type="button" data-tab-trigger="activity">Admin Activity</button>
        <button class="tab-btn" type="button" data-tab-trigger="student-activity">Student Activity</button>
    <?php endif; ?>
</div>

<!-- ── Overview Tab ── Shows high-level stats and recent summaries -->
<section class="tab-panel active" data-tab-panel="overview">
    <div class="stats-grid">
        <article class="stat-card">
            <span>Total Feedback</span>
            <strong><?= esc((string) ($stats['feedback_total'] ?? 0)) ?></strong>
        </article>
        <article class="stat-card">
            <span>Pending</span>
            <strong><?= esc((string) ($stats['feedback_pending'] ?? 0)) ?></strong>
        </article>
        <article class="stat-card">
            <span>Approved</span>
            <strong><?= esc((string) ($stats['feedback_approved'] ?? 0)) ?></strong>
        </article>
        <article class="stat-card">
            <span>Reviewed</span>
            <strong><?= esc((string) ($stats['feedback_reviewed'] ?? 0)) ?></strong>
        </article>
        <article class="stat-card">
            <span>Resolved</span>
            <strong><?= esc((string) ($stats['feedback_resolved'] ?? 0)) ?></strong>
        </article>
        <article class="stat-card">
            <span>Students</span>
            <strong><?= esc((string) ($stats['student_total'] ?? 0)) ?></strong>
        </article>
        <article class="stat-card">
            <span>Announcements</span>
            <strong><?= esc((string) ($stats['announcement_total'] ?? 0)) ?></strong>
        </article>
        <?php if ($canViewActivity): ?>
            <article class="stat-card">
                <span>Activity Today</span>
                <strong><?= esc((string) ($activityToday ?? 0)) ?></strong>
            </article>
        <?php endif; ?>
    </div>

    <div class="panel-grid">
        <section class="panel">
            <div class="panel-head">
                <h2>Recent Feedback</h2>
                <button class="btn-link" type="button" data-tab-trigger="feedback">Open Feedback</button>
            </div>

            <div class="cv-table-card"><div class="table-wrap">
                <table class="cv-admin-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (! empty($latestFeedback)): ?>
                        <?php $rowNum = 1; foreach ($latestFeedback as $item): ?>
                            <tr>
                                <td><a href="<?= site_url('admin/feedback/' . (int) $item['id']) ?>">#<?= $rowNum++ ?></a></td>
                                <td><?= esc((string) ($item['category_name'] ?? 'N/A')) ?></td>
                                <td><span class="pill type-<?= esc((string) $item['type']) ?>"><?= esc(ucfirst((string) $item['type'])) ?></span></td>
                                <td>
                                    <?php if ((int) ($item['is_anonymous'] ?? 0) === 1): ?>
                                        Anonymous
                                    <?php else: ?>
                                        <?= esc(trim(((string) ($item['first_name'] ?? '')) . ' ' . ((string) ($item['last_name'] ?? '')))) ?>
                                    <?php endif; ?>
                                </td>
                                <td><span class="pill status-<?= esc((string) $item['status']) ?>"><?= esc(ucfirst((string) $item['status'])) ?></span></td>
                                <td><?= esc((string) date('M d, Y H:i', strtotime((string) ($item['created_at'] ?? 'now')))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No feedback records yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div></div>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Latest Announcements</h2>
                <button class="btn-link" type="button" data-tab-trigger="announcements">Open Announcements</button>
            </div>

            <div class="announcement-list">
                <?php if (! empty($latestAnnouncements)): ?>
                    <?php foreach ($latestAnnouncements as $item): ?>
                        <article class="announce-card">
                            <h3><?= esc((string) $item['title']) ?></h3>
                            <p>
                                Published: <strong><?= (int) ($item['is_published'] ?? 0) === 1 ? 'Yes' : 'No' ?></strong>
                            </p>
                            <small>
                                <?= esc((string) date('M d, Y H:i', strtotime((string) ($item['created_at'] ?? 'now')))) ?>
                            </small>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="muted">No announcements yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</section>

<!-- ── Feedback Management Tab ── Interactive table for approving/rejecting posts -->
<section class="tab-panel" data-tab-panel="feedback">
    <section class="panel">
        <?php
        $stripCounts = [];
        foreach ($feedbackList as $_fbk) {
            $s = (string) ($_fbk['status'] ?? 'pending');
            $stripCounts[$s] = ($stripCounts[$s] ?? 0) + 1;
        }
        ?>
        <div class="fbk-status-strip">
            <button type="button" class="fbk-status-pill fbk-s-all active" data-filter-status="">
                All <span class="fbk-count"><?= count($feedbackList) ?></span>
            </button>
            <?php foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'reviewed' => 'Reviewed', 'resolved' => 'Resolved'] as $sKey => $sLabel): ?>
            <button type="button" class="fbk-status-pill fbk-s-<?= $sKey ?>" data-filter-status="<?= $sKey ?>">
                <?= $sLabel ?> <span class="fbk-count"><?= $stripCounts[$sKey] ?? 0 ?></span>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="panel-head stack-mobile">
            <h2>Feedback Management</h2>
            <div class="filter-row fbk-filter-row">
                <input id="feedback-search" type="text" placeholder="Search ref #, subject, message, student...">
                <select id="feedback-type-filter">
                    <option value="">All Types</option>
                    <option value="complaint">Complaint</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="praise">Praise</option>
                </select>
                <select id="feedback-category-filter">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>"><?= esc((string) $category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="fbk-bulk-bar" id="fbkBulkBar">
            <span class="fbk-bulk-count" id="fbkBulkCount">0 selected</span>
            <button type="button" class="fbk-bulk-approve-btn" id="fbkBulkApproveBtn" disabled>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Approve Selected
            </button>
        </div>

        <div class="cv-table-card"><div class="table-wrap">
            <table class="cv-admin-table">
                <thead>
                <tr>
                    <th class="fbk-check-col"><input type="checkbox" id="fbkSelectAll" title="Select all pending"></th>
                    <th>Ref #</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody id="feedback-table-body">
                <?php if (! empty($feedbackList)): ?>
                    <?php foreach ($feedbackList as $item): ?>
                        <?php
                        $fbkPadded  = str_pad((string) (int) $item['id'], 4, '0', STR_PAD_LEFT);
                        $currentStatus = (string) $item['status'];
                        $searchBlob = strtolower(
                            trim(
                                'fbk-' . $fbkPadded . ' fbk' . $fbkPadded . ' ' .
                                ((string) ($item['subject'] ?? '')) . ' ' .
                                ((string) ($item['message'] ?? '')) . ' ' .
                                ((string) ($item['category_name'] ?? '')) . ' ' .
                                ((string) ($item['first_name'] ?? '')) . ' ' .
                                ((string) ($item['last_name'] ?? ''))
                            )
                        );
                        ?>
                        <tr
                            data-feedback-row="1"
                            data-id="<?= (int) $item['id'] ?>"
                            data-status="<?= esc($currentStatus) ?>"
                            data-type="<?= esc((string) $item['type']) ?>"
                            data-category="<?= (int) ($item['category_id'] ?? 0) ?>"
                            data-search="<?= esc($searchBlob, 'attr') ?>"
                        >
                            <td class="fbk-check-col">
                                <?php if ($currentStatus === 'pending'): ?>
                                <input type="checkbox" class="fbk-row-check" value="<?= (int) $item['id'] ?>">
                                <?php endif; ?>
                            </td>
                            <td><a href="<?= site_url('admin/feedback/' . (int) $item['id']) ?>" class="fbk-badge-link"><span class="fbk-badge">#FBK-<?= $fbkPadded ?></span></a></td>
                            <td><span class="pill type-<?= esc((string) $item['type']) ?>"><?= esc(ucfirst((string) $item['type'])) ?></span></td>
                            <td><?= esc((string) ($item['category_name'] ?? 'N/A')) ?></td>
                            <td>
                                <?php
                                $subject = trim((string) ($item['subject'] ?? ''));
                                if ($subject === '') {
                                    $message = (string) ($item['message'] ?? '');
                                    $subject = strlen($message) > 70 ? substr($message, 0, 70) . '...' : $message;
                                }
                                ?>
                                <?= esc($subject) ?>
                            </td>
                            <td>
                                <?php if ((int) ($item['is_anonymous'] ?? 0) === 1): ?>
                                    <span class="muted">Anonymous</span>
                                <?php else: ?>
                                    <?= esc(trim(((string) ($item['first_name'] ?? '')) . ' ' . ((string) ($item['last_name'] ?? '')))) ?>
                                <?php endif; ?>
                            </td>
                            <td><span class="pill status-<?= esc($currentStatus) ?>"><?= esc(ucfirst($currentStatus)) ?></span></td>
                            <td class="muted" style="font-size:0.8rem;white-space:nowrap;"><?= esc(date('M d, Y', strtotime((string) ($item['created_at'] ?? 'now')))) ?></td>
                            <td class="fbk-actions-cell">
                                <?php if ($currentStatus === 'pending'): ?>
                                    <div class="fbk-action-btns">
                                        <form method="post" action="<?= site_url('admin/feedback/' . (int) $item['id'] . '/status') ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <input type="hidden" name="admin_notes" value="">
                                            <button class="fbk-act-btn fbk-act-approve" type="submit">Approve</button>
                                        </form>
                                        <form method="post" action="<?= site_url('admin/feedback/' . (int) $item['id'] . '/status') ?>" class="inline-status-form">
                                            <input type="hidden" name="status" value="rejected">
                                            <input type="hidden" name="admin_notes" value="">
                                            <button class="fbk-act-btn fbk-act-reject" type="submit">Reject</button>
                                        </form>
                                    </div>
                                <?php elseif ($currentStatus === 'approved'): ?>
                                    <div class="fbk-action-btns">
                                        <form method="post" action="<?= site_url('admin/feedback/' . (int) $item['id'] . '/status') ?>" class="inline-status-form">
                                            <input type="hidden" name="status" value="reviewed">
                                            <input type="hidden" name="admin_notes" value="">
                                            <button class="fbk-act-btn fbk-act-review" type="submit">Reviewed</button>
                                        </form>
                                        <form method="post" action="<?= site_url('admin/feedback/' . (int) $item['id'] . '/status') ?>" class="inline-status-form">
                                            <input type="hidden" name="status" value="resolved">
                                            <input type="hidden" name="admin_notes" value="">
                                            <button class="fbk-act-btn fbk-act-resolve" type="submit">Resolved</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="fbk-act-done">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No feedback available.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </section>
</section>

<!-- Feedback Status Update Modal -->
<div class="modal-overlay" id="statusUpdateModal" hidden>
    <div class="modal-card" style="max-width:460px; padding:24px 28px;">
        <div class="modal-head">
            <h3 style="margin:0; font-size:1rem;" id="statusModalLabel">Confirm Action</h3>
        </div>
        <p id="statusModalDesc" style="margin:0 0 12px; font-size:0.875rem; color:#5f7298;">Optionally add a message for the student. Leave blank to skip.</p>
        <textarea id="statusModalNotes" rows="4" placeholder="e.g. We have reviewed your concern and will address it shortly…" style="width:100%; box-sizing:border-box; padding:10px 12px; border:1px solid #c8d8ff; border-radius:8px; font-size:0.875rem; resize:vertical; min-height:90px; font-family:inherit;"></textarea>
        <div style="display:flex; gap:10px; margin-top:16px; justify-content:flex-end;">
            <button type="button" id="statusModalCancel" class="mini-btn secondary">Cancel</button>
            <button type="button" id="statusModalConfirm" class="mini-btn">Save Status</button>
        </div>
    </div>
</div>
<script>
(function () {
    var modal      = document.getElementById('statusUpdateModal');
    var label      = document.getElementById('statusModalLabel');
    var desc       = document.getElementById('statusModalDesc');
    var notes      = document.getElementById('statusModalNotes');
    var btnCancel  = document.getElementById('statusModalCancel');
    var btnConfirm = document.getElementById('statusModalConfirm');
    var pendingForm = null;

    var statusTitles = {
        rejected: 'Reject Feedback',
        reviewed: 'Mark as Reviewed',
        resolved: 'Mark as Resolved',
    };
    var statusDescriptions = {
        rejected: 'Please provide a reason — this will be shared with the student.',
        reviewed: 'Optionally add a message for the student about this review.',
        resolved: 'Optionally confirm to the student that this issue has been resolved.',
    };

    document.querySelectorAll('.inline-status-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var statusInput = form.querySelector('[name="status"]');
            if (!statusInput) return;
            var chosen = statusInput.value;
            if (chosen === 'approved') return;
            e.preventDefault();
            pendingForm = form;
            label.textContent = statusTitles[chosen] || ('Mark as ' + chosen.charAt(0).toUpperCase() + chosen.slice(1));
            if (desc) desc.textContent = statusDescriptions[chosen] || 'Optionally add a message for the student. Leave blank to skip.';
            notes.value = '';
            modal.removeAttribute('hidden');
            setTimeout(function () { notes.focus(); }, 50);
        });
    });

    function closeModal() {
        modal.setAttribute('hidden', '');
        pendingForm = null;
    }

    btnCancel.addEventListener('click', closeModal);

    btnConfirm.addEventListener('click', function () {
        if (!pendingForm) return;
        var hiddenNotes = pendingForm.querySelector('input[name="admin_notes"]');
        if (hiddenNotes) hiddenNotes.value = notes.value.trim();
        modal.setAttribute('hidden', '');
        pendingForm.submit();
        pendingForm = null;
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hasAttribute('hidden')) closeModal();
    });
}());
</script>
<script>
(function () {
    var selectAll  = document.getElementById('fbkSelectAll');
    var bulkBtn    = document.getElementById('fbkBulkApproveBtn');
    var countLabel = document.getElementById('fbkBulkCount');
    var bulkBar    = document.getElementById('fbkBulkBar');
    var bulkUrl    = '<?= site_url('admin/feedback/bulk-approve') ?>';

    function visiblePendingChecks() {
        return Array.from(document.querySelectorAll('.fbk-row-check')).filter(function (cb) {
            return cb.closest('tr') && cb.closest('tr').style.display !== 'none';
        });
    }

    function updateBar() {
        var checked = visiblePendingChecks().filter(function (cb) { return cb.checked; });
        var n = checked.length;
        countLabel.textContent = n + ' selected';
        bulkBtn.disabled = n === 0;
        bulkBar.classList.toggle('has-selection', n > 0);
        var all = visiblePendingChecks();
        selectAll.indeterminate = n > 0 && n < all.length;
        selectAll.checked = all.length > 0 && n === all.length;
    }

    selectAll.addEventListener('change', function () {
        visiblePendingChecks().forEach(function (cb) { cb.checked = selectAll.checked; });
        updateBar();
    });

    document.getElementById('feedback-table-body').addEventListener('change', function (e) {
        if (e.target.classList.contains('fbk-row-check')) updateBar();
    });

    bulkBtn.addEventListener('click', function () {
        var checked = visiblePendingChecks().filter(function (cb) { return cb.checked; });
        if (checked.length === 0) return;
        var ids = checked.map(function (cb) { return cb.value; });

        bulkBtn.disabled = true;
        bulkBtn.textContent = 'Approving…';

        var form = new FormData();
        ids.forEach(function (id) { form.append('ids[]', id); });

        fetch(bulkUrl, { method: 'POST', body: form })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.ok && data.approved) {
                    data.approved.forEach(function (id) {
                        var row = document.querySelector('tr[data-id="' + id + '"]');
                        if (!row) return;
                        row.setAttribute('data-status', 'approved');
                        var checkCell = row.querySelector('.fbk-check-col');
                        if (checkCell) checkCell.innerHTML = '';
                        var statusCell = row.querySelector('.pill[class*="status-"]');
                        if (statusCell) { statusCell.className = 'pill status-approved'; statusCell.textContent = 'Approved'; }
                        var actCell = row.querySelector('.fbk-actions-cell');
                        if (actCell) {
                            actCell.innerHTML = '<div class="fbk-action-btns">'
                                + '<form method="post" action="<?= site_url('admin/feedback/') ?>' + id + '/status" class="inline-status-form"><input type="hidden" name="status" value="reviewed"><input type="hidden" name="admin_notes" value=""><button class="fbk-act-btn fbk-act-review" type="submit">Reviewed</button></form>'
                                + '<form method="post" action="<?= site_url('admin/feedback/') ?>' + id + '/status" class="inline-status-form"><input type="hidden" name="status" value="resolved"><input type="hidden" name="admin_notes" value=""><button class="fbk-act-btn fbk-act-resolve" type="submit">Resolved</button></form>'
                                + '</div>';
                        }
                    });
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
            })
            .finally(function () {
                bulkBtn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Approve Selected';
                updateBar();
            });
    });
}());
</script>

<!-- ── Announcements Tab ── Form to create announcements and table to manage them -->
<section class="tab-panel" data-tab-panel="announcements">
    <div class="panel-grid">
        <section class="panel">
            <div class="panel-head">
                <h2 id="announcement-form-title">Create Announcement</h2>
            </div>

            <form method="post" action="<?= site_url('admin/announcements') ?>" class="form-grid" id="announcement-form" enctype="multipart/form-data">
                <label for="ann-title">Title</label>
                <input id="ann-title" name="title" required maxlength="180" placeholder="Title">

                <label for="ann-body">Body</label>
                <textarea id="ann-body" name="body" rows="8" required placeholder="Announcement content"></textarea>

                <label for="ann-image">Image <small class="muted">— optional, JPG/PNG/WebP, max 5 MB</small></label>
                <div id="ann-image-preview-wrap" style="display:none; margin-bottom:6px;">
                    <img id="ann-image-preview" src="" alt="Current image" style="max-width:100%; max-height:180px; border-radius:10px; border:1px solid var(--line); object-fit:cover; display:block; margin-bottom:6px;">
                    <label style="display:flex; align-items:center; gap:6px; font-size:0.82rem; cursor:pointer;">
                        <input type="checkbox" name="remove_image" id="ann-remove-image" value="1"> Remove current image
                    </label>
                </div>
                <input type="file" id="ann-image" name="image" accept="image/jpeg,image/png,image/webp"
                       style="border:1px solid var(--line); border-radius:10px; padding:8px 10px; font-size:0.85rem; width:100%; box-sizing:border-box;">

                <label>Publish At <small class="muted">— blank = publish immediately</small></label>
                <div class="ann-dt-row">
                    <input type="date" id="ann-publish-date" class="ann-dt-date">
                    <input type="time" id="ann-publish-time" class="ann-dt-time" value="08:00">
                    <button type="button" class="ann-dt-clear" data-dt-clear="publish" title="Clear">✕</button>
                </div>
                <div class="ann-dt-quick">
                    <button type="button" class="ann-quick-btn" data-quick-publish="today">Today</button>
                    <button type="button" class="ann-quick-btn" data-quick-publish="tomorrow">Tomorrow</button>
                    <button type="button" class="ann-quick-btn" data-quick-publish="week">+1 Week</button>
                </div>
                <input type="hidden" name="publish_at" id="ann-publish-at">

                <label>Expires At <small class="muted">— blank = never expires</small></label>
                <div class="ann-dt-row">
                    <input type="date" id="ann-expires-date" class="ann-dt-date">
                    <input type="time" id="ann-expires-time" class="ann-dt-time" value="23:59">
                    <button type="button" class="ann-dt-clear" data-dt-clear="expires" title="Clear">✕</button>
                </div>
                <script>
                (function () {
                    var now = new Date();
                    var today = now.getFullYear() + '-' +
                        String(now.getMonth() + 1).padStart(2, '0') + '-' +
                        String(now.getDate()).padStart(2, '0');
                    document.getElementById('ann-publish-date').min = today;
                    document.getElementById('ann-expires-date').min = today;
                })();
                </script>
                <div class="ann-dt-quick">
                    <button type="button" class="ann-quick-btn" data-quick-expires="week">+1 Week</button>
                    <button type="button" class="ann-quick-btn" data-quick-expires="month">+1 Month</button>
                    <button type="button" class="ann-quick-btn" data-quick-expires="year">+1 Year</button>
                </div>
                <input type="hidden" name="expires_at" id="ann-expires-at">

                <input type="hidden" name="is_published" id="ann-is-published" value="1">

                <div class="form-actions">
                    <button type="submit" id="announcement-submit-btn">Publish Announcement</button>
                    <button type="button" class="btn-link secondary" id="announcement-cancel-edit" style="display:none;">Cancel Edit</button>
                </div>
            </form>
        </section>

        <section class="panel" id="announcement-list">
            <div class="panel-head">
                <h2>Announcement List</h2>
            </div>

            <div class="cv-table-card"><div class="table-wrap">
                <table class="cv-admin-table">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Publishes</th>
                        <th>Expires</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (! empty($announcements)): ?>
                        <?php foreach ($announcements as $item): ?>
                            <?php
                            $annNow       = time();
                            $annPublishRaw = (string) ($item['publish_at'] ?? '');
                            $annExpiresRaw = (string) ($item['expires_at'] ?? '');
                            $annPublishTs  = $annPublishRaw !== '' ? strtotime($annPublishRaw) : null;
                            $annExpiresTs  = $annExpiresRaw !== '' ? strtotime($annExpiresRaw) : null;
                            $annIsLive     = (int) ($item['is_published'] ?? 0) === 1
                                             && ($annPublishTs === null || $annPublishTs <= $annNow)
                                             && ($annExpiresTs === null || $annExpiresTs >= $annNow);
                            $annIsExpired  = $annExpiresTs !== null && $annExpiresTs < $annNow;
                            $annIsScheduled = (int) ($item['is_published'] ?? 0) === 1
                                              && $annPublishTs !== null && $annPublishTs > $annNow;
                            ?>
                            <tr
                                data-announcement-row="1"
                                data-id="<?= (int) $item['id'] ?>"
                                data-title="<?= esc((string) $item['title'], 'attr') ?>"
                                data-body="<?= esc((string) $item['body'], 'attr') ?>"
                                data-audience="<?= esc((string) $item['audience'], 'attr') ?>"
                                data-is-published="<?= (int) ($item['is_published'] ?? 0) ?>"
                                data-publish-at="<?= esc($annPublishRaw, 'attr') ?>"
                                data-expires-at="<?= esc($annExpiresRaw, 'attr') ?>"
                                data-image-path="<?= esc($item['image_path'] ?? '', 'attr') ?>"
                                data-image-url="<?= esc($item['image_path'] ? FeedbackImageStorage::publicUrl((string)$item['image_path']) : '', 'attr') ?>"
                            >
                                <?php
                                $annTitle = (string) $item['title'];
                                $annBody  = (string) $item['body'];
                                $annTitleShort = mb_strlen($annTitle) > 42 ? mb_substr($annTitle, 0, 42) . '…' : $annTitle;
                                $annBodyShort  = mb_strlen($annBody)  > 60 ? mb_substr($annBody,  0, 60) . '…' : $annBody;
                                ?>
                                <td>
                                    <?php if ((int)($item['pinned'] ?? 0) === 1): ?>
                                        <span title="Pinned">📍</span>
                                    <?php endif; ?>
                                    <div class="cell-name">
                                        <strong title="<?= esc($annTitle, 'attr') ?>"><?= esc($annTitleShort) ?></strong>
                                        <small class="muted" title="<?= esc($annBody, 'attr') ?>"><?= esc($annBodyShort) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($annIsExpired): ?>
                                        <span class="pill status-rejected">Expired</span>
                                    <?php elseif ($annIsScheduled): ?>
                                        <span class="pill status-pending">Scheduled</span>
                                    <?php elseif ($annIsLive): ?>
                                        <span class="pill status-approved">Live</span>
                                    <?php else: ?>
                                        <span class="pill status-new">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $annPublishTs !== null ? esc(date('M d, Y H:i', $annPublishTs)) : '<em>Immediate</em>' ?>
                                </td>
                                <td>
                                    <?php if ($annExpiresTs !== null): ?>
                                        <span class="<?= $annIsExpired ? 'cv-expired' : 'muted' ?>">
                                            <?= esc(date('M d, Y H:i', $annExpiresTs)) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="muted"><em>Never</em></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $isPinned = (int)($item['pinned'] ?? 0); ?>
                                    <div class="smgmt-actions">
                                        <button type="button"
                                                class="act-btn act-pin pin-btn <?= $isPinned ? 'is-pinned' : '' ?>"
                                                data-announcement-id="<?= (int) $item['id'] ?>"
                                                title="<?= $isPinned ? 'Unpin' : 'Pin' ?>">
                                            <?= $isPinned ? '📍 Unpin' : '📌 Pin' ?>
                                        </button>
                                        <button type="button" class="act-btn act-edit" data-edit-announcement="<?= (int) $item['id'] ?>">✏️ Edit</button>
                                        <form method="post" action="<?= site_url('admin/announcements/' . (int) $item['id'] . '/delete') ?>" style="display:contents;" onsubmit="return confirm('Delete this announcement?');">
                                            <button class="act-btn act-delete" type="submit">🗑️ Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No announcements available.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div></div>
        </section>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.pin-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-announcement-id');
            var button = this;
            
            fetch('<?= site_url("admin/announcements/toggle-pin") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Reset all buttons to Pin
                    document.querySelectorAll('.pin-btn').forEach(function(b) {
                        b.textContent = '📌 Pin';
                        b.title = 'Pin this announcement';
                    });
                    
                    // If it was pinned, update this specific button
                    if (data.pinned) {
                        button.textContent = '📍 Unpin';
                        button.title = 'Unpin this announcement';
                    }
                } else {
                    alert('Error: ' + (data.message || 'Failed to toggle pin'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error occurred.');
            });
        });
    });
});
</script>


<?php
$usersList = $usersList ?? [];
$allCategories = $allCategories ?? [];
?>

<!-- ── Student Management Tab ── Manage student accounts, activate/deactivate, and reset passwords -->
<section class="tab-panel" data-tab-panel="users">
    <section class="panel">
        <div class="panel-head stack-mobile">
            <h2>Student Management</h2>
            <div class="filter-row">
                <input id="user-search" type="text" placeholder="Search student name, email, student number">
                <select id="user-status-filter">
                    <option value="">All Statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <div class="cv-table-card"><div class="table-wrap">
            <table class="cv-admin-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th class="status-col">Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="user-table-body">
                <?php if (! empty($usersList)): ?>
                    <?php foreach ($usersList as $u): ?>
                        <?php
                        $uName = trim(((string) ($u['first_name'] ?? '')) . ' ' . ((string) ($u['last_name'] ?? '')));
                        $uActive = (int) ($u['is_active'] ?? 1);
                        $uSearchBlob = strtolower($uName . ' ' . ($u['email'] ?? ''));
                        ?>
                        <tr
                            data-user-row="1"
                            data-active="<?= $uActive ?>"
                            data-search="<?= esc($uSearchBlob, 'attr') ?>"
                        >
                            <td><?= esc($uName !== '' ? $uName : 'Unknown') ?></td>
                            <td class="email-col"><?= esc((string) ($u['email'] ?? '')) ?></td>
                            <td class="status-col">
                                <?php if ($uActive === 1): ?>
                                    <span class="pill status-active">Active</span>
                                <?php else: ?>
                                    <span class="pill status-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= ! empty($u['last_login_at']) ? esc(date('M d, Y H:i', strtotime((string) $u['last_login_at']))) : '<span class="muted">Never</span>' ?></td>
                            <td>
                                <div class="smgmt-actions">
                                    <form method="post" action="<?= site_url('admin/users/' . (int) $u['id'] . '/toggle-status') ?>">
                                        <button class="act-btn <?= $uActive === 1 ? 'act-deactivate' : 'act-activate' ?>" type="submit">
                                            <?= $uActive === 1 ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                    <form method="post" action="<?= site_url('admin/users/' . (int) $u['id'] . '/send-reset') ?>" onsubmit="return confirm('Reset password for <?= esc($uName, 'attr') ?>?');">
                                        <button class="act-btn act-edit" type="submit">Reset PW</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No student records found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </section>
</section>

<section class="tab-panel" data-tab-panel="categories">
    <div class="panel-grid">
        <section class="panel" id="category-form-panel">
            <div class="panel-head">
                <h2 id="category-form-title">Add Category</h2>
            </div>

            <form method="post" action="<?= site_url('admin/categories') ?>" class="form-grid" id="category-form">
                <label for="cat-name">Name</label>
                <input id="cat-name" name="name" required maxlength="100" placeholder="e.g. Facility">

                <label for="cat-desc">Description</label>
                <textarea id="cat-desc" name="description" rows="3" maxlength="500" placeholder="Optional description"></textarea>

                <label>Color</label>
                <div class="cat-color-picker">
                    <div class="cat-swatches">
                        <button type="button" class="cat-swatch" data-color="#7c3aed" style="background:#7c3aed" title="Purple"></button>
                        <button type="button" class="cat-swatch" data-color="#be185d" style="background:#be185d" title="Pink"></button>
                        <button type="button" class="cat-swatch" data-color="#0f766e" style="background:#0f766e" title="Teal"></button>
                        <button type="button" class="cat-swatch" data-color="#c2410c" style="background:#c2410c" title="Orange"></button>
                        <button type="button" class="cat-swatch" data-color="#4338ca" style="background:#4338ca" title="Indigo"></button>
                        <button type="button" class="cat-swatch" data-color="#1d4ed8" style="background:#1d4ed8" title="Blue"></button>
                        <button type="button" class="cat-swatch" data-color="#15803d" style="background:#15803d" title="Green"></button>
                        <button type="button" class="cat-swatch" data-color="#374151" style="background:#374151" title="Gray"></button>
                        <button type="button" class="cat-swatch" data-color="#b45309" style="background:#b45309" title="Amber"></button>
                        <button type="button" class="cat-swatch" data-color="#0e7490" style="background:#0e7490" title="Cyan"></button>
                    </div>
                    <label class="cat-custom-wrap">
                        <span>Custom</span>
                        <input type="color" id="cat-color-custom" class="cat-color-custom" value="#7c3aed">
                    </label>
                    <input type="hidden" id="cat-color" name="color" value="">
                </div>

                <div class="form-actions">
                    <button type="submit" id="category-submit-btn">Add Category</button>
                    <button type="button" class="btn-link secondary" id="category-cancel-edit" style="display:none;">Cancel</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Category List</h2>
            </div>

            <div class="cv-table-card"><div class="table-wrap">
                <table class="cv-admin-table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (! empty($allCategories)): ?>
                        <?php foreach ($allCategories as $cat): ?>
                            <?php
                                $cColor = trim((string) ($cat['color'] ?? ''));
                                $cSlug  = preg_replace('/[^a-z0-9]/', '', strtolower((string) $cat['name'])) ?: 'general';
                                $cDotStyle = ($cColor !== '' && preg_match('/^#[0-9a-fA-F]{6}$/', $cColor))
                                    ? ' style="background:' . esc($cColor) . '"' : '';
                            ?>
                            <tr
                                data-category-row="1"
                                data-id="<?= (int) $cat['id'] ?>"
                                data-name="<?= esc((string) $cat['name'], 'attr') ?>"
                                data-description="<?= esc((string) ($cat['description'] ?? ''), 'attr') ?>"
                                data-color="<?= esc($cColor, 'attr') ?>"
                            >
                                <td><span class="cat-dot cat-dot--<?= esc($cSlug) ?>"<?= $cDotStyle ?>></span><strong><?= esc((string) $cat['name']) ?></strong></td>
                                <td><?= esc(strlen((string) ($cat['description'] ?? '')) > 60 ? substr((string) $cat['description'], 0, 60) . '...' : (string) ($cat['description'] ?? '—')) ?></td>
                                <td>
                                    <?php if ((int) ($cat['is_active'] ?? 1) === 1): ?>
                                        <span class="pill status-active">Active</span>
                                    <?php else: ?>
                                        <span class="pill status-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="center-col">
                                    <div class="smgmt-actions">
                                        <button type="button" class="act-btn act-edit" data-edit-category="<?= (int) $cat['id'] ?>">Edit</button>
                                        <form method="post" action="<?= site_url('admin/categories/' . (int) $cat['id'] . '/toggle') ?>" style="display:contents;">
                                            <button class="act-btn <?= (int) ($cat['is_active'] ?? 1) === 1 ? 'act-deactivate' : 'act-activate' ?>" type="submit">
                                                <?= (int) ($cat['is_active'] ?? 1) === 1 ? 'Disable' : 'Enable' ?>
                                            </button>
                                        </form>
                                        <form method="post" action="<?= site_url('admin/categories/' . (int) $cat['id'] . '/delete') ?>" style="display:contents;" onsubmit="return confirm('Delete category &quot;<?= esc((string) $cat['name'], 'attr') ?>&quot;?');">
                                            <button class="act-btn act-delete" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No categories found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div></div>
        </section>
    </div>
</section>

<?php if ($canViewActivity): ?>
    <?php
    $activityFilters = $activityFilters ?? [];
    $activityPagination = $activityPagination ?? ['page' => 1, 'pages' => 1, 'total' => 0, 'perPage' => 20];
    $activitySort = (string) ($activityFilters['sort'] ?? 'created_at');
    $activityDir = (string) ($activityFilters['dir'] ?? 'desc');
    $activityFilterQuery = [
        'tab'               => 'activity',
        'activity_q'        => (string) ($activityFilters['q'] ?? ''),
        'activity_action'   => (string) ($activityFilters['action'] ?? ''),
        'activity_admin_id' => (int) ($activityFilters['admin_id'] ?? 0),
        'activity_from'     => (string) ($activityFilters['from'] ?? ''),
        'activity_to'       => (string) ($activityFilters['to'] ?? ''),
        'activity_sort'     => $activitySort,
        'activity_dir'      => $activityDir,
    ];
    $currentPage = (int) ($activityPagination['page'] ?? 1);
    $totalPages = (int) ($activityPagination['pages'] ?? 1);
    $totalRows = (int) ($activityPagination['total'] ?? 0);
    $retentionOptions = $activityPurgeRetentionOptions ?? [30, 90, 180, 365, 730];
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    $buildSortQuery = static function (string $sortKey) use ($activityFilterQuery, $activitySort, $activityDir): string {
        $query = $activityFilterQuery;
        $query['activity_sort'] = $sortKey;
        $query['activity_dir'] = ($activitySort === $sortKey && $activityDir === 'asc') ? 'desc' : 'asc';
        $query['activity_page'] = 1;
        return http_build_query($query);
    };
    $sortIndicator = static function (string $sortKey) use ($activitySort, $activityDir): string {
        if ($activitySort !== $sortKey) {
            return '';
        }

        return '<span class="sort-indicator">' . ($activityDir === 'asc' ? '^' : 'v') . '</span>';
    };
    ?>
    <section class="tab-panel" data-tab-panel="activity">
        <div class="modal-overlay" id="purge-confirm-modal" hidden>
            <div class="modal-card" style="max-width: 400px; text-align: center;">
                <div class="confirm-modal-icon" style="font-size: 2.5rem; margin-bottom: 12px;">🗑️</div>
                <h3 style="margin: 0 0 8px; font-size: 1.2rem; color: var(--ink);">Confirm Purge</h3>
                <p style="color: var(--muted); font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">
                    Are you sure you want to permanently delete these activity logs? This action cannot be undone.
                </p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button type="button" class="btn-link secondary" id="purge-cancel-btn" style="padding: 10px 24px; border-radius: 12px; border: 1px solid #c7d7ff; background: #f8faff; color: var(--ink);">Cancel</button>
                    <button type="button" class="btn-link danger-action" id="purge-confirm-btn" style="padding: 10px 24px; background: linear-gradient(135deg, #a23030, #74211d); color: #fff;">Yes, Purge</button>
                </div>
            </div>
        </div>
        <section class="panel">
            <div class="panel-head stack-mobile">
                <h2>Admin Activity Log</h2>
                <div class="activity-toolbar">
                    <span class="muted"><?= esc((string) $totalRows) ?> matching records</span>
                </div>
            </div>

            <form method="get" action="<?= site_url('admin') ?>" class="activity-filter-form">
                <input type="hidden" name="tab" value="activity">
                <input type="text" name="activity_q" value="<?= esc((string) ($activityFilters['q'] ?? '')) ?>" placeholder="Search action, description, admin...">
                <select name="activity_action" onchange="this.form.submit()">
                    <option value="">All Actions</option>
                    <?php foreach (($activityActionOptions ?? []) as $actionOption): ?>
                        <option value="<?= esc((string) $actionOption) ?>" <?= ((string) ($activityFilters['action'] ?? '') === (string) $actionOption) ? 'selected' : '' ?>>
                            <?= esc((string) $actionOption) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="activity_admin_id" onchange="this.form.submit()">
                    <option value="0">All Admins</option>
                    <?php foreach (($activityAdminOptions ?? []) as $adminOption): ?>
                        <?php $adminName = (string) ($adminOption['full_name'] ?? ''); ?>
                        <option value="<?= (int) ($adminOption['id'] ?? 0) ?>" <?= ((int) ($activityFilters['admin_id'] ?? 0) === (int) ($adminOption['id'] ?? 0)) ? 'selected' : '' ?>>
                            <?= esc($adminName !== '' ? $adminName : ((string) ($adminOption['email'] ?? 'Unknown'))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="activity_from" value="<?= esc((string) ($activityFilters['from'] ?? '')) ?>" aria-label="From date">
                <input type="date" name="activity_to" value="<?= esc((string) ($activityFilters['to'] ?? '')) ?>" aria-label="To date">
                <button type="submit">Apply</button>
                <a class="btn-link secondary" href="<?= site_url('admin?tab=activity') ?>">Reset</a>
            </form>

            <form id="activity-purge-form" method="post" action="<?= site_url('admin/activity/purge') ?>" class="activity-purge-form">
                <label for="retention-days">Cleanup Retention</label>
                <select id="retention-days" name="retention_days" required>
                    <?php foreach ($retentionOptions as $option): ?>
                        <option value="<?= (int) $option ?>" <?= ((int) $option === 180) ? 'selected' : '' ?>>Keep last <?= (int) $option ?> days</option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="confirm_text" placeholder="Type PURGE" required>
                <button type="submit" class="danger-action">Purge Old Logs</button>
            </form>

            <div class="cv-table-card"><div class="table-wrap">
                <table class="cv-admin-table">
                    <thead>
                    <tr>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('created_at')) ?>#activity">Time<?= $sortIndicator('created_at') ?></a></th>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('admin')) ?>#activity">Admin<?= $sortIndicator('admin') ?></a></th>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('action')) ?>#activity">Action<?= $sortIndicator('action') ?></a></th>
                        <th>Description</th>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('target')) ?>#activity">Target<?= $sortIndicator('target') ?></a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (! empty($activityLogs)): ?>
                        <?php foreach ($activityLogs as $log): ?>
                            <?php
                            $fullName = (string) ($log['admin_display'] ?? trim((string) ($log['full_name'] ?? '')));
                            $targetLabel = '-';
                            if (! empty($log['target_type']) && ! empty($log['target_id'])) {
                                $targetLabel = (string) $log['target_type'] . ' #' . (int) $log['target_id'];
                            } elseif (! empty($log['target_type'])) {
                                $targetLabel = (string) $log['target_type'];
                            }
                            $actionText = (string) ($log['action'] ?? 'unknown');
                            $descText   = trim((string) ($log['description'] ?? ''));
                            ?>
                            <tr>
                                <td class="nowrap"><?= esc(date('M d, Y H:i', strtotime((string) ($log['created_at'] ?? 'now')))) ?></td>
                                <td>
                                    <div class="cell-name">
                                        <strong><?= esc($fullName !== '' ? $fullName : 'System') ?></strong>
                                        <?php if (! empty($log['admin_email'])): ?>
                                            <small class="muted"><?= esc((string) $log['admin_email']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><span class="pill act-pill"><?= esc($actionText) ?></span></td>
                                <td class="desc-col"><?= esc($descText !== '' ? $descText : '—') ?></td>
                                <td class="nowrap muted"><?= esc($targetLabel) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No activity logs yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div></div>

            <?php if ($totalPages > 1): ?>
                <nav class="activity-pagination" aria-label="Activity pages">
                    <?php if ($currentPage > 1): ?>
                        <?php $prevQuery = $activityFilterQuery; $prevQuery['activity_page'] = $currentPage - 1; ?>
                        <a href="<?= site_url('admin?' . http_build_query($prevQuery)) ?>#activity">Prev</a>
                    <?php endif; ?>

                    <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
                        <?php $pageQuery = $activityFilterQuery; $pageQuery['activity_page'] = $page; ?>
                        <a href="<?= site_url('admin?' . http_build_query($pageQuery)) ?>#activity" class="<?= $page === $currentPage ? 'active' : '' ?>"><?= $page ?></a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <?php $nextQuery = $activityFilterQuery; $nextQuery['activity_page'] = $currentPage + 1; ?>
                        <a href="<?= site_url('admin?' . http_build_query($nextQuery)) ?>#activity">Next</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </section>

    </section>

    <?php
    $saFilters    = $studentActivityFilters    ?? [];
    $saPagination = $studentActivityPagination ?? ['page' => 1, 'perPage' => 20, 'total' => 0, 'pages' => 1];
    $saLogs       = $studentActivityLogs       ?? [];
    $saActions    = $studentActivityActionOptions ?? [];

    $saBaseUrl = site_url('admin') . '?tab=student-activity';
    $saBuildPageUrl = static function (int $page) use ($saFilters, $saBaseUrl): string {
        $q = http_build_query(array_filter([
            'sa_q'      => $saFilters['q']      ?? '',
            'sa_action' => $saFilters['action']  ?? '',
            'sa_from'   => $saFilters['from']    ?? '',
            'sa_to'     => $saFilters['to']      ?? '',
            'sa_sort'   => $saFilters['sort']    ?? '',
            'sa_dir'    => $saFilters['dir']     ?? '',
            'sa_page'   => $page > 1 ? (string) $page : '',
        ]));
        return $saBaseUrl . ($q ? '&' . $q : '') . '#student-activity';
    };
    $saCurrentSort = (string) ($saFilters['sort'] ?? 'created_at');
    $saCurrentDir  = (string) ($saFilters['dir']  ?? 'desc');
    $saBuildSortUrl = static function (string $col) use ($saFilters, $saBaseUrl, $saCurrentSort, $saCurrentDir): string {
        $dir = ($saCurrentSort === $col && $saCurrentDir === 'asc') ? 'desc' : 'asc';
        $q = http_build_query(array_filter([
            'sa_q'      => $saFilters['q']      ?? '',
            'sa_action' => $saFilters['action']  ?? '',
            'sa_from'   => $saFilters['from']    ?? '',
            'sa_to'     => $saFilters['to']      ?? '',
            'sa_sort'   => $col,
            'sa_dir'    => $dir,
        ]));
        return $saBaseUrl . ($q ? '&' . $q : '') . '#student-activity';
    };
    $saSortIndicator = static function (string $col) use ($saCurrentSort, $saCurrentDir): string {
        if ($saCurrentSort !== $col) { return ''; }
        return ' <span class="sort-indicator">' . ($saCurrentDir === 'asc' ? '^' : 'v') . '</span>';
    };
    ?>
    <section class="tab-panel" data-tab-panel="student-activity">
        <section class="panel">
            <div class="panel-head stack-mobile">
                <h2>Student Activity Log</h2>
                <div class="activity-toolbar">
                    <span class="muted"><?= esc((string) $saPagination['total']) ?> matching records</span>
                </div>
            </div>

            <form method="get" action="<?= site_url('admin') ?>" class="cv-table-toolbar" id="sa-filter-form">
                <input type="hidden" name="tab" value="student-activity">
                <div class="cv-search-wrap">
                    <input type="text" name="sa_q" id="sa-search" class="cv-search cv-input"
                           placeholder="Search name, email, action — anonymous users searchable by real name"
                           value="<?= esc((string) ($saFilters['q'] ?? '')) ?>">
                </div>
                <select name="sa_action" class="cv-input" onchange="this.form.submit()">
                    <option value="">All Actions</option>
                    <?php foreach ($saActions as $act): ?>
                        <option value="<?= esc($act) ?>" <?= ($saFilters['action'] ?? '') === $act ? 'selected' : '' ?>>
                            <?= esc($act) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="sa_from" class="cv-input" value="<?= esc((string) ($saFilters['from'] ?? '')) ?>" title="From date">
                <input type="date" name="sa_to"   class="cv-input" value="<?= esc((string) ($saFilters['to']   ?? '')) ?>" title="To date">
                <select id="sa-anon-filter" class="cv-input" title="Filter by anonymous">
                    <option value="">All</option>
                    <option value="1">Anonymous Only</option>
                    <option value="0">Non-Anonymous Only</option>
                </select>
                <button type="submit" class="cv-btn-navy">Filter</button>
                <?php if (($saFilters['q'] ?? '') !== '' || ($saFilters['action'] ?? '') !== '' || ($saFilters['from'] ?? '') !== '' || ($saFilters['to'] ?? '') !== ''): ?>
                    <a href="<?= site_url('admin') ?>?tab=student-activity#student-activity" class="btn-link secondary">Clear</a>
                <?php endif; ?>
            </form>

            <div class="cv-table-card">
                <div class="table-wrap">
                    <table class="cv-admin-table">
                        <thead>
                        <tr>
                            <th><a class="sort-link sort-block" href="<?= $saBuildSortUrl('created_at') ?>">Date &amp; Time<?= $saSortIndicator('created_at') ?></a></th>
                            <th><a class="sort-link sort-block" href="<?= $saBuildSortUrl('student_name') ?>">Student<?= $saSortIndicator('student_name') ?></a></th>
                            <th><a class="sort-link sort-block" href="<?= $saBuildSortUrl('action') ?>">Action<?= $saSortIndicator('action') ?></a></th>
                            <th>Description</th>
                            <th>Target</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $saActionLabels = [
                            'feedback.submitted'  => 'Feedback Submitted',
                            'reaction.added'      => 'Reaction Added',
                            'comment.added'       => 'Comment Added',
                            'auth.login'          => 'Logged In',
                            'auth.logout'         => 'Logged Out',
                            'post.created'        => 'Post Created',
                            'profile.updated'     => 'Profile Updated',
                            'password.changed'    => 'Password Changed',
                        ];
                        ?>
                        <?php if (! empty($saLogs)): ?>
                            <?php foreach ($saLogs as $saRow): ?>
                                <?php
                                $saMeta = [];
                                if (! empty($saRow['metadata'])) {
                                    $saMeta = json_decode((string) $saRow['metadata'], true) ?? [];
                                }
                                $saIsAnon = ! empty($saMeta['anonymous']);
                                ?>
                                <tr data-sa-anonymous="<?= $saIsAnon ? '1' : '0' ?>">
                                    <td class="nowrap">
                                        <?= esc(date('M d, Y H:i', strtotime((string) ($saRow['created_at'] ?? '')))) ?>
                                    </td>
                                    <td>
                                        <?php if (! empty($saRow['student_name'])): ?>
                                            <div class="cell-name">
                                                <strong>
                                                    <?= esc((string) $saRow['student_name']) ?>
                                                    <?php if ($saIsAnon): ?>
                                                        <span class="sa-anon-badge" title="Posted anonymously">&#128100; Anon</span>
                                                    <?php endif; ?>
                                                </strong>
                                                <?php if (! empty($saRow['student_email'])): ?>
                                                    <small class="muted"><?= esc((string) $saRow['student_email']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $saRawAction = (string) ($saRow['action'] ?? ''); ?>
                                        <span class="pill status-reviewed" style="font-size:.75rem;">
                                            <?= esc($saActionLabels[$saRawAction] ?? ucwords(str_replace(['.', '_'], ' ', $saRawAction))) ?>
                                        </span>
                                    </td>
                                    <td class="desc-col">
                                        <?= esc((string) ($saRow['description'] ?? '')) ?>
                                    </td>
                                    <td class="nowrap muted">
                                        <?php if (! empty($saRow['target_type'])): ?>
                                            <?= esc((string) $saRow['target_type']) ?>
                                            <?php if (! empty($saRow['target_id'])): ?>
                                                #<?= esc((string) $saRow['target_id']) ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="muted" style="text-align:center;padding:2rem;">
                                    No student activity records found.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($saPagination['pages'] > 1): ?>
                    <div class="pagination-bar">
                        <?php if ($saPagination['page'] > 1): ?>
                            <a class="btn-link" href="<?= $saBuildPageUrl($saPagination['page'] - 1) ?>">&#8592; Prev</a>
                        <?php endif; ?>
                        <span class="muted" style="font-size:.85rem;">
                            Page <?= esc((string) $saPagination['page']) ?> of <?= esc((string) $saPagination['pages']) ?>
                        </span>
                        <?php if ($saPagination['page'] < $saPagination['pages']): ?>
                            <a class="btn-link" href="<?= $saBuildPageUrl($saPagination['page'] + 1) ?>">Next &#8594;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </section>
<?php endif; ?>

<script>
    (function () {
        const safePanelTab = <?= json_encode($safePanelTab) ?>;
        const allowedTabs = <?= json_encode($allowedTabs) ?>;
        const apiBase = <?= json_encode(rtrim(site_url('api'), '/')) ?>;

        const triggerSelector = '[data-tab-trigger]';
        const panelSelector = '[data-tab-panel]';

        function openTab(tabName, updateHash) {
            const buttons = document.querySelectorAll(triggerSelector);
            const panels = document.querySelectorAll(panelSelector);

            panels.forEach(function (panel) {
                panel.classList.toggle('active', panel.getAttribute('data-tab-panel') === tabName);
            });

            buttons.forEach(function (button) {
                button.classList.toggle('active', button.getAttribute('data-tab-trigger') === tabName);
            });

            if (updateHash === true) {
                window.location.hash = tabName;
            }
        }

        // Map element-level anchors inside tabs to their parent tab name
        const anchorToTab = { 'announcement-list': 'announcements' };

        function resolveInitialTab() {
            const hash = window.location.hash.replace('#', '').trim();
            if (allowedTabs.includes(hash)) {
                return hash;
            }

            // Handle anchors that live inside a tab panel (e.g. #announcement-list)
            if (anchorToTab[hash]) {
                return anchorToTab[hash];
            }

            if (allowedTabs.includes(safePanelTab)) {
                return safePanelTab;
            }

            return 'overview';
        }

        document.querySelectorAll(triggerSelector).forEach(function (button) {
            button.addEventListener('click', function () {
                openTab(button.getAttribute('data-tab-trigger'), true);
            });
        });

        window.addEventListener('hashchange', function () {
            openTab(resolveInitialTab(), false);
        });

        openTab(resolveInitialTab(), false);

        const feedbackSearch = document.getElementById('feedback-search');
        const feedbackTypeFilter = document.getElementById('feedback-type-filter');
        const feedbackCategoryFilter = document.getElementById('feedback-category-filter');
        let activeFeedbackStatus = '';

        function filterFeedbackRows() {
            const rawQuery = (feedbackSearch.value || '').toLowerCase().trim();
            const status = activeFeedbackStatus;
            const type = feedbackTypeFilter.value;
            const category = feedbackCategoryFilter.value;

            // Detect Ref # search: "46", "0046", "fbk-46", "fbk-0046", "fbk0046", "#fbk-0046"
            const refMatch = rawQuery.match(/^#?(?:fbk[-]?)?0*(\d+)$/);
            const refId = refMatch ? parseInt(refMatch[1], 10) : null;

            document.querySelectorAll('[data-feedback-row="1"]').forEach(function (row) {
                const rowStatus = row.getAttribute('data-status') || '';
                const rowType = row.getAttribute('data-type') || '';
                const rowCategory = row.getAttribute('data-category') || '';
                const rowId = parseInt(row.getAttribute('data-id') || '0', 10);
                const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();

                const matchesStatus = status === '' || rowStatus === status;
                const matchesType = type === '' || rowType === type;
                const matchesCategory = category === '' || rowCategory === category;
                const matchesRef = refId !== null && rowId === refId;
                const matchesSearch = rawQuery === '' || matchesRef || rowSearch.indexOf(rawQuery) !== -1;

                row.style.display = (matchesStatus && matchesType && matchesCategory && matchesSearch) ? '' : 'none';
            });
        }

        [feedbackSearch, feedbackTypeFilter, feedbackCategoryFilter].forEach(function (input) {
            if (input) {
                input.addEventListener('input', filterFeedbackRows);
                input.addEventListener('change', filterFeedbackRows);
            }
        });

        document.querySelectorAll('.fbk-status-pill').forEach(function (pill) {
            pill.addEventListener('click', function () {
                document.querySelectorAll('.fbk-status-pill').forEach(function (p) { p.classList.remove('active'); });
                pill.classList.add('active');
                activeFeedbackStatus = pill.getAttribute('data-filter-status') || '';
                filterFeedbackRows();
            });
        });

        // Student activity anonymous filter
        var saAnonFilter = document.getElementById('sa-anon-filter');
        if (saAnonFilter) {
            saAnonFilter.addEventListener('change', function () {
                var val = saAnonFilter.value;
                document.querySelectorAll('[data-sa-anonymous]').forEach(function (row) {
                    var rowAnon = row.getAttribute('data-sa-anonymous');
                    if (val === '') {
                        row.style.display = '';
                    } else {
                        row.style.display = rowAnon === val ? '' : 'none';
                    }
                });
            });
        }

        async function postJson(url, payload, statusEl, resultEl) {
            statusEl.className = 'status-text';
            statusEl.textContent = 'Sending...';
            resultEl.textContent = 'Loading...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const text = await response.text();
                let data = text;

                try {
                    data = JSON.parse(text);
                } catch (error) {
                    // keep text fallback
                }

                if (response.ok) {
                    statusEl.classList.add('ok');
                    statusEl.textContent = 'Success (' + response.status + ')';
                } else {
                    statusEl.classList.add('bad');
                    statusEl.textContent = 'Failed (' + response.status + ')';
                }

                resultEl.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
                return { ok: response.ok, data: data };
            } catch (error) {
                statusEl.classList.add('bad');
                statusEl.textContent = 'Network Error';
                resultEl.textContent = error.message;
                return { ok: false, data: null };
            }
        }

        const otpRequestForm = document.getElementById('otp-request-form');
        const otpVerifyForm = document.getElementById('otp-verify-form');
        const otpResetForm = document.getElementById('otp-reset-form');

        const requestStatus = document.getElementById('otp-request-status');
        const requestResult = document.getElementById('otp-request-result');
        const verifyStatus = document.getElementById('otp-verify-status');
        const verifyResult = document.getElementById('otp-verify-result');
        const resetStatus = document.getElementById('otp-reset-status');
        const resetResult = document.getElementById('otp-reset-result');

        const announcementForm = document.getElementById('announcement-form');
        const announcementTitle = document.getElementById('announcement-form-title');
        const announcementSubmitBtn = document.getElementById('announcement-submit-btn');
        const announcementCancelBtn = document.getElementById('announcement-cancel-edit');

        var annPublishDate = document.getElementById('ann-publish-date');
        var annPublishTime = document.getElementById('ann-publish-time');
        var annExpiresDate = document.getElementById('ann-expires-date');
        var annExpiresTime = document.getElementById('ann-expires-time');
        var annPublishHidden = document.getElementById('ann-publish-at');
        var annExpiresHidden = document.getElementById('ann-expires-at');

        function splitDateTimeInto(prefix, rawValue) {
            var dateEl = prefix === 'publish' ? annPublishDate : annExpiresDate;
            var timeEl = prefix === 'publish' ? annPublishTime : annExpiresTime;
            var defaultTime = prefix === 'publish' ? '08:00' : '23:59';
            if (rawValue && rawValue.length >= 10) {
                var normalized = rawValue.replace(' ', 'T');
                dateEl.value = normalized.slice(0, 10);
                timeEl.value = normalized.length >= 16 ? normalized.slice(11, 16) : defaultTime;
            } else {
                dateEl.value = '';
                timeEl.value = defaultTime;
            }
        }

        function buildHiddenDateTimes() {
            annPublishHidden.value = annPublishDate.value
                ? annPublishDate.value + ' ' + (annPublishTime.value || '00:00') + ':00'
                : '';
            annExpiresHidden.value = annExpiresDate.value
                ? annExpiresDate.value + ' ' + (annExpiresTime.value || '23:59') + ':00'
                : '';
        }

        function resetAnnouncementForm() {
            if (!announcementForm) { return; }
            announcementForm.action = <?= json_encode(site_url('admin/announcements')) ?>;
            announcementTitle.textContent = 'Create Announcement';
            announcementSubmitBtn.textContent = 'Publish Announcement';
            announcementCancelBtn.style.display = 'none';
            announcementForm.reset();
            announcementForm.elements.is_published.value = '1';
            splitDateTimeInto('publish', '');
            splitDateTimeInto('expires', '');
            // Clear image preview
            var previewWrap = document.getElementById('ann-image-preview-wrap');
            var previewImg  = document.getElementById('ann-image-preview');
            if (previewWrap) { previewWrap.style.display = 'none'; }
            if (previewImg)  { previewImg.src = ''; }
        }

        if (announcementForm) {
            announcementForm.addEventListener('submit', buildHiddenDateTimes);
        }

        // Live image preview when user picks a new file
        var annImageInput = document.getElementById('ann-image');
        if (annImageInput) {
            annImageInput.addEventListener('change', function () {
                var file = this.files && this.files[0];
                var previewWrap = document.getElementById('ann-image-preview-wrap');
                var previewImg  = document.getElementById('ann-image-preview');
                var removeChk   = document.getElementById('ann-remove-image');
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                        previewWrap.style.display = '';
                        if (removeChk) removeChk.checked = false;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        document.querySelectorAll('[data-dt-clear]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var prefix = btn.getAttribute('data-dt-clear');
                if (prefix === 'publish') { annPublishDate.value = ''; annPublishTime.value = '08:00'; }
                else { annExpiresDate.value = ''; annExpiresTime.value = '23:59'; }
            });
        });

        function addDays(d, n) { var r = new Date(d); r.setDate(r.getDate() + n); return r; }
        function addMonths(d, n) { var r = new Date(d); r.setMonth(r.getMonth() + n); return r; }
        function addYears(d, n) { var r = new Date(d); r.setFullYear(r.getFullYear() + n); return r; }
        function toDateStr(d) {
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + day;
        }
        function currentTimeStr() {
            var now = new Date();
            return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        }

        document.querySelectorAll('[data-quick-publish]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var today = new Date();
                var type = btn.getAttribute('data-quick-publish');
                if (type === 'today') annPublishDate.value = toDateStr(today);
                else if (type === 'tomorrow') annPublishDate.value = toDateStr(addDays(today, 1));
                else if (type === 'week') annPublishDate.value = toDateStr(addDays(today, 7));
                // Only pre-fill the time if the user hasn't already typed one.
                // An empty or default '08:00' value means they haven't set it yet.
                if (!annPublishTime.value || annPublishTime.value === '08:00') {
                    annPublishTime.value = currentTimeStr();
                }
            });
        });

        document.querySelectorAll('[data-quick-expires]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var today = new Date();
                var type = btn.getAttribute('data-quick-expires');
                if (type === 'week') annExpiresDate.value = toDateStr(addDays(today, 7));
                else if (type === 'month') annExpiresDate.value = toDateStr(addMonths(today, 1));
                else if (type === 'year') annExpiresDate.value = toDateStr(addYears(today, 1));
            });
        });

        document.querySelectorAll('[data-edit-announcement]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!announcementForm) { return; }
                const id = button.getAttribute('data-edit-announcement');
                const row = document.querySelector('[data-announcement-row="1"][data-id="' + id + '"]');
                if (!row) { return; }

                announcementForm.action = <?= json_encode(site_url('admin/announcements')) ?> + '/' + id;
                announcementTitle.textContent = 'Edit Announcement #' + id;
                announcementSubmitBtn.textContent = 'Save Changes';
                announcementCancelBtn.style.display = '';

                announcementForm.elements.title.value = row.getAttribute('data-title') || '';
                announcementForm.elements.body.value = row.getAttribute('data-body') || '';
                announcementForm.elements.is_published.value = row.getAttribute('data-is-published') || '1';
                splitDateTimeInto('publish', row.getAttribute('data-publish-at') || '');
                splitDateTimeInto('expires', row.getAttribute('data-expires-at') || '');

                // Show existing image preview if present
                var existingImgUrl = row.getAttribute('data-image-url') || '';
                var previewWrap = document.getElementById('ann-image-preview-wrap');
                var previewImg  = document.getElementById('ann-image-preview');
                var removeChk   = document.getElementById('ann-remove-image');
                if (existingImgUrl) {
                    previewImg.src = existingImgUrl;
                    previewWrap.style.display = '';
                    removeChk.checked = false;
                } else {
                    previewWrap.style.display = 'none';
                    previewImg.src = '';
                }

                openTab('announcements', true);
                announcementForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        if (announcementCancelBtn) {
            announcementCancelBtn.addEventListener('click', function () { resetAnnouncementForm(); });
        }

        resetAnnouncementForm();

        if (otpRequestForm) {
            otpRequestForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                const email = otpRequestForm.elements.email.value.trim();

                const res = await postJson(apiBase + '/auth/password/otp/request', { email: email }, requestStatus, requestResult);
                if (res.ok) {
                    if (otpVerifyForm) { otpVerifyForm.elements.email.value = email; }
                    if (otpResetForm)  { otpResetForm.elements.email.value  = email; }
                }
            });
        }

        if (otpVerifyForm) {
            otpVerifyForm.addEventListener('submit', function (event) {
                event.preventDefault();

                postJson(
                    apiBase + '/auth/password/otp/verify',
                    {
                        email: otpVerifyForm.elements.email.value.trim(),
                        otp: otpVerifyForm.elements.otp.value.trim(),
                    },
                    verifyStatus,
                    verifyResult
                );
            });
        }

        if (otpResetForm) {
            otpResetForm.addEventListener('submit', function (event) {
                event.preventDefault();

                postJson(
                    apiBase + '/auth/password/reset',
                    {
                        email: otpResetForm.elements.email.value.trim(),
                        otp: otpResetForm.elements.otp.value.trim(),
                        new_password: otpResetForm.elements.new_password.value,
                        confirm_password: otpResetForm.elements.confirm_password.value,
                    },
                    resetStatus,
                    resetResult
                );
            });
        }

        var purgeForm = document.getElementById('activity-purge-form');
        var purgeModal = document.getElementById('purge-confirm-modal');
        var purgeCancel = document.getElementById('purge-cancel-btn');
        var purgeConfirm = document.getElementById('purge-confirm-btn');

        if (purgeForm && purgeModal) {
            purgeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                purgeModal.removeAttribute('hidden');
            });
            if (purgeCancel) {
                purgeCancel.addEventListener('click', function () {
                    purgeModal.setAttribute('hidden', 'hidden');
                });
            }
            if (purgeConfirm) {
                purgeConfirm.addEventListener('click', function () {
                    purgeForm.submit();
                });
            }
        }


        // ── User table filtering ──────────────────────────────────────
        var userSearch = document.getElementById('user-search');
        var userStatusFilter = document.getElementById('user-status-filter');

        function filterUserRows() {
            var query = (userSearch ? userSearch.value : '').toLowerCase().trim();
            var status = userStatusFilter ? userStatusFilter.value : '';

            document.querySelectorAll('[data-user-row="1"]').forEach(function (row) {
                var rowActive = row.getAttribute('data-active') || '';
                var rowSearch = (row.getAttribute('data-search') || '').toLowerCase();

                var matchStatus = status === '' || rowActive === status;
                var matchSearch = query === '' || rowSearch.indexOf(query) !== -1;

                row.style.display = (matchStatus && matchSearch) ? '' : 'none';
            });
        }

        [userSearch, userStatusFilter].forEach(function (el) {
            if (el) {
                el.addEventListener('input', filterUserRows);
                el.addEventListener('change', filterUserRows);
            }
        });

        // ── Category inline edit ──────────────────────────────────────
        var categoryForm = document.getElementById('category-form');
        var categoryTitle = document.getElementById('category-form-title');
        var categorySubmitBtn = document.getElementById('category-submit-btn');
        var categoryCancelBtn = document.getElementById('category-cancel-edit');
        var categoryPanel = document.getElementById('category-form-panel');

        // ── Color picker ──────────────────────────────────────────────
        var colorHiddenInput = document.getElementById('cat-color');
        var colorCustomInput = document.getElementById('cat-color-custom');
        var colorSwatches    = document.querySelectorAll('.cat-swatch');

        function setPickerColor(hex) {
            if (!colorHiddenInput) { return; }
            colorHiddenInput.value = hex || '';
            if (colorCustomInput) { colorCustomInput.value = hex || '#7c3aed'; }
            colorSwatches.forEach(function (s) {
                s.classList.toggle('cat-swatch--active', s.getAttribute('data-color') === hex);
            });
        }

        colorSwatches.forEach(function (swatch) {
            swatch.addEventListener('click', function () {
                setPickerColor(swatch.getAttribute('data-color'));
            });
        });

        if (colorCustomInput) {
            colorCustomInput.addEventListener('input', function () {
                colorHiddenInput.value = colorCustomInput.value;
                colorSwatches.forEach(function (s) { s.classList.remove('cat-swatch--active'); });
            });
        }

        function resetCategoryForm() {
            if (!categoryForm) { return; }
            categoryForm.action = <?= json_encode(site_url('admin/categories')) ?>;
            categoryTitle.textContent = 'Add Category';
            categorySubmitBtn.textContent = 'Add Category';
            categoryCancelBtn.style.display = 'none';
            categoryForm.reset();
            setPickerColor('');
            if (categoryPanel) { categoryPanel.classList.remove('panel--editing'); }
        }

        document.querySelectorAll('[data-edit-category]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!categoryForm) { return; }
                var id = button.getAttribute('data-edit-category');
                var row = document.querySelector('[data-category-row="1"][data-id="' + id + '"]');
                if (!row) { return; }

                categoryForm.action = <?= json_encode(site_url('admin/categories')) ?> + '/' + id + '/update';
                categoryTitle.textContent = 'Edit Category';
                categorySubmitBtn.textContent = 'Save Changes';
                categoryCancelBtn.style.display = '';

                categoryForm.elements['name'].value = row.getAttribute('data-name') || '';
                categoryForm.elements['description'].value = row.getAttribute('data-description') || '';
                setPickerColor(row.getAttribute('data-color') || '');

                if (categoryPanel) { categoryPanel.classList.add('panel--editing'); }

                openTab('categories', true);

                // Scroll with offset to clear the fixed topbar
                setTimeout(function () {
                    var topbar = document.querySelector('.admin-topbar');
                    var offset = topbar ? topbar.offsetHeight + 16 : 72;
                    var top = categoryPanel
                        ? categoryPanel.getBoundingClientRect().top + window.scrollY - offset
                        : categoryForm.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                    categoryForm.elements['name'].focus();
                }, 50);
            });
        });

        if (categoryCancelBtn) {
            categoryCancelBtn.addEventListener('click', resetCategoryForm);
        }

        resetCategoryForm();
    })();
</script>
<?= $this->endSection() ?>
