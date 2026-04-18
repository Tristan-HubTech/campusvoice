<?php
// Usage: php writable/create_admin_user.php

use App\Models\UserModel;
use CodeIgniter\Config\Factories;

require_once __DIR__ . '/../vendor/autoload.php';

// --- CONFIGURE ---
$firstName = 'Admin';
$lastName = 'User';
$email = 'admin' . rand(1000,9999) . '@campusvoice.local';
$password = bin2hex(random_bytes(6)); // 12-char random password
$roleId = 1; // 1 = system_admin, 2 = admin

// --- HASH PASSWORD ---
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// --- CREATE USER ---
$userModel = Factories::models('UserModel');
$userId = $userModel->insert([
    'role_id' => $roleId,
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'password_hash' => $passwordHash,
    'is_active' => 1,
]);

if ($userId) {
    echo "SUCCESS!\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "Role: system_admin\n";
} else {
    echo "FAILED to create admin user.\n";
    print_r($userModel->errors());
}
