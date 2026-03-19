<?php

namespace App\Controllers\Student;

use App\Models\AnnouncementModel;
use App\Models\FeedbackCategoryModel;
use App\Models\FeedbackModel;
use App\Models\FeedbackReplyModel;
use CodeIgniter\Controller;

class PortalController extends Controller
{
    private function studentUser(): array
    {
        return (array) (session()->get('student_auth') ?? []);
    }

<<<<<<< HEAD
    public function index()
    {
        return redirect()->to(site_url('feed'));
=======
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

        $announcements = (new AnnouncementModel())
            ->where('is_published', 1)
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        $stats = [
            'total'    => (new FeedbackModel())->where('user_id', $userId)->countAllResults(),
            'new'      => (new FeedbackModel())->where('user_id', $userId)->where('status', 'new')->countAllResults(),
            'reviewed' => (new FeedbackModel())->where('user_id', $userId)->where('status', 'reviewed')->countAllResults(),
            'resolved' => (new FeedbackModel())->where('user_id', $userId)->where('status', 'resolved')->countAllResults(),
        ];

        return view('student/portal/home', [
            'title'         => 'My Portal',
            'studentUser'   => $studentUser,
            'myFeedback'    => $myFeedback,
            'announcements' => $announcements,
            'stats'         => $stats,
        ]);
>>>>>>> 8f683a475b049c70f2e46bdc1a59b56eb5b110f1
    }

    public function myFeedback(): string
    {
        $studentUser = $this->studentUser();
        $userId = (int) ($studentUser['id'] ?? 0);

        $feedbackList = (new FeedbackModel())
            ->select('feedbacks.*, feedback_categories.name as category_name')
            ->join('feedback_categories', 'feedback_categories.id = feedbacks.category_id', 'left')
            ->where('feedbacks.user_id', $userId)
            ->orderBy('feedbacks.created_at', 'DESC')
            ->findAll(200);

        return view('student/portal/my_feedback', [
            'title'        => 'My Submissions',
            'studentUser'  => $studentUser,
            'feedbackList' => $feedbackList,
        ]);
    }

    public function submitFeedback()
    {
        $studentUser = $this->studentUser();
        $userId = (int) ($studentUser['id'] ?? 0);

        $categories = (new FeedbackCategoryModel())
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

<<<<<<< HEAD
        if (strtolower($this->request->getMethod()) === 'post') {
=======
        if ($this->request->getMethod() === 'POST') {
>>>>>>> 8f683a475b049c70f2e46bdc1a59b56eb5b110f1
            $post = $this->request->getPost();

            $rules = [
                'category_id'  => 'required|is_natural_no_zero',
                'type'         => 'required|in_list[complaint,suggestion,praise]',
                'subject'      => 'required|min_length[5]|max_length[180]',
                'message'      => 'required|min_length[10]',
                'is_anonymous' => 'permit_empty|in_list[0,1]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()))->withInput();
            }

            $isAnonymous = (int) ($post['is_anonymous'] ?? 0);

            (new FeedbackModel())->insert([
                'user_id'      => $userId,
                'category_id'  => (int) $post['category_id'],
                'type'         => $post['type'],
                'subject'      => trim((string) $post['subject']),
                'message'      => trim((string) $post['message']),
                'is_anonymous' => $isAnonymous,
                'status'       => 'new',
                'submitted_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to(site_url('portal/feedback'))->with('success', 'Your feedback has been submitted successfully.');
        }

        return view('student/portal/submit', [
            'title'       => 'Submit Feedback',
            'studentUser' => $studentUser,
            'categories'  => $categories,
        ]);
    }

<<<<<<< HEAD
    public function viewFeedback(int $id)
=======
    public function viewFeedback(int $id): string
>>>>>>> 8f683a475b049c70f2e46bdc1a59b56eb5b110f1
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
            return redirect()->to(site_url('portal/feedback'))->with('error', 'Feedback not found or you do not have access.');
        }

        $replies = (new FeedbackReplyModel())
            ->select('feedback_replies.*, users.first_name, users.last_name')
            ->join('users', 'users.id = feedback_replies.admin_user_id', 'left')
            ->where('feedback_replies.feedback_id', $id)
            ->orderBy('feedback_replies.created_at', 'ASC')
            ->findAll();

        return view('student/portal/view_feedback', [
            'title'       => 'Feedback Detail',
            'studentUser' => $studentUser,
            'feedback'    => $feedback,
            'replies'     => $replies,
        ]);
    }

    public function announcements(): string
    {
        $studentUser = $this->studentUser();

        $announcements = (new AnnouncementModel())
            ->where('is_published', 1)
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(100);

        return view('student/portal/announcements', [
            'title'         => 'Announcements',
            'studentUser'   => $studentUser,
            'announcements' => $announcements,
        ]);
    }
}
