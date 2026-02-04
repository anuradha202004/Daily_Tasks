<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user = 'postgres';
$host = 'localhost';
$port = '5432';
$dbname = 'easycart';

$candidates = [
    '1234',
    '12345',
    'postgres',
    'admin',
    'root',
    'password',
    'pass',
    ''
];

echo "<h2>Database Password/Port Checker</h2>";

foreach ($candidates as $pass) {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $pass);
        echo "<div style='color:green; padding:10px; border:1px solid green; margin:5px;'>";
        echo "✅ <strong>SUCCESS!</strong> Password is: <code>" . ($pass === '' ? '(empty)' : $pass) . "</code>";
        echo "</div>";
        exit; // Stop on first success
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (strpos($msg, 'password authentication failed') !== false) {
             echo "<div style='color:red; margin:5px;'>❌ Password '$pass' failed.</div>";
        } else {
             echo "<div style='color:orange; margin:5px;'>⚠️ Error with '$pass': $msg</div>";
        }
    }
}

// Try Port 5433 just in case
$port = '5433';
echo "<h3>Trying Port 5433...</h3>";
foreach ($candidates as $pass) {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $pass);
        echo "<div style='color:green; padding:10px; border:1px solid green; margin:5px;'>";
        echo "✅ <strong>SUCCESS!</strong> Port: 5433, Password is: <code>" . ($pass === '' ? '(empty)' : $pass) . "</code>";
        echo "</div>";
        exit;
    } catch (PDOException $e) {
        // Ignore errors here
    }
}

echo "<hr><p>Could not find working password. Please check pgAdmin.</p>";
?>
