<?php

namespace App\Controllers\Student;

use App\Libraries\FeedbackImageStorage;
use App\Models\AnnouncementModel;
use App\Models\FeedbackCategoryModel;
use App\Models\FeedbackModel;
use App\Models\FeedbackReplyModel;
use App\Models\SocialCommentModel;
use App\Models\SocialPostModel;
use App\Models\SocialProfileModel;
use App\Models\SocialReactionModel;
use App\Models\SocialShareModel;
use CodeIgniter\Controller;

class PortalController extends Controller
{
    private array $avatarPalette = ['blue', 'teal', 'coral', 'violet', 'amber', 'rose'];

    private function studentUser(): array
    {
        return (array) (session()->get('student_auth') ?? []);
    }

    private function anonViewData(int $userId): array
    {
        $profile = (new SocialProfileModel())->where('user_id', $userId)->first();
        $isAnon = (int) (($profile['is_anonymous'] ?? 0)) === 1;
        return [
            'isAnonymous' => $isAnon,
            'anonAlias'   => $isAnon ? $this->anonymousAlias($userId) : '',
        ];
    }

    public function index(): string
    {
        $studentUser = $this->studentUser();
        $userId = (int) ($studentUser['id'] ?? 0);

        $myFeedback = (new FeedbackModel())
            ->select('feedbacks.*, feedback_categories.name as category_name')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->where('feedbacks.user_id', $userId)
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll(5);

        $pinnedAnnouncements = (new AnnouncementModel())
            ->where('is_published', 1)
            ->where('pinned', 1)
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->findAll(1);

        $latestAnnouncements = (new AnnouncementModel())
            ->where('is_published', 1)
            ->where('pinned', 0)
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(1);

        $announcements = array_merge($pinnedAnnouncements, $latestAnnouncements);

        $profileModel = new SocialProfileModel();
        $profile = $profileModel->where('user_id', $userId)->first();
        $isAnon = (int) ($profile['is_anonymous'] ?? 0) === 1;

        return view('student/portal/home', array_merge([
            'title'              => 'My Portal',
            'studentUser'        => $studentUser,
            'currentUser'        => $studentUser,
            'posts'              => $this->buildCommunityPosts($userId),
            'myFeedback'         => $myFeedback,
            'announcements'      => $announcements,
            'showAnnouncements'  => true,
        ], $this->anonViewData($userId)));
    }

    public function myFeedback(): string
    {
        $studentUser = $this->studentUser();
        $userId = (int) ($studentUser['id'] ?? 0);

        $feedbackList = (new FeedbackModel())
            ->select('feedbacks.*, feedback_categories.name as category_name, (SELECT COUNT(*) FROM feedback_replies WHERE feedback_replies.feedback_id = feedbacks.id) as reply_count')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->where('feedbacks.user_id', $userId)
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll(200);

        return view('student/portal/my_feedback', array_merge([
            'title'        => 'My Voice',
            'studentUser'  => $studentUser,
            'feedbackList' => $feedbackList,
        ], $this->anonViewData($userId)));
    }

    public function submitFeedback()
    {
        $studentUser = $this->studentUser();
        $userId = (int) ($studentUser['id'] ?? 0);

        $categories = (new FeedbackCategoryModel())
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        if ($this->request->getMethod() === 'post') {
            $post = $this->request->getPost();

            $rules = [
                'category_id'  => 'required|is_natural_no_zero',
                'type'         => 'required|in_list[complaint,suggestion,praise]',
                'message'      => 'required|min_length[10]',
                'is_anonymous' => 'permit_empty|in_list[0,1]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()))->withInput();
            }

            $uploadedFile = $this->request->getFile('image');
            $imagePath    = null;
            try {
                $imagePath = FeedbackImageStorage::tryStore($uploadedFile);
            } catch (\RuntimeException $e) {
                return redirect()->back()->with('error', $e->getMessage())->withInput();
            }

            $isAnonymous = (int) ($post['is_anonymous'] ?? 0);

            $autoSubject = ucfirst(trim((string) $post['type'])) . ': ' . mb_substr(trim((string) $post['message']), 0, 80);

            $feedbackModel = new FeedbackModel();
            $feedbackModel->insert([
                'user_id'      => $userId,
                'category_id'  => (int) $post['category_id'],
                'type'         => $post['type'],
                'subject'      => $autoSubject,
                'message'      => trim((string) $post['message']),
                'image_path'   => $imagePath,
                'is_anonymous' => $isAnonymous,
                'status'       => 'new',
                'submitted_at' => date('Y-m-d H:i:s'),
            ]);

            // Also create a Community Feed post so feedback appears in the feed
            $feedbackType = ucfirst(trim((string) $post['type']));
            $feedbackMessage = trim((string) $post['message']);
            $feedBody = "{$feedbackType}\n\n{$feedbackMessage}";

            $feedbackId = $feedbackModel->getInsertID();

            (new SocialPostModel())->insert([
                'user_id'      => $userId,
                'feedback_id'  => $feedbackId,
                'body'         => $feedBody,
                'is_public'    => 1,
                'is_anonymous' => $isAnonymous,
            ]);

            return redirect()->to(site_url('users/feedback'))->with('success', 'Your feedback has been submitted successfully.');
        }

        return view('student/portal/submit', array_merge([
            'title'       => 'Share Feedback',
            'studentUser' => $studentUser,
            'categories'  => $categories,
        ], $this->anonViewData($userId)));
    }

    public function viewFeedback(int $id): string
    {
        $studentUser = $this->studentUser();
        $userId = (int) ($studentUser['id'] ?? 0);

        $feedback = (new FeedbackModel())
            ->select('feedbacks.*, feedback_categories.name as category_name')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->where('feedbacks.id', $id)
            ->where('feedbacks.user_id', $userId)
            ->first();

        if ($feedback === null) {
            return redirect()->to(site_url('users/feedback'))->with('error', 'Feedback not found or you do not have access.');
        }

        $replies = (new FeedbackReplyModel())
            ->select('feedback_replies.*, users.first_name, users.last_name')
            ->join('users', 'users.id = feedback_replies.admin_user_id', 'left')
            ->where('feedback_replies.feedback_id', $id)
            ->orderBy('feedback_replies.created_at', 'ASC')
            ->findAll();

        return view('student/portal/view_feedback', array_merge([
            'title'       => 'Feedback Detail',
            'studentUser' => $studentUser,
            'feedback'    => $feedback,
            'replies'     => $replies,
        ], $this->anonViewData($userId)));
    }

    public function deleteFeedback(int $id)
    {
        $studentUser = $this->studentUser();
        $userId = (int) ($studentUser['id'] ?? 0);

        $feedbackModel = new FeedbackModel();
        $feedback = $feedbackModel
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($feedback === null) {
            return redirect()->to(site_url('users/feedback'))->with('error', 'Feedback not found or you do not have access.');
        }

        FeedbackImageStorage::delete($feedback['image_path'] ?? null);

        $feedbackModel->delete((int) $feedback['id']);

        // Also delete the corresponding social post linked by feedback_id
        $socialPost = (new SocialPostModel())
            ->where('feedback_id', (int) $feedback['id'])
            ->first();
        if ($socialPost !== null) {
            (new SocialPostModel())->delete((int) $socialPost['id']);
        }

        return redirect()->to(site_url('users/feedback'))->with('success', 'Feedback deleted successfully.');
    }

    public function announcements(): string
    {
        $studentUser = $this->studentUser();

        $userId = (int) ($studentUser['id'] ?? 0);

        $announcements = (new AnnouncementModel())
            ->select('announcements.*, users.first_name AS author_first_name, users.last_name AS author_last_name')
            ->join('users', 'users.id = announcements.posted_by', 'left')
            ->where('is_published', 1)
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(100);

        return view('student/portal/announcements', array_merge([
            'title'         => 'Announcements',
            'studentUser'   => $studentUser,
            'announcements' => $announcements,
        ], $this->anonViewData($userId)));
    }

    private function anonymousAlias(int $userId): string
    {
        $number = str_pad((string) (($userId * 31 + 7) % 100), 2, '0', STR_PAD_LEFT);
        return 'Versace' . $number;
    }

    private function buildCommunityPosts(int $viewerId): array
    {
        $posts = (new SocialPostModel())
            ->select('social_posts.*, users.first_name, users.last_name, social_profiles.avatar_color, social_profiles.is_anonymous as profile_is_anonymous, feedbacks.status as feedback_status, feedbacks.type as feedback_type, feedbacks.image_path as feedback_image_path')
            ->join('users', 'users.id = social_posts.user_id', 'inner')
            ->join('social_profiles', 'social_profiles.user_id = users.id', 'left')
            ->join('feedbacks', 'feedbacks.id = social_posts.feedback_id', 'left')
            ->where('users.is_active', 1)
            ->where('social_posts.is_public', 1)
            ->orderBy('social_posts.created_at', 'DESC')
            ->findAll(12);

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
        $viewerReactionRows = $db->table('social_post_reactions')
            ->select('post_id, reaction_type')
            ->where('user_id', $viewerId)
            ->whereIn('post_id', $postIds)
            ->get()
            ->getResultArray();

        foreach ($viewerReactionRows as $row) {
            $viewerReactions[(int) $row['post_id']] = (string) $row['reaction_type'];
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

            $parentId = !empty($row['parent_id']) ? (int) $row['parent_id'] : 0;
            if ($parentId > 0) {
                $repliesByComment[$parentId][] = $row;
            } else {
                $commentsByPost[(int) $row['post_id']][] = $row;
            }
        }

        // Attach replies to their parent comments
        foreach ($commentsByPost as &$comments) {
            foreach ($comments as &$c) {
                $cid = (int) $c['id'];
                $c['replies'] = array_reverse($repliesByComment[$cid] ?? []);
            }
            unset($c);
        }
        unset($comments);

        // Fetch comment reaction counts and viewer reactions
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
            foreach ($postComments as $pc) { $replyCount += count($pc['replies'] ?? []); }
            $post['comment_total'] = count($postComments) + $replyCount;
            $post['profile_url'] = $postIsAnonymous ? '' : site_url('profile/' . $postUserId);
            $post['is_anonymous'] = $postIsAnonymous ? 1 : 0;
        }
        unset($post);

        return $posts;
    }
}
