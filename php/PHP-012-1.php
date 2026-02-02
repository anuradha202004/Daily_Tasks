<?php
// ===============================
// Task: PHP-012 (Page 1)
// Topic: Sessions
// ===============================

session_start();

// Setting session value
$_SESSION['username'] = 'InternName';
$_SESSION['login_time'] = date("H:i:s");

echo "Session started successfully.<br>";
echo "<a href='PHP-012-2.php'>Go to Dashboard</a>";
?>
