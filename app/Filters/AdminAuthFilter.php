<?php

namespace App\Filters;

use App\Models\AdminUserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = session('admin_auth');

        if (! is_array($auth) || empty($auth['id'])) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Please login to continue.');
        }

        // Re-verify the admin account is still active on every request.
        $user = (new AdminUserModel())->select('id, is_active')->find((int) $auth['id']);

        if ($user === null || (int) $user['is_active'] !== 1) {
            session()->remove('admin_auth');
            return redirect()->to(site_url('admin/login'))->with('error', 'Your admin account is no longer active.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
