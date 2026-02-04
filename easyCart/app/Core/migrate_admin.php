<?php
require_once __DIR__ . '/../Core/db.php';

try {
    $pdo = getDBConnection();
    
    // Add is_admin column to users table if it doesn't exist
    $pdo->exec("
        ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS is_admin BOOLEAN DEFAULT FALSE
    ");
    echo "✓ Added is_admin column to users table\n";
    
    // Create a default admin user (email: admin@easycart.com, password: admin123)
    $adminEmail = 'admin@easycart.com';
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $adminName = 'Admin User';
    
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, name, is_admin, created_at)
        VALUES (?, ?, ?, TRUE, NOW())
        ON CONFLICT (email) DO UPDATE SET is_admin = TRUE
    ");
    $stmt->execute([$adminEmail, $adminPassword, $adminName]);
    
    echo "✓ Admin user created/updated\n";
    echo "  Email: admin@easycart.com\n";
    echo "  Password: admin123\n";
    echo "\n⚠️  IMPORTANT: Change this password after first login!\n";
    
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>
