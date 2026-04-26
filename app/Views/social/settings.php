<?= $this->extend('social/layout') ?>

<?= $this->section('content') ?>
<?php
$settingsProfile = (array) ($currentUserProfile ?? []);
$anonVal = old('is_anonymous', (string) ($settingsProfile['is_anonymous'] ?? '0')) === '1' ? '1' : '0';
$selectedColor = (string) old('avatar_color', (string) ($settingsProfile['avatar_color'] ?? 'blue'));
?>

<div class="st-page">

    <!-- Hero Header -->
    <div class="st-hero">
        <div class="st-hero__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
            </svg>
        </div>
        <div>
            <h1 class="st-hero__title">Account Settings</h1>
            <p class="st-hero__sub">Manage your profile, privacy, and security</p>
        </div>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="st-alert st-alert--success"><?= esc(session('success')) ?></div>
    <?php endif ?>
    <?php if (session()->has('error')): ?>
        <div class="st-alert st-alert--error"><?= esc(session('error')) ?></div>
    <?php endif ?>

    <form method="post" action="<?= site_url('settings') ?>" class="st-form" id="settingsForm">

        <!-- ── Privacy ── -->
        <div class="st-card">
            <div class="st-card__header">
                <div class="st-card__header-icon st-icon--privacy">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <span class="st-card__header-title">Privacy</span>
                    <span class="st-card__header-sub">Control how others see you</span>
                </div>
            </div>

            <div class="st-anon-block">
                <div class="st-anon-block__text">
                    <span class="st-anon-block__name">Anonymous Mode</span>
                    <span class="st-anon-block__desc">Your posts and comments will appear as "Anonymous"</span>
                </div>
                <div class="st-seg" id="anonSeg" data-checked="<?= $anonVal ?>">
                    <input id="is_anonymous" name="is_anonymous" type="checkbox" value="1" <?= $anonVal === '1' ? 'checked' : '' ?>>
                    <button type="button" class="st-seg__btn st-seg__btn--off" onclick="setAnon(false)">OFF</button>
                    <button type="button" class="st-seg__btn st-seg__btn--on"  onclick="setAnon(true)">ON</button>
                </div>
            </div>
        </div>

        <!-- ── Profile Information ── -->
        <div class="st-card">
            <div class="st-card__header">
                <div class="st-card__header-icon st-icon--profile">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <span class="st-card__header-title">Profile Information</span>
                    <span class="st-card__header-sub">Update your name and appearance</span>
                </div>
            </div>

            <div class="st-grid-2">
                <div class="st-field">
                    <label class="st-label" for="first_name">First Name</label>
                    <input id="first_name" name="first_name" type="text" maxlength="100" class="st-input"
                           value="<?= esc((string) old('first_name', (string) ($settingsUser['first_name'] ?? ''))) ?>">
                </div>
                <div class="st-field">
                    <label class="st-label" for="last_name">Last Name</label>
                    <input id="last_name" name="last_name" type="text" maxlength="100" class="st-input"
                           value="<?= esc((string) old('last_name', (string) ($settingsUser['last_name'] ?? ''))) ?>">
                </div>
                <div class="st-field">
                    <label class="st-label" for="email">Email <span class="st-field__hint">read-only</span></label>
                    <input id="email" name="email" type="email" class="st-input st-input--readonly"
                           value="<?= esc((string) ($settingsUser['email'] ?? '')) ?>" readonly>
                </div>
                <div class="st-field">
                    <label class="st-label" for="avatar_color">Avatar Color</label>
                    <div class="st-color-row">
                        <div class="st-color-dot" id="avatarDot"></div>
                        <select id="avatar_color" name="avatar_color" class="st-input st-select">
                            <?php foreach ($avatarPalette as $color): ?>
                                <option value="<?= esc($color) ?>" <?= $selectedColor === $color ? 'selected' : '' ?>><?= esc(ucfirst($color)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Security ── -->
        <div class="st-card">
            <div class="st-card__header">
                <div class="st-card__header-icon st-icon--security">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <span class="st-card__header-title">Security</span>
                    <span class="st-card__header-sub">Change your login password</span>
                </div>
            </div>

            <button type="button" class="st-pw-toggle" id="changePwToggle" onclick="togglePasswordSection()">
                <span class="st-pw-toggle__left">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Change Password
                </span>
                <svg class="st-pw-toggle__arrow" id="pwArrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <div class="st-pw-section" id="passwordSection" style="display:none;">
                <div class="st-grid-2">
                    <div class="st-field">
                        <label class="st-label" for="password">New Password</label>
                        <input id="password" name="password" type="password" maxlength="255" class="st-input" placeholder="Minimum 8 characters">
                    </div>
                    <div class="st-field">
                        <label class="st-label" for="password_confirm">Confirm Password</label>
                        <input id="password_confirm" name="password_confirm" type="password" maxlength="255" class="st-input" placeholder="Repeat the new password">
                    </div>
                </div>

                <div class="st-otp-block">
                    <div class="st-otp-block__info">
                        <div class="st-otp-block__icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <span class="st-otp-block__title">Email Verification Required</span>
                            <span class="st-otp-block__sub">We'll send a 6-digit code to your email to verify this change.</span>
                        </div>
                    </div>
                    <div class="st-otp-row">
                        <input id="email_otp" name="email_otp" type="text" maxlength="6" inputmode="numeric"
                               pattern="[0-9]{6}" placeholder="6-digit code" autocomplete="off" class="st-input st-otp-input">
                        <button type="button" class="st-otp-send-btn" id="sendOtpBtn" onclick="requestOtp()">Send Code</button>
                    </div>
                    <p class="st-otp-status" id="otpStatus"></p>
                </div>
            </div>
        </div>

        <!-- ── Save ── -->
        <div class="st-save-row">
            <button type="button" class="st-save-btn" id="saveBtn" onclick="confirmSave()" disabled style="opacity:.45;cursor:not-allowed;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Save Changes
            </button>
        </div>

    </form>
</div>

<!-- Confirm Modal -->
<div class="st-modal-overlay" id="confirmModal" style="display:none;">
    <div class="st-modal">
        <div class="st-modal__icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3 class="st-modal__title">Confirm Changes</h3>
        <p class="st-modal__text" id="confirmMsg">Are you sure you want to update your account settings?</p>
        <div class="st-modal__actions">
            <button type="button" class="st-modal__btn st-modal__btn--cancel" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="st-modal__btn st-modal__btn--confirm" onclick="submitSettings()">Yes, Save</button>
        </div>
    </div>
</div>

<script>
/* ── Anon toggle ── */
(function () {
    var cb  = document.getElementById('is_anonymous');
    var seg = document.getElementById('anonSeg');
    window.setAnon = function (val) {
        var prev = cb.checked;
        cb.checked = val;
        seg.setAttribute('data-checked', val ? '1' : '0');
        fetch('<?= site_url('settings/anonymous') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'is_anonymous=' + (val ? '1' : '0')
        }).catch(function () {
            cb.checked = prev;
            seg.setAttribute('data-checked', prev ? '1' : '0');
        });
    };
})();

/* ── Avatar color dot ── */
var colorMap = {
    blue:   '#3b82f6',
    teal:   '#14b8a6',
    coral:  '#f4614e',
    violet: '#8b5cf6',
    amber:  '#f59e0b',
    rose:   '#f43f5e'
};
(function () {
    var sel = document.getElementById('avatar_color');
    var dot = document.getElementById('avatarDot');
    function updateDot() { if (dot && sel) dot.style.background = colorMap[sel.value] || '#3b82f6'; }
    if (sel) { sel.addEventListener('change', updateDot); updateDot(); }
})();

/* ── Password section toggle ── */
function togglePasswordSection() {
    var sec   = document.getElementById('passwordSection');
    var arrow = document.getElementById('pwArrow');
    var open  = sec.style.display === 'none';
    sec.style.display = open ? 'block' : 'none';
    arrow.style.transform = open ? 'rotate(180deg)' : '';
    if (!open) {
        document.getElementById('password').value = '';
        document.getElementById('password_confirm').value = '';
        document.getElementById('email_otp').value = '';
        document.getElementById('otpStatus').textContent = '';
        window._settingsCheckChanges && window._settingsCheckChanges();
    }
}

/* ── OTP ── */
var otpCooldown = 0, otpTimer = null;
function requestOtp() {
    if (otpCooldown > 0) return;
    var btn = document.getElementById('sendOtpBtn');
    var status = document.getElementById('otpStatus');
    btn.disabled = true;
    btn.textContent = 'Sending…';
    status.textContent = '';
    status.className = 'st-otp-status';
    fetch('<?= site_url('settings/send-password-otp') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
    .then(function (res) {
        status.textContent = res.data.message || (res.data.ok ? 'OTP sent!' : 'Failed.');
        status.className = 'st-otp-status ' + (res.data.ok ? 'st-otp-status--ok' : 'st-otp-status--err');
        if (res.data.ok) { startCooldown(60); } else { btn.disabled = false; btn.textContent = 'Send Code'; }
    })
    .catch(function () {
        status.textContent = 'Network error. Please try again.';
        status.className = 'st-otp-status st-otp-status--err';
        btn.disabled = false; btn.textContent = 'Send Code';
    });
}
function startCooldown(s) {
    otpCooldown = s;
    var btn = document.getElementById('sendOtpBtn');
    btn.disabled = true;
    (function tick() {
        btn.textContent = 'Resend (' + otpCooldown + 's)';
        if (otpCooldown-- <= 0) { btn.disabled = false; btn.textContent = 'Resend Code'; return; }
        otpTimer = setTimeout(tick, 1000);
    })();
}
document.getElementById('email_otp').addEventListener('input', function () {
    this.style.borderColor = '';
    this.value = this.value.replace(/[^0-9]/g, '');
});

/* ── Change detection ── */
(function () {
    var saveBtn = document.getElementById('saveBtn');
    var initial = {
        first_name:   document.getElementById('first_name').value,
        last_name:    document.getElementById('last_name').value,
        avatar_color: document.getElementById('avatar_color').value
    };
    function checkChanges() {
        var changed = document.getElementById('first_name').value !== initial.first_name
                   || document.getElementById('last_name').value  !== initial.last_name
                   || document.getElementById('avatar_color').value !== initial.avatar_color
                   || document.getElementById('password').value.length > 0;
        saveBtn.disabled = !changed;
        saveBtn.style.opacity = changed ? '1' : '.45';
        saveBtn.style.cursor  = changed ? 'pointer' : 'not-allowed';
    }
    ['first_name','last_name','password'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', checkChanges);
    });
    document.getElementById('avatar_color').addEventListener('change', checkChanges);
    window._settingsCheckChanges = checkChanges;
})();

/* ── Confirm / submit ── */
function confirmSave() {
    var newPw = document.getElementById('password').value;
    if (newPw) {
        var otp = document.getElementById('email_otp').value.trim();
        if (!otp || otp.length !== 6) {
            var otpEl = document.getElementById('email_otp');
            otpEl.focus();
            otpEl.style.borderColor = '#ef4444';
            var st = document.getElementById('otpStatus');
            st.textContent = 'Please enter the 6-digit code sent to your email.';
            st.className = 'st-otp-status st-otp-status--err';
            return;
        }
        if (newPw !== document.getElementById('password_confirm').value) {
            document.getElementById('password_confirm').focus();
            return;
        }
    }
    var msg = 'Are you sure you want to update your account settings?';
    if (newPw) msg += '<br><strong style="color:#ef4444;">Your password will also be changed.</strong>';
    document.getElementById('confirmMsg').innerHTML = msg;
    document.getElementById('confirmModal').style.display = 'flex';
}
function closeConfirmModal() { document.getElementById('confirmModal').style.display = 'none'; }
function submitSettings()    { closeConfirmModal(); document.getElementById('settingsForm').submit(); }
</script>
<?= $this->endSection() ?>