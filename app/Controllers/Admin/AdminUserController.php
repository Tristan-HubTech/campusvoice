<?php

namespace App\Controllers\Admin;

use App\Models\AdminRoleModel;
use App\Models\AdminUserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class AdminUserController extends AdminBaseController
{
    public function index()
    {
        if ($guard = $this->requirePermission('admin.view')) {
            return $guard;
        }

        $users = (new AdminUserModel())
            ->select('admin_users.*, admin_roles.name as role_name')
            ->join('admin_roles', 'admin_roles.id = admin_users.role_id', 'left')
            ->orderBy('admin_users.created_at', 'DESC')
            ->findAll();

        return view('admin/admins/index', [
            'title'      => 'Admin Accounts',
            'activeMenu' => 'admins',
            'adminUser'  => $this->adminUser(),
            'users'      => $users,
        ]);
    }

    public function create()
    {
        if ($guard = $this->requirePermission('admin.create')) {
            return $guard;
        }

        return view('admin/admins/create', [
            'title'      => 'Add Admin Account',
            'activeMenu' => 'admins',
            'adminUser'  => $this->adminUser(),
            'roles'      => (new AdminRoleModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        if ($guard = $this->requirePermission('admin.create')) {
            return $guard;
        }

        $payload = $this->request->getPost();

        $rules = [
            'full_name'        => 'required|min_length[2]|max_length[150]',
            'email'            => 'required|valid_email|max_length[150]|is_unique[admin_users.email]',
            'role_id'          => 'required|is_natural_no_zero',
            'password'         => 'required|min_length[8]|max_length[100]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $role = (new AdminRoleModel())->find((int) $payload['role_id']);
        if ($role === null) {
            return redirect()->back()->withInput()->with('error', 'Selected role does not exist.');
        }

        (new AdminUserModel())->insert([
            'role_id'       => (int) $payload['role_id'],
            'full_name'     => trim((string) $payload['full_name']),
            'email'         => strtolower(trim((string) $payload['email'])),
            'password_hash' => password_hash((string) $payload['password'], PASSWORD_DEFAULT),
            'is_active'     => 1,
        ]);

        $this->logActivity(
            'admin.created',
            'Created admin account: ' . $payload['email'],
            ['target_type' => 'admin_user', 'role' => $role['name']]
        );

        return redirect()->to(site_url('admin/admins'))->with('success', 'Admin account created successfully.');
    }

    public function edit(int $id)
    {
        if ($guard = $this->requirePermission('admin.edit')) {
            return $guard;
        }

        $user = (new AdminUserModel())->find($id);
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('Admin account not found.');
        }

        return view('admin/admins/edit', [
            'title'      => 'Edit Admin Account',
            'activeMenu' => 'admins',
            'adminUser'  => $this->adminUser(),
            'editUser'   => $user,
            'roles'      => (new AdminRoleModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        if ($guard = $this->requirePermission('admin.edit')) {
            return $guard;
        }

        $me      = $this->adminUser();
        $payload = $this->request->getPost();

        $adminUserModel = new AdminUserModel();
        $user = $adminUserModel->find($id);
        if ($user === null) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'Admin account not found.');
        }

        $isSelf = (int) $me['id'] === $id;

        $rules = [
            'full_name' => 'required|min_length[2]|max_length[150]',
            'email'     => 'required|valid_email|max_length[150]|is_unique[admin_users.email,id,' . $id . ']',
        ];

        $canChangeRole = ! $isSelf && $this->hasPermission('admin.assign_roles');
        if ($canChangeRole) {
            $rules['role_id'] = 'required|is_natural_no_zero';
        }

        if (! empty($payload['password'])) {
            $rules['password']         = 'min_length[8]|max_length[100]';
            $rules['password_confirm'] = 'required|matches[password]';
        }

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $data = [
            'full_name' => trim((string) $payload['full_name']),
            'email'     => strtolower(trim((string) $payload['email'])),
        ];

        if ($canChangeRole) {
            $role = (new AdminRoleModel())->find((int) $payload['role_id']);
            if ($role === null) {
                return redirect()->back()->withInput()->with('error', 'Selected role does not exist.');
            }
            $data['role_id'] = (int) $payload['role_id'];
        }

        if (! empty($payload['password'])) {
            $data['password_hash'] = password_hash((string) $payload['password'], PASSWORD_DEFAULT);
        }

        $adminUserModel->update($id, $data);

        $this->logActivity(
            'admin.updated',
            'Updated admin account: ' . $user['email'],
            ['target_type' => 'admin_user', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin/admins'))->with('success', 'Admin account updated.');
    }

    public function toggleStatus(int $id)
    {
        if ($guard = $this->requirePermission('admin.edit')) {
            return $guard;
        }

        $me = $this->adminUser();
        if ((int) $me['id'] === $id) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'You cannot deactivate your own account.');
        }

        $adminUserModel = new AdminUserModel();
        $user = $adminUserModel->find($id);
        if ($user === null) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'Admin account not found.');
        }

        $newStatus = (int) $user['is_active'] === 1 ? 0 : 1;
        $adminUserModel->update($id, ['is_active' => $newStatus, 'login_attempts' => 0, 'locked_until' => null]);

        $verb = $newStatus === 1 ? 'activated' : 'deactivated';
        $this->logActivity(
            'admin.' . $verb,
            ucfirst($verb) . ' admin account: ' . $user['email'],
            ['target_type' => 'admin_user', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin/admins'))->with('success', 'Admin account ' . $verb . '.');
    }

    public function delete(int $id)
    {
        if ($guard = $this->requirePermission('admin.delete')) {
            return $guard;
        }

        $me = $this->adminUser();
        if ((int) $me['id'] === $id) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'You cannot delete your own account.');
        }

        $adminUserModel = new AdminUserModel();
        $user = $adminUserModel->find($id);
        if ($user === null) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'Admin account not found.');
        }

        $adminUserModel->delete($id);

        $this->logActivity(
            'admin.deleted',
            'Deleted admin account: ' . $user['email'],
            ['target_type' => 'admin_user', 'target_id' => $id, 'email' => $user['email']]
        );

        return redirect()->to(site_url('admin/admins'))->with('success', 'Admin account deleted.');
    }

    public function unlock(int $id)
    {
        if ($guard = $this->requirePermission('admin.edit')) {
            return $guard;
        }

        $user = (new AdminUserModel())->find($id);
        if ($user === null) {
            return redirect()->to(site_url('admin/admins'))->with('error', 'Admin account not found.');
        }

        (new AdminUserModel())->unlockAccount($id);

        $this->logActivity(
            'admin.unlocked',
            'Manually unlocked admin account: ' . $user['email'],
            ['target_type' => 'admin_user', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin/admins'))->with('success', 'Account unlocked.');
    }
}
