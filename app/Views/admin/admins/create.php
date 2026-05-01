<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="cv-form-card">
    <div class="cv-form-head">
        <h2>Add Admin Account</h2>
        <a href="<?= site_url('admin/admins') ?>" class="cv-btn-outline" style="padding:7px 16px;font-size:.82rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form method="post" action="<?= site_url('admin/admins') ?>">
        <div class="cv-form-body">

            <div class="cv-field">
                <label for="full_name">Full Name</label>
                <input id="full_name" name="full_name" class="cv-input" required maxlength="150"
                       placeholder="e.g. Maria Santos"
                       value="<?= esc((string) old('full_name', '')) ?>">
            </div>

            <div class="cv-field">
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" class="cv-input" required maxlength="150"
                       placeholder="admin@school.edu"
                       value="<?= esc((string) old('email', '')) ?>">
            </div>

            <div class="cv-field">
                <label for="role_id">Role</label>
                <select id="role_id" name="role_id" class="cv-select" required>
                    <option value="">— Select a role —</option>
                    <?php $selectedRole = (string) old('role_id', ''); ?>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= (int) $role['id'] ?>" <?= $selectedRole === (string) $role['id'] ? 'selected' : '' ?>>
                            <?= esc((string) $role['name']) ?>
                            <?php if (! empty($role['description'])): ?>
                                — <?= esc((string) $role['description']) ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="cv-field">
                <label for="password">Password</label>
                <div class="cv-pw-wrap">
                    <input id="password" name="password" type="password" class="cv-input"
                           required minlength="8" maxlength="100"
                           placeholder="Min. 8 characters" autocomplete="new-password">
                    <button type="button" class="cv-pw-toggle" onclick="togglePw('password',this)" tabindex="-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="cv-field">
                <label for="password_confirm">Confirm Password</label>
                <div class="cv-pw-wrap">
                    <input id="password_confirm" name="password_confirm" type="password" class="cv-input"
                           required maxlength="100"
                           placeholder="Repeat password" autocomplete="new-password">
                    <button type="button" class="cv-pw-toggle" onclick="togglePw('password_confirm',this)" tabindex="-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

        </div>

        <div class="cv-form-footer">
            <button type="submit" class="cv-btn-navy">Create Account</button>
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
