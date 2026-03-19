<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = session('admin_auth');

        if (! is_array($auth) || ! isset($auth['id'])) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Please login to continue.');
        }

        if (! in_array($auth['role'] ?? '', ['system_admin', 'admin'], true)) {
            session()->remove('admin_auth');
            return redirect()->to(site_url('admin/login'))->with('error', 'You do not have admin access.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
