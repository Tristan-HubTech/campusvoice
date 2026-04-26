<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php
$forgotOtpVerified = ! empty($forgotOtpVerified);
$verifiedEmail = (string) ($forgotOtpVerifiedEmail ?? '');
$emailValue = (string) (old('email') ?? ($forgotOtpVerified ? $verifiedEmail : ''));
$sessionOtp  = (string) ($forgotSessionOtp ?? '');
?>
<div class="auth-shell">
    <header class="auth-topbar">
        <div aria-hidden="true"></div>
        <div class="portal-brand portal-brand--hero">
            <img src="<?= base_url('assets/admin/logo-mark.svg') ?>" alt="CampusVoice" class="portal-logo">
            <div class="brand-hero-text">
                <span class="brand-hero-name">CampusVoice</span>
                <span class="brand-hero-sub">Your campus, your voice</span>
            </div>
        </div>
        <div class="auth-topbar-end">
            <?= $this->include('partials/theme_toggle') ?>
            <a href="<?= site_url('users/login') ?>" class="auth-topbar-exit" aria-label="Back to login">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path fill-rule="evenodd" d="M10.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L9.293 7.5H1.5a.5.5 0 0 0 0 1h7.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H8a.5.5 0 0 0 0 1h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H8a.5.5 0 0 0 0 1h6z"/></svg>
                Back to Login
            </a>
        </div>
    </header>

    <div class="auth-split auth-split--center">
        <section class="auth-panel fp-panel">

            <!-- Header -->
            <div class="fp-hero">
                <div class="fp-hero__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <h1 class="fp-hero__title">Reset Password</h1>
                    <p class="fp-hero__sub">Enter your email to receive a one-time code.</p>
                </div>
            </div>

            <!-- Step track -->
            <div class="fp-steps">
                <div class="fp-step<?= !$forgotOtpVerified ? ' fp-step--active' : ' fp-step--done' ?>">
                    <span class="fp-step__num"><?= !$forgotOtpVerified ? '1' : '✓' ?></span>
                    <span class="fp-step__label">Email</span>
                </div>
                <div class="fp-step__line"></div>
                <div class="fp-step<?= !$forgotOtpVerified ? '' : ' fp-step--done' ?>">
                    <span class="fp-step__num">2</span>
                    <span class="fp-step__label">Verify OTP</span>
                </div>
                <div class="fp-step__line"></div>
                <div class="fp-step<?= $forgotOtpVerified ? ' fp-step--active' : '' ?>">
                    <span class="fp-step__num">3</span>
                    <span class="fp-step__label">New Password</span>
                </div>
            </div>

            <?php if (session()->has('error')): ?>
                <div class="auth-alert auth-alert--error"><?= esc(session('error')) ?></div>
            <?php endif ?>
            <?php if (session()->has('success')): ?>
                <div class="auth-alert auth-alert--success"><?= esc(session('success')) ?></div>
            <?php endif ?>

            <form method="post" action="<?= site_url('users/forgot-password') ?>" class="auth-form" novalidate autocomplete="off" id="forgot-form">

                <div class="fp-field-group">
                    <label for="fp-email">Email Address</label>
                    <div class="auth-otp-row">
                        <input id="fp-email" name="email" type="email" required maxlength="150"
                               autocomplete="off" placeholder="you@example.com"
                               value="<?= esc($emailValue) ?>"<?= $forgotOtpVerified ? ' readonly' : '' ?>>
                        <button type="button" class="auth-otp-btn" id="send-forgot-otp-btn" disabled>Send OTP</button>
                    </div>
                    <small id="forgot-otp-status" class="otp-status-text" aria-live="polite"></small>
                </div>

                <div class="fp-field-group">
                    <label for="fp-otp">OTP Code</label>
                    <?php if ($forgotOtpVerified && $sessionOtp !== ''): ?>
                        <!-- Hidden: submit the verified OTP from session -->
                        <input type="hidden" name="otp" value="<?= esc($sessionOtp) ?>">
                        <div class="auth-otp-row">
                            <input id="fp-otp" type="text" placeholder="OTP verified" readonly
                                   style="background:rgba(34,197,94,0.06);border-color:#86efac;color:#15803d;">
                            <button type="button" class="auth-otp-btn ready" disabled>✓ Verified</button>
                        </div>
                    <?php else: ?>
                        <div class="auth-otp-row">
                            <input id="fp-otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*"
                                   required maxlength="6" placeholder="Send OTP first"
                                   value="<?= esc((string) (old('otp') ?? '')) ?>"<?= $forgotOtpVerified ? ' readonly' : ' disabled' ?>>
                            <button type="button" class="auth-otp-btn<?= $forgotOtpVerified ? ' ready' : '' ?>" id="verify-forgot-otp-btn" disabled>Verify OTP</button>
                        </div>
                    <?php endif ?>
                </div>

                <div class="fp-field-group">
                    <label for="fp-password">New Password <small>(min 8 characters)</small></label>
                    <input id="fp-password" name="password" type="password" required
                           minlength="8" maxlength="255" autocomplete="new-password"
                           placeholder="Create new password"<?= $forgotOtpVerified ? '' : ' disabled' ?>>
                    <small id="fp-pw-len-warn" class="fp-warn" style="display:none;"></small>
                </div>

                <div class="fp-field-group">
                    <label for="fp-confirm">Confirm New Password</label>
                    <input id="fp-confirm" name="password_confirm" type="password" required
                           maxlength="255" autocomplete="new-password"
                           placeholder="Repeat new password"<?= $forgotOtpVerified ? '' : ' disabled' ?>>
                    <small id="fp-pw-match-warn" class="fp-warn" style="display:none;"></small>
                </div>

                <button type="submit" class="btn-primary" id="fp-submit-btn"<?= $forgotOtpVerified ? '' : ' disabled' ?> style="<?= $forgotOtpVerified ? '' : 'opacity:0.5;cursor:not-allowed;' ?>">
                    Reset Password
                </button>

                <?php if ($forgotOtpVerified): ?>
                <div style="text-align:center;margin-top:12px;">
                    <a href="<?= site_url('users/forgot-password?restart=1') ?>" class="auth-link-subtle" style="font-size:0.8rem;color:#6b7280;">
                        ↩ Use a different email?
                    </a>
                </div>
                <?php endif ?>
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
        submitBtn.style.opacity = ready ? '1' : '0.5';
        submitBtn.style.cursor  = ready ? 'pointer' : 'not-allowed';

        // Show/hide password match warning
        var warnEl = document.getElementById('fp-pw-match-warn');
        if (warnEl) {
            if (confirm.length > 0 && password !== confirm) {
                warnEl.textContent = '⚠ Passwords do not match.';
                warnEl.style.display = 'block';
            } else {
                warnEl.style.display = 'none';
            }
        }

        // Show/hide password length warning
        var lenWarnEl = document.getElementById('fp-pw-len-warn');
        if (lenWarnEl) {
            if (password.length > 0 && password.length < 8) {
                lenWarnEl.textContent = '⚠ Password must be at least 8 characters.';
                lenWarnEl.style.display = 'block';
            } else {
                lenWarnEl.style.display = 'none';
            }
        }
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
