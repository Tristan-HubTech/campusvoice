<?php

namespace App\Controllers\Student;

use App\Models\PasswordOtpModel;
use App\Models\SocialProfileModel;
use App\Models\UserModel;

class SettingsController extends StudentBaseController
{
    public function index()
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $viewer = $this->viewer();
        $viewerId = (int) $viewer['id'];
        $profile = $this->ensureProfile($viewerId);
        $userModel = new UserModel();
        $user = $userModel->find($viewerId);

        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'first_name'   => 'required|min_length[2]|max_length[100]',
                'last_name'    => 'required|min_length[2]|max_length[100]',
                'bio'          => 'permit_empty|max_length[500]',
                'avatar_color' => 'required|in_list[' . implode(',', $this->avatarPalette) . ']',
                'is_anonymous' => 'permit_empty|in_list[0,1]',
            ];

            $password = (string) ($this->request->getPost('password') ?? '');
            $passwordConfirm = (string) ($this->request->getPost('password_confirm') ?? '');
            if ($password !== '' || $passwordConfirm !== '') {
                $rules['password']         = 'required|min_length[8]|max_length[255]';
                $rules['password_confirm'] = 'required|matches[password]';
                $rules['email_otp']        = 'required|exact_length[6]|numeric';
            }

            if (! $this->validate($rules)) {
                return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()))->withInput();
            }

            if ($password !== '') {
                $email = strtolower(trim((string) ($user['email'] ?? '')));
                $otpCode = trim((string) ($this->request->getPost('email_otp') ?? ''));
                $otpModel = new PasswordOtpModel();
                $now = date('Y-m-d H:i:s');

                $otpRecord = $otpModel
                    ->where('email', $email)
                    ->where('purpose', 'password_change')
                    ->where('used_at', null)
                    ->where('expires_at >=', $now)
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($otpRecord === null) {
                    return redirect()->back()->with('error', 'No valid verification code found. Please request a new one.')->withInput();
                }

                if ((int) $otpRecord['attempts'] >= (int) $otpRecord['max_attempts']) {
                    $otpModel->update((int) $otpRecord['id'], ['used_at' => $now]);
                    return redirect()->back()->with('error', 'Too many failed attempts. Please request a new code.')->withInput();
                }

                if (! password_verify($otpCode, (string) $otpRecord['otp_hash'])) {
                    $otpModel->update((int) $otpRecord['id'], ['attempts' => (int) $otpRecord['attempts'] + 1]);
                    return redirect()->back()->with('error', 'Invalid verification code.')->withInput();
                }

                $otpModel->update((int) $otpRecord['id'], ['used_at' => $now]);

                if (password_verify($password, (string) ($user['password_hash'] ?? ''))) {
                    return redirect()->back()->with('error', 'New password cannot be the same as your current password.')->withInput();
                }
            }

            $updateUser = [
                'first_name' => trim((string) $this->request->getPost('first_name')),
                'last_name'  => trim((string) $this->request->getPost('last_name')),
            ];

            if ($password !== '') {
                $updateUser['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $userModel->update($viewerId, $updateUser);

            $profileModel = new SocialProfileModel();
            $profilePayload = [
                'bio'          => trim((string) ($this->request->getPost('bio') ?? '')) ?: null,
                'avatar_color' => (string) $this->request->getPost('avatar_color'),
            ];

            if (db_connect()->fieldExists('is_anonymous', 'social_profiles')) {
                $profilePayload['is_anonymous'] = (int) ($this->request->getPost('is_anonymous') ?? 0);
            }

            $profileModel->update((int) $profile['id'], $profilePayload);

            $user = $userModel->find($viewerId);
            session()->set('student_auth', [
                'id'    => (int) $user['id'],
                'name'  => trim((string) $user['first_name'] . ' ' . (string) $user['last_name']),
                'email' => (string) $user['email'],
                'role'  => (string) ($viewer['role'] ?? 'user'),
            ]);

            if ($password !== '') {
                $this->logStudentActivity('password.changed', 'Changed account password');
            }
            $this->logStudentActivity('profile.updated', 'Updated account settings');

            return redirect()->to(site_url('settings'))->with('success', 'Your account settings were updated.');
        }

        $settingsProfile = $this->ensureProfile($viewerId);
        $isAnon = (int) ($settingsProfile['is_anonymous'] ?? 0) === 1;

        return view('social/settings', [
            'title'              => 'Settings',
            'pageKey'            => 'settings',
            'studentUser'        => $viewer,
            'currentUser'        => $viewer,
            'currentUserProfile' => $settingsProfile,
            'avatarPalette'      => $this->avatarPalette,
            'settingsUser'       => $userModel->find($viewerId),
            'isAnonymous'        => $isAnon,
            'anonAlias'          => $isAnon ? $this->anonymousAlias($viewerId) : '',
        ]);
    }

    public function toggleAnonymous()
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $this->response->setJSON(['ok' => false])->setStatusCode(401);
        }

        $viewer = $this->viewer();
        $viewerId = (int) $viewer['id'];
        $value = (int) ($this->request->getPost('is_anonymous') ?? 0) === 1 ? 1 : 0;

        if (db_connect()->fieldExists('is_anonymous', 'social_profiles')) {
            $profile = $this->ensureProfile($viewerId);
            (new SocialProfileModel())->update((int) $profile['id'], ['is_anonymous' => $value]);
        }

        $this->logStudentActivity('settings.anonymous_toggled', $value === 1 ? 'Enabled anonymous mode' : 'Disabled anonymous mode', null, null, ['is_anonymous' => $value]);

        return $this->response->setJSON(['ok' => true, 'is_anonymous' => $value]);
    }

    public function sendPasswordOtp()
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Unauthorized.'])->setStatusCode(401);
        }

        $viewer = $this->viewer();
        $email = strtolower(trim((string) ($viewer['email'] ?? '')));
        if ($email === '') {
            return $this->response->setJSON(['ok' => false, 'message' => 'No email on file.']);
        }

        $otpModel = new PasswordOtpModel();
        $now = date('Y-m-d H:i:s');

        $recent = $otpModel
            ->where('email', $email)
            ->where('purpose', 'password_change')
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->first();

        if ($recent !== null && strtotime((string) $recent['created_at']) > (time() - 60)) {
            return $this->response->setStatusCode(429)->setJSON([
                'ok'      => false,
                'message' => 'Please wait at least 60 seconds before requesting another code.',
            ]);
        }

        $otpPlain = (string) random_int(100000, 999999);
        $inserted = $otpModel->insert([
            'user_id'      => (int) $viewer['id'],
            'email'        => $email,
            'purpose'      => 'password_change',
            'otp_hash'     => password_hash($otpPlain, PASSWORD_DEFAULT),
            'attempts'     => 0,
            'max_attempts' => 5,
            'expires_at'   => date('Y-m-d H:i:s', time() + 600),
            'used_at'      => null,
        ]);

        if ($inserted === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok'      => false,
                'message' => 'Unable to create verification code.',
            ]);
        }

        $fromEmail = (string) env('email.fromEmail', '');
        $fromName  = (string) env('email.fromName', 'CampusVoice');
        if ($fromEmail === '') {
            $emailConfig = config('Email');
            $fromEmail = (string) ($emailConfig->SMTPUser ?? '');
        }
        if ($fromEmail === '') {
            return $this->response->setStatusCode(500)->setJSON([
                'ok'      => false,
                'message' => 'Email not configured.',
            ]);
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#0a1535;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a1535;padding:40px 0;">
    <tr><td align="center">
      <table width="520" cellpadding="0" cellspacing="0" style="max-width:520px;width:100%;border-radius:20px;overflow:hidden;box-shadow:0 8px 48px rgba(0,0,0,0.55);">
        <tr>
          <td align="center" style="background:linear-gradient(135deg,#0d214e 0%,#102a62 100%);padding:36px 40px 28px;">
            <div style="display:inline-block;background:rgba(255,255,255,0.08);border:1px solid rgba(133,172,255,0.3);border-radius:14px;padding:10px 22px;">
              <span style="color:#85acff;font-size:13px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">CampusVoice</span>
            </div>
            <h1 style="margin:18px 0 0;color:#ffffff;font-size:26px;font-weight:700;">Password Change Verification</h1>
          </td>
        </tr>
        <tr>
          <td style="background:#ffffff;padding:44px 48px 36px;">
            <p style="margin:0 0 10px;color:#4a5880;font-size:15px;line-height:1.6;">Hello,</p>
            <p style="margin:0 0 32px;color:#4a5880;font-size:15px;line-height:1.6;">
              You requested to change your password on <strong style="color:#0d214e;">CampusVoice</strong>. Use the code below to verify your identity. This code is valid for <strong style="color:#0d214e;">10 minutes</strong>.
            </p>
            <div style="text-align:center;margin:0 0 36px;">
              <div style="display:inline-block;background:linear-gradient(135deg,#0d214e,#1a3a8f);border-radius:16px;padding:28px 52px;">
                <p style="margin:0 0 6px;color:rgba(255,255,255,0.55);font-size:11px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;">Verification Code</p>
                <span style="font-size:48px;font-weight:900;letter-spacing:0.22em;color:#ffffff;font-family:'Courier New',monospace;">{$otpPlain}</span>
              </div>
            </div>
            <p style="margin:0 0 12px;color:#8a94aa;font-size:13px;line-height:1.6;text-align:center;">
              ⚠️ If you did not request a password change, ignore this email and your password will remain unchanged.
            </p>
          </td>
        </tr>
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

        $emailService = service('email');
        $emailService->clear(true);
        $emailService->setMailType('html');
        $emailService->setFrom($fromEmail, $fromName);
        $emailService->setTo($email);
        $emailService->setSubject('CampusVoice Password Change Verification');
        $emailService->setMessage($html);
        $emailService->setAltMessage("Your CampusVoice password change code is: {$otpPlain}\nValid for 10 minutes.");

        if (! $emailService->send()) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok'      => false,
                'message' => 'Failed to send verification email. Please try again.',
            ]);
        }

        $parts = explode('@', $email);
        $masked = substr($parts[0], 0, 2) . str_repeat('*', max(strlen($parts[0]) - 2, 1)) . '@' . $parts[1];

        $this->logStudentActivity('password.change_requested', 'Requested a password change OTP');

        return $this->response->setJSON([
            'ok'      => true,
            'message' => 'Verification code sent to ' . $masked,
        ]);
    }
}
