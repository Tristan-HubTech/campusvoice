<?php

namespace App\Controllers\Api;

use App\Models\FeedbackModel;
use App\Models\FeedbackReplyModel;

class FeedbackController extends ApiController
{
    public function index()
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if (! $this->isAdmin($user)) {
            return $this->failForbidden('Admin access is required.');
        }

        $feedbackModel = new FeedbackModel();
        $feedbackModel
            ->select('feedbacks.*, feedback_categories.name as category_name, feedback_categories.color as category_color, users.first_name, users.last_name')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->join('users', 'users.id = feedbacks.user_id', 'left');

        $status = $this->request->getGet('status');
        $type = $this->request->getGet('type');
        $categoryId = $this->request->getGet('category_id');

        if ($status !== null && $status !== '') {
            $feedbackModel->where('feedbacks.status', $status);
        }

        if ($type !== null && $type !== '') {
            $feedbackModel->where('feedbacks.type', $type);
        }

        if ($categoryId !== null && $categoryId !== '') {
            $feedbackModel->where('feedbacks.category_id', $categoryId);
        }

        $records = $feedbackModel->orderBy('feedbacks.created_at', 'DESC')->findAll();

        return $this->respond(['data' => $records]);
    }

    public function myFeedback()
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        $feedbackModel = new FeedbackModel();
        $records = $feedbackModel
            ->select('feedbacks.*, feedback_categories.name as category_name, feedback_categories.color as category_color')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->where('feedbacks.user_id', $user['id'])
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll();

        return $this->respond(['data' => $records]);
    }

    public function store()
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if ($user['role'] !== 'student') {
            return $this->failForbidden('Only student accounts can submit feedback.');
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'category_id'   => 'required|integer|is_not_unique[feedback_categories.id]',
            'type'          => 'required|in_list[complaint,suggestion,praise]',
            'subject'       => 'permit_empty|max_length[150]',
            'message'       => 'required|min_length[5]',
            'is_anonymous'  => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $feedbackModel = new FeedbackModel();
        $id = $feedbackModel->insert([
            'user_id'      => $user['id'],
            'category_id'  => (int) $payload['category_id'],
            'type'         => $payload['type'],
            'subject'      => $payload['subject'] ?? null,
            'message'      => trim($payload['message']),
            'is_anonymous' => isset($payload['is_anonymous']) ? (int) $payload['is_anonymous'] : 0,
            'status'       => 'pending',
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);

        if ($id === false) {
            return $this->failServerError('Unable to save feedback.');
        }

        return $this->respondCreated([
            'message'     => 'Feedback submitted successfully.',
            'feedback_id' => $id,
        ]);
    }

    public function show($id = null)
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if ($id === null) {
            return $this->failValidationErrors(['id' => 'Feedback ID is required.']);
        }

        $feedbackModel = new FeedbackModel();
        $record = $feedbackModel
            ->select('feedbacks.*, feedback_categories.name as category_name, feedback_categories.color as category_color')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->where('feedbacks.id', $id)
            ->first();

        if ($record === null) {
            return $this->failNotFound('Feedback not found.');
        }

        if (! $this->isAdmin($user) && (int) $record['user_id'] !== (int) $user['id']) {
            return $this->failForbidden('You do not have access to this feedback.');
        }

        $replyModel = new FeedbackReplyModel();
        $replies = $replyModel
            ->select('feedback_replies.*, users.first_name, users.last_name')
            ->join('users', 'users.id = feedback_replies.admin_user_id', 'left')
            ->where('feedback_id', $id)
            ->orderBy('feedback_replies.created_at', 'ASC')
            ->findAll();

        return $this->respond([
            'data'    => $record,
            'replies' => $replies,
        ]);
    }

    public function updateStatus($id = null)
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if (! $this->isAdmin($user)) {
            return $this->failForbidden('Admin access is required.');
        }

        if ($id === null) {
            return $this->failValidationErrors(['id' => 'Feedback ID is required.']);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $rules = [
            'status' => 'required|in_list[new,reviewed,resolved]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $feedbackModel = new FeedbackModel();
        $exists = $feedbackModel->find($id);
        if ($exists === null) {
            return $this->failNotFound('Feedback not found.');
        }

        $status = $payload['status'];
        $updated = $feedbackModel->update($id, [
            'status'      => $status,
            'resolved_at' => $status === 'resolved' ? date('Y-m-d H:i:s') : null,
        ]);

        if ($updated === false) {
            return $this->failServerError('Unable to update feedback status.');
        }

        return $this->respond([
            'message' => 'Feedback status updated successfully.',
        ]);
    }

    public function reply($id = null)
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if (! $this->isAdmin($user)) {
            return $this->failForbidden('Admin access is required.');
        }

        if ($id === null) {
            return $this->failValidationErrors(['id' => 'Feedback ID is required.']);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = ['message' => 'required|min_length[2]'];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $feedbackModel = new FeedbackModel();
        $exists = $feedbackModel->find($id);
        if ($exists === null) {
            return $this->failNotFound('Feedback not found.');
        }

        $replyModel = new FeedbackReplyModel();
        $saved = $replyModel->insert([
            'feedback_id'   => (int) $id,
            'admin_user_id' => (int) $user['id'],
            'message'       => trim($payload['message']),
        ]);

        if ($saved === false) {
            return $this->failServerError('Unable to save reply.');
        }

        if ($exists['status'] === 'new') {
            $feedbackModel->update($id, ['status' => 'reviewed']);
        }

        return $this->respondCreated([
            'message' => 'Reply saved successfully.',
        ]);
    }
}
