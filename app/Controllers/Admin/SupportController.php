<?php

namespace App\Controllers\Admin;

use App\Models\AdminUserModel;
use App\Models\SupportReplyModel;
use App\Models\SupportTicketModel;

class SupportController extends AdminBaseController
{
    public function index(): string
    {
        if ($guard = $this->requirePermission('support.view')) return $guard;

        $model    = new SupportTicketModel();
        $status   = (string) ($this->request->getGet('status')   ?? '');
        $category = (string) ($this->request->getGet('category') ?? '');
        $priority = (string) ($this->request->getGet('priority') ?? '');

        $query = $model
            ->select('support_tickets.*, users.first_name, users.last_name, users.email as user_email, admin_users.full_name as assigned_name')
            ->join('users', 'users.id = support_tickets.user_id', 'left')
            ->join('admin_users', 'admin_users.id = support_tickets.assigned_to', 'left')
            ->orderBy('support_tickets.created_at', 'DESC');

        if ($status !== '') {
            $query->where('support_tickets.status', $status);
        }
        if ($category !== '') {
            $query->where('support_tickets.category', $category);
        }
        if ($priority !== '') {
            $query->where('support_tickets.priority', $priority);
        }

        $tickets = $query->findAll();

        $stats = [
            'open'        => (new SupportTicketModel())->where('status', 'open')->countAllResults(),
            'in_progress' => (new SupportTicketModel())->where('status', 'in_progress')->countAllResults(),
            'resolved'    => (new SupportTicketModel())->where('status', 'resolved')->countAllResults(),
            'closed'      => (new SupportTicketModel())->where('status', 'closed')->countAllResults(),
        ];

        return view('admin/support/index', [
            'title'      => 'Support Tickets',
            'activeMenu' => 'support',
            'adminUser'  => $this->adminUser(),
            'tickets'    => $tickets,
            'stats'      => $stats,
            'filters'    => compact('status', 'category', 'priority'),
        ]);
    }

    public function show(int $id): string
    {
        if ($guard = $this->requirePermission('support.view')) return $guard;

        $ticket = (new SupportTicketModel())
            ->select('support_tickets.*, users.first_name, users.last_name, users.email as user_email, admin_users.full_name as assigned_name')
            ->join('users', 'users.id = support_tickets.user_id', 'left')
            ->join('admin_users', 'admin_users.id = support_tickets.assigned_to', 'left')
            ->where('support_tickets.id', $id)
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

        $admins = (new AdminUserModel())->where('is_active', 1)->findAll();

        return view('admin/support/show', [
            'title'      => 'Ticket #' . $id,
            'activeMenu' => 'support',
            'adminUser'  => $this->adminUser(),
            'ticket'     => $ticket,
            'replies'    => $replies,
            'admins'     => $admins,
        ]);
    }

    public function reply(int $id)
    {
        if ($guard = $this->requirePermission('support.reply')) return $guard;

        $rules = ['message' => 'required|min_length[2]'];
        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $model  = new SupportTicketModel();
        $ticket = $model->find($id);

        if ($ticket === null) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        (new SupportReplyModel())->insert([
            'ticket_id'     => $id,
            'admin_user_id' => (int) ($this->adminUser()['id'] ?? 0),
            'message'       => trim((string) $this->request->getPost('message')),
        ]);

        if ((string) $ticket['status'] === 'open') {
            $model->update($id, ['status' => 'in_progress']);
        }

        $this->logActivity(
            'support.reply_posted',
            'Replied to support ticket.',
            ['target_type' => 'support_ticket', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin/support/' . $id))->with('success', 'Reply posted.');
    }

    public function updateStatus(int $id)
    {
        if ($guard = $this->requirePermission('support.change_status')) return $guard;

        $status  = (string) ($this->request->getPost('status') ?? '');
        $allowed = ['open', 'in_progress', 'resolved', 'closed'];

        if (! in_array($status, $allowed, true)) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $model  = new SupportTicketModel();
        $ticket = $model->find($id);

        if ($ticket === null) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        if ($status === 'closed') {
            (new SupportReplyModel())->where('ticket_id', $id)->delete();
            $model->delete($id);

            $this->logActivity(
                'support.status_changed',
                'Closed and deleted support ticket.',
                ['target_type' => 'support_ticket', 'target_id' => $id, 'from' => $ticket['status'], 'to' => 'closed']
            );

            return redirect()->to(site_url('admin/support'))->with('success', 'Ticket closed and deleted.');
        }

        $model->update($id, ['status' => $status]);

        $this->logActivity(
            'support.status_changed',
            'Changed support ticket status.',
            ['target_type' => 'support_ticket', 'target_id' => $id, 'from' => $ticket['status'], 'to' => $status]
        );

        return redirect()->to(site_url('admin/support/' . $id))->with('success', 'Status updated.');
    }

    public function assign(int $id)
    {
        if ($guard = $this->requirePermission('support.change_status')) return $guard;

        $raw      = $this->request->getPost('assigned_to');
        $assignTo = ($raw !== '' && $raw !== null) ? (int) $raw : null;

        $model  = new SupportTicketModel();
        $ticket = $model->find($id);

        if ($ticket === null) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        $model->update($id, ['assigned_to' => $assignTo]);

        $this->logActivity(
            'support.assigned',
            'Assigned support ticket.',
            ['target_type' => 'support_ticket', 'target_id' => $id, 'assigned_to' => $assignTo]
        );

        return redirect()->to(site_url('admin/support/' . $id))->with('success', 'Ticket assigned.');
    }

    public function updatePriority(int $id)
    {
        if ($guard = $this->requirePermission('support.change_status')) return $guard;

        $priority = (string) ($this->request->getPost('priority') ?? '');
        $allowed  = ['low', 'normal', 'high', 'urgent'];

        if (! in_array($priority, $allowed, true)) {
            return redirect()->back()->with('error', 'Invalid priority.');
        }

        $model  = new SupportTicketModel();
        $ticket = $model->find($id);

        if ($ticket === null) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        $model->update($id, ['priority' => $priority]);

        $this->logActivity(
            'support.priority_changed',
            'Changed support ticket priority.',
            ['target_type' => 'support_ticket', 'target_id' => $id, 'from' => $ticket['priority'], 'to' => $priority]
        );

        return redirect()->to(site_url('admin/support/' . $id))->with('success', 'Priority updated.');
    }
}
