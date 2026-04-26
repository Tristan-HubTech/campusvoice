<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php $authMode = (string) ($authMode ?? 'login'); ?>
<!-- NOTE: Student login form view file is app/Views/student/auth/login.php -->
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
            </a>
        </div>
    </header>

    <div class="auth-split">
        <section class="auth-panel">
            <div class="auth-tab-switch" id="auth-tab-switch">
                <button type="button" class="auth-tab-btn" data-auth-tab="login">Login</button>
                <button type="button" class="auth-tab-btn" data-auth-tab="register">Register</button>
            </div>

            <!-- NOTE: Login form starts here (data-auth-pane="login") -->
            <section class="auth-pane" data-auth-pane="login">
                <form method="post" action="<?= site_url('users/login') ?>" class="auth-form" novalidate autocomplete="off">
                    <input type="hidden" name="auth_mode" value="login">

                    <label for="login-email">Email Address</label>
                    <input
                        id="login-email"
                        name="email"
                        type="email"
                        required
                        autocomplete="off"
                        placeholder="you@example.com"
                    >

                    <label for="login-password">Password</label>
                    <input
                        id="login-password"
                        name="password"
                        type="password"
                        required
                        autocomplete="off"
                        placeholder="Your password"
                    >

                    <div class="g-recaptcha" data-sitekey="<?= esc(RECAPTCHA_SITE_KEY) ?>"></div>

                    <button type="submit" class="btn-primary">Log In</button>
                    <div class="auth-form-footer">
                        <a href="<?= site_url('users/forgot-password') ?>" class="auth-link-subtle">Forgot password?</a>
                    </div>
                </form>
            </section>
            <!-- NOTE: Login form ends here -->

            <section class="auth-pane" data-auth-pane="register" hidden>
                <form method="post" action="<?= site_url('users/login') ?>" class="auth-form" novalidate autocomplete="off">
                    <input type="hidden" name="auth_mode" value="register">

                    <div class="form-row-half">
                        <div>
                            <label for="reg-first">First Name</label>
                            <input id="reg-first" name="first_name" type="text" required maxlength="100" placeholder="First name">
                        </div>
                        <div>
                            <label for="reg-last">Last Name</label>
                            <input id="reg-last" name="last_name" type="text" required maxlength="100" placeholder="Last name">
                        </div>
                    </div>

                    <label for="reg-email">Email Address</label>
                    <input id="reg-email" name="email" type="email" required maxlength="150" autocomplete="off" placeholder="you@example.com">
                    <small id="reg-email-hint" class="otp-status-text" aria-live="polite"></small>

                    <label for="reg-otp">OTP Code</label>
                    <button type="button" class="auth-otp-btn" id="send-register-otp-btn">Send OTP to Email</button>
                    <small id="register-otp-status" class="otp-status-text" aria-live="polite"></small>
                    <input id="reg-otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*" required maxlength="6" placeholder="Send OTP first" disabled id="reg-otp">

                    <label for="reg-password">Password <small>(min 8 characters)</small></label>
                    <input id="reg-password" name="password" type="password" required minlength="8" maxlength="255" autocomplete="new-password" placeholder="Create a password">

                    <label for="reg-confirm">Confirm Password</label>
                    <input id="reg-confirm" name="password_confirm" type="password" required autocomplete="new-password" placeholder="Repeat password">

                    <button type="submit" class="btn-primary">Create Account</button>
                </form>
            </section>

</div>

<script>
    (function () {
        var authMode = <?= json_encode($authMode) ?>;
        if (authMode !== 'register') {
            authMode = 'login';
        }

        var tabButtons = document.querySelectorAll('[data-auth-tab]');
        var panes = document.querySelectorAll('[data-auth-pane]');
        var sendOtpBtn = document.getElementById('send-register-otp-btn');
        var otpStatus = document.getElementById('register-otp-status');
        var otpInput = document.getElementById('reg-otp');

        function showOtpStatus(message, isError) {
            if (!otpStatus) {
                return;
            }

            otpStatus.textContent = message;
            otpStatus.style.color = isError ? '#8a251a' : '#1f8f5f';
        }

        function setMode(mode, updateUrl) {
            panes.forEach(function (pane) {
                var active = pane.getAttribute('data-auth-pane') === mode;
                if (active) {
                    pane.removeAttribute('hidden');
                } else {
                    pane.setAttribute('hidden', 'hidden');
                }
            });

            tabButtons.forEach(function (btn) {
                btn.classList.toggle('active', btn.getAttribute('data-auth-tab') === mode);
            });

            if (updateUrl) {
                var nextUrl = mode === 'register'
                    ? <?= json_encode(site_url('users/login?mode=register')) ?>
                    : <?= json_encode(site_url('users/login?mode=login')) ?>;
                window.history.replaceState({}, '', nextUrl);
            }
        }

        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var mode = btn.getAttribute('data-auth-tab');
                setMode(mode, true);
            });
        });

        if (sendOtpBtn) {
            sendOtpBtn.disabled = true;

            function gatherRegisterFields() {
                var firstNameEl = document.getElementById('reg-first');
                var lastNameEl = document.getElementById('reg-last');
                var emailEl = document.getElementById('reg-email');
                var passwordEl = document.getElementById('reg-password');
                var confirmEl = document.getElementById('reg-confirm');

                var firstName = firstNameEl ? firstNameEl.value.trim() : '';
                var lastName = lastNameEl ? lastNameEl.value.trim() : '';
                var email = emailEl ? emailEl.value.trim() : '';
                var password = passwordEl ? passwordEl.value : '';
                var confirm = confirmEl ? confirmEl.value : '';

                return {
                    firstName: firstName,
                    lastName: lastName,
                    email: email,
                    password: password,
                    confirm: confirm,
                };
            }

            function areRegisterRequirementsComplete(values) {
                var hasAllFields = !!(values.firstName && values.lastName && values.email && values.password && values.confirm);
                if (!hasAllFields) {
                    return false;
                }

                var emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
                if (!emailPattern.test(values.email)) {
                    return false;
                }

                if (values.password.length < 8) {
                    return false;
                }

                if (values.password !== values.confirm) {
                    return false;
                }

                return true;
            }

            function requestOtp() {
                var values = gatherRegisterFields();
                var firstName = values.firstName;
                var lastName = values.lastName;
                var email = values.email;
                var password = values.password;
                var confirm = values.confirm;

                if (!firstName || !lastName || !email || !password || !confirm) {
                    showOtpStatus('Please complete all required fields first.', true);
                    return;
                }

                if (password.length < 8) {
                    showOtpStatus('Password must be at least 8 characters before requesting OTP.', true);
                    return;
                }

                if (password !== confirm) {
                    showOtpStatus('Password and confirm password do not match.', true);
                    return;
                }

                var body = new URLSearchParams();
                body.set('first_name', firstName);
                body.set('last_name', lastName);
                body.set('email', email);
                body.set('password', password);
                body.set('password_confirm', confirm);

                sendOtpBtn.disabled = true;
                showOtpStatus('Sending OTP to your email...', false);

                fetch(<?= json_encode(site_url('users/register/send-otp')) ?>, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: body.toString()
                })
                    .then(function (res) {
                        return res.json().catch(function () {
                            return { ok: false, message: 'Unexpected response from server.' };
                        });
                    })
                    .then(function (data) {
                        var ok = !!(data && data.ok);
                        var message = (data && data.message) ? data.message : (ok ? 'OTP sent.' : 'Unable to send OTP.');
                        showOtpStatus(message, !ok);
                        if (ok && otpInput) {
                            otpInput.disabled = false;
                            otpInput.placeholder = 'Enter 6-digit OTP';
                            otpInput.focus();
                        }
                    })
                    .catch(function () {
                        showOtpStatus('Failed to send OTP. Please try again.', true);
                    })
                    .finally(function () {
                        sendOtpBtn.disabled = false;
                    });
            }

            var emailHintEl = document.getElementById('reg-email-hint');

            function getEmailHint(values) {
                var emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
                if (!emailPattern.test(values.email)) {
                    return '';
                }
                var noName     = !values.firstName || !values.lastName;
                var noPassword = !values.password || values.password.length < 8;
                var mismatch   = values.password && values.password.length >= 8 && values.password !== values.confirm;

                if (noName && noPassword) {
                    return '👆 Fill in your name and create a password first, then you can send the OTP.';
                }
                if (noName) {
                    return '👆 Please fill in your first and last name to continue.';
                }
                if (!values.password) {
                    return '🔒 Create a password below, then we\'ll send an OTP to verify your email.';
                }
                if (values.password.length < 8) {
                    return '🔒 Your password needs at least 8 characters.';
                }
                if (mismatch) {
                    return '⚠ The passwords you entered don\'t match yet.';
                }
                return '';
            }

            function updateOtpButtonState() {
                var values = gatherRegisterFields();
                var isReady = areRegisterRequirementsComplete(values);
                sendOtpBtn.classList.toggle('ready', isReady);
                sendOtpBtn.disabled = !isReady;
                if (emailHintEl) {
                    var hint = isReady ? '' : getEmailHint(values);
                    emailHintEl.textContent = hint;
                    emailHintEl.style.color = hint ? '#92400e' : '';
                }
            }

            sendOtpBtn.addEventListener('click', function () {
                requestOtp();
            });

            ['reg-first', 'reg-last', 'reg-email', 'reg-password', 'reg-confirm'].forEach(function (id) {
                var el = document.getElementById(id);
                if (!el) {
                    return;
                }

                el.addEventListener('input', updateOtpButtonState);
                el.addEventListener('change', updateOtpButtonState);
            });
        }

        setMode(authMode, false);

        // ── OTP auto-clear on failed submission ──────────────────────────
        // When the server redirects back with an error (wrong OTP), withInput()
        // re-populates the field. Clear it, focus it, and flash a red border.
        (function () {
            var errorEl = document.querySelector('.portal-alert.error, .auth-alert.error, .auth-alert--error');
            if (!errorEl) {
                // Fallback: check common flash selectors
                errorEl = document.querySelector('.alert-error, .flash-error, [class*="alert"][class*="error"]');
            }
            var otpField = document.getElementById('reg-otp');
            if (otpField && otpField.value !== '' && errorEl) {
                // Clear the old wrong value
                otpField.value = '';
                // Re-enable if it had been populated (withInput keeps it enabled after OTP was sent)
                otpField.disabled = false;
                otpField.placeholder = 'Enter 6-digit OTP';
                // Flash red border for 900ms then restore
                otpField.style.transition = 'border-color 0.2s, box-shadow 0.2s';
                otpField.style.borderColor = '#e53e3e';
                otpField.style.boxShadow = '0 0 0 3px rgba(229, 62, 62, 0.18)';
                setTimeout(function () {
                    otpField.style.borderColor = '';
                    otpField.style.boxShadow = '';
                }, 900);
                // Auto-focus so user can type immediately
                otpField.focus();
            }
        })();
    })();
</script>
<?= $this->endSection() ?>
