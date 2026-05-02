<?php
/**
 * PORTAL CONTROLLER
 * Handles the main student dashboard pages (home, my feedback, announcements, and submitting posts).
 * 
 * CONNECTS TO:
 * - Views: student/portal/home, my_feedback, submit, view_feedback
 * - Models: FeedbackModel, AnnouncementModel, SocialPostModel, etc.
 */

namespace App\Controllers\Student;

use App\Libraries\FeedbackImageStorage;
use App\Models\AnnouncementModel;
use App\Models\FeedbackCategoryModel;
use App\Models\FeedbackModel;
use App\Models\FeedbackReplyModel;
use App\Models\SocialPostModel;
use App\Models\SocialProfileModel;

class PortalController extends StudentBaseController
{

    private function anonViewData(int $userId): array
    {
        $profile = (new SocialProfileModel())->where('user_id', $userId)->first();
        $isAnon = (int) (($profile['is_anonymous'] ?? 0)) === 1;
        return [
            'isAnonymous' => $isAnon,
            'anonAlias'   => $isAnon ? $this->anonymousAlias($userId) : '',
        ];
    }

    // HOME PAGE: Loads the latest announcements, user's feedback, and the community feed
    public function index(): string
    {
        $studentUser = $this->viewer();
        $userId = (int) ($studentUser['id'] ?? 0);

        $myFeedback = (new FeedbackModel())
            ->select('feedbacks.*, feedback_categories.name as category_name')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->where('feedbacks.user_id', $userId)
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll(5);

        $now = date('Y-m-d H:i:s');

        $pinnedAnnouncements = (new AnnouncementModel())
            ->where('is_published', 1)
            ->where('pinned', 1)
            ->groupStart()
                ->where('publish_at IS NULL')
                ->orWhere('publish_at <=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', $now)
            ->groupEnd()
            ->findAll(1);

        $latestAnnouncements = (new AnnouncementModel())
            ->where('is_published', 1)
            ->where('pinned', 0)
            ->groupStart()
                ->where('publish_at IS NULL')
                ->orWhere('publish_at <=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', $now)
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
            'posts'              => $this->buildPosts($userId, null, 12),
            'myFeedback'         => $myFeedback,
            'announcements'      => $announcements,
            'showAnnouncements'  => true,
        ], $this->anonViewData($userId)));
    }

    // MY FEEDBACK PAGE: Shows a history of all feedback submitted by the student
    public function myFeedback(): string
    {
        $studentUser = $this->viewer();
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

    // SUBMIT FEEDBACK PAGE: Handles rendering the form and processing the POST submission
    public function submitFeedback()
    {
        $studentUser = $this->viewer();
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
                'status'       => 'pending',
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

            return redirect()->to(site_url('users/feedback'))->with('success', 'Your feedback has been submitted and is pending admin review.');
        }

        return view('student/portal/submit', array_merge([
            'title'       => 'Share Feedback',
            'studentUser' => $studentUser,
            'categories'  => $categories,
        ], $this->anonViewData($userId)));
    }

    public function viewFeedback(int $id): string
    {
        $studentUser = $this->viewer();
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
        $studentUser = $this->viewer();
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
        $studentUser = $this->viewer();

        $userId = (int) ($studentUser['id'] ?? 0);

        $now = date('Y-m-d H:i:s');
        $announcements = (new AnnouncementModel())
            ->select('announcements.*, users.first_name AS author_first_name, users.last_name AS author_last_name')
            ->join('users', 'users.id = announcements.posted_by', 'left')
            ->where('is_published', 1)
            ->groupStart()
                ->where('publish_at IS NULL')
                ->orWhere('publish_at <=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', $now)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(100);

        return view('student/portal/announcements', array_merge([
            'title'         => 'Announcements',
            'studentUser'   => $studentUser,
            'announcements' => $announcements,
        ], $this->anonViewData($userId)));
    }

}
