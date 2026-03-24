<?php

namespace App\Services;

/**
 * NotificationService
 *
 * Handles automated email notifications to students on feedback events.
 * Uses the same Gmail SMTP pattern as OtpController::sendOtpMail().
 */
class NotificationService
{
    /**
     * Email a student when their feedback status has been updated by an admin.
     *
     * @param array $user     User record (must have 'email', 'first_name', 'last_name')
     * @param array $feedback Feedback record (must have 'id', 'subject')
     * @param string $newStatus  New status string (new|reviewed|resolved)
     * @return bool  true on send success, false on failure
     */
    public function sendStatusChange(array $user, array $feedback, string $newStatus): bool
    {
        $toEmail = (string) ($user['email'] ?? '');
        if ($toEmail === '') {
            return false;
        }

        $name        = trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? '')));
        $subject     = trim((string) ($feedback['subject'] ?? ''));
        $feedbackId  = (int) ($feedback['id'] ?? 0);
        $statusLabel = ucfirst($newStatus);
        $portalLink  = site_url('users/feedback/' . $feedbackId);

        $body =
            "Hello " . ($name !== '' ? $name : 'Student') . ",\n\n" .
            "The status of your feedback submission has been updated.\n\n" .
            "Feedback: " . ($subject !== '' ? $subject : 'Feedback #' . $feedbackId) . "\n" .
            "New Status: " . $statusLabel . "\n\n" .
            "You can view the details here:\n" . $portalLink . "\n\n" .
            "If you have questions, please contact your campus administrator.\n\n" .
            "— CampusVoice Team";

        return $this->send($toEmail, 'CampusVoice — Feedback Status Updated: ' . $statusLabel, $body);
    }

    /**
     * Email a student when an admin posts a reply to their feedback.
     *
     * @param array $user     User record
     * @param array $feedback Feedback record
     * @param string $replyMessage The admin's reply text
     * @return bool
     */
    public function sendAdminReply(array $user, array $feedback, string $replyMessage): bool
    {
        $toEmail = (string) ($user['email'] ?? '');
        if ($toEmail === '') {
            return false;
        }

        $name        = trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? '')));
        $subject     = trim((string) ($feedback['subject'] ?? ''));
        $feedbackId  = (int) ($feedback['id'] ?? 0);
        $portalLink  = site_url('users/feedback/' . $feedbackId);
        $preview     = strlen($replyMessage) > 200 ? substr($replyMessage, 0, 200) . '...' : $replyMessage;

        $body =
            "Hello " . ($name !== '' ? $name : 'Student') . ",\n\n" .
            "An administrator has replied to your feedback submission.\n\n" .
            "Feedback: " . ($subject !== '' ? $subject : 'Feedback #' . $feedbackId) . "\n\n" .
            "Admin Reply:\n" . $preview . "\n\n" .
            "View the full conversation:\n" . $portalLink . "\n\n" .
            "— CampusVoice Team";

        return $this->send($toEmail, 'CampusVoice — New Reply on Your Feedback', $body);
    }

    /**
     * Low-level send helper — mirrors the pattern from OtpController::sendOtpMail().
     */
    private function send(string $toEmail, string $subject, string $body): bool
    {
        try {
            $emailConfig = config('Email');
            $email = \Config\Services::email();
            $email->setFrom($emailConfig->SMTPUser, 'CampusVoice');
            $email->setTo($toEmail);
            $email->setSubject($subject);
            $email->setMessage($body);

            return (bool) $email->send();
        } catch (\Throwable $e) {
            log_message('error', '[NotificationService] Failed to send email to ' . $toEmail . ': ' . $e->getMessage());
            return false;
        }
    }
}
