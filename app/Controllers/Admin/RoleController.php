<?php

namespace App\Controllers\Admin;

use App\Models\AdminRoleModel;
use App\Models\AdminUserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class RoleController extends AdminBaseController
{
    public function index()
    {
        if ($guard = $this->requirePermission('roles.view')) {
            return $guard;
        }

        $db = db_connect();

        $roles = $db->table('admin_roles ar')
            ->select('ar.*, COUNT(au.id) as user_count')
            ->join('admin_users au', 'au.role_id = ar.id', 'left')
            ->groupBy('ar.id')
            ->orderBy('ar.created_at', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/roles/index', [
            'title'            => 'Roles & Permissions',
            'activeMenu'       => 'roles',
            'adminUser'        => $this->adminUser(),
            'roles'            => $roles,
            'permissionGroups' => AdminRoleModel::PERMISSION_GROUPS,
        ]);
    }

    public function create()
    {
        if ($guard = $this->requirePermission('roles.create')) {
            return $guard;
        }

        return view('admin/roles/create', [
            'title'            => 'Create Role',
            'activeMenu'       => 'roles',
            'adminUser'        => $this->adminUser(),
            'permissionGroups' => AdminRoleModel::PERMISSION_GROUPS,
        ]);
    }

    public function store()
    {
        if ($guard = $this->requirePermission('roles.create')) {
            return $guard;
        }

        $payload = $this->request->getPost();

        $rules = [
            'name'        => 'required|min_length[2]|max_length[80]|is_unique[admin_roles.name]',
            'description' => 'permit_empty|max_length[255]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $selected = (array) ($payload['permissions'] ?? []);
        $map      = (new AdminRoleModel())->buildPermissionsMap($selected);

        (new AdminRoleModel())->insert([
            'name'        => trim((string) $payload['name']),
            'description' => trim((string) ($payload['description'] ?? '')),
            'permissions' => json_encode($map),
            'is_system'   => 0,
        ]);

        $this->logActivity('role.created', 'Created role: ' . $payload['name'], ['target_type' => 'admin_role']);

        return redirect()->to(site_url('admin/roles'))->with('success', 'Role created successfully.');
    }

    public function edit(int $id)
    {
        if ($guard = $this->requirePermission('roles.edit')) {
            return $guard;
        }

        $roleModel = new AdminRoleModel();
        $role = $roleModel->find($id);
        if ($role === null) {
            throw PageNotFoundException::forPageNotFound('Role not found.');
        }

        return view('admin/roles/edit', [
            'title'              => 'Edit Role: ' . esc($role['name']),
            'activeMenu'         => 'roles',
            'adminUser'          => $this->adminUser(),
            'role'               => $role,
            'permissionGroups'   => AdminRoleModel::PERMISSION_GROUPS,
            'currentPermissions' => $roleModel->getPermissions($role),
        ]);
    }

    public function update(int $id)
    {
        if ($guard = $this->requirePermission('roles.edit')) {
            return $guard;
        }

        $roleModel = new AdminRoleModel();
        $role = $roleModel->find($id);
        if ($role === null) {
            return redirect()->to(site_url('admin/roles'))->with('error', 'Role not found.');
        }

        $payload = $this->request->getPost();

        $rules = [
            'description' => 'permit_empty|max_length[255]',
        ];

        // System roles cannot be renamed.
        if (! (bool) $role['is_system']) {
            $rules['name'] = 'required|min_length[2]|max_length[80]|is_unique[admin_roles.name,id,' . $id . ']';
        }

        if (! $this->validateData($payload, $rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $selected = (array) ($payload['permissions'] ?? []);
        $map      = $roleModel->buildPermissionsMap($selected);

        $data = [
            'description' => trim((string) ($payload['description'] ?? '')),
            'permissions' => json_encode($map),
        ];

        if (! (bool) $role['is_system']) {
            $data['name'] = trim((string) $payload['name']);
        }

        $roleModel->update($id, $data);

        $this->logActivity(
            'role.updated',
            'Updated role: ' . $role['name'],
            ['target_type' => 'admin_role', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin/roles'))->with('success', 'Role updated successfully.');
    }

    public function delete(int $id)
    {
        if ($guard = $this->requirePermission('roles.delete')) {
            return $guard;
        }

        $roleModel = new AdminRoleModel();
        $role = $roleModel->find($id);
        if ($role === null) {
            return redirect()->to(site_url('admin/roles'))->with('error', 'Role not found.');
        }

        if ((bool) $role['is_system']) {
            return redirect()->to(site_url('admin/roles'))->with('error', 'System roles cannot be deleted.');
        }

        $userCount = (new AdminUserModel())->where('role_id', $id)->countAllResults();
        if ($userCount > 0) {
            return redirect()->to(site_url('admin/roles'))->with(
                'error',
                "Cannot delete role \"{$role['name']}\" — {$userCount} admin account(s) are assigned to it."
            );
        }

        $roleModel->delete($id);

        $this->logActivity(
            'role.deleted',
            'Deleted role: ' . $role['name'],
            ['target_type' => 'admin_role', 'target_id' => $id, 'role_name' => $role['name']]
        );

        return redirect()->to(site_url('admin/roles'))->with('success', 'Role deleted.');
    }
}
