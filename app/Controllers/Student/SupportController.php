<?php

namespace App\Controllers\Student;

use App\Models\SupportTicketModel;
use App\Models\SupportReplyModel;

class SupportController extends StudentBaseController
{
    public function index(): string
    {
        $viewer = $this->viewer();
        $userId = (int) ($viewer['id'] ?? 0);

        $tickets = (new SupportTicketModel())
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('student/support/index', [
            'title'       => 'My Support Tickets',
            'studentUser' => $viewer,
            'tickets'     => $tickets,
        ]);
    }

    public function create(): string
    {
        return view('student/support/create', [
            'title'       => 'Submit Support Ticket',
            'navTitle'    => 'My Support Tickets',
            'studentUser' => $this->viewer(),
        ]);
    }

    public function store()
    {
        $rules = [
            'subject'  => 'required|min_length[5]|max_length[200]',
            'category' => 'required|in_list[technical,account,general,other]',
            'message'  => 'required|min_length[10]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $viewer   = $this->viewer();
        $userId   = (int) ($viewer['id'] ?? 0);
        $model    = new SupportTicketModel();

        $ticketId = $model->insert([
            'user_id'  => $userId,
            'subject'  => trim((string) $this->request->getPost('subject')),
            'category' => (string) $this->request->getPost('category'),
            'message'  => trim((string) $this->request->getPost('message')),
            'status'   => 'open',
            'priority' => 'normal',
        ]);

        $this->logStudentActivity(
            'support.ticket_created',
            'Submitted a support ticket.',
            'support_ticket',
            (int) $ticketId
        );

        return redirect()->to(site_url('users/support/' . $ticketId))
            ->with('success', 'Your support ticket has been submitted. We will get back to you shortly.');
    }

    public function show(int $id): string
    {
        $viewer = $this->viewer();
        $userId = (int) ($viewer['id'] ?? 0);

        $ticket = (new SupportTicketModel())
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($ticket === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ticket not found.');
        }

        $replies = (new SupportReplyModel())
            ->select('support_replies.*, users.first_name, users.last_name, admin_users.full_name as admin_name')
            ->join('users', 'users.id = support_replies.user_id', 'left')
            ->join('admin_users', 'admin_users.id = support_replies.admin_user_id', 'left')
            ->where('ticket_id', $id)
            ->orderBy('support_replies.created_at', 'ASC')
            ->findAll();

        return view('student/support/show', [
            'title'       => 'Ticket: ' . esc($ticket['subject']),
            'navTitle'    => 'My Support Tickets',
            'studentUser' => $viewer,
            'ticket'      => $ticket,
            'replies'     => $replies,
        ]);
    }

    public function close(int $id)
    {
        $viewer = $this->viewer();
        $userId = (int) ($viewer['id'] ?? 0);
        $model  = new SupportTicketModel();

        $ticket = $model->where('id', $id)->where('user_id', $userId)->first();

        if ($ticket === null) {
            return redirect()->to(site_url('users/support'))->with('error', 'Ticket not found.');
        }

        if (in_array((string) $ticket['status'], ['closed', 'resolved'], true)) {
            return redirect()->to(site_url('users/support/' . $id))->with('error', 'This ticket is already closed.');
        }

        (new SupportReplyModel())->where('ticket_id', $id)->delete();
        $model->delete($id);

        $this->logStudentActivity(
            'support.ticket_closed',
            'Closed a support ticket.',
            'support_ticket',
            $id
        );

        return redirect()->to(site_url('users/support'))->with('success', 'Ticket closed and removed.');
    }
}
