<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
$me = $adminUser ?? [];
$canCreate      = ! empty($me['permissions']['admin.create']);
$canEdit        = ! empty($me['permissions']['admin.edit']);
$canDelete      = ! empty($me['permissions']['admin.delete']);
?>


<!-- Content Card -->
<div class="cv-table-card">
    <!-- Toolbar -->
    <div class="cv-table-toolbar">
        <div class="cv-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="admin-search" class="cv-search" type="text" placeholder="Search name or email…">
        </div>
        <?php if ($canCreate): ?>
            <a href="<?= site_url('admin/admins/create') ?>" class="cv-btn-gold">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Admin
            </a>
        <?php endif; ?>
    </div>

    <!-- Admin Table -->
    <table class="cv-admin-table" id="admins-table">
        <thead>
            <tr>
                <th class="col-admin">Admin</th>
                <th class="col-role">Role</th>
                <th class="col-status">Status</th>
                <th class="col-login">Last Login</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (! empty($users)): ?>
                <?php foreach ($users as $u): ?>
                    <?php
                    $uActive   = (int) ($u['is_active'] ?? 1);
                    $uLocked   = ! empty($u['locked_until']) && strtotime((string) $u['locked_until']) > time();
                    $uIsSelf   = (int) ($me['id'] ?? 0) === (int) $u['id'];
                    $uSearch   = strtolower(((string) $u['full_name']) . ' ' . ((string) $u['email']));
                    $uName     = (string) $u['full_name'];
                    $uWords    = array_filter(explode(' ', $uName));
                    $uInitials = strtoupper(implode('', array_map(fn($w) => $w[0], $uWords)));
                    $uInitials = substr($uInitials, 0, 2) ?: 'AD';
                    $avatarIdx = ((int) $u['id'] % 6) + 1;
                    $roleName  = (string) ($u['role_name'] ?? 'Unknown');
                    $roleSlug  = strtolower(str_replace(' ', '', $roleName));
                    ?>
                    <tr data-search="<?= esc($uSearch, 'attr') ?>">
                        <!-- Admin (avatar + name + email) -->
                        <td class="col-admin">
                            <div class="admin-cell">
                                <div class="cv-avatar cv-avatar-<?= $avatarIdx ?>"><?= esc($uInitials) ?></div>
                                <div class="admin-identity">
                                    <span class="admin-name">
                                        <?= esc($uName) ?>
                                        <?php if ($uIsSelf): ?>
                                            <span class="cv-you-tag">You</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="admin-email"><?= esc((string) $u['email']) ?></span>
                                    <?php if ($uLocked): ?>
                                        <span class="cv-locked-tag">🔒 Locked until <?= esc(date('H:i M d', strtotime((string) $u['locked_until']))) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="col-role">
                            <span class="role-pill role-<?= esc($roleSlug, 'attr') ?>"><?= esc($roleName) ?></span>
                        </td>

                        <!-- Status -->
                        <td class="col-status">
                            <?php if ($uActive === 1): ?>
                                <span class="status-pill status-active">Active</span>
                            <?php else: ?>
                                <span class="status-pill status-inactive">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <!-- Last Login -->
                        <td class="col-login">
                            <?php if (! empty($u['last_login_at'])): ?>
                                <span class="login-date"><?= esc(date('M d, Y', strtotime((string) $u['last_login_at']))) ?></span>
                                <span class="login-time"><?= esc(date('H:i', strtotime((string) $u['last_login_at']))) ?></span>
                            <?php else: ?>
                                <span class="login-never">Never</span>
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td class="col-actions">
                            <div class="action-btns">
                                <?php if ($canEdit): ?>
                                    <a href="<?= site_url('admin/admins/' . (int) $u['id'] . '/edit') ?>" class="act-btn act-edit">Edit</a>
                                <?php endif; ?>

                                <?php if ($canEdit && ! $uIsSelf): ?>
                                    <form method="post" action="<?= site_url('admin/admins/' . (int) $u['id'] . '/toggle-status') ?>" style="display:contents;">
                                        <button type="submit" class="act-btn <?= $uActive === 1 ? 'act-deactivate' : 'act-activate' ?>"
                                                onclick="return cvConfirm(this, '<?= $uActive === 1 ? 'Deactivate' : 'Activate' ?> Admin', 'Are you sure you want to <?= $uActive === 1 ? 'deactivate' : 'activate' ?> <?= esc((string)$u['full_name'], 'attr') ?>?', '<?= $uActive === 1 ? 'Deactivate' : 'Activate' ?>')">
                                            <?= $uActive === 1 ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($canEdit && $uLocked): ?>
                                    <form method="post" action="<?= site_url('admin/admins/' . (int) $u['id'] . '/unlock') ?>" style="display:contents;">
                                        <button type="submit" class="act-btn act-activate">Unlock</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($canDelete && ! $uIsSelf): ?>
                                    <form method="post" action="<?= site_url('admin/admins/' . (int) $u['id'] . '/delete') ?>" style="display:contents;">
                                        <button type="submit" class="act-btn act-delete"
                                                onclick="return cvConfirm(this, 'Delete Admin Account', 'Permanently delete <strong><?= esc((string)$u['full_name'], 'attr') ?></strong> (<?= esc((string)$u['email'], 'attr') ?>)? This cannot be undone.', 'Delete', true)">
                                            Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-state">No admin accounts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Confirmation Modal -->
<div class="cv-modal-overlay hidden" id="cv-modal">
    <div class="cv-modal" role="dialog" aria-modal="true">
        <div class="cv-modal-head">
            <div class="cv-modal-icon" id="cv-modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </div>
            <h3 id="cv-modal-title">Confirm Action</h3>
        </div>
        <div class="cv-modal-body" id="cv-modal-body">Are you sure?</div>
        <div class="cv-modal-foot">
            <button type="button" class="cv-btn-outline" onclick="cvModalClose()">Cancel</button>
            <button type="button" class="cv-btn-danger" id="cv-modal-confirm">Confirm</button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="cv-toast-container"></div>

<script>
(function () {
    // Search
    var search = document.getElementById('admin-search');
    var rows   = document.querySelectorAll('#admins-table tbody tr[data-search]');
    if (search) {
        search.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            rows.forEach(function (row) {
                row.style.display = (q === '' || row.getAttribute('data-search').includes(q)) ? '' : 'none';
            });
        });
    }

    // Modal
    var modal     = document.getElementById('cv-modal');
    var modalTitle= document.getElementById('cv-modal-title');
    var modalBody = document.getElementById('cv-modal-body');
    var modalBtn  = document.getElementById('cv-modal-confirm');
    var pendingBtn = null;

    window.cvConfirm = function(btn, title, body, label, isDanger) {
        pendingBtn = btn;
        modalTitle.textContent = title;
        modalBody.innerHTML = body;
        modalBtn.textContent = label || 'Confirm';
        modalBtn.className = isDanger ? 'cv-btn-danger' : 'cv-btn-navy';
        modal.classList.remove('hidden');
        return false;
    };

    window.cvModalClose = function() {
        modal.classList.add('hidden');
        pendingBtn = null;
    };

    modalBtn.addEventListener('click', function () {
        if (pendingBtn) {
            var form = pendingBtn.closest('form');
            if (form) {
                var old = pendingBtn.getAttribute('onclick');
                pendingBtn.removeAttribute('onclick');
                form.submit();
                if (old) pendingBtn.setAttribute('onclick', old);
            }
        }
        cvModalClose();
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) cvModalClose();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cvModalClose();
    });
}());
</script>
<?= $this->endSection() ?>
