<?php
require __DIR__ . '/../system/bootstrap.php';
$db = \Config\Database::connect();
echo "comment_reactions table:\n";
$rows = $db->table('comment_reactions')->get()->getResultArray();
print_r($rows);
echo "Total rows: " . count($rows) . "\n";
