<?php

namespace App\Controllers;

/**
 * Stub — all methods moved to Student\FeedController, Student\ProfileController,
 * and Student\SettingsController. Routes updated in app/Config/Routes.php.
 */
class SocialController extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('users'));
    }
}
