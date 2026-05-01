<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class StudentAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session     = session();
        $studentAuth = $session->get('student_auth');

        if (empty($studentAuth) || empty($studentAuth['id'])) {
            return redirect()->to(site_url('users/login'))->with('error', 'Please log in to continue.');
        }

        $user = (new UserModel())->select('id, is_active')->find((int) $studentAuth['id']);

        if ($user === null || (int) $user['is_active'] !== 1) {
            $session->remove('student_auth');
            $session->destroy();
            return redirect()->to(site_url('users/deactivated'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
