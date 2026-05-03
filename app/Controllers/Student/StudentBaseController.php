<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\SocialCommentModel;
use App\Models\SocialPostModel;
use App\Models\SocialProfileModel;
use App\Models\SocialReactionModel;
use App\Models\SocialShareModel;
use App\Models\StudentActivityLogModel;
use Throwable;

abstract class StudentBaseController extends BaseController
{
    protected array $avatarPalette = ['blue', 'teal', 'coral', 'violet', 'amber', 'rose'];

    protected function viewer(): array
    {
        return (array) (session()->get('student_auth') ?? []);
    }

    protected function requireUser()
    {
        if (empty($this->viewer()['id'])) {
            return redirect()->to(site_url('users/login'))->with('error', 'Please log in to continue.');
        }

        return null;
    }

    protected function anonymousAlias(int $userId): string
    {
        $number = str_pad((string) (($userId * 31 + 7) % 100), 2, '0', STR_PAD_LEFT);
        return 'Versace' . $number;
    }

    protected function logStudentActivity(
        string $action,
        string $description,
        ?string $targetType = null,
        ?int $targetId = null,
        ?array $metadata = null
    ): void {
        try {
            $viewer = $this->viewer();
            $userId = (int) ($viewer['id'] ?? 0);
            (new StudentActivityLogModel())->log(
                $userId > 0 ? $userId : null,
                $action,
                $description,
                $targetType,
                $targetId,
                $metadata,
                (string) ($viewer['name'] ?? ''),
                (string) ($viewer['email'] ?? ''),
                method_exists($this->request, 'getIPAddress') ? (string) $this->request->getIPAddress() : ''
            );
        } catch (Throwable $e) {
            log_message('error', 'Student activity logging failed: {message}', ['message' => $e->getMessage()]);
        }
    }

    protected function redirectToReferrer(string $fallback, string $anchor = '')
    {
        $referer = trim((string) $this->request->getServer('HTTP_REFERER'));

        if ($anchor !== '') {
            $anchor = '#' . ltrim($anchor, '#');
        }

        if ($referer !== '') {
            $referer = preg_replace('/#.*$/', '', $referer);
            return redirect()->to($referer . $anchor);
        }

        return redirect()->to(site_url($fallback) . $anchor);
    }

    protected function ensureProfile(int $userId): array
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

    protected function profileStats(int $userId): array
    {
        return [
            'posts'     => (new SocialPostModel())->where('user_id', $userId)->countAllResults(),
            'comments'  => (new SocialCommentModel())->where('user_id', $userId)->countAllResults(),
            'reactions' => (new SocialReactionModel())->where('user_id', $userId)->countAllResults(),
            'shares'    => (new SocialShareModel())->where('user_id', $userId)->countAllResults(),
        ];
    }

    /**
     * Build an enriched posts array with reactions, comments, and author data.
     * Used by both the community feed (all posts) and profile pages (user-specific posts).
     *
     * @param int       $viewerId Current viewer's user ID (0 if guest)
     * @param int|null  $userId   Filter posts to this user; null = all posts
     * @param int       $limit    Max posts to return
     */
    protected function buildPosts(int $viewerId = 0, ?int $userId = null, int $limit = 20): array
    {
        $query = (new SocialPostModel())
            ->select('social_posts.*, users.first_name, users.last_name, users.email, social_profiles.avatar_color, social_profiles.bio, social_profiles.is_anonymous as profile_is_anonymous, feedbacks.status as feedback_status, feedbacks.type as feedback_type, feedbacks.image_path as feedback_image_path, feedback_categories.name as category_name, feedback_categories.color as category_color')
            ->join('users', 'users.id = social_posts.user_id', 'inner')
            ->join('social_profiles', 'social_profiles.user_id = users.id', 'left')
            ->join('feedbacks', 'feedbacks.id = social_posts.feedback_id', 'left')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->where('users.is_active', 1)
            ->where('social_posts.is_public', 1)
            ->groupStart()
                ->where('social_posts.feedback_id IS NULL')
                ->orWhereIn('feedbacks.status', ['approved', 'reviewed', 'resolved'])
            ->groupEnd()
            ->orderBy('social_posts.created_at', 'DESC');

        if ($userId !== null) {
            $query->where('social_posts.user_id', $userId);
        }

        $posts = $query->findAll($limit);
        if ($posts === []) {
            return [];
        }

        $postIds = array_map(static fn (array $post): int => (int) $post['id'], $posts);
        $db = db_connect();

        $feedbackIds = array_values(array_unique(array_filter(array_map(
            static fn (array $p): int => (int) ($p['feedback_id'] ?? 0),
            $posts
        ))));
        $feedbackImageById = [];
        if ($feedbackIds !== []) {
            foreach ($db->table('feedbacks')->select('id, image_path')->whereIn('id', $feedbackIds)->get()->getResultArray() as $fr) {
                $feedbackImageById[(int) $fr['id']] = $fr['image_path'] ?? null;
            }
        }

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

        $commentRows = $db->table('social_post_comments')
            ->select('social_post_comments.*, users.first_name, users.last_name, social_profiles.avatar_color, social_profiles.is_anonymous as profile_is_anonymous')
            ->join('users', 'users.id = social_post_comments.user_id', 'inner')
            ->join('social_profiles', 'social_profiles.user_id = users.id', 'left')
            ->whereIn('social_post_comments.post_id', $postIds)
            ->where('social_post_comments.deleted_at', null)
            ->orderBy('social_post_comments.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $commentsByPost = [];
        $repliesByComment = [];
        foreach ($commentRows as $row) {
            $commentIsAnonymous = (int) ($row['is_anonymous'] ?? 0) === 1 || (int) ($row['profile_is_anonymous'] ?? 0) === 1;
            $row['author_name'] = $commentIsAnonymous
                ? $this->anonymousAlias((int) $row['user_id'])
                : trim((string) $row['first_name'] . ' ' . (string) $row['last_name']);
            $row['avatar_color'] = $commentIsAnonymous
                ? 'violet'
                : (string) ($row['avatar_color'] ?? 'blue');

            $parentId = ! empty($row['parent_id']) ? (int) $row['parent_id'] : 0;
            if ($parentId > 0) {
                $repliesByComment[$parentId][] = $row;
            } else {
                $commentsByPost[(int) $row['post_id']][] = $row;
            }
        }

        foreach ($commentsByPost as &$comments) {
            foreach ($comments as &$c) {
                $c['replies'] = array_reverse($repliesByComment[(int) $c['id']] ?? []);
            }
            unset($c);
        }
        unset($comments);

        $allCommentIds = [];
        foreach ($commentsByPost as $comments) {
            foreach ($comments as $c) {
                $allCommentIds[] = (int) $c['id'];
                foreach ($c['replies'] as $reply) {
                    $allCommentIds[] = (int) $reply['id'];
                }
            }
        }

        $commentReactionBreakdown = [];
        $commentViewerReactions = [];
        if ($allCommentIds !== []) {
            $crRows = $db->table('comment_reactions')
                ->select('comment_id, reaction_type, COUNT(*) as total')
                ->whereIn('comment_id', $allCommentIds)
                ->groupBy('comment_id, reaction_type')
                ->get()->getResultArray();
            foreach ($crRows as $cr) {
                $commentReactionBreakdown[(int) $cr['comment_id']][(string) $cr['reaction_type']] = (int) $cr['total'];
            }

            if ($viewerId > 0) {
                $crViewerRows = $db->table('comment_reactions')
                    ->select('comment_id, reaction_type')
                    ->where('user_id', $viewerId)
                    ->whereIn('comment_id', $allCommentIds)
                    ->get()->getResultArray();
                foreach ($crViewerRows as $cr) {
                    $commentViewerReactions[(int) $cr['comment_id']] = (string) $cr['reaction_type'];
                }
            }
        }

        foreach ($commentsByPost as &$comments) {
            foreach ($comments as &$c) {
                $cid = (int) $c['id'];
                $c['reaction_breakdown'] = $commentReactionBreakdown[$cid] ?? [];
                $c['reaction_total'] = array_sum($commentReactionBreakdown[$cid] ?? []);
                $c['viewer_reaction'] = $commentViewerReactions[$cid] ?? null;
                foreach ($c['replies'] as &$reply) {
                    $rid = (int) $reply['id'];
                    $reply['reaction_breakdown'] = $commentReactionBreakdown[$rid] ?? [];
                    $reply['reaction_total'] = array_sum($commentReactionBreakdown[$rid] ?? []);
                    $reply['viewer_reaction'] = $commentViewerReactions[$rid] ?? null;
                }
                unset($reply);
            }
            unset($c);
        }
        unset($comments);

        foreach ($posts as &$post) {
            $feedbackId = (int) ($post['feedback_id'] ?? 0);
            if ($feedbackId > 0) {
                $post['feedback_image_path'] = $feedbackImageById[$feedbackId]
                    ?? $post['feedback_image_path']
                    ?? null;
            }
            $postUserId = (int) $post['user_id'];
            $postIsAnonymous = (int) ($post['is_anonymous'] ?? 0) === 1 || (int) ($post['profile_is_anonymous'] ?? 0) === 1;
            $post['author_name'] = $postIsAnonymous
                ? $this->anonymousAlias($postUserId)
                : trim((string) $post['first_name'] . ' ' . (string) $post['last_name']);
            $post['avatar_color'] = $postIsAnonymous
                ? 'violet'
                : (string) ($post['avatar_color'] ?? $this->avatarPalette[$postUserId % count($this->avatarPalette)]);
            $post['initials'] = $postIsAnonymous
                ? 'AN'
                : strtoupper(substr((string) $post['first_name'], 0, 1) . substr((string) $post['last_name'], 0, 1));
            $post['reaction_total'] = (int) ($reactionTotals[(int) $post['id']] ?? 0);
            $post['reaction_breakdown'] = $reactionBreakdown[(int) $post['id']] ?? [];
            $post['viewer_reaction'] = $viewerReactions[(int) $post['id']] ?? null;
            $post['comments'] = $commentsByPost[(int) $post['id']] ?? [];
            $postComments = $commentsByPost[(int) $post['id']] ?? [];
            $replyCount = 0;
            foreach ($postComments as $pc) {
                $replyCount += count($pc['replies'] ?? []);
            }
            $post['comment_total'] = count($postComments) + $replyCount;
            $post['profile_url'] = $postIsAnonymous ? '' : site_url('profile/' . $postUserId);
            $post['is_anonymous'] = $postIsAnonymous ? 1 : 0;
        }
        unset($post);

        return $posts;
    }
}
