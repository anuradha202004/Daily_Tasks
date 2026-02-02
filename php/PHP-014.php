<?php
// ===============================
// Task: PHP-014
// Topic: Using Namespaces
// ===============================

require 'PHP-013-1.php';
require 'PHP-013-2.php';

use Library\Database\Connection as DBConnection;
use Library\API\Connection as APIConnection;

// Creating objects
$db = new DBConnection();
$api = new APIConnection();

// Calling methods
$db->connect();
$api->connect();
?>
