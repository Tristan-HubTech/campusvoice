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

    <form method="post" action="<?= site_url('settings') ?>" class="settings-form" id="settingsForm">
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

        <!-- Change Password (collapsible) -->
        <div class="settings-section-toggle" id="changePwToggle">
            <button type="button" class="ghost-btn" onclick="togglePasswordSection()">
                🔒 Change Password <span id="pwArrow">▸</span>
            </button>
        </div>
        <div class="password-section" id="passwordSection" style="display:none;">
            <div class="field-row">
                <div>
                    <label for="password">New Password</label>
                    <input id="password" name="password" type="password" maxlength="255" placeholder="Minimum 8 characters">
                </div>
                <div>
                    <label for="password_confirm">Confirm Password</label>
                    <input id="password_confirm" name="password_confirm" type="password" maxlength="255" placeholder="Repeat the new password">
                </div>
            </div>
            <div class="otp-verify-row">
                <div class="otp-verify-info">
                    <span class="otp-lock-icon">📧</span>
                    <div>
                        <strong>Email Verification Required</strong>
                        <p>We'll send a 6-digit code to your email to verify this password change.</p>
                    </div>
                </div>
                <div class="otp-input-row">
                    <input id="email_otp" name="email_otp" type="text" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" placeholder="Enter 6-digit code" autocomplete="off" class="otp-input">
                    <button type="button" class="ghost-btn otp-send-btn" id="sendOtpBtn" onclick="requestOtp()">Send Code</button>
                </div>
                <p class="otp-status" id="otpStatus"></p>
            </div>
        </div>

        <div class="settings-actions">
            <button type="button" class="solid-btn" onclick="confirmSave()">Save Changes</button>
        </div>
    </form>
</section>

<!-- Confirmation Modal -->
<div class="confirm-modal-overlay" id="confirmModal" style="display:none;">
    <div class="confirm-modal">
        <div class="confirm-modal-icon">⚠️</div>
        <h3>Confirm Changes</h3>
        <p id="confirmMsg">Are you sure you want to update your account settings?</p>
        <div class="confirm-modal-actions">
            <button type="button" class="ghost-btn" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="solid-btn" onclick="submitSettings()">Yes, Save Changes</button>
        </div>
    </div>
</div>

<script>
function togglePasswordSection() {
    var section = document.getElementById('passwordSection');
    var arrow = document.getElementById('pwArrow');
    if (section.style.display === 'none') {
        section.style.display = 'block';
        arrow.textContent = '▾';
    } else {
        section.style.display = 'none';
        arrow.textContent = '▸';
        document.getElementById('password').value = '';
        document.getElementById('password_confirm').value = '';
        document.getElementById('email_otp').value = '';
        document.getElementById('otpStatus').textContent = '';
    }
}

var otpCooldown = 0;
var otpTimer = null;

function requestOtp() {
    if (otpCooldown > 0) return;
    var btn = document.getElementById('sendOtpBtn');
    var status = document.getElementById('otpStatus');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    status.textContent = '';
    status.className = 'otp-status';

    fetch('<?= site_url('settings/send-password-otp') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
    .then(function(res) {
        if (res.data.ok) {
            status.textContent = res.data.message;
            status.className = 'otp-status otp-success';
            startCooldown(60);
        } else {
            status.textContent = res.data.message || 'Failed to send code.';
            status.className = 'otp-status otp-error';
            btn.disabled = false;
            btn.textContent = 'Send Code';
        }
    })
    .catch(function() {
        status.textContent = 'Network error. Please try again.';
        status.className = 'otp-status otp-error';
        btn.disabled = false;
        btn.textContent = 'Send Code';
    });
}

function startCooldown(seconds) {
    otpCooldown = seconds;
    var btn = document.getElementById('sendOtpBtn');
    btn.disabled = true;
    btn.textContent = 'Resend (' + otpCooldown + 's)';
    otpTimer = setInterval(function() {
        otpCooldown--;
        if (otpCooldown <= 0) {
            clearInterval(otpTimer);
            btn.disabled = false;
            btn.textContent = 'Resend Code';
        } else {
            btn.textContent = 'Resend (' + otpCooldown + 's)';
        }
    }, 1000);
}

function confirmSave() {
    var newPw = document.getElementById('password').value;
    if (newPw) {
        var otp = document.getElementById('email_otp').value.trim();
        if (!otp || otp.length !== 6) {
            document.getElementById('email_otp').focus();
            document.getElementById('email_otp').style.borderColor = '#d55b54';
            document.getElementById('otpStatus').textContent = 'Please enter the 6-digit code sent to your email.';
            document.getElementById('otpStatus').className = 'otp-status otp-error';
            return;
        }
        var confirm = document.getElementById('password_confirm').value;
        if (newPw !== confirm) {
            document.getElementById('password_confirm').focus();
            return;
        }
    }
    var msg = 'Are you sure you want to update your account settings?';
    if (newPw) msg += '<br><strong style="color:#d55b54;">Your password will also be changed.</strong>';
    document.getElementById('confirmMsg').innerHTML = msg;
    document.getElementById('confirmModal').style.display = 'flex';
}
function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}
function submitSettings() {
    document.getElementById('confirmModal').style.display = 'none';
    document.getElementById('settingsForm').submit();
}
document.getElementById('email_otp').addEventListener('input', function() {
    this.style.borderColor = '';
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
<?= $this->endSection() ?>