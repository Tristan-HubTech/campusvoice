<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php
$forgotOtpVerified = ! empty($forgotOtpVerified);
$verifiedEmail = (string) ($forgotOtpVerifiedEmail ?? '');
$emailValue = (string) (old('email') ?? ($forgotOtpVerified ? $verifiedEmail : ''));
?>
<div class="auth-shell">
    <header class="auth-topbar">
        <a href="<?= site_url('/') ?>" class="auth-topbar-brand" aria-label="CampusVoice home">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice" class="auth-topbar-logo">
        </a>
        <h1 class="auth-topbar-title-text">CampusVoice</h1>
        <div class="auth-topbar-end">
            <?= $this->include('partials/theme_toggle') ?>
            <a href="<?= site_url('users/login') ?>" class="auth-topbar-exit" aria-label="Back to login">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path fill-rule="evenodd" d="M10.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L9.293 7.5H1.5a.5.5 0 0 0 0 1h7.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H8a.5.5 0 0 0 0 1h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H8a.5.5 0 0 0 0 1h6z"/></svg>
                Back to Login
            </a>
        </div>
    </header>

    <div class="auth-split auth-split--center">
        <section class="auth-panel">

            <div class="auth-forgot-header">
                <div class="auth-forgot-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a4.5 4.5 0 0 0-4.5 4.5v.88l-.853.58A1 1 0 0 0 2 7.8V14a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7.8a1 1 0 0 0-.647-.94L12.5 6.38V5.5A4.5 4.5 0 0 0 8 1zm0 1a3.5 3.5 0 0 1 3.5 3.5v1.07L8 8.571 4.5 6.57V5.5A3.5 3.5 0 0 1 8 2zm4 5.535V14H4V7.535l4 2.286 4-2.286z"/></svg>
                </div>
                <h2 class="auth-forgot-title">Reset Password</h2>
                <p class="auth-forgot-subtitle">Enter your registered email to receive a one-time code.</p>
            </div>

            <?php if (session()->has('error')): ?>
                <div class="auth-alert auth-alert--error"><?= esc(session('error')) ?></div>
            <?php endif ?>
            <?php if (session()->has('success')): ?>
                <div class="auth-alert auth-alert--success"><?= esc(session('success')) ?></div>
            <?php endif ?>

            <form method="post" action="<?= site_url('users/forgot-password') ?>" class="auth-form" novalidate autocomplete="off" id="forgot-form">

                <label for="fp-email">Email Address</label>
                <div class="auth-otp-row">
                    <input id="fp-email" name="email" type="email" required maxlength="150"
                           autocomplete="off" placeholder="you@example.com"
                          value="<?= esc($emailValue) ?>"<?= $forgotOtpVerified ? ' readonly' : '' ?>>
                    <button type="button" class="auth-otp-btn" id="send-forgot-otp-btn" disabled>Send OTP</button>
                </div>
                <small id="forgot-otp-status" class="otp-status-text" aria-live="polite"></small>

                <label for="fp-otp">OTP Code</label>
                  <div class="auth-otp-row">
                      <input id="fp-otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*"
                          required maxlength="6" placeholder="Send OTP first"
                          value="<?= esc((string) (old('otp') ?? '')) ?>"<?= $forgotOtpVerified ? ' readonly' : ' disabled' ?>>
                    <button type="button" class="auth-otp-btn<?= $forgotOtpVerified ? ' ready' : '' ?>" id="verify-forgot-otp-btn" disabled>Verify OTP</button>
                  </div>

                <label for="fp-password">New Password <small>(min 8 characters)</small></label>
                <input id="fp-password" name="password" type="password" required
                      minlength="8" maxlength="255" autocomplete="new-password"
                      placeholder="Create new password"<?= $forgotOtpVerified ? '' : ' disabled' ?>>

                <label for="fp-confirm">Confirm New Password</label>
                <input id="fp-confirm" name="password_confirm" type="password" required
                       maxlength="255" autocomplete="new-password"
                      placeholder="Repeat new password"<?= $forgotOtpVerified ? '' : ' disabled' ?>>

                  <button type="submit" class="btn-primary" id="fp-submit-btn"<?= $forgotOtpVerified ? '' : ' disabled' ?>>Reset Password</button>
            </form>

        </section>
    </div>
</div>

<script>
(function () {
    var emailEl    = document.getElementById('fp-email');
    var otpEl      = document.getElementById('fp-otp');
    var passwordEl = document.getElementById('fp-password');
    var confirmEl  = document.getElementById('fp-confirm');
    var sendBtn    = document.getElementById('send-forgot-otp-btn');
    var verifyBtn  = document.getElementById('verify-forgot-otp-btn');
    var submitBtn  = document.getElementById('fp-submit-btn');
    var statusEl   = document.getElementById('forgot-otp-status');
    var otpVerified = <?= $forgotOtpVerified ? 'true' : 'false' ?>;

    function isValidEmail(val) {
        return /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/.test(val.trim());
    }

    function showStatus(msg, isError) {
        if (!statusEl) return;
        statusEl.textContent = msg;
        statusEl.style.color = isError ? '#8a251a' : '#1f8f5f';
    }

    function updateSendBtn() {
        var ready = emailEl && isValidEmail(emailEl.value) && !emailEl.readOnly;
        sendBtn.disabled = !ready;
        sendBtn.classList.toggle('ready', !!ready);
    }

    function updateVerifyBtn() {
        if (!verifyBtn) {
            return;
        }

        var ready = !otpVerified && otpEl && !otpEl.disabled && otpEl.value.trim().length === 6;
        verifyBtn.disabled = !ready;
        verifyBtn.classList.toggle('ready', !!ready);
    }

    function setPasswordAccess(isEnabled) {
        otpVerified = isEnabled;

        if (passwordEl) {
            passwordEl.disabled = !isEnabled;
            if (!isEnabled) {
                passwordEl.value = '';
            }
        }

        if (confirmEl) {
            confirmEl.disabled = !isEnabled;
            if (!isEnabled) {
                confirmEl.value = '';
            }
        }

        if (otpEl) {
            otpEl.readOnly = isEnabled;
        }

        if (verifyBtn) {
            verifyBtn.textContent = isEnabled ? 'Verified' : 'Verify OTP';
        }

        updateVerifyBtn();
        updateSubmitBtn();
    }

    function updateSubmitBtn() {
        var password = passwordEl ? passwordEl.value : '';
        var confirm  = confirmEl ? confirmEl.value : '';
        var ready    = otpVerified && password.length >= 8 && password === confirm;
        submitBtn.disabled = !ready;
    }

    if (emailEl) {
        emailEl.addEventListener('input', function () {
            updateSendBtn();
            if (otpEl) {
                otpEl.disabled = true;
                otpEl.value = '';
                otpEl.placeholder = 'Send OTP first';
            }
            setPasswordAccess(false);
        });
    }

    if (otpEl) {
        otpEl.addEventListener('input', function () {
            if (otpVerified) {
                setPasswordAccess(false);
            }

            updateVerifyBtn();
            updateSubmitBtn();
        });
    }
    if (passwordEl) { passwordEl.addEventListener('input', updateSubmitBtn); }
    if (confirmEl)  { confirmEl.addEventListener('input', updateSubmitBtn); }

    if (sendBtn) {
        sendBtn.addEventListener('click', function () {
            if (!emailEl || !isValidEmail(emailEl.value)) return;

            sendBtn.disabled = true;
            sendBtn.classList.remove('ready');
            showStatus('Sending OTP to your email...', false);

            var body = new URLSearchParams();
            body.set('email', emailEl.value.trim());

            fetch(<?= json_encode(site_url('users/forgot-password/send-otp')) ?>, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString()
            })
            .then(function (res) {
                return res.json().catch(function () {
                    return { ok: false, message: 'Unexpected server response.' };
                });
            })
            .then(function (data) {
                var ok = !!(data && data.ok);
                var message = (data && data.message) ? data.message : (ok ? 'OTP sent.' : 'Failed to send OTP.');
                showStatus(message, !ok);

                if (ok) {
                    emailEl.readOnly = true;
                    otpEl.disabled = false;
                    otpEl.readOnly = false;
                    otpEl.value = '';
                    otpEl.placeholder = 'Enter 6-digit OTP';
                    setPasswordAccess(false);
                    otpEl.focus();
                } else {
                    sendBtn.disabled = false;
                    updateSendBtn();
                }
            })
            .catch(function () {
                showStatus('Failed to send OTP. Please try again.', true);
                sendBtn.disabled = false;
                updateSendBtn();
            });
        });
    }

    if (verifyBtn) {
        verifyBtn.addEventListener('click', function () {
            if (!emailEl || !otpEl || !isValidEmail(emailEl.value) || otpEl.value.trim().length !== 6) {
                return;
            }

            verifyBtn.disabled = true;
            verifyBtn.classList.remove('ready');
            showStatus('Verifying OTP...', false);

            var body = new URLSearchParams();
            body.set('email', emailEl.value.trim());
            body.set('otp', otpEl.value.trim());

            fetch(<?= json_encode(site_url('users/forgot-password/verify-otp')) ?>, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString()
            })
            .then(function (res) {
                return res.json().catch(function () {
                    return { ok: false, message: 'Unexpected server response.' };
                });
            })
            .then(function (data) {
                var ok = !!(data && data.ok);
                var message = (data && data.message) ? data.message : (ok ? 'OTP verified.' : 'OTP verification failed.');
                showStatus(message, !ok);

                if (ok) {
                    setPasswordAccess(true);
                    if (passwordEl) {
                        passwordEl.focus();
                    }
                    return;
                }

                setPasswordAccess(false);
                updateVerifyBtn();
            })
            .catch(function () {
                showStatus('Failed to verify OTP. Please try again.', true);
                setPasswordAccess(false);
                updateVerifyBtn();
            });
        });
    }

    if (otpVerified && emailEl) {
        emailEl.readOnly = true;
        if (otpEl) {
            otpEl.disabled = false;
            otpEl.readOnly = true;
            otpEl.placeholder = 'OTP verified';
        }
        setPasswordAccess(true);
    } else {
        setPasswordAccess(false);
    }

    updateSendBtn();
    updateVerifyBtn();
    updateSubmitBtn();
})();
</script>
<?= $this->endSection() ?>
