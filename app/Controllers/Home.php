<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $studentAuth = session()->get('student_auth');
        if (! empty($studentAuth['id'])) {
            return redirect()->to(site_url('users'));
        }

        $adminAuth = session()->get('admin_auth');
        if (! empty($adminAuth['id'])) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return view('landing');
    }
}
