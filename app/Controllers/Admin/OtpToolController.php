<?php

namespace App\Controllers\Admin;

class OtpToolController extends AdminBaseController
{
    public function index()
    {
        return redirect()->to(site_url('admin') . '#otp');
    }
}
