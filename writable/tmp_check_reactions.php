<?php
$db = new mysqli('localhost', 'root', '', 'campusvoice');
echo "Social posts:\n";
$result = $db->query('SELECT id, user_id, LEFT(body, 80) as body_preview, deleted_at FROM social_posts ORDER BY id DESC LIMIT 20');
while ($row = $result->fetch_assoc()) {
    $del = $row['deleted_at'] ? " [DELETED]" : "";
    echo "id={$row['id']} user={$row['user_id']}{$del} body=[{$row['body_preview']}]\n";
}
echo "\nFeedback records:\n";
$result = $db->query('SELECT id, user_id, type, LEFT(message, 50) as msg, deleted_at FROM feedback ORDER BY id DESC LIMIT 20');
while ($row = $result->fetch_assoc()) {
    $del = $row['deleted_at'] ? " [DELETED]" : "";
    echo "id={$row['id']} user={$row['user_id']} type={$row['type']}{$del} msg=[{$row['msg']}]\n";
}
$db->close();
