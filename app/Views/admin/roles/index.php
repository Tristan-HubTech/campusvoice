<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
$me        = $adminUser ?? [];
$canCreate = ! empty($me['permissions']['roles.create']);
$canEdit   = ! empty($me['permissions']['roles.edit']);
$canDelete = ! empty($me['permissions']['roles.delete']);
?>


<div class="cv-card" style="margin-bottom:20px;">
    <div class="cv-card-header">
        <h2>All Roles</h2>
        <?php if ($canCreate): ?>
            <a href="<?= site_url('admin/roles/create') ?>" class="cv-btn-gold">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Role
            </a>
        <?php endif; ?></div>

    <?php if (! empty($roles)): ?>
        <div class="cv-roles-grid" style="padding:20px;">
            <?php foreach ($roles as $role): ?>
                <?php
                $perms    = json_decode((string) ($role['permissions'] ?? '{}'), true);
                $perms    = is_array($perms) ? $perms : [];
                $granted  = count(array_filter($perms));
                $total    = count($perms);
                $pct      = $total > 0 ? round(($granted / $total) * 100) : 0;
                $isSystem = (bool) ($role['is_system'] ?? false);
                $admins   = (int) ($role['user_count'] ?? 0);
                ?>
                <div class="cv-role-card">
                    <div class="cv-role-card-top">
                        <span class="cv-role-title"><?= esc((string) $role['name']) ?></span>
                        <div style="display:flex;gap:5px;flex-shrink:0;">
                            <?php if ($isSystem): ?>
                                <span class="cv-badge cv-badge-system">System</span>
                            <?php else: ?>
                                <span class="cv-badge cv-badge-active">Custom</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <p class="cv-role-desc">
                        <?= esc((string) ($role['description'] ?? '—')) ?>
                    </p>

                    <div class="cv-role-stats">
                        <div class="cv-role-stat">
                            <strong><?= $granted ?>/<?= $total ?></strong>
                            <span>Permissions</span>
                        </div>
                        <div class="cv-role-stat">
                            <strong><?= $admins ?></strong>
                            <span>Admins</span>
                        </div>
                    </div>


                    <div class="cv-role-actions">
                        <?php if ($canEdit): ?>
                            <a href="<?= site_url('admin/roles/' . (int) $role['id'] . '/edit') ?>"
                               class="cv-icon-btn cv-ib-edit" title="Edit role">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit Role
                            </a>
                        <?php endif; ?>
                        <?php if ($canDelete && ! $isSystem): ?>
                            <form method="post" action="<?= site_url('admin/roles/' . (int) $role['id'] . '/delete') ?>"
                                  style="display:contents;">
                                <button type="submit" class="cv-icon-btn cv-ib-delete" title="Delete role"
                                        <?= $admins > 0 ? 'disabled title="Role is in use"' : '' ?>
                                        onclick="return confirm('Delete role &quot;<?= esc((string) $role['name'], 'attr') ?>&quot;? This cannot be undone.');">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    Delete
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="padding:32px;text-align:center;color:var(--cv-muted);font-size:.9rem;">No roles found.</div>
    <?php endif; ?>
</div>

<!-- Permission reference -->
<div class="cv-card">
    <div class="cv-card-header">
        <h2>Permission Reference</h2>
    </div>
    <div class="cv-chip-grid" style="padding:20px;">
        <?php foreach ($permissionGroups as $groupName => $keys): ?>
            <?php
            $groupLower = strtolower($groupName);
            $chipClass = match(true) {
                str_contains($groupLower, 'dashboard') => 'cv-chip-db',
                str_contains($groupLower, 'feedback')  => 'cv-chip-fb',
                str_contains($groupLower, 'announce')  => 'cv-chip-ann',
                str_contains($groupLower, 'user')      => 'cv-chip-usr',
                str_contains($groupLower, 'categor')   => 'cv-chip-cat',
                str_contains($groupLower, 'activ')     => 'cv-chip-act',
                str_contains($groupLower, 'admin')     => 'cv-chip-adm',
                str_contains($groupLower, 'role')      => 'cv-chip-rol',
                str_contains($groupLower, 'tool')      => 'cv-chip-tool',
                default                                 => 'cv-chip-db',
            };
            ?>
            <div class="cv-chip-group">
                <span class="cv-chip-group-label"><?= esc($groupName) ?></span>
                <?php foreach ($keys as $key): ?>
                    <span class="cv-chip <?= $chipClass ?>"><?= esc(substr($key, strpos($key, '.') + 1)) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div id="cv-toast-container"></div>
<?= $this->endSection() ?>
