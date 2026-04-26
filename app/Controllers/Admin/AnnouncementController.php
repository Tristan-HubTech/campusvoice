<?php

namespace App\Controllers\Admin;

use App\Libraries\FeedbackImageStorage;
use App\Models\AnnouncementModel;

class AnnouncementController extends AdminBaseController
{
    public function index()
    {
        return redirect()->to(site_url('admin') . '#announcements');
    }

    public function create()
    {
        return redirect()->to(site_url('admin') . '#announcements');
    }

    public function store()
    {
        $payload = $this->request->getPost();
        $rules = [
            'title'        => 'required|max_length[180]',
            'body'         => 'required|min_length[3]',
            'is_published' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        // Handle optional image upload
        $imagePath = null;
        try {
            $imagePath = FeedbackImageStorage::tryStore($this->request->getFile('image'));
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $announcementModel = new AnnouncementModel();
        $announcementId = $announcementModel->insert([
            'title'        => trim((string) $payload['title']),
            'body'         => trim((string) $payload['body']),
            'image_path'   => $imagePath,
            'posted_by'    => (int) ($this->adminUser()['id'] ?? 0),
            'audience'     => 'all',
            'publish_at'   => $this->normalizeDateTime($payload['publish_at'] ?? null),
            'expires_at'   => $this->normalizeDateTime($payload['expires_at'] ?? null),
            'is_published' => isset($payload['is_published']) ? (int) $payload['is_published'] : 1,
        ]);

        $this->logActivity(
            'announcement.created',
            'Created an announcement.',
            [
                'target_type' => 'announcement',
                'target_id'   => (int) $announcementId,
                'title'       => trim((string) $payload['title']),
                'published'   => isset($payload['is_published']) ? (int) $payload['is_published'] : 1,
            ]
        );

        return redirect()->to(site_url('admin') . '#announcement-list')->with('success', 'Announcement created successfully.');
    }

    public function edit(int $id)
    {
        return redirect()->to(site_url('admin') . '#announcements');
    }

    public function update(int $id)
    {
        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if ($announcement === null) {
            return redirect()->to(site_url('admin') . '#announcements')->with('error', 'Announcement not found.');
        }

        $payload = $this->request->getPost();
        $rules = [
            'title'        => 'required|max_length[180]',
            'body'         => 'required|min_length[3]',
            'is_published' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $oldTitle  = (string) ($announcement['title'] ?? '');
        $oldImage  = (string) ($announcement['image_path'] ?? '');

        // Determine new image path
        $newImagePath = $oldImage !== '' ? $oldImage : null; // keep existing by default
        $removeImage  = (string) ($payload['remove_image'] ?? '') === '1';

        try {
            $uploaded = FeedbackImageStorage::tryStore($this->request->getFile('image'));
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        if ($uploaded !== null) {
            // New file uploaded — delete old and use new
            FeedbackImageStorage::delete($oldImage !== '' ? $oldImage : null);
            $newImagePath = $uploaded;
        } elseif ($removeImage) {
            FeedbackImageStorage::delete($oldImage !== '' ? $oldImage : null);
            $newImagePath = null;
        }

        $announcementModel->update($id, [
            'title'        => trim((string) $payload['title']),
            'body'         => trim((string) $payload['body']),
            'image_path'   => $newImagePath,
            'audience'     => 'all',
            'publish_at'   => $this->normalizeDateTime($payload['publish_at'] ?? null),
            'expires_at'   => $this->normalizeDateTime($payload['expires_at'] ?? null),
            'is_published' => isset($payload['is_published']) ? (int) $payload['is_published'] : 1,
        ]);

        $this->logActivity(
            'announcement.updated',
            'Updated an announcement.',
            [
                'target_type' => 'announcement',
                'target_id'   => $id,
                'old_title'   => $oldTitle,
                'new_title'   => trim((string) $payload['title']),
                'published'   => isset($payload['is_published']) ? (int) $payload['is_published'] : 1,
            ]
        );

        return redirect()->to(site_url('admin') . '#announcement-list')->with('success', 'Announcement updated successfully.');
    }

    public function delete(int $id)
    {
        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if ($announcement === null) {
            return redirect()->to(site_url('admin') . '#announcements')->with('error', 'Announcement not found.');
        }

        $deletedTitle = (string) ($announcement['title'] ?? '');

        // Delete image file from disk
        FeedbackImageStorage::delete((string) ($announcement['image_path'] ?? '') ?: null);

        $announcementModel->delete($id);

        $this->logActivity(
            'announcement.deleted',
            'Deleted an announcement.',
            [
                'target_type' => 'announcement',
                'target_id'   => $id,
                'title'       => $deletedTitle,
            ]
        );

        return redirect()->to(site_url('admin') . '#announcement-list')->with('success', 'Announcement deleted successfully.');
    }

    public function togglePin()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $id = $this->request->getPost('id');
        if (! $id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing ID']);
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (! $announcement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Announcement not found']);
        }

        $currentStatus = (int) ($announcement['pinned'] ?? 0);
        $newStatus = $currentStatus === 1 ? 0 : 1;

        if ($newStatus === 1) {
            $db = \Config\Database::connect();
            $db->table('announcements')->update(['pinned' => 0]);
        }

        $announcementModel->update($id, ['pinned' => $newStatus]);

        return $this->response->setJSON([
            'success' => true,
            'pinned'  => $newStatus,
            'message' => $newStatus ? 'Announcement pinned' : 'Announcement unpinned',
        ]);
    }

    private function normalizeDateTime(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}
