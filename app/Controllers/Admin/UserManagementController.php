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

        $newStatus = (int) $user['is_active'] === 1 ? 0 : 1;
        $userModel->update($id, ['is_active' => $newStatus]);

        $action = $newStatus === 1 ? 'Activated' : 'Deactivated';
        $this->logActivity(
            'user.' . ($newStatus === 1 ? 'activated' : 'deactivated'),
            $action . ' student account: ' . trim($user['first_name'] . ' ' . $user['last_name']) . ' (' . $user['email'] . ')',
            ['target_type' => 'user', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin?tab=users'))->with('success', 'Student ' . strtolower($action) . ' successfully.');
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
