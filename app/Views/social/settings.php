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
        <div class="field-row anon-toggle-row">
            <div>
                <div>
                    <label class="toggle-label-text">Anonymous Mode</label>
                    <p class="toggle-desc">Your posts and comments will appear as "Anonymous"</p>
                </div>
                <?php $anonVal = old('is_anonymous', (string) ($settingsProfile['is_anonymous'] ?? '0')) === '1' ? '1' : '0'; ?>
                <div class="anon-seg" id="anonSeg" data-checked="<?= $anonVal ?>">
                    <input id="is_anonymous" name="is_anonymous" type="checkbox" value="1" <?= $anonVal === '1' ? 'checked' : '' ?>>
                    <button type="button" class="seg-btn seg-off" onclick="setAnon(false)">OFF</button>
                    <button type="button" class="seg-btn seg-on" onclick="setAnon(true)">ON</button>
                </div>
            </div>
            <div></div>
        </div>
        <script>
        (function(){
            var cb  = document.getElementById('is_anonymous');
            var seg = document.getElementById('anonSeg');
            window.setAnon = function(val){
                var prev = cb.checked;
                cb.checked = val;
                seg.setAttribute('data-checked', val ? '1' : '0');
                fetch('<?= site_url('settings/anonymous') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'is_anonymous=' + (val ? '1' : '0')
                }).then(function(res){
                    if (!res.ok) {
                        cb.checked = prev;
                        seg.setAttribute('data-checked', prev ? '1' : '0');
                    }
                }).catch(function(){
                    cb.checked = prev;
                    seg.setAttribute('data-checked', prev ? '1' : '0');
                });
            };
        })();
        </script>

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
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= esc((string) ($settingsUser['email'] ?? '')) ?>" readonly>
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

        <div class="field-row">
            <div>
                <label for="current_password">Current Password</label>
                <input id="current_password" name="current_password" type="password" maxlength="255" placeholder="Required only when changing password">
            </div>
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
        </div>
    </form>
</section>
<?= $this->endSection() ?>