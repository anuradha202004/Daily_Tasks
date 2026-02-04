# Fix all PHP files that incorrectly reference bootstrap.php
$files = @(
    "signup.php",
    "signin.php", 
    "logout.php",
    "profile.php",
    "orders.php",
    "product-detail.php",
    "search-results.php",
    "track-order.php",
    "checkout.php"
)

$oldPattern = "require_once 'bootstrap.php';"
$newCode = @"
session_start();

require_once 'includes/auth.php';
require_once 'includes/data.php';
"@

foreach ($file in $files) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw
        if ($content -match [regex]::Escape($oldPattern)) {
            Write-Host "Fixing $file..."
            # Replace only the first occurrence after <?php
            $content = $content -replace "(<\?php\s*\r?\n)\s*require_once 'bootstrap\.php';", "`$1$newCode"
            Set-Content -Path $file -Value $content -NoNewline
            Write-Host "Fixed $file" -ForegroundColor Green
        } else {
            Write-Host "Skipping $file (pattern not found)" -ForegroundColor Yellow
        }
    } else {
        Write-Host "File not found: $file" -ForegroundColor Red
    }
}

Write-Host "`nAll files processed!" -ForegroundColor Cyan
