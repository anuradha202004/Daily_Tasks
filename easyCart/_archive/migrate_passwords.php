<?php
/**
 * Migration Script: Hash Existing Plain Text Passwords
 * 
 * This script updates all existing user passwords from plain text to bcrypt hashes.
 * Run this ONCE after implementing password hashing.
 * 
 * Usage: php migrate_passwords.php
 */

require_once 'includes/db.php';

echo "=== Password Migration Script ===\n\n";

try {
    $pdo = getDBConnection();
    
    // Get all users
    $stmt = $pdo->query("SELECT id, email, password FROM users");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "No users found in database.\n";
        exit(0);
    }
    
    echo "Found " . count($users) . " users.\n";
    echo "Starting password migration...\n\n";
    
    $updated = 0;
    $skipped = 0;
    
    foreach ($users as $user) {
        // Check if password is already hashed (bcrypt hashes start with $2y$)
        if (substr($user['password'], 0, 4) === '$2y$') {
            echo "✓ User {$user['email']}: Already hashed (skipped)\n";
            $skipped++;
            continue;
        }
        
        // Hash the plain text password
        $hashedPassword = password_hash($user['password'], PASSWORD_BCRYPT);
        
        // Update in database
        $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->execute([
            ':password' => $hashedPassword,
            ':id' => $user['id']
        ]);
        
        echo "✓ User {$user['email']}: Password hashed successfully\n";
        $updated++;
    }
    
    echo "\n=== Migration Complete ===\n";
    echo "Updated: $updated users\n";
    echo "Skipped: $skipped users (already hashed)\n";
    echo "Total: " . count($users) . " users\n\n";
    
    echo "✅ All passwords have been migrated to secure bcrypt hashes!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
