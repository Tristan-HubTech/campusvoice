<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\FeedbackModel;
use App\Models\SocialCommentModel;
use App\Models\SocialPostModel;
use App\Models\SocialProfileModel;
use App\Models\SocialReactionModel;
use App\Models\SocialShareModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class SocialController extends BaseController
{
    private array $avatarPalette = ['blue', 'teal', 'coral', 'violet', 'amber', 'rose'];

    public function index(): string
    {
        $viewer = $this->viewer();
        $viewerId = (int) ($viewer['id'] ?? 0);

        if ($viewerId > 0) {
            $this->ensureProfile($viewerId);
        }

        return view('social/feed', [
            'title'              => 'Community Feed',
            'pageKey'            => 'feed',
            'studentUser'        => $viewer,
            'currentUser'        => $viewer,
            'currentUserProfile' => $viewerId > 0 ? $this->ensureProfile($viewerId) : null,
            'posts'              => $this->buildPosts($viewerId),
            'stats'              => $this->communityStats($viewerId),
            'topCreators'        => $this->topCreators(),
            'announcements'      => $this->latestAnnouncements(),
        ]);
    }

    public function show(int $postId): string
    {
        $viewer = $this->viewer();
        $viewerId = (int) ($viewer['id'] ?? 0);
        $posts = $this->buildPosts($viewerId, null, $postId);

        if ($posts === []) {
            throw PageNotFoundException::forPageNotFound('Post not found.');
        }

        return view('social/feed', [
            'title'              => 'Post',
            'pageKey'            => 'feed',
            'studentUser'        => $viewer,
            'currentUser'        => $viewer,
            'currentUserProfile' => $viewerId > 0 ? $this->ensureProfile($viewerId) : null,
            'posts'              => $posts,
            'stats'              => $this->communityStats($viewerId),
            'topCreators'        => $this->topCreators(),
            'announcements'      => $this->latestAnnouncements(),
            'focusMode'          => true,
        ]);
    }

    public function profile(int $userId): string
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
            'title'              => trim((string) $user['first_name'] . ' ' . (string) $user['last_name']),
            'pageKey'            => 'profile',
            'studentUser'        => $viewer,
            'currentUser'        => $viewer,
            'currentUserProfile' => $viewerId > 0 ? $this->ensureProfile($viewerId) : null,
            'profileUser'        => $user,
            'profileDetails'     => $profile,
            'profileStats'       => $this->profileStats((int) $user['id']),
            'posts'              => $this->buildPosts($viewerId, (int) $user['id']),
        ]);
    }

    public function settings()
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $viewer = $this->viewer();
        $viewerId = (int) $viewer['id'];
        $profile = $this->ensureProfile($viewerId);
        $userModel = new UserModel();

        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[100]',
                'last_name'  => 'required|min_length[2]|max_length[100]',
                'phone'      => 'permit_empty|max_length[30]',
                'bio'        => 'permit_empty|max_length[500]',
                'avatar_color' => 'required|in_list[' . implode(',', $this->avatarPalette) . ']',
            ];

            $password = (string) ($this->request->getPost('password') ?? '');
            $passwordConfirm = (string) ($this->request->getPost('password_confirm') ?? '');
            if ($password !== '' || $passwordConfirm !== '') {
                $rules['password'] = 'required|min_length[8]|max_length[255]';
                $rules['password_confirm'] = 'required|matches[password]';
            }

            if (! $this->validate($rules)) {
                return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()))->withInput();
            }

            $updateUser = [
                'first_name' => trim((string) $this->request->getPost('first_name')),
                'last_name'  => trim((string) $this->request->getPost('last_name')),
                'phone'      => trim((string) ($this->request->getPost('phone') ?? '')) ?: null,
            ];

            if ($password !== '') {
                $updateUser['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $userModel->update($viewerId, $updateUser);

            $profileModel = new SocialProfileModel();
            $profileModel->update((int) $profile['id'], [
                'bio'          => trim((string) ($this->request->getPost('bio') ?? '')) ?: null,
                'avatar_color' => (string) $this->request->getPost('avatar_color'),
            ]);

            $user = $userModel->find($viewerId);
            session()->set('student_auth', [
                'id'    => (int) $user['id'],
                'name'  => trim((string) $user['first_name'] . ' ' . (string) $user['last_name']),
                'email' => (string) $user['email'],
                'role'  => (string) ($viewer['role'] ?? 'user'),
            ]);

            return redirect()->to(site_url('settings'))->with('success', 'Your account settings were updated.');
        }

        return view('social/settings', [
            'title'              => 'Settings',
            'pageKey'            => 'settings',
            'studentUser'        => $viewer,
            'currentUser'        => $viewer,
            'currentUserProfile' => $this->ensureProfile($viewerId),
            'avatarPalette'      => $this->avatarPalette,
            'settingsUser'       => $userModel->find($viewerId),
        ]);
    }

    public function createPost()
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $rules = [
            'body' => 'required|min_length[3]|max_length[4000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()))->withInput();
        }

        (new SocialPostModel())->insert([
            'user_id'   => (int) $this->viewer()['id'],
            'body'      => trim((string) $this->request->getPost('body')),
            'is_public' => 1,
        ]);

        return redirect()->to(site_url('feed'))->with('success', 'Your post is now live.');
    }

    public function react(int $postId)
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $reactionType = strtolower(trim((string) ($this->request->getPost('reaction_type') ?? '')));
        if (! in_array($reactionType, ['like', 'love', 'support', 'fire'], true)) {
            return $this->redirectToReferrer('feed')->with('error', 'Unsupported reaction.');
        }

        $post = (new SocialPostModel())->find($postId);
        if ($post === null) {
            return $this->redirectToReferrer('feed')->with('error', 'Post not found.');
        }

        $viewerId = (int) $this->viewer()['id'];
        $reactionModel = new SocialReactionModel();
        $existing = $reactionModel
            ->where('post_id', $postId)
            ->where('user_id', $viewerId)
            ->first();

        if ($existing !== null && (string) $existing['reaction_type'] === $reactionType) {
            $reactionModel->delete((int) $existing['id']);
        } else {
            $payload = [
                'post_id'        => $postId,
                'user_id'        => $viewerId,
                'reaction_type'  => $reactionType,
            ];

            if ($existing !== null) {
                $payload['id'] = (int) $existing['id'];
            }

            $reactionModel->save($payload);
        }

        return $this->redirectToReferrer('posts/' . $postId);
    }

    public function comment(int $postId)
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $rules = [
            'body' => 'required|min_length[1]|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return $this->redirectToReferrer('posts/' . $postId)->with('error', implode(' ', $this->validator->getErrors()))->withInput();
        }

        $post = (new SocialPostModel())->find($postId);
        if ($post === null) {
            return $this->redirectToReferrer('feed')->with('error', 'Post not found.');
        }

        (new SocialCommentModel())->insert([
            'post_id' => $postId,
            'user_id' => (int) $this->viewer()['id'],
            'body'    => trim((string) $this->request->getPost('body')),
        ]);

        return $this->redirectToReferrer('posts/' . $postId)->with('success', 'Comment added.');
    }

    public function share(int $postId)
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $post = (new SocialPostModel())->find($postId);
        if ($post === null) {
            return $this->redirectToReferrer('feed')->with('error', 'Post not found.');
        }

        $viewerId = (int) $this->viewer()['id'];
        $shareModel = new SocialShareModel();
        $existing = $shareModel
            ->where('post_id', $postId)
            ->where('user_id', $viewerId)
            ->first();

        if ($existing === null) {
            $shareModel->insert([
                'post_id' => $postId,
                'user_id' => $viewerId,
            ]);
        } else {
            $shareModel->update((int) $existing['id'], [
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->redirectToReferrer('posts/' . $postId)->with('success', 'Post link saved to your shares.');
    }

    private function viewer(): array
    {
        return (array) (session()->get('student_auth') ?? []);
    }

    private function requireUser()
    {
        if (empty($this->viewer()['id'])) {
            return redirect()->to(site_url('users/login'))->with('error', 'Please log in to continue.');
        }

        return null;
    }

    private function redirectToReferrer(string $fallback)
    {
        $referer = trim((string) $this->request->getServer('HTTP_REFERER'));
        if ($referer !== '') {
            return redirect()->to($referer);
        }

        return redirect()->to(site_url($fallback));
    }

    private function ensureProfile(int $userId): array
    {
        $profileModel = new SocialProfileModel();
        $profile = $profileModel->where('user_id', $userId)->first();
        if ($profile !== null) {
            return $profile;
        }

        $profileModel->insert([
            'user_id'      => $userId,
            'bio'          => null,
            'avatar_color' => $this->avatarPalette[$userId % count($this->avatarPalette)],
        ]);

        return (array) $profileModel->where('user_id', $userId)->first();
    }

    private function buildPosts(int $viewerId = 0, ?int $userId = null, ?int $postId = null): array
    {
        $query = (new SocialPostModel())
            ->select('social_posts.*, users.first_name, users.last_name, users.email, social_profiles.avatar_color, social_profiles.bio')
            ->join('users', 'users.id = social_posts.user_id', 'inner')
            ->join('social_profiles', 'social_profiles.user_id = users.id', 'left')
            ->where('users.is_active', 1)
            ->where('social_posts.is_public', 1)
            ->orderBy('social_posts.created_at', 'DESC');

        if ($userId !== null) {
            $query->where('social_posts.user_id', $userId);
        }

        if ($postId !== null) {
            $query->where('social_posts.id', $postId);
        }

        $posts = $query->findAll($postId !== null ? 1 : 20);
        if ($posts === []) {
            return [];
        }

        $postIds = array_map(static fn (array $post): int => (int) $post['id'], $posts);
        $db = db_connect();

        $reactionRows = $db->table('social_post_reactions')
            ->select('post_id, reaction_type, COUNT(*) as total')
            ->whereIn('post_id', $postIds)
            ->groupBy('post_id, reaction_type')
            ->get()
            ->getResultArray();

        $reactionTotals = [];
        $reactionBreakdown = [];
        foreach ($reactionRows as $row) {
            $rowPostId = (int) $row['post_id'];
            $total = (int) $row['total'];
            $type = (string) $row['reaction_type'];
            $reactionTotals[$rowPostId] = ($reactionTotals[$rowPostId] ?? 0) + $total;
            $reactionBreakdown[$rowPostId][$type] = $total;
        }

        $viewerReactions = [];
        if ($viewerId > 0) {
            $viewerReactionRows = $db->table('social_post_reactions')
                ->select('post_id, reaction_type')
                ->where('user_id', $viewerId)
                ->whereIn('post_id', $postIds)
                ->get()
                ->getResultArray();

            foreach ($viewerReactionRows as $row) {
                $viewerReactions[(int) $row['post_id']] = (string) $row['reaction_type'];
            }
        }

        $shareRows = $db->table('social_post_shares')
            ->select('post_id, COUNT(*) as total')
            ->whereIn('post_id', $postIds)
            ->groupBy('post_id')
            ->get()
            ->getResultArray();

        $shareTotals = [];
        foreach ($shareRows as $row) {
            $shareTotals[(int) $row['post_id']] = (int) $row['total'];
        }

        $commentRows = $db->table('social_post_comments')
            ->select('social_post_comments.*, users.first_name, users.last_name, social_profiles.avatar_color')
            ->join('users', 'users.id = social_post_comments.user_id', 'inner')
            ->join('social_profiles', 'social_profiles.user_id = users.id', 'left')
            ->whereIn('social_post_comments.post_id', $postIds)
            ->where('social_post_comments.deleted_at', null)
            ->orderBy('social_post_comments.created_at', 'ASC')
            ->get()
            ->getResultArray();

        $commentsByPost = [];
        foreach ($commentRows as $row) {
            $row['author_name'] = trim((string) $row['first_name'] . ' ' . (string) $row['last_name']);
            $row['avatar_color'] = (string) ($row['avatar_color'] ?? 'blue');
            $commentsByPost[(int) $row['post_id']][] = $row;
        }

        foreach ($posts as &$post) {
            $postUserId = (int) $post['user_id'];
            $post['author_name'] = trim((string) $post['first_name'] . ' ' . (string) $post['last_name']);
            $post['avatar_color'] = (string) ($post['avatar_color'] ?? $this->avatarPalette[$postUserId % count($this->avatarPalette)]);
            $post['initials'] = strtoupper(substr((string) $post['first_name'], 0, 1) . substr((string) $post['last_name'], 0, 1));
            $post['reaction_total'] = (int) ($reactionTotals[(int) $post['id']] ?? 0);
            $post['reaction_breakdown'] = $reactionBreakdown[(int) $post['id']] ?? [];
            $post['viewer_reaction'] = $viewerReactions[(int) $post['id']] ?? null;
            $post['share_total'] = (int) ($shareTotals[(int) $post['id']] ?? 0);
            $post['comments'] = array_slice($commentsByPost[(int) $post['id']] ?? [], -3);
            $post['comment_total'] = count($commentsByPost[(int) $post['id']] ?? []);
            $post['permalink'] = site_url('posts/' . (int) $post['id']);
            $post['profile_url'] = site_url('profile/' . $postUserId);
        }
        unset($post);

        return $posts;
    }

    private function communityStats(int $viewerId): array
    {
        $postModel = new SocialPostModel();
        $commentModel = new SocialCommentModel();
        $reactionModel = new SocialReactionModel();
        $shareModel = new SocialShareModel();
        $userModel = new UserModel();
        $feedbackModel = new FeedbackModel();

        return [
            'posts'       => $postModel->countAllResults(),
            'people'      => $userModel->where('is_active', 1)->countAllResults(),
            'comments'    => $commentModel->countAllResults(),
            'reactions'   => $reactionModel->countAllResults(),
            'shares'      => $shareModel->countAllResults(),
            'my_feedback' => $viewerId > 0 ? $feedbackModel->where('user_id', $viewerId)->countAllResults() : 0,
        ];
    }

    private function profileStats(int $userId): array
    {
        return [
            'posts'     => (new SocialPostModel())->where('user_id', $userId)->countAllResults(),
            'comments'  => (new SocialCommentModel())->where('user_id', $userId)->countAllResults(),
            'reactions' => (new SocialReactionModel())->where('user_id', $userId)->countAllResults(),
            'shares'    => (new SocialShareModel())->where('user_id', $userId)->countAllResults(),
        ];
    }

    private function topCreators(): array
    {
        return db_connect()->table('social_posts')
            ->select('users.id, users.first_name, users.last_name, social_profiles.avatar_color, COUNT(social_posts.id) as post_total')
            ->join('users', 'users.id = social_posts.user_id', 'inner')
            ->join('social_profiles', 'social_profiles.user_id = users.id', 'left')
            ->groupBy('users.id, users.first_name, users.last_name, social_profiles.avatar_color')
            ->orderBy('post_total', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }

    private function latestAnnouncements(): array
    {
        return (new AnnouncementModel())
            ->where('is_published', 1)
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(3);
    }
}