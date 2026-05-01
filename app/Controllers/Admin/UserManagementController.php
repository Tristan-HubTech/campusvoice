<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class UserManagementController extends AdminBaseController
{
    public function toggleStatus(int $id)
    {
        $userModel = new UserModel();
        $user = $this->findManagedStudent($userModel, $id);

        if ($user === null) {
            return redirect()->to(site_url('admin?tab=users'))->with('error', 'Student account not found.');
        }

        $newStatus   = (int) $user['is_active'] === 1 ? 0 : 1;
        $userModel->update($id, ['is_active' => $newStatus]);

        $action      = $newStatus === 1 ? 'Activated' : 'Deactivated';
        $studentName = trim($user['first_name'] . ' ' . $user['last_name']);

        $this->logActivity(
            'user.' . ($newStatus === 1 ? 'activated' : 'deactivated'),
            $action . ' student account: ' . $studentName . ' (' . $user['email'] . ')',
            ['target_type' => 'user', 'target_id' => $id]
        );

        $this->sendStatusEmail($user, $newStatus === 0);

        $msg = 'Student ' . strtolower($action) . ' successfully. Notification email sent to ' . $user['email'] . '.';
        return redirect()->to(site_url('admin?tab=users'))->with('success', $msg);
    }

    private function sendStatusEmail(array $user, bool $isDeactivation): void
    {
        $fromEmail = env('email.fromEmail', '');
        if (trim($fromEmail) === '') {
            return;
        }

        $studentName = trim($user['first_name'] . ' ' . $user['last_name']);
        $email       = $user['email'];

        if ($isDeactivation) {
            $subject    = 'CampusVoice — Your Account Has Been Deactivated';
            $headerText = 'Account Deactivated';
            $headerBg   = 'linear-gradient(135deg,#4a0d0d 0%,#7a1a1a 100%)';
            $badgeColor = '#f28b82';
            $badgeBg    = 'rgba(242,139,130,0.15)';
            $body       = <<<HTML
<p style="margin:0 0 14px;color:#4a5880;font-size:15px;line-height:1.6;">Hello <strong>{$studentName}</strong>,</p>
<p style="margin:0 0 20px;color:#4a5880;font-size:15px;line-height:1.6;">
    Your <strong style="color:#0d214e;">CampusVoice</strong> account has been <strong style="color:#c0392b;">deactivated</strong> by a school administrator.
</p>
<div style="background:#fff5f5;border:1px solid #fcc;border-radius:10px;padding:16px 20px;margin:0 0 24px;">
    <p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c0392b;letter-spacing:0.04em;">WHAT THIS MEANS</p>
    <ul style="margin:0;padding-left:18px;font-size:14px;color:#4a5880;line-height:1.8;">
        <li>You cannot log in to the portal</li>
        <li>You cannot submit or view feedback</li>
        <li>Your existing records are preserved</li>
    </ul>
</div>
<p style="margin:0;color:#8a94aa;font-size:14px;line-height:1.6;">
    If you believe this is a mistake, please contact your school administrator or visit the school office to appeal.
</p>
HTML;
        } else {
            $subject    = 'CampusVoice — Your Account Has Been Reactivated';
            $headerText = 'Account Reactivated';
            $headerBg   = 'linear-gradient(135deg,#0d4a1a 0%,#1a7a2a 100%)';
            $badgeColor = '#7dffc0';
            $badgeBg    = 'rgba(125,255,192,0.15)';
            $body       = <<<HTML
<p style="margin:0 0 14px;color:#4a5880;font-size:15px;line-height:1.6;">Hello <strong>{$studentName}</strong>,</p>
<p style="margin:0 0 20px;color:#4a5880;font-size:15px;line-height:1.6;">
    Great news — your <strong style="color:#0d214e;">CampusVoice</strong> account has been <strong style="color:#1a7a2a;">reactivated</strong> by a school administrator.
    You can now log in and use the portal again.
</p>
<div style="text-align:center;margin:0 0 28px;">
    <a href="{$this->loginUrl()}" style="display:inline-block;background:linear-gradient(135deg,#0d214e,#1a3a8f);color:#fff;font-size:15px;font-weight:700;text-decoration:none;padding:14px 36px;border-radius:12px;">
        Log In Now
    </a>
</div>
HTML;
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
          <td align="center" style="background:{$headerBg};padding:36px 40px 28px;">
            <div style="display:inline-block;background:{$badgeBg};border:1px solid {$badgeColor};border-radius:14px;padding:10px 22px;">
              <span style="color:{$badgeColor};font-size:13px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">CampusVoice</span>
            </div>
            <h1 style="margin:18px 0 0;color:#ffffff;font-size:24px;font-weight:700;">{$headerText}</h1>
          </td>
        </tr>
        <tr>
          <td style="background:#ffffff;padding:44px 48px 36px;">
            {$body}
            <hr style="border:none;border-top:1px solid #e8edf8;margin:28px 0;">
            <p style="margin:0;color:#b0b9cc;font-size:12px;line-height:1.7;text-align:center;">
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

        try {
            $mailer = service('email');
            $mailer->clear(true);
            $mailer->setMailType('html');
            $mailer->setFrom($fromEmail, env('email.fromName', 'CampusVoice'));
            $mailer->setTo($email);
            $mailer->setSubject($subject);
            $mailer->setMessage($html);
            $mailer->send();
        } catch (\Throwable $e) {
            log_message('error', '[UserManagement] Failed to send status email: ' . $e->getMessage());
        }
    }

    private function loginUrl(): string
    {
        return site_url('users/login');
    }

    public function sendPasswordReset(int $id)
    {
        $userModel = new UserModel();
        $user = $this->findManagedStudent($userModel, $id);

        if ($user === null) {
            return redirect()->to(site_url('admin?tab=users'))->with('error', 'Student account not found.');
        }

        $token     = bin2hex(random_bytes(32));
        $otpModel  = new \App\Models\PasswordOtpModel();

        $inserted = $otpModel->insert([
            'user_id'      => (int) $user['id'],
            'email'        => $user['email'],
            'purpose'      => 'admin_reset',
            'otp_hash'     => password_hash($token, PASSWORD_DEFAULT),
            'attempts'     => 0,
            'max_attempts' => 5,
            'expires_at'   => date('Y-m-d H:i:s', time() + 86400),
            'used_at'      => null,
        ]);

        if ($inserted === false) {
            return redirect()->to(site_url('admin?tab=users'))->with('error', 'Failed to generate reset token. Please try again.');
        }

        $studentName = trim($user['first_name'] . ' ' . $user['last_name']);
        $resetUrl    = site_url('users/set-password/' . $token);
        $emailSent   = false;

        try {
            $fromEmail = env('email.fromEmail', '');
            $fromName  = env('email.fromName', 'CampusVoice');

            if (trim($fromEmail) !== '') {
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
            <h1 style="margin:18px 0 0;color:#ffffff;font-size:26px;font-weight:700;">Set Your New Password</h1>
          </td>
        </tr>
        <tr>
          <td style="background:#ffffff;padding:44px 48px 36px;">
            <p style="margin:0 0 10px;color:#4a5880;font-size:15px;line-height:1.6;">Hello {$studentName},</p>
            <p style="margin:0 0 28px;color:#4a5880;font-size:15px;line-height:1.6;">
              An administrator has initiated a password reset for your <strong style="color:#0d214e;">CampusVoice</strong> account.
              Click the button below to set a new password. This link is valid for <strong style="color:#0d214e;">24 hours</strong>.
            </p>
            <div style="text-align:center;margin:0 0 32px;">
              <a href="{$resetUrl}" style="display:inline-block;background:linear-gradient(135deg,#0d214e,#1a3a8f);color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;padding:14px 36px;border-radius:12px;letter-spacing:0.02em;">
                Set New Password
              </a>
            </div>
            <p style="margin:0 0 10px;color:#8a94aa;font-size:13px;line-height:1.6;text-align:center;">
              Or copy and paste this link into your browser:
            </p>
            <p style="margin:0;font-size:12px;color:#6b7a9a;word-break:break-all;text-align:center;">{$resetUrl}</p>
            <hr style="border:none;border-top:1px solid #e8edf8;margin:28px 0;">
            <p style="margin:0;color:#b0b9cc;font-size:13px;line-height:1.6;text-align:center;">
              If you did not expect this email, you can safely ignore it. Your password will not change unless you click the link above.
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
                $mailer = service('email');
                $mailer->clear(true);
                $mailer->setMailType('html');
                $mailer->setFrom($fromEmail, $fromName);
                $mailer->setTo($user['email']);
                $mailer->setSubject('CampusVoice — Password Reset Request');
                $mailer->setMessage($html);
                $mailer->setAltMessage(
                    "Hello {$studentName},\n\n" .
                    "An administrator has initiated a password reset for your CampusVoice account.\n\n" .
                    "Set your new password here (valid for 24 hours):\n{$resetUrl}\n\n" .
                    "If you did not expect this, you can ignore this email.\n\n— CampusVoice Team"
                );
                $emailSent = $mailer->send();
            }
        } catch (\Throwable $e) {
            log_message('error', '[UserManagement] Failed to send reset email: ' . $e->getMessage());
        }

        $this->logActivity(
            'user.password_reset',
            'Sent password reset link to student: ' . $studentName . ' (' . $user['email'] . ')',
            ['target_type' => 'user', 'target_id' => $id, 'email_sent' => $emailSent]
        );

        $msg = $emailSent
            ? 'Password reset link sent to ' . $user['email'] . '. Link expires in 24 hours.'
            : 'Email delivery failed. Please check mail configuration.';

        return redirect()->to(site_url('admin?tab=users'))->with($emailSent ? 'success' : 'error', $msg);
    }

    private function findManagedStudent(UserModel $userModel, int $id): ?array
    {
        $user = $userModel
            ->select('users.*, roles.name as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.id', $id)
            ->where('roles.name', 'student')
            ->first();

        return is_array($user) ? $user : null;
    }
}
