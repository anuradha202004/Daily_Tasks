<?php
// Load Application Bootstrap
require_once 'app/bootstrap.php';

// Logout user
logoutUser();

// Redirect to home page
header('Location: /');
exit;
?>
