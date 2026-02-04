<?php
/**
 * Clear PHP OpCache and verify all files are fixed
 */

// Clear opcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OpCache cleared successfully!\n";
} else {
    echo "ℹ️  OpCache not available (this is okay)\n";
}

// Verify all main PHP files
$filesToCheck = [
    'index.php',
    'products.php',
    'cart.php',
    'wishlist.php',
    'checkout.php',
    'signin.php',
    'signup.php',
    'logout.php',
    'profile.php',
    'orders.php',
    'product-detail.php',
    'search-results.php',
    'track-order.php',
    'order-confirmation.php'
];

echo "\n📋 Checking files for bootstrap.php references...\n\n";

$allGood = true;
foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $hasBootstrap = strpos($content, "require_once 'bootstrap.php';") !== false;
        
        if ($hasBootstrap) {
            echo "❌ $file - STILL HAS bootstrap.php reference!\n";
            $allGood = false;
        } else {
            echo "✅ $file - OK\n";
        }
    } else {
        echo "⚠️  $file - File not found\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";

if ($allGood) {
    echo "🎉 All files are fixed! No bootstrap.php references found.\n";
    echo "\n💡 If you're still seeing errors:\n";
    echo "   1. Clear your browser cache (Ctrl+Shift+Delete)\n";
    echo "   2. Restart your web server (Apache/Nginx)\n";
    echo "   3. Try accessing the page in incognito/private mode\n";
} else {
    echo "⚠️  Some files still need fixing!\n";
}

echo "\n";
?>
