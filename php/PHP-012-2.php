<?php
// ===============================
// Task: PHP-012 (Page 2)
// ===============================

session_start();

if (isset($_SESSION['username'])) {
    echo "Welcome " . $_SESSION['username'] . "<br>";
    echo "Login Time: " . $_SESSION['login_time'] . "<br>";
} else {
    echo "No active session.<br>";
}
?>

<form method="post">
    <button name="logout">Logout</button>
</form>

<?php
// Logout logic
if (isset($_POST['logout'])) {
    session_destroy();
    echo "Session destroyed. Logged out successfully.";
}
?>
