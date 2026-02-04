<?php
/**
 * Clean up corrupted PHP files with duplicate session_start() and require statements
 */

$filesToClean = [
    'profile.php',
    'orders.php'
];

foreach ($filesToClean as $file) {
    if (!file_exists($file)) {
        echo "⚠️  $file not found\n";
        continue;
    }
    
    echo "Cleaning $file...\n";
    
    $content = file_get_contents($file);
    
    // Count occurrences before
    $sessionCount = substr_count($content, 'session_start()');
    $requireAuthCount = substr_count($content, "require_once 'includes/auth.php'");
    $requireDataCount = substr_count($content, "require_once 'includes/data.php'");
    
    echo "  Before: session_start()=$sessionCount, auth=$requireAuthCount, data=$requireDataCount\n";
    
    // Remove all PHP blocks that only contain session_start and requires
    $pattern = '/<\?php\s*\n\s*session_start\(\);\s*\r?\n\s*\r?\n\s*require_once\s+[\'"]includes\/auth\.php[\'"];\s*\r?\n\s*require_once\s+[\'"]includes\/data\.php[\'"];\s*\n/';
    $content = preg_replace($pattern, '', $content);
    
    // Also remove standalone occurrences
    $content = preg_replace('/<\?php\s*session_start\(\);\s*\r?\n\s*\r?\n\s*require_once\s+[\'"]includes\/auth\.php[\'"];\s*\r?\n\s*require_once\s+[\'"]includes\/data\.php[\'"];\s*\n/', '', $content);
    
    // Count after
    $sessionCountAfter = substr_count($content, 'session_start()');
    $requireAuthCountAfter = substr_count($content, "require_once 'includes/auth.php'");
    $requireDataCountAfter = substr_count($content, "require_once 'includes/data.php'");
    
    echo "  After: session_start()=$sessionCountAfter, auth=$requireAuthCountAfter, data=$requireDataCountAfter\n";
    
    if ($sessionCountAfter <= 1 && $requireAuthCountAfter <= 1 && $requireDataCountAfter <= 1) {
        file_put_contents($file, $content);
        echo "  ✅ Cleaned successfully!\n\n";
    } else {
        echo "  ⚠️  Still has duplicates, manual intervention needed\n\n";
    }
}

echo "Done!\n";
?>
