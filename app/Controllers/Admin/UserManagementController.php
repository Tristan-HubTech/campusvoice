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

        // Generate a temporary password
        $tempPassword = substr(str_shuffle('ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'), 0, 10);
        $userModel->update($id, ['password_hash' => password_hash($tempPassword, PASSWORD_DEFAULT)]);

        // Try to email the user
        $emailSent = false;
        try {
            $emailConfig = config('Email');
            $email = \Config\Services::email();
            $email->setFrom($emailConfig->SMTPUser, 'CampusVoice Admin');
            $email->setTo($user['email']);
            $email->setSubject('CampusVoice — Password Reset by Administrator');
            $email->setMessage(
                "Hello " . trim($user['first_name'] . ' ' . $user['last_name']) . ",\n\n" .
                "An administrator has reset your CampusVoice account password.\n\n" .
                "Your temporary password is: " . $tempPassword . "\n\n" .
                "Please log in at " . site_url('portal/login') . " and change your password immediately.\n\n" .
                "If you did not request this, please contact your campus administrator.\n\n" .
                "— CampusVoice Team"
            );
            $emailSent = $email->send();
        } catch (\Throwable $e) {
            log_message('error', '[UserManagement] Failed to send reset email: ' . $e->getMessage());
        }

        $this->logActivity(
            'user.password_reset',
            'Force-reset password for student: ' . trim($user['first_name'] . ' ' . $user['last_name']) . ' (' . $user['email'] . ')',
            ['target_type' => 'user', 'target_id' => $id, 'email_sent' => $emailSent]
        );

        $msg = $emailSent
            ? 'Password reset and emailed to ' . $user['email'] . '.'
            : 'Password reset. Email delivery failed — temporary password: ' . $tempPassword;

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
