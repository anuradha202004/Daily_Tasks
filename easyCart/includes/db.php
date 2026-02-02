<?php
// Database Configuration
$host = 'localhost';
$port = '5432';          // Default PostgreSQL port
$dbname = 'easycart';    // Your database name
$user = 'postgres';      // Your database username
$password = '1234';      // Correct password verified by check_config.php

/**
 * Get the PostgreSQL database connection
 * @return PDO
 */
function getDBConnection() {
    global $host, $port, $dbname, $user, $password;
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("<h3>Database Connection Failed:</h3> " . $e->getMessage());
    }
}
?>
