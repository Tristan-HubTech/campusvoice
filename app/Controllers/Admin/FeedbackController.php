<?php

namespace App\Controllers\Admin;

use App\Models\FeedbackCategoryModel;
use App\Models\FeedbackModel;
use App\Models\FeedbackReplyModel;
use App\Models\UserModel;
use App\Services\NotificationService;

class FeedbackController extends AdminBaseController
{
    public function index()
    {
        return redirect()->to(site_url('admin') . '#feedback');
    }

    public function show(int $id): string
    {
        $feedbackModel = new FeedbackModel();
        $replyModel = new FeedbackReplyModel();

        $feedback = $feedbackModel
            ->select('feedbacks.*, feedback_categories.name as category_name, users.first_name, users.last_name, users.email')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->join('users', 'users.id = feedbacks.user_id', 'left')
            ->where('feedbacks.id', $id)
            ->first();

        if ($feedback === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Feedback item not found.');
        }

        $replies = $replyModel
            ->select('feedback_replies.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = feedback_replies.admin_user_id', 'left')
            ->where('feedback_replies.feedback_id', $id)
            ->orderBy('feedback_replies.created_at', 'ASC')
            ->findAll();

        return view('admin/feedback/show', [
            'title'      => 'Feedback Detail',
            'activeMenu' => 'feedback',
            'adminUser'  => $this->adminUser(),
            'feedback'   => $feedback,
            'replies'    => $replies,
        ]);
    }

    public function updateStatus(int $id)
    {
        $payload = $this->request->getPost();

        $rules = [
            'status' => 'required|in_list[pending,approved,rejected,reviewed,resolved]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $feedbackModel = new FeedbackModel();
        $feedback = $feedbackModel->find($id);

        if ($feedback === null) {
            return redirect()->back()->with('error', 'Feedback item not found.');
        }

        $status = (string) $payload['status'];
        $previousStatus = (string) ($feedback['status'] ?? '');

        $adminNotes = trim((string) ($payload['admin_notes'] ?? ''));

        $feedbackModel->update($id, [
            'status'      => $status,
            'resolved_at' => $status === 'resolved' ? date('Y-m-d H:i:s') : null,
            'admin_notes' => $adminNotes,
        ]);

        $previousAdminNotes = trim((string) ($feedback['admin_notes'] ?? ''));

        if ($adminNotes !== '' && $adminNotes !== $previousAdminNotes) {
            (new FeedbackReplyModel())->insert([
                'feedback_id'   => $id,
                'admin_user_id' => (int) ($this->adminUser()['id'] ?? 0),
                'message'       => $adminNotes,
            ]);
        }

        $this->logActivity(
            'feedback.status_updated',
            'Updated feedback status.',
            [
                'target_type'    => 'feedback',
                'target_id'      => $id,
                'from_status'    => $previousStatus,
                'to_status'      => $status,
                'admin_notes'    => $adminNotes,
            ]
        );

        // Email the submitter when status actually changed and it is not anonymous
        if ($status !== $previousStatus && (int) ($feedback['is_anonymous'] ?? 0) === 0) {
            $submitter = (new UserModel())->find((int) ($feedback['user_id'] ?? 0));
            if ($submitter !== null) {
                (new NotificationService())->sendStatusChange($submitter, array_merge($feedback, ['id' => $id]), $status);
            }
        }

        return redirect()->to(site_url('admin/feedback/' . $id))->with('success', 'Feedback status updated.');
    }

    public function reply(int $id)
    {
        $payload = $this->request->getPost();
        $rules = ['message' => 'required|min_length[2]'];

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $feedbackModel = new FeedbackModel();
        $feedback = $feedbackModel->find($id);

        if ($feedback === null) {
            return redirect()->back()->with('error', 'Feedback item not found.');
        }

        $replyModel = new FeedbackReplyModel();
        $replyMessage = trim((string) $payload['message']);
        
        $existing = $replyModel->where('feedback_id', $id)
            ->where('message', $replyMessage)
            ->first();
            
        if ($existing !== null) {
            return redirect()->back()->with('error', 'This reply has already been posted.');
        }

        $replyId = $replyModel->insert([
            'feedback_id'   => $id,
            'admin_user_id' => (int) ($this->adminUser()['id'] ?? 0),
            'message'       => $replyMessage,
        ]);

        if (in_array((string) $feedback['status'], ['approved', 'pending'], true)) {
            $feedbackModel->update($id, ['status' => 'reviewed']);
        }

        $this->logActivity(
            'feedback.reply_posted',
            'Posted a reply to feedback.',
            [
                'target_type' => 'feedback',
                'target_id'   => $id,
                'reply_id'    => (int) $replyId,
                'status_now'  => in_array((string) $feedback['status'], ['approved', 'pending'], true) ? 'reviewed' : (string) $feedback['status'],
            ]
        );

        // Email the submitter if not anonymous
        if ((int) ($feedback['is_anonymous'] ?? 0) === 0) {
            $submitter = (new UserModel())->find((int) ($feedback['user_id'] ?? 0));
            if ($submitter !== null) {
                (new NotificationService())->sendAdminReply($submitter, array_merge($feedback, ['id' => $id]), $replyMessage);
            }
        }

        return redirect()->to(site_url('admin/feedback/' . $id))->with('success', 'Reply posted successfully.');
    }

    public function approve(int $id)
    {
        $feedbackModel = new FeedbackModel();
        $feedback = $feedbackModel->find($id);

        $isAjax = $this->request->isAJAX();

        if ($feedback === null) {
            return $isAjax
                ? $this->response->setJSON(['ok' => false, 'message' => 'Feedback not found.'])
                : redirect()->back()->with('error', 'Feedback not found.');
        }

        if (! in_array((string) $feedback['status'], ['pending', 'rejected'], true)) {
            return $isAjax
                ? $this->response->setJSON(['ok' => false, 'message' => 'This feedback has already been processed.'])
                : redirect()->back()->with('error', 'This feedback has already been processed.');
        }

        $adminId = (int) ($this->adminUser()['id'] ?? 0);

        $feedbackModel->update($id, [
            'status'           => 'approved',
            'rejection_reason' => null,
            'reviewed_by'      => $adminId,
            'reviewed_at'      => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity(
            'feedback.approved',
            'Approved feedback for public feed.',
            ['target_type' => 'feedback', 'target_id' => $id, 'from_status' => (string) $feedback['status']]
        );

        if ($isAjax) {
            return $this->response->setJSON(['ok' => true, 'status' => 'approved']);
        }
        return redirect()->to(site_url('admin') . '#feedback')->with('success', 'Feedback approved.');
    }

    public function reject(int $id)
    {
        $feedbackModel = new FeedbackModel();
        $feedback = $feedbackModel->find($id);

        $isAjax = $this->request->isAJAX();
        $reason = trim((string) ($this->request->getPost('reason') ?? ''));

        if ($feedback === null) {
            return $isAjax
                ? $this->response->setJSON(['ok' => false, 'message' => 'Feedback not found.'])
                : redirect()->back()->with('error', 'Feedback not found.');
        }

        if ($reason === '') {
            return $isAjax
                ? $this->response->setJSON(['ok' => false, 'message' => 'Rejection reason is required.'])
                : redirect()->back()->with('error', 'Rejection reason is required.');
        }

        if (mb_strlen($reason) > 1000) {
            return $isAjax
                ? $this->response->setJSON(['ok' => false, 'message' => 'Rejection reason is too long (max 1000 chars).'])
                : redirect()->back()->with('error', 'Rejection reason is too long.');
        }

        if (! in_array((string) $feedback['status'], ['pending', 'approved'], true)) {
            return $isAjax
                ? $this->response->setJSON(['ok' => false, 'message' => 'Only pending or approved feedback can be rejected.'])
                : redirect()->back()->with('error', 'Only pending or approved feedback can be rejected.');
        }

        $adminId = (int) ($this->adminUser()['id'] ?? 0);

        $feedbackModel->update($id, [
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by'      => $adminId,
            'reviewed_at'      => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity(
            'feedback.rejected',
            'Rejected feedback.',
            ['target_type' => 'feedback', 'target_id' => $id, 'from_status' => (string) $feedback['status'], 'reason' => $reason]
        );

        if ($isAjax) {
            return $this->response->setJSON(['ok' => true, 'status' => 'rejected']);
        }
        return redirect()->to(site_url('admin') . '#feedback')->with('success', 'Feedback rejected.');
    }
}
