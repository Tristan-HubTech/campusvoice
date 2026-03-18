<?php

namespace App\Controllers\Api;

use App\Models\AnnouncementModel;

class AnnouncementController extends ApiController
{
    public function index()
    {
        $now = date('Y-m-d H:i:s');

        $announcementModel = new AnnouncementModel();
        $records = $announcementModel
            ->where('is_published', 1)
            ->groupStart()
                ->where('publish_at', null)
                ->orWhere('publish_at <=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('expires_at', null)
                ->orWhere('expires_at >', $now)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->respond(['data' => $records]);
    }

    public function adminIndex()
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if (! $this->isAdmin($user)) {
            return $this->failForbidden('Admin access is required.');
        }

        $announcementModel = new AnnouncementModel();
        $records = $announcementModel
            ->select('announcements.*, users.first_name, users.last_name')
            ->join('users', 'users.id = announcements.posted_by', 'left')
            ->orderBy('announcements.created_at', 'DESC')
            ->findAll();

        return $this->respond(['data' => $records]);
    }

    public function store()
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if (! $this->isAdmin($user)) {
            return $this->failForbidden('Admin access is required.');
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'title'        => 'required|max_length[180]',
            'body'         => 'required|min_length[3]',
            'audience'     => 'permit_empty|in_list[all,students,admins]',
            'publish_at'   => 'permit_empty',
            'expires_at'   => 'permit_empty',
            'is_published' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $announcementModel = new AnnouncementModel();
        $saved = $announcementModel->insert([
            'title'        => trim($payload['title']),
            'body'         => trim($payload['body']),
            'posted_by'    => $user['id'],
            'audience'     => $payload['audience'] ?? 'all',
            'publish_at'   => $payload['publish_at'] ?? null,
            'expires_at'   => $payload['expires_at'] ?? null,
            'is_published' => isset($payload['is_published']) ? (int) $payload['is_published'] : 1,
        ]);

        if ($saved === false) {
            return $this->failServerError('Unable to create announcement.');
        }

        return $this->respondCreated([
            'message'         => 'Announcement created successfully.',
            'announcement_id' => $saved,
        ]);
    }

    public function update($id = null)
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if (! $this->isAdmin($user)) {
            return $this->failForbidden('Admin access is required.');
        }

        if ($id === null) {
            return $this->failValidationErrors(['id' => 'Announcement ID is required.']);
        }

        $announcementModel = new AnnouncementModel();
        $existing = $announcementModel->find($id);
        if ($existing === null) {
            return $this->failNotFound('Announcement not found.');
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getRawInput();

        $updateData = [];
        foreach (['title', 'body', 'audience', 'publish_at', 'expires_at', 'is_published'] as $field) {
            if (array_key_exists($field, $payload)) {
                $updateData[$field] = $payload[$field];
            }
        }

        if ($updateData === []) {
            return $this->failValidationErrors(['payload' => 'No fields provided to update.']);
        }

        if (isset($updateData['is_published'])) {
            $updateData['is_published'] = (int) $updateData['is_published'];
        }

        $updated = $announcementModel->update($id, $updateData);
        if ($updated === false) {
            return $this->failServerError('Unable to update announcement.');
        }

        return $this->respond(['message' => 'Announcement updated successfully.']);
    }

    public function delete($id = null)
    {
        $user = $this->authUser();
        if ($user === null) {
            return $this->failUnauthorized('Unauthorized.');
        }

        if (! $this->isAdmin($user)) {
            return $this->failForbidden('Admin access is required.');
        }

        if ($id === null) {
            return $this->failValidationErrors(['id' => 'Announcement ID is required.']);
        }

        $announcementModel = new AnnouncementModel();
        $exists = $announcementModel->find($id);
        if ($exists === null) {
            return $this->failNotFound('Announcement not found.');
        }

        $announcementModel->delete($id);

        return $this->respondDeleted(['message' => 'Announcement deleted successfully.']);
    }
}
