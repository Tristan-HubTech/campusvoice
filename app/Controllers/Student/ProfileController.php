<?php

namespace App\Controllers\Student;

use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProfileController extends StudentBaseController
{
    public function view(int $userId): string
    {
        $viewer = $this->viewer();
        $viewerId = (int) ($viewer['id'] ?? 0);

        $user = (new UserModel())
            ->where('id', $userId)
            ->where('is_active', 1)
            ->first();

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('Profile not found.');
        }

        $profile = $this->ensureProfile((int) $user['id']);

        return view('social/profile', [
            'title'          => trim((string) $user['first_name'] . ' ' . (string) $user['last_name']),
            'pageKey'        => 'profile',
            'studentUser'    => $viewer,
            'currentUser'    => $viewer,
            'profileUser'    => $user,
            'profileDetails' => $profile,
            'profileStats'   => $this->profileStats((int) $user['id']),
            'posts'          => $this->buildPosts($viewerId, (int) $user['id']),
            'isAnonymous'    => $viewerId > 0 && (int) ($this->ensureProfile($viewerId)['is_anonymous'] ?? 0) === 1,
            'anonAlias'      => $viewerId > 0 ? $this->anonymousAlias($viewerId) : '',
        ]);
    }
}
