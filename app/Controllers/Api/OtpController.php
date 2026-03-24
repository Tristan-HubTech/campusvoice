<?php

namespace App\Controllers\Api;

use App\Models\ApiTokenModel;
use App\Models\PasswordOtpModel;
use App\Models\UserModel;

class OtpController extends ApiController
{
    public function requestPasswordOtp()
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'email' => 'required|valid_email|max_length[150]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $email = strtolower(trim($payload['email']));
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user === null) {
            return $this->respond([
                'message' => 'If the account exists, an OTP has been sent to the email address.',
            ]);
        }

        $now = date('Y-m-d H:i:s');
        $otpModel = new PasswordOtpModel();

        $recentRequest = $otpModel
            ->where('email', $email)
            ->where('purpose', 'password_reset')
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->first();

        if ($recentRequest !== null && strtotime($recentRequest['created_at']) > (time() - 60)) {
            return $this->failTooManyRequests('Please wait at least 60 seconds before requesting another OTP.');
        }

        $otpPlain = (string) random_int(100000, 999999);
        $inserted = $otpModel->insert([
            'user_id'       => $user['id'],
            'email'         => $email,
            'purpose'       => 'password_reset',
            'otp_hash'      => password_hash($otpPlain, PASSWORD_DEFAULT),
            'attempts'      => 0,
            'max_attempts'  => 5,
            'expires_at'    => date('Y-m-d H:i:s', time() + 600),
            'used_at'       => null,
        ]);

        if ($inserted === false) {
            return $this->failServerError('Unable to create OTP request.');
        }

        if (! $this->sendOtpMail($email, $otpPlain)) {
            $otpModel->delete($inserted);
            return $this->failServerError('Failed to send OTP email. Please check Gmail SMTP configuration.');
        }

        return $this->respondCreated([
            'message' => 'OTP has been sent to your email. It will expire in 10 minutes.',
        ]);
    }

    public function verifyPasswordOtp()
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'email' => 'required|valid_email|max_length[150]',
            'otp'   => 'required|exact_length[6]|numeric',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $email = strtolower(trim($payload['email']));
        $otp = trim($payload['otp']);

        $otpRecord = $this->findValidOtpRecord($email, $otp, false);
        if ($otpRecord === null) {
            return $this->failValidationErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        return $this->respond([
            'message' => 'OTP is valid.',
        ]);
    }

    public function resetPassword()
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'email'            => 'required|valid_email|max_length[150]',
            'otp'              => 'required|exact_length[6]|numeric',
            'new_password'     => 'required|min_length[8]|max_length[255]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $email = strtolower(trim($payload['email']));
        $otp = trim($payload['otp']);

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        if ($user === null) {
            return $this->failNotFound('User not found.');
        }

        $otpRecord = $this->findValidOtpRecord($email, $otp, true);
        if ($otpRecord === null) {
            return $this->failValidationErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        $updated = $userModel->update($user['id'], [
            'password_hash' => password_hash($payload['new_password'], PASSWORD_DEFAULT),
        ]);

        if ($updated === false) {
            return $this->failServerError('Unable to update password.');
        }

        // Revoke all existing API tokens to force login with the new password.
        $tokenModel = new ApiTokenModel();
        $tokenModel->where('user_id', $user['id'])->delete();

        return $this->respond([
            'message' => 'Password reset successful. Please login again.',
        ]);
    }

    private function findValidOtpRecord(string $email, string $otp, bool $consume): ?array
    {
        $now = date('Y-m-d H:i:s');
        $otpModel = new PasswordOtpModel();

        $records = $otpModel
            ->where('email', $email)
            ->where('purpose', 'password_reset')
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->findAll(5);

        if ($records === []) {
            return null;
        }

        $recordToIncrement = null;

        foreach ($records as $record) {
            if ((int) $record['attempts'] >= (int) $record['max_attempts']) {
                continue;
            }

            if ($recordToIncrement === null) {
                // Charge failed attempts against only one active record to avoid mass lockouts.
                $recordToIncrement = $record;
            }

            if (password_verify($otp, $record['otp_hash'])) {
                if ($consume) {
                    $otpModel->update($record['id'], ['used_at' => $now]);
                }

                return $record;
            }
        }

        if ($recordToIncrement !== null) {
            $otpModel->update($recordToIncrement['id'], ['attempts' => ((int) $recordToIncrement['attempts']) + 1]);
        }

        return null;
    }

    private function sendOtpMail(string $emailAddress, string $otp): bool
    {
        $fromEmail = env('email.fromEmail');
        $fromName = env('email.fromName', 'CampusVoice');

        if ($fromEmail === null || trim($fromEmail) === '') {
            return false;
        }

        $email = service('email');
        $email->clear(true);
        $email->setFrom($fromEmail, $fromName);
        $email->setTo($emailAddress);
        $email->setSubject('CampusVoice Password Reset OTP');
        $email->setMessage(
            "Your CampusVoice OTP code is: {$otp}\n\n" .
            "This code will expire in 10 minutes.\n" .
            "If you did not request this, please ignore this email."
        );

        return $email->send();
    }
}
