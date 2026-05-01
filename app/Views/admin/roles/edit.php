<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php $isSystem = (bool) ($role['is_system'] ?? false); ?>

<div class="cv-form-card cv-form-card-wide">
    <div class="cv-form-head">
        <h2>Edit Role: <?= esc((string) $role['name']) ?></h2>
        <a href="<?= site_url('admin/roles') ?>" class="cv-btn-outline" style="padding:7px 16px;font-size:.82rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <?php if ($isSystem): ?>
        <div class="alert" style="background:var(--cv-amber-bg)!important;border-color:rgba(154,112,16,.3)!important;color:var(--cv-amber)!important;margin:14px 22px 0;">
            This is a protected system role. The name cannot be changed.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('admin/roles/' . (int) $role['id'] . '/update') ?>">
        <div class="cv-form-body">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="cv-field">
                    <label for="name">Role Name</label>
                    <?php if ($isSystem): ?>
                        <input class="cv-input" type="text" id="name"
                               value="<?= esc((string) $role['name']) ?>" disabled>
                    <?php else: ?>
                        <input id="name" name="name" class="cv-input" required minlength="2" maxlength="80"
                               value="<?= esc((string) old('name', (string) $role['name'])) ?>">
                    <?php endif; ?>
                </div>
                <div class="cv-field">
                    <label for="description">Description <small>— optional</small></label>
                    <input id="description" name="description" class="cv-input" maxlength="255"
                           value="<?= esc((string) old('description', (string) ($role['description'] ?? ''))) ?>">
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

                <?php $oldPerms = old('permissions'); ?>
                <?php foreach ($permissionGroups as $groupName => $keys): ?>
                    <div class="cv-card" style="margin-bottom:10px;overflow:visible;">
                        <div class="perm-section-header">
                            <span class="perm-group-name"><?= esc($groupName) ?></span>
                            <span class="perm-group-counter" data-group-counter="<?= esc($groupName) ?>">0/<?= count($keys) ?></span>
                            <div class="perm-section-actions">
                                <button type="button" class="perm-toggle-btn"
                                        onclick="toggleGroup('<?= esc($groupName, 'attr') ?>',true)">All</button>
                                <button type="button" class="perm-toggle-btn"
                                        onclick="toggleGroup('<?= esc($groupName, 'attr') ?>',false)">None</button>
                            </div>
                        </div>
                        <div class="perm-items" data-group="<?= esc($groupName, 'attr') ?>">
                            <?php foreach ($keys as $key): ?>
                                <?php
                                if ($oldPerms !== null) {
                                    $checked = in_array($key, (array) $oldPerms, true);
                                } else {
                                    $checked = ! empty($currentPermissions[$key]);
                                }
                                ?>
                                <label class="cv-toggle">
                                    <input type="checkbox" name="permissions[]" value="<?= esc($key) ?>"
                                           <?= $checked ? 'checked' : '' ?>
                                           onchange="updatePermCounters()">
                                    <span class="cv-track"></span>
                                    <span class="cv-toggle-label"><?= esc(substr($key, strpos($key, '.') + 1)) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

        <div class="cv-form-footer">
            <button type="submit" class="cv-btn-navy">Save Changes</button>
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
