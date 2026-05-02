<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
$permGroupColors = [
    'Dashboard'        => '#3b82f6',
    'Feedback'         => '#10b981',
    'Announcements'    => '#f59e0b',
    'Students'         => '#8b5cf6',
    'Categories'       => '#ec4899',
    'Activity Log'     => '#14b8a6',
    'Student Activity' => '#6366f1',
    'Admin Accounts'   => '#ef4444',
    'Roles'            => '#f97316',
    'Tools'            => '#6b7280',
];
?>

<div class="cv-form-card cv-form-card-wide">
    <div class="cv-form-head">
        <h2>Create Role</h2>
        <a href="<?= site_url('admin/roles') ?>" class="cv-btn-outline" style="padding:7px 16px;font-size:.82rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form method="post" action="<?= site_url('admin/roles') ?>">
        <div class="cv-form-body">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="cv-field">
                    <label for="name">Role Name</label>
                    <input id="name" name="name" class="cv-input" required minlength="2" maxlength="80"
                           placeholder="e.g. Moderator"
                           value="<?= esc((string) old('name', '')) ?>">
                </div>
                <div class="cv-field">
                    <label for="description">Description <small>— optional</small></label>
                    <input id="description" name="description" class="cv-input" maxlength="255"
                           placeholder="Brief description of this role"
                           value="<?= esc((string) old('description', '')) ?>">
                </div>
            </div>

            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-weight:700;color:var(--cv-ink);">Permissions</span>
                    <div style="display:flex;gap:8px;">
                        <span class="cv-perm-total" id="perm-counter">0 selected</span>
                        <button type="button" class="perm-toggle-btn" onclick="toggleAllPermissions(true)">Check All</button>
                        <button type="button" class="perm-toggle-btn" onclick="toggleAllPermissions(false)">Uncheck All</button>
                    </div>
                </div>

                <?php $oldPerms = (array) old('permissions', []); ?>
                <div class="cv-perm-grid">
                <?php foreach ($permissionGroups as $groupName => $keys): ?>
                    <?php $permColor = $permGroupColors[$groupName] ?? '#6b7280'; ?>
                    <div class="cv-card perm-card" style="--perm-color:<?= $permColor ?>;overflow:visible;">
                        <div class="perm-section-header">
                            <span class="perm-group-name"><?= esc($groupName) ?></span>
                            <span class="perm-group-counter" data-group-counter="<?= esc($groupName) ?>">0/<?= count($keys) ?></span>
                            <div class="perm-section-actions">
                                <button type="button" class="perm-toggle-btn"
                                        onclick="toggleGroup('<?= esc($groupName, 'attr') ?>',true)">Check All</button>
                                <button type="button" class="perm-toggle-btn"
                                        onclick="toggleGroup('<?= esc($groupName, 'attr') ?>',false)">Uncheck All</button>
                            </div>
                        </div>
                        <div class="perm-items" data-group="<?= esc($groupName, 'attr') ?>">
                            <?php foreach ($keys as $key): ?>
                                <?php $checked = in_array($key, $oldPerms, true); ?>
                                <label class="cv-toggle">
                                    <input type="checkbox" name="permissions[]" value="<?= esc($key) ?>"
                                           <?= $checked ? 'checked' : '' ?>
                                           onchange="updatePermCounters()">
                                    <span class="cv-track"></span>
                                    <span class="cv-toggle-label"><?= esc(ucwords(str_replace('_', ' ', substr($key, strpos($key, '.') + 1)))) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>

        </div>

        <div class="cv-form-footer cv-form-footer-sticky">
            <button type="submit" class="cv-btn-navy">Create Role</button>
            <a href="<?= site_url('admin/roles') ?>" class="cv-btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function updatePermCounters() {
    var total = 0;
    document.querySelectorAll('[data-group]').forEach(function(grp) {
        var name = grp.getAttribute('data-group');
        var boxes = grp.querySelectorAll('input[type=checkbox]');
        var checked = grp.querySelectorAll('input[type=checkbox]:checked').length;
        total += checked;
        var counter = document.querySelector('[data-group-counter="'+name+'"]');
        if (counter) counter.textContent = checked + '/' + boxes.length;
    });
    var el = document.getElementById('perm-counter');
    if (el) el.textContent = total + ' selected';
}

function toggleAllPermissions(check) {
    document.querySelectorAll('input[name="permissions[]"]').forEach(function(cb) { cb.checked = check; });
    updatePermCounters();
}

function toggleGroup(name, check) {
    var grp = document.querySelector('[data-group="'+name+'"]');
    if (!grp) return;
    grp.querySelectorAll('input[type=checkbox]').forEach(function(cb) { cb.checked = check; });
    updatePermCounters();
}

updatePermCounters();
</script>
<?= $this->endSection() ?>
