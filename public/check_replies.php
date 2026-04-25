<?php
define('FCPATH', __DIR__ . '/');
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
define('ENVIRONMENT', $_SERVER['CI_ENVIRONMENT'] ?? 'development');
$db = \Config\Database::connect();

$replies = $db->table('feedback_replies')->get()->getResultArray();
foreach ($replies as $r) {
    echo "ID: {$r['id']} | FB_ID: {$r['feedback_id']} | User: {$r['admin_user_id']} | Time: {$r['created_at']} | Msg: {$r['message']}\n";
}
