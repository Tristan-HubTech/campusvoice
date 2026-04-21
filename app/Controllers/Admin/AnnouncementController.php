<?php

namespace App\Controllers\Admin;

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

        $announcementModel = new AnnouncementModel();
        $announcementId = $announcementModel->insert([
            'title'        => trim((string) $payload['title']),
            'body'         => trim((string) $payload['body']),
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

        return redirect()->to(site_url('admin') . '#announcements')->with('success', 'Announcement created successfully.');
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

        $oldTitle = (string) ($announcement['title'] ?? '');

        $announcementModel->update($id, [
            'title'        => trim((string) $payload['title']),
            'body'         => trim((string) $payload['body']),
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

        return redirect()->to(site_url('admin') . '#announcements')->with('success', 'Announcement updated successfully.');
    }

    public function delete(int $id)
    {
        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if ($announcement === null) {
            return redirect()->to(site_url('admin') . '#announcements')->with('error', 'Announcement not found.');
        }

        $deletedTitle = (string) ($announcement['title'] ?? '');

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

        return redirect()->to(site_url('admin') . '#announcements')->with('success', 'Announcement deleted successfully.');
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
