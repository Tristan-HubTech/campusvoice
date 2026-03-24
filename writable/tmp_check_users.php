<?php
$mysqli = new mysqli('localhost', 'root', '', 'campusvoice');
if ($mysqli->connect_error) {
    fwrite(STDERR, 'DB connect error: ' . $mysqli->connect_error . PHP_EOL);
    exit(1);
}
$sql = "SELECT id, email, role_id, is_active, CHAR_LENGTH(password_hash) AS hash_len, deleted_at, created_at FROM users ORDER BY id DESC LIMIT 20";
$result = $mysqli->query($sql);
if ($result === false) {
    fwrite(STDERR, 'Query error: ' . $mysqli->error . PHP_EOL);
    exit(1);
}
while ($row = $result->fetch_assoc()) {
    echo implode(' | ', [
        $row['id'],
        $row['email'],
        $row['role_id'],
        $row['is_active'],
        $row['hash_len'],
        (string) ($row['deleted_at'] ?? ''),
        $row['created_at'],
    ]) . PHP_EOL;
}
