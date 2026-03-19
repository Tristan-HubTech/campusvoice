<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-brand">
        <img src="<?= base_url('assets/admin/logo.svg') ?>" alt="CampusVoice" class="auth-logo">
        <h1>CampusVoice</h1>
        <p>Create a Student Account</p>
    </div>

    <form method="post" action="<?= site_url('portal/register') ?>" class="auth-form" novalidate>
        <div class="form-row-half">
            <div>
                <label for="reg-first">First Name</label>
                <input id="reg-first" name="first_name" type="text" required maxlength="100"
                    placeholder="First name"
                    value="<?= esc((string) (old('first_name') ?? '')) ?>">
            </div>
            <div>
                <label for="reg-last">Last Name</label>
                <input id="reg-last" name="last_name" type="text" required maxlength="100"
                    placeholder="Last name"
                    value="<?= esc((string) (old('last_name') ?? '')) ?>">
            </div>
        </div>

        <label for="reg-email">Email Address</label>
        <input id="reg-email" name="email" type="email" required maxlength="150"
            placeholder="you@example.com"
            value="<?= esc((string) (old('email') ?? '')) ?>">

        <label for="reg-student-no">Student Number <small>(optional)</small></label>
        <input id="reg-student-no" name="student_no" type="text" maxlength="50"
            placeholder="e.g. 2023-00001"
            value="<?= esc((string) (old('student_no') ?? '')) ?>">

        <label for="reg-phone">Phone <small>(optional)</small></label>
        <input id="reg-phone" name="phone" type="tel" maxlength="30"
            placeholder="e.g. 09xx-xxx-xxxx"
            value="<?= esc((string) (old('phone') ?? '')) ?>">

        <label for="reg-password">Password <small>(min 8 characters)</small></label>
        <input id="reg-password" name="password" type="password" required minlength="8" maxlength="255"
            autocomplete="new-password" placeholder="Create a password">

        <label for="reg-confirm">Confirm Password</label>
        <input id="reg-confirm" name="password_confirm" type="password" required
            autocomplete="new-password" placeholder="Repeat password">

        <button type="submit" class="btn-primary">Create Account</button>
    </form>

    <p class="auth-footer-text">
        Already have an account? <a href="<?= site_url('portal/login') ?>">Log in here</a>
    </p>
</div>
<?= $this->endSection() ?>
