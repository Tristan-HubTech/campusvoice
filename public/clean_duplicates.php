<?php
define('FCPATH', __DIR__ . '/');
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
define('ENVIRONMENT', $_SERVER['CI_ENVIRONMENT'] ?? 'development');
$db = \Config\Database::connect();

$replies = $db->table('feedback_replies')->orderBy('id', 'ASC')->get()->getResultArray();

$seen = [];
$deleted = 0;

foreach ($replies as $r) {
    $hash = $r['feedback_id'] . '|' . $r['admin_user_id'] . '|' . $r['message'];
    if (isset($seen[$hash])) {
        echo "Deleting duplicate reply ID: {$r['id']} (Matches ID: {$seen[$hash]})\n";
        $db->table('feedback_replies')->where('id', $r['id'])->delete();
        $deleted++;
    } else {
        $seen[$hash] = $r['id'];
    }
}

echo "Deleted {$deleted} duplicate replies.\n";
