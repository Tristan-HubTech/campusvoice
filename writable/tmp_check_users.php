<?php
$db = new mysqli('localhost', 'root', '', 'campusvoice');
if ($db->connect_error) { echo 'ERR:' . $db->connect_error; exit; }
$r = $db->query('SELECT id, email, first_name, is_active FROM users LIMIT 5');
while ($row = $r->fetch_assoc()) {
    echo $row['id'] . ' | ' . $row['email'] . ' | ' . $row['first_name'] . ' | active=' . $row['is_active'] . PHP_EOL;
}
$db->close();
