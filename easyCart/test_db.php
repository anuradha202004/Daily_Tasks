<?php
// Test File for Database Connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php';

echo '<div style="font-family: Arial, sans-serif; padding: 20px; text-align: center;">';
echo '<h2>PostgreSQL Connection Test</h2>';

try {
    $pdo = getDBConnection();
    echo '<div style="color: green; font-size: 1.2em; border: 2px solid green; padding: 20px; display: inline-block; border-radius: 10px;">';
    echo '✅ <strong>SUCCESS!</strong><br><br>';
    echo 'Connected to database: <strong>' . htmlspecialchars($dbname) . '</strong><br>';
    echo 'Host: <strong>' . htmlspecialchars($host) . '</strong>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div style="color: red; border: 2px solid red; padding: 20px; display: inline-block; border-radius: 10px;">';
    echo '❌ <strong>ERROR</strong><br><br>';
    echo $e->getMessage();
    echo '</div>';
}

echo '<br><br><p>If you see an error, check your <code>includes/db.php</code> file and ensure your PostgreSQL credentials are correct.</p>';
echo '</div>';
?>
