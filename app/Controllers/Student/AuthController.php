<?php

namespace App\Controllers\Student;

use App\Models\PasswordOtpModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

// Handles student login, registration, and logout.
// View: app/Views/student/auth/login.php

class AuthController extends Controller
{
    private string $lastMailError = '';

    // Shows the login/register page. Also processes form submissions.
    public function login()
    {
        if (! empty((array) session()->get('student_auth')) && ! empty(session()->get('student_auth')['id'])) {
            return redirect()->to(site_url('users'));
        }

        // Read ?mode=login or ?mode=register from the URL to open the correct tab.
        $mode = strtolower(trim((string) ($this->request->getGet('mode') ?? 'login')));
        if (! in_array($mode, ['login', 'register'], true)) {
            $mode = 'login';
        }

        if ($this->request->getMethod() === 'post') {
            $postedMode = strtolower(trim((string) ($this->request->getPost('auth_mode') ?? 'login')));
            if ($postedMode === 'register') {
                return $this->handleRegister();
            }

            return $this->handleLogin();
        }

        return view('student/auth/login', [
            'title'    => 'Student Portal Access',
            'authMode' => $mode,
        ]);
    }

    public function register()
    {
        if (! empty((array) session()->get('student_auth')) && ! empty(session()->get('student_auth')['id'])) {
            return redirect()->to(site_url('users'));
        }

        // Alias route kept for backwards compatibility.
        if ($this->request->getMethod() === 'post') {
            return $this->handleRegister();
        }

        return redirect()->to(site_url('users/login?mode=register'));
    }

    public function sendRegisterOtp()
    {
        $payload = $this->request->getPost();
        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|max_length[150]',
            'password'         => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'      => false,
                'message' => 'Please complete all required fields correctly before requesting OTP.',
            ]);
        }

        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $userModel = new UserModel();
        $existingUser = $userModel->withDeleted()->where('email', $email)->first();

        if ($existingUser !== null) {
            if (! empty($existingUser['deleted_at'])) {
                return $this->response->setStatusCode(409)->setJSON([
                    'ok'      => false,
                    'message' => 'This email belongs to a deactivated account. Please contact the administrator.',
                ]);
            }

            return $this->response->setStatusCode(409)->setJSON([
                'ok'      => false,
                'message' => 'Email already registered. Please log in instead.',
            ]);
        }

        $now = date('Y-m-d H:i:s');
        $otpModel = new PasswordOtpModel();

        $recentRequest = $otpModel
            ->where('email', $email)
            ->where('purpose', 'register')
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->first();

        if ($recentRequest !== null && strtotime((string) $recentRequest['created_at']) > (time() - 60)) {
            return $this->response->setStatusCode(429)->setJSON([
                'ok'      => false,
                'message' => 'Please wait at least 60 seconds before requesting another OTP.',
            ]);
        }

        $otpPlain = (string) random_int(100000, 999999);
        $inserted = $otpModel->insert([
            'user_id'       => null,
            'email'         => $email,
            'purpose'       => 'register',
            'otp_hash'      => password_hash($otpPlain, PASSWORD_DEFAULT),
            'attempts'      => 0,
            'max_attempts'  => 5,
            'expires_at'    => date('Y-m-d H:i:s', time() + 600),
            'used_at'       => null,
        ]);

        if ($inserted === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok'      => false,
                'message' => 'Unable to create OTP request. Please try again.',
            ]);
        }

        if (! $this->sendOtpMail($email, $otpPlain, 'CampusVoice Registration OTP')) {
            $otpModel->delete((int) $inserted);
            $message = 'Failed to send OTP email. Please check mail configuration.';
            if (ENVIRONMENT === 'development' && $this->lastMailError !== '') {
                $message .= ' Debug: ' . $this->lastMailError;
            }

            return $this->response->setStatusCode(500)->setJSON([
                'ok'      => false,
                'message' => $message,
            ]);
        }

        return $this->response->setJSON([
            'ok'      => true,
            'message' => 'OTP sent successfully. Please check your email.',
        ]);
    }

    // Clears the session and redirects back to login.
    public function logout()
    {
        session()->remove('student_auth');
        return redirect()->to(site_url('users/login'))->with('success', 'You have been logged out.');
    }

    // Validates credentials and starts a session on success.
    private function handleLogin()
    {
        $post = $this->request->getPost();

        // Verify reCAPTCHA
        $recaptchaResponse = (string) ($post['g-recaptcha-response'] ?? '');
        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            return redirect()->to(site_url('users/login?mode=login'))->with('error', 'Please complete the CAPTCHA verification.')->withInput();
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validateData($post, $rules)) {
            return redirect()->to(site_url('users/login?mode=login'))->with('error', implode(' ', $this->validator->getErrors()))->withInput();
        }

        $email    = strtolower(trim((string) ($post['email'] ?? '')));
        $password = (string) ($post['password'] ?? '');

        $userModel = new UserModel();
        $user = $userModel
            ->select('users.*, roles.name as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.email', $email)
            ->first();

        if ($user === null || ! password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            return redirect()->to(site_url('users/login?mode=login'))->with('error', 'Invalid email or password.')->withInput();
        }

        if ((int) ($user['is_active'] ?? 0) !== 1) {
            return redirect()->to(site_url('users/login?mode=login'))->with('error', 'Your account is inactive. Please contact the administrator.')->withInput();
        }

        $isNewUser = empty($user['last_login_at']);
        $userModel->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        // Hard reset: never let a student session coexist with an admin session in the same browser.
        session()->remove('admin_auth');

        session()->set('student_auth', [
            'id'         => (int) $user['id'],
            'name'       => trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? ''))),
            'email'      => $user['email'],
            'role'       => $user['role'],
            'is_new_user' => $isNewUser,
        ]);

        return redirect()->to(site_url('users'));
    }

    // Validates the registration form and creates the student account.
    private function handleRegister()
    {
        $post = $this->request->getPost();
        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|max_length[150]',
            'password'         => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
            'otp'              => 'required|numeric|exact_length[6]',
        ];

        if (! $this->validateData($post, $rules)) {
            return redirect()->to(site_url('users/login?mode=register'))->with('error', 'Please complete all required fields before proceeding.')->withInput();
        }

        $roleModel = new RoleModel();
        $studentRole = $roleModel->where('name', 'student')->first();
        if ($studentRole === null) {
            return redirect()->to(site_url('users/login?mode=register'))->with('error', 'Registration is temporarily unavailable.')->withInput();
        }

        $userModel = new UserModel();
        $email = strtolower(trim((string) ($post['email'] ?? '')));

        if (! $this->verifyRegisterOtp($email, trim((string) ($post['otp'] ?? '')))) {
            return redirect()->to(site_url('users/login?mode=register'))
                ->with('error', 'Invalid or expired OTP code. Please request a new one.')
                ->withInput();
        }

        // Check if the email is already registered (including deactivated accounts).
        $existingUser = $userModel->withDeleted()->where('email', $email)->first();
        if ($existingUser !== null) {
            if (! empty($existingUser['deleted_at'])) {
                return redirect()->to(site_url('users/login?mode=register'))
                    ->with('error', 'This email is already used by a deactivated account. Please contact the administrator.')
                    ->withInput();
            }

            return redirect()->to(site_url('users/login?mode=login'))
                ->with('error', 'Email already registered. Please log in instead.')
                ->withInput();
        }

        $userId = $userModel->insert([
            'role_id'       => (int) $studentRole['id'],
            'first_name'    => trim((string) $post['first_name']),
            'last_name'     => trim((string) $post['last_name']),
            'email'         => $email,
            'password_hash' => password_hash((string) $post['password'], PASSWORD_DEFAULT),
            'is_active'     => 1,
        ]);

        if ($userId === false) {
            return redirect()->to(site_url('users/login?mode=register'))->with('error', 'Registration failed. Please try again.')->withInput();
        }

        $newUser = $userModel
            ->select('users.*, roles.name as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->find((int) $userId);

        return redirect()->to(site_url('users/login'))->with('success', 'Account created! Please log in to continue.');
    }

    private function verifyRegisterOtp(string $email, string $otp): bool
    {
        $now = date('Y-m-d H:i:s');
        $otpModel = new PasswordOtpModel();

        $records = $otpModel
            ->where('email', $email)
            ->where('purpose', 'register')
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->findAll(5);

        if ($records === []) {
            return false;
        }

        $recordToIncrement = null;

        foreach ($records as $record) {
            if ((int) $record['attempts'] >= (int) $record['max_attempts']) {
                continue;
            }

            if ($recordToIncrement === null) {
                $recordToIncrement = $record;
            }

            if (password_verify($otp, (string) $record['otp_hash'])) {
                $otpModel->update((int) $record['id'], ['used_at' => $now]);
                return true;
            }
        }

        if ($recordToIncrement !== null) {
            $otpModel->update((int) $recordToIncrement['id'], ['attempts' => ((int) $recordToIncrement['attempts']) + 1]);
        }

        return false;
    }

    // Shows the set-password page linked from an admin-initiated reset email.
    public function adminResetPassword(string $token)
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processAdminReset($token);
        }

        $record = $this->findAdminResetToken($token);

        return view('student/auth/set-password', [
            'title'        => 'Set New Password',
            'isAuthScreen' => true,
            'valid'        => $record !== null,
            'token'        => $token,
        ]);
    }

    private function processAdminReset(string $token)
    {
        $post  = $this->request->getPost();
        $rules = [
            'password'         => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validateData($post, $rules)) {
            return redirect()->to(site_url('users/set-password/' . $token))
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $record = $this->findAdminResetToken($token);

        if ($record === null) {
            return view('student/auth/set-password', [
                'title'        => 'Set New Password',
                'isAuthScreen' => true,
                'valid'        => false,
                'token'        => $token,
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $record['email'])->where('is_active', 1)->first();

        if ($user === null) {
            return redirect()->to(site_url('users/login'))
                ->with('error', 'Account not found or inactive. Please contact your administrator.');
        }

        $password = (string) ($post['password'] ?? '');

        if (password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            return redirect()->to(site_url('users/set-password/' . $token))
                ->with('error', 'Your new password cannot be the same as your current password.');
        }

        $now = date('Y-m-d H:i:s');
        $userModel->update((int) $user['id'], [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $otpModel = new PasswordOtpModel();
        $otpModel->update((int) $record['id'], ['used_at' => $now]);

        return redirect()->to(site_url('users/login'))
            ->with('success', 'Password set successfully. You can now log in.');
    }

    private function findAdminResetToken(string $token): ?array
    {
        $now      = date('Y-m-d H:i:s');
        $otpModel = new PasswordOtpModel();

        $records = $otpModel
            ->where('purpose', 'admin_reset')
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->findAll();

        foreach ($records as $record) {
            if (password_verify($token, (string) $record['otp_hash'])) {
                return $record;
            }
        }

        return null;
    }

    // Shows and processes the forgot-password page (step 1: email, step 2: OTP + new password).
    public function forgotPassword()
    {
        if (! empty((array) session()->get('student_auth')) && ! empty(session()->get('student_auth')['id'])) {
            return redirect()->to(site_url('users'));
        }

        if ($this->request->getMethod() === 'post') {
            return $this->handleResetPassword();
        }

        if ($this->request->getGet('restart') === '1') {
            session()->remove('forgot_password_reset');
            return redirect()->to(site_url('users/forgot-password'));
        }

        $forgotResetState = session()->get('forgot_password_reset');

        return view('student/auth/forgot-password', [
            'title'                  => 'Reset Password',
            'isAuthScreen'           => true,
            'forgotOtpVerified'      => is_array($forgotResetState) && ! empty($forgotResetState['email']),
            'forgotOtpVerifiedEmail' => is_array($forgotResetState) ? (string) ($forgotResetState['email'] ?? '') : '',
            'forgotSessionOtp'       => is_array($forgotResetState) ? (string) ($forgotResetState['otp'] ?? '') : '',
        ]);
    }

    // AJAX: send OTP to the given email for password reset.
    public function sendForgotOtp()
    {
        session()->remove('forgot_password_reset');
        $email = strtolower(trim((string) ($this->request->getPost('email') ?? '')));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'      => false,
                'message' => 'Please enter a valid email address.',
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->where('is_active', 1)->first();

        // Only send OTP if the email belongs to an active account.
        if ($user === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok'      => false,
                'message' => 'No account found with that email address.',
            ]);
        }

        $now      = date('Y-m-d H:i:s');
        $otpModel = new PasswordOtpModel();

        $recentRequest = $otpModel
            ->where('email', $email)
            ->where('purpose', 'reset')
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->first();

        if ($recentRequest !== null && strtotime((string) $recentRequest['created_at']) > (time() - 60)) {
            return $this->response->setStatusCode(429)->setJSON([
                'ok'      => false,
                'message' => 'Please wait at least 60 seconds before requesting another OTP.',
            ]);
        }

        $otpPlain = (string) random_int(100000, 999999);
        $inserted = $otpModel->insert([
            'user_id'      => (int) $user['id'],
            'email'        => $email,
            'purpose'      => 'reset',
            'otp_hash'     => password_hash($otpPlain, PASSWORD_DEFAULT),
            'attempts'     => 0,
            'max_attempts' => 5,
            'expires_at'   => date('Y-m-d H:i:s', time() + 600),
            'used_at'      => null,
        ]);

        if ($inserted === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok'      => false,
                'message' => 'Unable to create OTP request. Please try again.',
            ]);
        }

        if (! $this->sendOtpMail($email, $otpPlain, 'CampusVoice Password Reset OTP')) {
            $otpModel->delete((int) $inserted);
            $message = 'Failed to send OTP email. Please check mail configuration.';
            if (ENVIRONMENT === 'development' && $this->lastMailError !== '') {
                $message .= ' Debug: ' . $this->lastMailError;
            }

            return $this->response->setStatusCode(500)->setJSON([
                'ok'      => false,
                'message' => $message,
            ]);
        }

        return $this->response->setJSON([
            'ok'      => true,
            'message' => 'OTP sent to ' . $email . '. Please check your inbox.',
        ]);
    }

    public function verifyForgotOtp()
    {
        $payload = $this->request->getPost();
        $rules = [
            'email' => 'required|valid_email|max_length[150]',
            'otp'   => 'required|numeric|exact_length[6]',
        ];

        if (! $this->validateData($payload, $rules)) {
            session()->remove('forgot_password_reset');
            return $this->response->setStatusCode(422)->setJSON([
                'ok'      => false,
                'message' => 'Enter a valid email and OTP first.',
            ]);
        }

        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $otp = trim((string) ($payload['otp'] ?? ''));

        if (! $this->verifyResetOtp($email, $otp, false)) {
            session()->remove('forgot_password_reset');
            return $this->response->setStatusCode(422)->setJSON([
                'ok'      => false,
                'message' => 'Incorrect or expired OTP. Please try again.',
            ]);
        }

        session()->set('forgot_password_reset', [
            'email'       => $email,
            'verified_at' => time(),
            'otp'         => $otp,
        ]);

        return $this->response->setJSON([
            'ok'      => true,
            'message' => 'OTP verified. You can now set a new password.',
        ]);
    }

    // Processes the reset-password form submission.
    private function handleResetPassword()
    {
        $post  = $this->request->getPost();
        $rules = [
            'email'            => 'required|valid_email|max_length[150]',
            'otp'              => 'required|numeric|exact_length[6]',
            'password'         => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validateData($post, $rules)) {
            return redirect()->to(site_url('users/forgot-password'))
                ->with('error', implode(' ', $this->validator->getErrors()))
                ->withInput();
        }

        $email    = strtolower(trim((string) ($post['email'] ?? '')));
        $otp      = trim((string) ($post['otp'] ?? ''));
        $password = (string) ($post['password'] ?? '');

        $forgotResetState = session()->get('forgot_password_reset');
        $isVerifiedForEmail = is_array($forgotResetState)
            && (($forgotResetState['email'] ?? '') === $email)
            && ((int) ($forgotResetState['verified_at'] ?? 0) >= (time() - 600));

        if (! $isVerifiedForEmail) {
            return redirect()->to(site_url('users/forgot-password'))
                ->with('error', 'Please verify the OTP first before setting a new password.');
        }

        // ── Fetch user FIRST ──────────────────────────────────────────────────
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->where('is_active', 1)->first();

        if ($user === null) {
            session()->remove('forgot_password_reset');
            return redirect()->to(site_url('users/forgot-password'))
                ->with('error', 'Account not found.');
        }

        // ── Same-password check BEFORE consuming the OTP ──────────────────────
        // If this fails the OTP is still valid and the user can retry immediately.
        if (password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            return redirect()->to(site_url('users/forgot-password'))
                ->with('error', '⚠ Your new password cannot be the same as your current password. Please choose a different one.');
            // Note: NO ->withInput() so the password fields are blank on retry.
        }

        // ── Consume the OTP only when we are sure we can proceed ──────────────
        if (! $this->verifyResetOtp($email, $otp, true)) {
            session()->remove('forgot_password_reset');
            return redirect()->to(site_url('users/forgot-password'))
                ->with('error', 'Invalid or expired OTP. Please request a new one.');
        }

        $userModel->update((int) $user['id'], [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        session()->remove('forgot_password_reset');

        return redirect()->to(site_url('users/login'))
            ->with('success', 'Password reset successfully. You can now log in with your new password.');
    }

    private function verifyResetOtp(string $email, string $otp, bool $consume): bool
    {
        $now = date('Y-m-d H:i:s');
        $otpModel = new PasswordOtpModel();

        $records = $otpModel
            ->where('email', $email)
            ->where('purpose', 'reset')
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->findAll(5);

        if ($records === []) {
            return false;
        }

        $recordToIncrement = null;

        foreach ($records as $record) {
            if ((int) $record['attempts'] >= (int) $record['max_attempts']) {
                continue;
            }

            if ($recordToIncrement === null) {
                $recordToIncrement = $record;
            }

            if (password_verify($otp, (string) $record['otp_hash'])) {
                if ($consume) {
                    $otpModel->update((int) $record['id'], ['used_at' => $now]);
                }

                return true;
            }
        }

        if ($recordToIncrement !== null) {
            $otpModel->update((int) $recordToIncrement['id'], [
                'attempts' => ((int) $recordToIncrement['attempts']) + 1,
            ]);
        }

        return false;
    }

    private function sendOtpMail(string $emailAddress, string $otp, string $subject): bool
    {
        $fromEmail = env('email.fromEmail');
        $fromName  = env('email.fromName', 'CampusVoice');
        $this->lastMailError = '';

        if ($fromEmail === null || trim($fromEmail) === '') {
            $this->lastMailError = 'email.fromEmail is empty in environment config.';
            return false;
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#0a1535;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a1535;padding:40px 0;">
    <tr><td align="center">
      <table width="520" cellpadding="0" cellspacing="0" style="max-width:520px;width:100%;border-radius:20px;overflow:hidden;box-shadow:0 8px 48px rgba(0,0,0,0.55);">

        <!-- Header -->
        <tr>
          <td align="center" style="background:linear-gradient(135deg,#0d214e 0%,#102a62 100%);padding:36px 40px 28px;">
            <div style="display:inline-block;background:rgba(255,255,255,0.08);border:1px solid rgba(133,172,255,0.3);border-radius:14px;padding:10px 22px;">
              <span style="color:#85acff;font-size:13px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">CampusVoice</span>
            </div>
            <h1 style="margin:18px 0 0;color:#ffffff;font-size:26px;font-weight:700;letter-spacing:0.01em;">Verification Code</h1>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="background:#ffffff;padding:44px 48px 36px;">

            <p style="margin:0 0 10px;color:#4a5880;font-size:15px;line-height:1.6;">Hello,</p>
            <p style="margin:0 0 32px;color:#4a5880;font-size:15px;line-height:1.6;">
              Use the one-time code below to complete your <strong style="color:#0d214e;">CampusVoice</strong> registration. This code is valid for <strong style="color:#0d214e;">10 minutes</strong>.
            </p>

            <!-- OTP Box -->
            <div style="text-align:center;margin:0 0 36px;">
              <div style="display:inline-block;background:linear-gradient(135deg,#0d214e,#1a3a8f);border-radius:16px;padding:28px 52px;">
                <p style="margin:0 0 6px;color:rgba(255,255,255,0.55);font-size:11px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;">Your OTP Code</p>
                <span style="font-size:48px;font-weight:900;letter-spacing:0.22em;color:#ffffff;font-family:'Courier New',monospace;">{$otp}</span>
              </div>
            </div>

            <p style="margin:0 0 12px;color:#8a94aa;font-size:13px;line-height:1.6;text-align:center;">
              ⚠️ Never share this code with anyone. CampusVoice will never ask for it.
            </p>
            <p style="margin:0;color:#b0b9cc;font-size:13px;line-height:1.6;text-align:center;">
              If you did not request this, you can safely ignore this email.
            </p>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td align="center" style="background:#f4f7ff;padding:20px 48px;border-top:1px solid #e2e9f8;">
            <p style="margin:0;color:#aab3cc;font-size:12px;line-height:1.7;">
              This is an automated message from <strong style="color:#4a5880;">CampusVoice</strong>.<br>
              Please do not reply to this email.
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;

        $email = service('email');
        $email->clear(true);
        $email->setMailType('html');
        $email->setFrom($fromEmail, $fromName);
        $email->setTo($emailAddress);
        $email->setSubject($subject);
        $email->setMessage($html);
        $email->setAltMessage(
            "Your CampusVoice OTP code is: {$otp}\n\n" .
            "This code will expire in 10 minutes.\n" .
            "If you did not request this, please ignore this email."
        );

        $sent = $email->send();
        if (! $sent) {
            $rawDebug = (string) $email->printDebugger();
            $cleanDebug = trim(preg_replace('/\s+/', ' ', strip_tags($rawDebug)) ?? '');
            $this->lastMailError = $cleanDebug !== '' ? mb_substr($cleanDebug, 0, 220) : 'SMTP send failed.';
        }

        return $sent;
    }

    private function verifyRecaptcha(string $response): bool
    {
        if ($response === '') {
            return false;
        }

        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'secret'   => RECAPTCHA_SECRET_KEY,
                'response' => $response,
                'remoteip' => $this->request->getIPAddress(),
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false) {
            return false;
        }

        $data = json_decode($result, true);
        return is_array($data) && !empty($data['success']);
    }
}
