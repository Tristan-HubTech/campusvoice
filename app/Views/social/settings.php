<?= $this->extend('social/layout') ?>

<?= $this->section('content') ?>
<?php
$settingsProfile = (array) ($currentUserProfile ?? []);
?>
<section class="panel-card settings-card">
    <div class="panel-head">
        <h2>Account Settings</h2>
        <span class="summary-muted">Update your profile for the main website</span>
    </div>

    <form method="post" action="<?= site_url('settings') ?>" class="settings-form">
        <div class="field-row">
            <div>
                <label for="first_name">First Name</label>
                <input id="first_name" name="first_name" type="text" maxlength="100" value="<?= esc((string) old('first_name', (string) ($settingsUser['first_name'] ?? ''))) ?>">
            </div>
            <div>
                <label for="last_name">Last Name</label>
                <input id="last_name" name="last_name" type="text" maxlength="100" value="<?= esc((string) old('last_name', (string) ($settingsUser['last_name'] ?? ''))) ?>">
            </div>
        </div>

        <div class="field-row">
            <div>
                <label for="phone">Phone</label>
                <input id="phone" name="phone" type="text" maxlength="30" value="<?= esc((string) old('phone', (string) ($settingsUser['phone'] ?? ''))) ?>">
            </div>
            <div>
                <label for="avatar_color">Avatar Color</label>
                <select id="avatar_color" name="avatar_color">
                    <?php $selectedColor = (string) old('avatar_color', (string) ($settingsProfile['avatar_color'] ?? 'blue')); ?>
                    <?php foreach ($avatarPalette as $color): ?>
                        <option value="<?= esc($color) ?>" <?= $selectedColor === $color ? 'selected' : '' ?>><?= esc(ucfirst($color)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="4" maxlength="500" placeholder="Tell people what you care about on campus."><?= esc((string) old('bio', (string) ($settingsProfile['bio'] ?? ''))) ?></textarea>
        </div>

        <div class="field-row">
            <div>
                <label for="password">New Password</label>
                <input id="password" name="password" type="password" maxlength="255" placeholder="Leave blank to keep current password">
            </div>
            <div>
                <label for="password_confirm">Confirm Password</label>
                <input id="password_confirm" name="password_confirm" type="password" maxlength="255" placeholder="Repeat the new password">
            </div>
        </div>

        <div class="settings-actions">
            <button type="submit" class="solid-btn">Save Changes</button>
            <a href="<?= site_url('profile/' . (int) ($settingsUser['id'] ?? 0)) ?>" class="ghost-btn">View Profile</a>
        </div>
    </form>
</section>
<?= $this->endSection() ?>