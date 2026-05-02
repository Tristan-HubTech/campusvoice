<?php
/**
 * ALL URL ROUTES
 * Maps incoming URLs to the correct controllers and functions across the entire CampusVoice platform.
 * 
 * CONNECTS TO:
 * - All Controllers (Admin, Student, Social, API)
 * - Middleware Filters (adminauth, studentauth)
 */
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static function () { return redirect()->to(site_url('users')); });

$routes->get('feed', static function () { return redirect()->to(site_url('users')); });
$routes->post('feed/post', 'Student\\FeedController::createPost');
$routes->post('posts/(:num)/react', 'Student\\FeedController::react/$1');
$routes->post('posts/(:num)/comment', 'Student\\FeedController::comment/$1');
$routes->post('comments/(:num)/react', 'Student\\FeedController::commentReact/$1');
$routes->post('posts/(:num)/delete', 'Student\\FeedController::deletePost/$1');
$routes->post('posts/(:num)/share', 'Student\\FeedController::share/$1');
$routes->get('profile/(:num)', 'Student\\ProfileController::view/$1');
$routes->match(['get', 'post'], 'settings', 'Student\\SettingsController::index');
$routes->post('settings/anonymous', 'Student\\SettingsController::toggleAnonymous');
$routes->post('settings/send-password-otp', 'Student\\SettingsController::sendPasswordOtp');

$routes->match(['get', 'post'], 'admin/login', 'Admin\\AuthController::login');
$routes->get('admin/logout', 'Admin\\AuthController::logout', ['filter' => 'adminauth']);

$routes->group('admin', ['filter' => 'adminauth'], static function (RouteCollection $routes): void {
	$routes->get('/', 'Admin\\DashboardController::index');
	$routes->get('dashboard', 'Admin\\DashboardController::index');

	$routes->get('feedback', 'Admin\\FeedbackController::index');
	$routes->get('feedback/(:num)', 'Admin\\FeedbackController::show/$1');
	$routes->post('feedback/(:num)/status', 'Admin\\FeedbackController::updateStatus/$1');
	$routes->post('feedback/(:num)/reply', 'Admin\\FeedbackController::reply/$1');

	$routes->get('announcements', 'Admin\\AnnouncementController::index');
	$routes->get('announcements/create', 'Admin\\AnnouncementController::create');
	$routes->post('announcements', 'Admin\\AnnouncementController::store');
	$routes->get('announcements/(:num)/edit', 'Admin\\AnnouncementController::edit/$1');
	$routes->post('announcements/(:num)', 'Admin\\AnnouncementController::update/$1');
	$routes->post('announcements/(:num)/delete', 'Admin\\AnnouncementController::delete/$1');
	$routes->post('announcements/toggle-pin', 'Admin\\AnnouncementController::togglePin');

	$routes->get('student-activity', 'Admin\\StudentActivityController::index');

	$routes->get('tools/otp', 'Admin\\OtpToolController::index');
	$routes->post('activity/purge', 'Admin\\DashboardController::purgeActivity');

	// User Management
	$routes->post('users/(:num)/toggle-status', 'Admin\\UserManagementController::toggleStatus/$1');
	$routes->post('users/(:num)/send-reset', 'Admin\\UserManagementController::sendPasswordReset/$1');

	// Feedback approval workflow
	$routes->post('feedback/bulk-approve',   'Admin\\FeedbackController::bulkApprove');
	$routes->post('feedback/(:num)/approve', 'Admin\\FeedbackController::approve/$1');
	$routes->post('feedback/(:num)/reject',  'Admin\\FeedbackController::reject/$1');

	// Category Management
	$routes->post('categories', 'Admin\\CategoryController::store');
	$routes->post('categories/(:num)/update', 'Admin\\CategoryController::update/$1');
	$routes->post('categories/(:num)/delete', 'Admin\\CategoryController::delete/$1');
	$routes->post('categories/(:num)/toggle', 'Admin\\CategoryController::toggleStatus/$1');

	// Admin Account Management (RBAC)
	$routes->get('admins', 'Admin\\AdminUserController::index');
	$routes->get('admins/create', 'Admin\\AdminUserController::create');
	$routes->post('admins', 'Admin\\AdminUserController::store');
	$routes->get('admins/(:num)/edit', 'Admin\\AdminUserController::edit/$1');
	$routes->post('admins/(:num)/update', 'Admin\\AdminUserController::update/$1');
	$routes->post('admins/(:num)/toggle-status', 'Admin\\AdminUserController::toggleStatus/$1');
	$routes->post('admins/(:num)/delete', 'Admin\\AdminUserController::delete/$1');
	$routes->post('admins/(:num)/unlock', 'Admin\\AdminUserController::unlock/$1');

	// Role Management (RBAC)
	$routes->get('roles', 'Admin\\RoleController::index');
	$routes->get('roles/create', 'Admin\\RoleController::create');
	$routes->post('roles', 'Admin\\RoleController::store');
	$routes->get('roles/(:num)/edit', 'Admin\\RoleController::edit/$1');
	$routes->post('roles/(:num)/update', 'Admin\\RoleController::update/$1');
	$routes->post('roles/(:num)/delete', 'Admin\\RoleController::delete/$1');
});

// Student Portal
$routes->match(['get', 'post'], 'users/login', 'Student\\AuthController::login');
$routes->match(['get', 'post'], 'users/register', 'Student\\AuthController::register');
$routes->post('users/register/send-otp', 'Student\\AuthController::sendRegisterOtp');
$routes->match(['get', 'post'], 'users/forgot-password', 'Student\\AuthController::forgotPassword');
$routes->post('users/forgot-password/send-otp', 'Student\\AuthController::sendForgotOtp');
$routes->post('users/forgot-password/verify-otp', 'Student\\AuthController::verifyForgotOtp');
$routes->match(['get', 'post'], 'users/set-password/(:segment)', 'Student\\AuthController::adminResetPassword/$1');
$routes->get('users/logout', 'Student\\AuthController::logout');
$routes->get('users/deactivated', 'Student\\AuthController::deactivated');

// Legacy portal aliases (kept for old bookmarks/links)
$routes->match(['get', 'post'], 'portal/login', 'Student\\AuthController::login');
$routes->match(['get', 'post'], 'portal/register', 'Student\\AuthController::register');
$routes->post('portal/register/send-otp', 'Student\\AuthController::sendRegisterOtp');
$routes->match(['get', 'post'], 'portal/forgot-password', 'Student\\AuthController::forgotPassword');
$routes->post('portal/forgot-password/send-otp', 'Student\\AuthController::sendForgotOtp');
$routes->post('portal/forgot-password/verify-otp', 'Student\\AuthController::verifyForgotOtp');
$routes->get('portal/logout', 'Student\\AuthController::logout');

$routes->group('users', ['filter' => 'studentauth'], static function (RouteCollection $routes): void {
	$routes->get('/', 'Student\\PortalController::index');
	$routes->get('announcements', 'Student\\PortalController::announcements');
	$routes->get('feedback', 'Student\\PortalController::myFeedback');
	$routes->match(['get', 'post'], 'feedback/submit', 'Student\\PortalController::submitFeedback');
	$routes->get('feedback/(:num)', 'Student\\PortalController::viewFeedback/$1');
	$routes->post('feedback/(:num)/delete', 'Student\\PortalController::deleteFeedback/$1');
});


$routes->group('api', ['namespace' => 'App\\Controllers\\Api'], static function (RouteCollection $routes): void {
	$routes->post('auth/register', 'AuthController::register');
	$routes->post('auth/login', 'AuthController::login');
	$routes->post('auth/logout', 'AuthController::logout');
	$routes->get('auth/profile', 'AuthController::profile');
	$routes->post('auth/password/otp/request', 'OtpController::requestPasswordOtp');
	$routes->post('auth/password/otp/verify', 'OtpController::verifyPasswordOtp');
	$routes->post('auth/password/reset', 'OtpController::resetPassword');

	$routes->get('announcements', 'AnnouncementController::index');

	$routes->get('feedback/my', 'FeedbackController::myFeedback');
	$routes->post('feedback', 'FeedbackController::store');
	$routes->get('feedback/(:num)', 'FeedbackController::show/$1');

	$routes->group('admin', static function (RouteCollection $routes): void {
		$routes->get('feedback', 'FeedbackController::index');
		$routes->patch('feedback/(:num)/status', 'FeedbackController::updateStatus/$1');
		$routes->post('feedback/(:num)/reply', 'FeedbackController::reply/$1');

		$routes->get('announcements', 'AnnouncementController::adminIndex');
		$routes->post('announcements', 'AnnouncementController::store');
		$routes->put('announcements/(:num)', 'AnnouncementController::update/$1');
		$routes->delete('announcements/(:num)', 'AnnouncementController::delete/$1');
	});
});
