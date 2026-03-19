<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // If already logged in as student, go to portal dashboard
        $studentAuth = session()->get('student_auth');
        if (! empty($studentAuth['id'])) {
            return redirect()->to(site_url('portal'));
        }

        // If already logged in as admin, go to admin dashboard
        $adminAuth = session()->get('admin_auth');
        if (! empty($adminAuth['id'])) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return view('landing');
    }
}
