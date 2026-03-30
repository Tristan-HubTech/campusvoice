<?php
$db = new PDO('mysql:host=localhost;dbname=campusvoice', 'root', '');
$stmt = $db->query('DESCRIBE social_profiles');
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
echo "Columns: " . implode(', ', $cols) . "\n";
$stmt2 = $db->query('SELECT id, user_id, is_anonymous FROM social_profiles LIMIT 5');
$rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "id={$r['id']} user_id={$r['user_id']} is_anonymous={$r['is_anonymous']}\n";
}
