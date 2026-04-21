<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
$canViewActivity = ! empty($canViewActivity);
$allowedTabs = ['overview', 'feedback', 'announcements', 'users', 'categories'];
if ($canViewActivity) {
    $allowedTabs[] = 'activity';
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
        <button class="tab-btn" type="button" data-tab-trigger="activity">Activity</button>
    <?php endif; ?>
</div>

<section class="tab-panel active" data-tab-panel="overview">
    <div class="stats-grid">
        <article class="stat-card">
            <span>Total Feedback</span>
            <strong><?= esc((string) ($stats['feedback_total'] ?? 0)) ?></strong>
        </article>
        <article class="stat-card">
            <span>New</span>
            <strong><?= esc((string) ($stats['feedback_new'] ?? 0)) ?></strong>
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

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th style="cursor:pointer" onclick="sortTableById()">#</th>
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
            </div>
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

<section class="tab-panel" data-tab-panel="feedback">
    <section class="panel">
        <div class="panel-head stack-mobile">
            <h2>Feedback Management</h2>
            <div class="filter-row">
                <input id="feedback-search" type="text" placeholder="Search subject, message, category, student">
                <select id="feedback-status-filter">
                    <option value="">All Statuses</option>
                    <option value="new">New</option>
                    <option value="reviewed">Reviewed</option>
                    <option value="resolved">Resolved</option>
                </select>
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

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Quick Action</th>
                </tr>
                </thead>
                <tbody id="feedback-table-body">
                <?php if (! empty($feedbackList)): ?>
                    <?php foreach ($feedbackList as $item): ?>
                        <?php
                        $searchBlob = strtolower(
                            trim(
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
                            data-status="<?= esc((string) $item['status']) ?>"
                            data-type="<?= esc((string) $item['type']) ?>"
                            data-category="<?= (int) ($item['category_id'] ?? 0) ?>"
                            data-search="<?= esc($searchBlob, 'attr') ?>"
                        >
                            <td><a href="<?= site_url('admin/feedback/' . (int) $item['id']) ?>">#<?= (int) $item['id'] ?></a></td>
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
                                    Anonymous
                                <?php else: ?>
                                    <?= esc(trim(((string) ($item['first_name'] ?? '')) . ' ' . ((string) ($item['last_name'] ?? '')))) ?>
                                <?php endif; ?>
                            </td>
                            <td><span class="pill status-<?= esc((string) $item['status']) ?>"><?= esc(ucfirst((string) $item['status'])) ?></span></td>
                            <td>
                                <form method="post" action="<?= site_url('admin/feedback/' . (int) $item['id'] . '/status') ?>" class="inline-status-form" data-current-status="<?= esc((string) $item['status']) ?>">
                                    <select name="status">
                                        <?php foreach (['new', 'reviewed', 'resolved'] as $status): ?>
                                            <option value="<?= esc($status) ?>" <?= ((string) $item['status'] === $status) ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="admin_notes" value="">
                                    <button class="mini-btn" type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No feedback available.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>

<!-- Feedback Status Update Modal -->
<div class="modal-overlay" id="statusUpdateModal" hidden>
    <div class="modal-card" style="max-width:460px; padding:24px 28px;">
        <div class="modal-head">
            <h3 style="margin:0; font-size:1rem;">Mark as <span id="statusModalLabel" style="text-transform:capitalize;"></span></h3>
        </div>
        <p style="margin:0 0 12px; font-size:0.875rem; color:#5f7298;">Optionally add a message for the student explaining this status change. Leave blank to skip.</p>
        <textarea id="statusModalNotes" rows="4" placeholder="e.g. We have reviewed your concern and will address it shortly…" style="width:100%; box-sizing:border-box; padding:10px 12px; border:1px solid #c8d8ff; border-radius:8px; font-size:0.875rem; resize:vertical; min-height:90px; font-family:inherit;"></textarea>
        <div style="display:flex; gap:10px; margin-top:16px; justify-content:flex-end;">
            <button type="button" id="statusModalCancel" class="mini-btn secondary">Cancel</button>
            <button type="button" id="statusModalConfirm" class="mini-btn">Save Status</button>
        </div>
    </div>
</div>
<script>
(function () {
    var modal   = document.getElementById('statusUpdateModal');
    var label   = document.getElementById('statusModalLabel');
    var notes   = document.getElementById('statusModalNotes');
    var btnCancel  = document.getElementById('statusModalCancel');
    var btnConfirm = document.getElementById('statusModalConfirm');
    var pendingForm = null;

    document.querySelectorAll('.inline-status-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var select = form.querySelector('select[name="status"]');
            if (!select) return;
            var chosen = select.value;
            if (chosen === 'reviewed' || chosen === 'resolved') {
                e.preventDefault();
                pendingForm = form;
                label.textContent = chosen.charAt(0).toUpperCase() + chosen.slice(1);
                notes.value = '';
                modal.removeAttribute('hidden');
                setTimeout(function () { notes.focus(); }, 50);
            }
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

<section class="tab-panel" data-tab-panel="announcements">
    <div class="panel-grid">
        <section class="panel">
            <div class="panel-head">
                <h2 id="announcement-form-title">Create Announcement</h2>
            </div>

            <form method="post" action="<?= site_url('admin/announcements') ?>" class="form-grid" id="announcement-form">
                <label for="ann-title">Title</label>
                <input id="ann-title" name="title" required maxlength="180" placeholder="Title">

                <label for="ann-body">Body</label>
                <textarea id="ann-body" name="body" rows="8" required placeholder="Announcement content"></textarea>

                <label for="ann-publish">Publish At</label>
                <input id="ann-publish" name="publish_at" type="datetime-local" min="<?= date('Y-m-d') ?>T00:00">

                <label for="ann-expires">Expires At</label>
                <input id="ann-expires" name="expires_at" type="datetime-local" min="<?= date('Y-m-d') ?>T00:00">

                <label for="ann-is-published">Status</label>
                <select id="ann-is-published" name="is_published">
                    <option value="1">Published</option>
                    <option value="0">Draft</option>
                </select>

                <div class="form-actions">
                    <button type="submit" id="announcement-submit-btn">Publish Announcement</button>
                    <button type="button" class="btn-link secondary" id="announcement-cancel-edit" style="display:none;">Cancel Edit</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Announcement List</h2>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (! empty($announcements)): ?>
                        <?php foreach ($announcements as $item): ?>
                            <tr
                                data-announcement-row="1"
                                data-id="<?= (int) $item['id'] ?>"
                                data-title="<?= esc((string) $item['title'], 'attr') ?>"
                                data-body="<?= esc((string) $item['body'], 'attr') ?>"
                                data-audience="<?= esc((string) $item['audience'], 'attr') ?>"
                                data-is-published="<?= (int) ($item['is_published'] ?? 0) ?>"
                                data-publish-at="<?= esc((string) ($item['publish_at'] ?? ''), 'attr') ?>"
                                data-expires-at="<?= esc((string) ($item['expires_at'] ?? ''), 'attr') ?>"
                            >
                                <td>
                                    <strong><?= esc((string) $item['title']) ?></strong>
                                    <div class="muted"><?= esc(strlen((string) $item['body']) > 70 ? substr((string) $item['body'], 0, 70) . '...' : (string) $item['body']) ?></div>
                                </td>
                                <td>
                                    <?php if ((int) ($item['is_published'] ?? 0) === 1): ?>
                                        <span class="pill status-reviewed">Published</span>
                                    <?php else: ?>
                                        <span class="pill status-new">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="text-btn" data-edit-announcement="<?= (int) $item['id'] ?>">Edit</button>
                                    <form method="post" action="<?= site_url('admin/announcements/' . (int) $item['id'] . '/delete') ?>" class="inline-form" onsubmit="return confirm('Delete this announcement?');">
                                        <button class="text-btn danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No announcements available.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</section>


<?php
$usersList = $usersList ?? [];
$allCategories = $allCategories ?? [];
?>

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

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
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
                            <td><?= esc((string) ($u['email'] ?? '')) ?></td>
                            <td>
                                <?php if ($uActive === 1): ?>
                                    <span class="pill status-reviewed">Active</span>
                                <?php else: ?>
                                    <span class="pill status-new">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= ! empty($u['last_login_at']) ? esc(date('M d, Y H:i', strtotime((string) $u['last_login_at']))) : '<span class="muted">Never</span>' ?></td>
                            <td>
                                <form method="post" action="<?= site_url('admin/users/' . (int) $u['id'] . '/toggle-status') ?>" class="inline-form">
                                    <button class="mini-btn" type="submit"><?= $uActive === 1 ? 'Deactivate' : 'Activate' ?></button>
                                </form>
                                <form method="post" action="<?= site_url('admin/users/' . (int) $u['id'] . '/send-reset') ?>" class="inline-form" onsubmit="return confirm('Reset password for <?= esc($uName, 'attr') ?>?');">
                                    <button class="mini-btn secondary" type="submit">Reset PW</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No student records found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>

<section class="tab-panel" data-tab-panel="categories">
    <div class="panel-grid">
        <section class="panel">
            <div class="panel-head">
                <h2 id="category-form-title">Add Category</h2>
            </div>

            <form method="post" action="<?= site_url('admin/categories') ?>" class="form-grid" id="category-form">
                <label for="cat-name">Name</label>
                <input id="cat-name" name="name" required maxlength="100" placeholder="e.g. Facility">

                <label for="cat-desc">Description</label>
                <textarea id="cat-desc" name="description" rows="3" maxlength="500" placeholder="Optional description"></textarea>

                <div class="form-actions">
                    <button type="submit" id="category-submit-btn">Add Category</button>
                    <button type="button" class="btn-link secondary" id="category-cancel-edit" style="display:none;">Cancel Edit</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Category List</h2>
            </div>

            <div class="table-wrap">
                <table class="data-table">
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
                            <tr
                                data-category-row="1"
                                data-id="<?= (int) $cat['id'] ?>"
                                data-name="<?= esc((string) $cat['name'], 'attr') ?>"
                                data-description="<?= esc((string) ($cat['description'] ?? ''), 'attr') ?>"
                            >
                                <td><strong><?= esc((string) $cat['name']) ?></strong></td>
                                <td><?= esc(strlen((string) ($cat['description'] ?? '')) > 60 ? substr((string) $cat['description'], 0, 60) . '...' : (string) ($cat['description'] ?? '—')) ?></td>
                                <td>
                                    <?php if ((int) ($cat['is_active'] ?? 1) === 1): ?>
                                        <span class="pill status-reviewed">Active</span>
                                    <?php else: ?>
                                        <span class="pill status-new">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="text-btn" data-edit-category="<?= (int) $cat['id'] ?>">Edit</button>
                                    <form method="post" action="<?= site_url('admin/categories/' . (int) $cat['id'] . '/toggle') ?>" class="inline-form">
                                        <button class="text-btn" type="submit"><?= (int) ($cat['is_active'] ?? 1) === 1 ? 'Disable' : 'Enable' ?></button>
                                    </form>
                                    <form method="post" action="<?= site_url('admin/categories/' . (int) $cat['id'] . '/delete') ?>" class="inline-form" onsubmit="return confirm('Delete category &quot;<?= esc((string) $cat['name'], 'attr') ?>&quot;?');">
                                        <button class="text-btn danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No categories found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</section>

<?php if ($canViewActivity): ?>
    <div class="export-toast" id="export-toast" hidden>
        <span id="export-toast-text"></span>
        <button type="button" class="export-toast-close" id="export-toast-close" aria-label="Dismiss">&#x2715;</button>
    </div>
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
    $exportQuery = array_filter($activityFilterQuery, static function ($value): bool {
        return $value !== '' && $value !== 0 && $value !== null;
    });
    $currentPage = (int) ($activityPagination['page'] ?? 1);
    $totalPages = (int) ($activityPagination['pages'] ?? 1);
    $totalRows = (int) ($activityPagination['total'] ?? 0);
    $exportMaxRows = (int) ($activityExportMaxRows ?? 20000);
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
        <section class="panel">
            <div class="panel-head stack-mobile">
                <h2>Admin Activity Log</h2>
                <div class="activity-toolbar">
                    <span class="muted"><?= esc((string) $totalRows) ?> matching records</span>
                    <a class="btn-link secondary" id="export-csv-btn" href="<?= site_url('admin/activity/export?' . http_build_query($exportQuery)) ?>" data-export-max="<?= esc((string) $exportMaxRows) ?>">Export CSV</a>
                </div>
            </div>
            <p class="muted activity-note">Export is processed in batches and capped at <?= esc((string) $exportMaxRows) ?> rows per download.</p>

            <form method="get" action="<?= site_url('admin') ?>" class="activity-filter-form">
                <input type="hidden" name="tab" value="activity">
                <input type="text" name="activity_q" value="<?= esc((string) ($activityFilters['q'] ?? '')) ?>" placeholder="Search action, description, admin, IP">
                <select name="activity_action">
                    <option value="">All Actions</option>
                    <?php foreach (($activityActionOptions ?? []) as $actionOption): ?>
                        <option value="<?= esc((string) $actionOption) ?>" <?= ((string) ($activityFilters['action'] ?? '') === (string) $actionOption) ? 'selected' : '' ?>>
                            <?= esc((string) $actionOption) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="activity_admin_id">
                    <option value="0">All Admins</option>
                    <?php foreach (($activityAdminOptions ?? []) as $adminOption): ?>
                        <?php $adminName = trim(((string) ($adminOption['first_name'] ?? '')) . ' ' . ((string) ($adminOption['last_name'] ?? ''))); ?>
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

            <form method="post" action="<?= site_url('admin/activity/purge') ?>" class="activity-purge-form" onsubmit="return confirm('Delete all logs older than selected retention?');">
                <label for="retention-days">Cleanup Retention</label>
                <select id="retention-days" name="retention_days" required>
                    <?php foreach ($retentionOptions as $option): ?>
                        <option value="<?= (int) $option ?>" <?= ((int) $option === 180) ? 'selected' : '' ?>>Keep last <?= (int) $option ?> days</option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="confirm_text" placeholder="Type PURGE" required>
                <button type="submit" class="danger-action">Purge Old Logs</button>
            </form>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('created_at')) ?>#activity">Time<?= $sortIndicator('created_at') ?></a></th>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('admin')) ?>#activity">Admin<?= $sortIndicator('admin') ?></a></th>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('action')) ?>#activity">Action<?= $sortIndicator('action') ?></a></th>
                        <th>Description</th>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('target')) ?>#activity">Target<?= $sortIndicator('target') ?></a></th>
                        <th><a class="sort-link" href="<?= site_url('admin?' . $buildSortQuery('ip')) ?>#activity">IP<?= $sortIndicator('ip') ?></a></th>
                        <th>Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (! empty($activityLogs)): ?>
                        <?php foreach ($activityLogs as $log): ?>
                            <?php
                            $fullName = trim(((string) ($log['first_name'] ?? '')) . ' ' . ((string) ($log['last_name'] ?? '')));
                            $targetLabel = '-';
                            if (! empty($log['target_type']) && ! empty($log['target_id'])) {
                                $targetLabel = (string) $log['target_type'] . ' #' . (int) $log['target_id'];
                            } elseif (! empty($log['target_type'])) {
                                $targetLabel = (string) $log['target_type'];
                            }

                            $metadataRaw = (string) ($log['metadata'] ?? '');
                            $metadataDecoded = null;
                            if ($metadataRaw !== '') {
                                $metadataDecoded = json_decode($metadataRaw, true);
                            }

                            $detailPayload = [
                                'time'        => (string) ($log['created_at'] ?? ''),
                                'admin_name'  => $fullName !== '' ? $fullName : 'System',
                                'admin_email' => (string) ($log['email'] ?? ''),
                                'action'      => (string) ($log['action'] ?? ''),
                                'description' => (string) ($log['description'] ?? ''),
                                'target'      => $targetLabel,
                                'ip_address'  => (string) ($log['ip_address'] ?? ''),
                                'user_agent'  => (string) ($log['user_agent'] ?? ''),
                                'metadata'    => is_array($metadataDecoded) ? $metadataDecoded : ($metadataRaw !== '' ? ['raw' => $metadataRaw] : []),
                            ];
                            $detailPayloadJson = json_encode($detailPayload, JSON_UNESCAPED_SLASHES);
                            ?>
                            <tr>
                                <td><?= esc((string) date('M d, Y H:i:s', strtotime((string) ($log['created_at'] ?? 'now')))) ?></td>
                                <td>
                                    <?= esc($fullName !== '' ? $fullName : 'System') ?>
                                    <?php if (! empty($log['email'])): ?>
                                        <div class="muted"><?= esc((string) $log['email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="pill status-reviewed"><?= esc((string) ($log['action'] ?? 'unknown')) ?></span></td>
                                <td><?= esc((string) ($log['description'] ?? 'No description')) ?></td>
                                <td><?= esc($targetLabel) ?></td>
                                <td><?= esc((string) ($log['ip_address'] ?? '-')) ?></td>
                                <td>
                                    <button
                                        type="button"
                                        class="text-btn"
                                        data-activity-details="<?= esc((string) ($detailPayloadJson ?: '{}'), 'attr') ?>"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No activity logs yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

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

        <div class="modal-overlay" id="activity-detail-modal" hidden>
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="activity-detail-title">
                <div class="modal-head">
                    <h3 id="activity-detail-title">Activity Details</h3>
                    <button type="button" class="text-btn" id="activity-detail-close">Close</button>
                </div>
                <div class="detail-grid single-column-mobile">
                    <div>
                        <h4>Time</h4>
                        <p id="activity-detail-time">-</p>
                    </div>
                    <div>
                        <h4>Admin</h4>
                        <p id="activity-detail-admin">-</p>
                    </div>
                    <div>
                        <h4>Action</h4>
                        <p id="activity-detail-action">-</p>
                    </div>
                    <div>
                        <h4>Target</h4>
                        <p id="activity-detail-target">-</p>
                    </div>
                    <div>
                        <h4>IP Address</h4>
                        <p id="activity-detail-ip">-</p>
                    </div>
                    <div>
                        <h4>User Agent</h4>
                        <p id="activity-detail-user-agent">-</p>
                    </div>
                </div>

                <h4>Description</h4>
                <p id="activity-detail-description" class="message-box">-</p>

                <h4>Metadata</h4>
                <pre id="activity-detail-metadata" class="code-box activity-json">{}</pre>
            </div>
        </div>
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

        function resolveInitialTab() {
            const hash = window.location.hash.replace('#', '').trim();
            if (allowedTabs.includes(hash)) {
                return hash;
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
        const feedbackStatusFilter = document.getElementById('feedback-status-filter');
        const feedbackTypeFilter = document.getElementById('feedback-type-filter');
        const feedbackCategoryFilter = document.getElementById('feedback-category-filter');

        function filterFeedbackRows() {
            const query = (feedbackSearch.value || '').toLowerCase().trim();
            const status = feedbackStatusFilter.value;
            const type = feedbackTypeFilter.value;
            const category = feedbackCategoryFilter.value;

            document.querySelectorAll('[data-feedback-row="1"]').forEach(function (row) {
                const rowStatus = row.getAttribute('data-status') || '';
                const rowType = row.getAttribute('data-type') || '';
                const rowCategory = row.getAttribute('data-category') || '';
                const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();

                const matchesStatus = status === '' || rowStatus === status;
                const matchesType = type === '' || rowType === type;
                const matchesCategory = category === '' || rowCategory === category;
                const matchesSearch = query === '' || rowSearch.indexOf(query) !== -1;

                row.style.display = (matchesStatus && matchesType && matchesCategory && matchesSearch) ? '' : 'none';
            });
        }

        [feedbackSearch, feedbackStatusFilter, feedbackTypeFilter, feedbackCategoryFilter].forEach(function (input) {
            if (input) {
                input.addEventListener('input', filterFeedbackRows);
                input.addEventListener('change', filterFeedbackRows);
            }
        });

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

        function toDateTimeLocal(value) {
            if (!value) {
                return '';
            }

            const normalized = value.replace(' ', 'T');
            return normalized.length >= 16 ? normalized.slice(0, 16) : '';
        }

        function resetAnnouncementForm() {
            if (!announcementForm) {
                return;
            }

            announcementForm.action = <?= json_encode(site_url('admin/announcements')) ?>;
            announcementTitle.textContent = 'Create Announcement';
            announcementSubmitBtn.textContent = 'Publish Announcement';
            announcementCancelBtn.style.display = 'none';
            announcementForm.reset();
            announcementForm.elements.is_published.value = '1';
        }

        document.querySelectorAll('[data-edit-announcement]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!announcementForm) {
                    return;
                }

                const id = button.getAttribute('data-edit-announcement');
                const row = document.querySelector('[data-announcement-row="1"][data-id="' + id + '"]');
                if (!row) {
                    return;
                }

                announcementForm.action = <?= json_encode(site_url('admin/announcements')) ?> + '/' + id;
                announcementTitle.textContent = 'Edit Announcement #' + id;
                announcementSubmitBtn.textContent = 'Save Changes';
                announcementCancelBtn.style.display = '';

                announcementForm.elements.title.value = row.getAttribute('data-title') || '';
                announcementForm.elements.body.value = row.getAttribute('data-body') || '';
                announcementForm.elements.is_published.value = row.getAttribute('data-is-published') || '1';
                announcementForm.elements.publish_at.value = toDateTimeLocal(row.getAttribute('data-publish-at') || '');
                announcementForm.elements.expires_at.value = toDateTimeLocal(row.getAttribute('data-expires-at') || '');

                openTab('announcements', true);
                announcementForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        if (announcementCancelBtn) {
            announcementCancelBtn.addEventListener('click', function () {
                resetAnnouncementForm();
            });
        }

        resetAnnouncementForm();

        otpRequestForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            const email = otpRequestForm.elements.email.value.trim();

            const res = await postJson(apiBase + '/auth/password/otp/request', { email: email }, requestStatus, requestResult);
            if (res.ok) {
                otpVerifyForm.elements.email.value = email;
                otpResetForm.elements.email.value = email;
            }
        });

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

        const activityModal = document.getElementById('activity-detail-modal');
        const activityCloseBtn = document.getElementById('activity-detail-close');
        const activityTimeEl = document.getElementById('activity-detail-time');
        const activityAdminEl = document.getElementById('activity-detail-admin');
        const activityActionEl = document.getElementById('activity-detail-action');
        const activityTargetEl = document.getElementById('activity-detail-target');
        const activityIpEl = document.getElementById('activity-detail-ip');
        const activityUaEl = document.getElementById('activity-detail-user-agent');
        const activityDescriptionEl = document.getElementById('activity-detail-description');
        const activityMetadataEl = document.getElementById('activity-detail-metadata');

        function closeActivityModal() {
            if (!activityModal) {
                return;
            }

            activityModal.setAttribute('hidden', 'hidden');
        }

        function openActivityModal(payload) {
            if (!activityModal) {
                return;
            }

            activityTimeEl.textContent = payload.time || '-';
            const adminLine = payload.admin_email
                ? (payload.admin_name || 'System') + ' (' + payload.admin_email + ')'
                : (payload.admin_name || 'System');
            activityAdminEl.textContent = adminLine;
            activityActionEl.textContent = payload.action || '-';
            activityTargetEl.textContent = payload.target || '-';
            activityIpEl.textContent = payload.ip_address || '-';
            activityUaEl.textContent = payload.user_agent || '-';
            activityDescriptionEl.textContent = payload.description || '-';

            const metadata = (payload.metadata && typeof payload.metadata === 'object') ? payload.metadata : {};
            activityMetadataEl.textContent = JSON.stringify(metadata, null, 2);

            activityModal.removeAttribute('hidden');
        }

        document.querySelectorAll('[data-activity-details]').forEach(function (button) {
            button.addEventListener('click', function () {
                const raw = button.getAttribute('data-activity-details') || '{}';
                let payload = {};

                try {
                    payload = JSON.parse(raw);
                } catch (error) {
                    payload = { metadata: { raw: raw } };
                }

                openActivityModal(payload);
            });
        });

        if (activityCloseBtn) {
            activityCloseBtn.addEventListener('click', closeActivityModal);
        }

        if (activityModal) {
            activityModal.addEventListener('click', function (event) {
                if (event.target === activityModal) {
                    closeActivityModal();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !activityModal.hasAttribute('hidden')) {
                    closeActivityModal();
                }
            });
        }

        var exportToast = document.getElementById('export-toast');
        var exportToastText = document.getElementById('export-toast-text');
        var exportToastClose = document.getElementById('export-toast-close');
        var exportToastTimer = null;
        var exportToastDismiss = null;

        function showExportToast(message, type) {
            if (!exportToast) { return; }
            exportToastText.textContent = message;
            exportToast.className = 'export-toast export-toast--' + (type || 'info');
            exportToast.removeAttribute('hidden');

            clearTimeout(exportToastDismiss);
            exportToastDismiss = setTimeout(function () {
                exportToast.setAttribute('hidden', 'hidden');
            }, 8000);
        }

        if (exportToastClose) {
            exportToastClose.addEventListener('click', function () {
                clearTimeout(exportToastDismiss);
                exportToast.setAttribute('hidden', 'hidden');
            });
        }

        var exportBtn = document.getElementById('export-csv-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                var maxRows = parseInt(exportBtn.getAttribute('data-export-max') || '20000', 10);
                clearTimeout(exportToastTimer);
                exportToastTimer = setTimeout(function () {
                    showExportToast(
                        'Export downloaded. Large datasets are capped at ' + maxRows.toLocaleString() + ' rows. Apply date or action filters to narrow the export.',
                        'info'
                    );
                }, 5000);
            });
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

        function resetCategoryForm() {
            if (!categoryForm) { return; }
            categoryForm.action = <?= json_encode(site_url('admin/categories')) ?>;
            categoryTitle.textContent = 'Add Category';
            categorySubmitBtn.textContent = 'Add Category';
            categoryCancelBtn.style.display = 'none';
            categoryForm.reset();
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

                categoryForm.elements.name.value = row.getAttribute('data-name') || '';
                categoryForm.elements.description.value = row.getAttribute('data-description') || '';

                openTab('categories', true);
                categoryForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        if (categoryCancelBtn) {
            categoryCancelBtn.addEventListener('click', resetCategoryForm);
        }

        resetCategoryForm();
    })();
</script>
<?= $this->endSection() ?>
