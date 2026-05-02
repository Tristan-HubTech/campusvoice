<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
$me        = $adminUser ?? [];
$isSelf    = (int) ($me['id'] ?? 0) === (int) ($editUser['id'] ?? 0);
$canAssign = ! empty($me['permissions']['admin.assign_roles']) && ! $isSelf;
?>

<div class="cv-form-card cv-form-card-sm">
    <div class="cv-form-head">
        <h2>Edit Admin Account</h2>
        <a href="<?= site_url('admin/admins') ?>" class="cv-btn-outline" style="padding:7px 16px;font-size:.82rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form method="post" action="<?= site_url('admin/admins/' . (int) $editUser['id'] . '/update') ?>">
        <div class="cv-form-body">

            <div class="cv-field">
                <label for="full_name">Full Name</label>
                <input id="full_name" name="full_name" class="cv-input" required maxlength="150"
                       value="<?= esc((string) old('full_name', (string) ($editUser['full_name'] ?? ''))) ?>">
            </div>

            <div class="cv-field">
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" class="cv-input" required maxlength="150"
                       readonly
                       value="<?= esc((string) old('email', (string) ($editUser['email'] ?? ''))) ?>">
            </div>

            <div class="cv-field">
                <label for="role_id">Role</label>
                <?php if ($canAssign): ?>
                    <select id="role_id" name="role_id" class="cv-select" required>
                        <option value="">— Select a role —</option>
                        <?php $selectedRole = (string) old('role_id', (string) ($editUser['role_id'] ?? '')); ?>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= (int) $role['id'] ?>" <?= $selectedRole === (string) $role['id'] ? 'selected' : '' ?>>
                                <?= esc((string) $role['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <?php
                    $currentRole = '';
                    foreach ($roles as $r) {
                        if ((int) $r['id'] === (int) ($editUser['role_id'] ?? 0)) {
                            $currentRole = (string) $r['name'];
                            break;
                        }
                    }
                    ?>
                    <input class="cv-input" type="text" value="<?= esc($currentRole !== '' ? $currentRole : 'Unknown') ?>" disabled>
                    <?php if ($isSelf): ?>
                        <small style="color:var(--cv-muted);font-size:.76rem;">You cannot change your own role.</small>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="cv-field">
                <label for="password">New Password <small>— leave blank to keep current</small></label>
                <div class="cv-pw-wrap">
                    <input id="password" name="password" type="password" class="cv-input"
                           minlength="8" maxlength="100"
                           placeholder="Min. 8 characters" autocomplete="new-password">
                    <button type="button" class="cv-pw-toggle" onclick="togglePw('password',this)" tabindex="-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="cv-field">
                <label for="password_confirm">Confirm New Password</label>
                <div class="cv-pw-wrap">
                    <input id="password_confirm" name="password_confirm" type="password" class="cv-input"
                           maxlength="100"
                           placeholder="Repeat new password" autocomplete="new-password">
                    <button type="button" class="cv-pw-toggle" onclick="togglePw('password_confirm',this)" tabindex="-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

        </div>

        <div class="cv-form-footer">
            <button type="submit" class="cv-btn-navy">Save Changes</button>
            <a href="<?= site_url('admin/admins') ?>" class="cv-btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function togglePw(id, btn) {
    var inp = document.getElementById(id);
    if (!inp) return;
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
<?= $this->endSection() ?>
