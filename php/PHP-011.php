<?php
// ===============================
// Task: PHP-011
// Topic: Cookies
// ===============================

// Setting cookie (must be before HTML output)
setcookie("user_preference", "dark_mode", time() + 3600, "/");

// Extra practical: another cookie
setcookie("font_size", "medium", time() + 3600, "/");

echo "<h3>Cookie Practice</h3>";

// Checking and retrieving cookie
if (isset($_COOKIE['user_preference'])) {
    echo "User Preference: " . $_COOKIE['user_preference'] . "<br>";
} else {
    echo "User Preference cookie not set.<br>";
}

// Extra practical usage
if (isset($_COOKIE['font_size'])) {
    echo "Font Size: " . $_COOKIE['font_size'];
}
?>
