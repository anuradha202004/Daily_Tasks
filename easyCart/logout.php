<?php
session_start();

require_once 'auth.php';

// Logout user
logoutUser();

// Redirect to home page
header('Location: index.php');
exit;
?>
