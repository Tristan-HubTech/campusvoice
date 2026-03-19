<?php

namespace App\Controllers;

class Home extends BaseController
{
<<<<<<< HEAD
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
=======
    public function index(): string
    {
        return view('welcome_message');
>>>>>>> 8f683a475b049c70f2e46bdc1a59b56eb5b110f1
    }
}
