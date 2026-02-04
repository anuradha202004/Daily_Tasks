<?php
// Header Component - Used in all pages
require_once 'auth.php';
$isUserLoggedIn = isLoggedIn();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - EasyCart' : 'EasyCart'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-wrapper">
            <!-- Logo (Left) -->
            <div class="header-logo">
                <a href="index.php" class="logo">🛒 EasyCart</a>
            </div>

            <!-- Navigation (Center-Left) -->
            <nav class="header-nav">
                <a href="index.php" class="nav-link">Home</a>
                <a href="products.php" class="nav-link">Products</a>
                <a href="index.php#about" class="nav-link">About</a>
                <a href="index.php#contact" class="nav-link">Contact</a>
            </nav>

            <!-- Search Bar (Center) -->
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search products..." id="searchInput" onkeypress="handleSearchKeypress(event)">
                <button class="search-btn" onclick="performSearch()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>

            <!-- Right Section: Cart, Orders, Auth -->
            <div class="header-actions">
                    <!-- Cart Icon (visible for all users) -->
                    <a href="cart.php" class="action-icon cart-icon" title="Shopping Cart">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <span class="badge"><?php echo count($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </a>

                    <?php if ($isUserLoggedIn): ?>
                        <!-- Logged In: Show Wishlist, Orders, User Menu -->
                        <a href="wishlist.php" class="action-icon wishlist-icon" title="My Wishlist">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <?php if (!empty($_SESSION['wishlist'])): ?>
                                <span class="badge"><?php echo count($_SESSION['wishlist']); ?></span>
                            <?php endif; ?>
                        </a>

                        <a href="orders.php" class="action-icon orders-icon" title="My Orders">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </a>

                        <!-- User Menu -->
                        <div class="user-menu">
                            <button class="user-btn" onclick="toggleUserMenu()" title="<?php echo htmlspecialchars($currentUser['name']); ?>">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="user-name"><?php echo htmlspecialchars(explode(' ', $currentUser['name'])[0]); ?></span>
                                <svg class="dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="user-dropdown" id="userDropdown">
                                <a href="profile.php" class="dropdown-item">👤 My Profile</a>
                                <a href="wishlist.php" class="dropdown-item">❤️ My Wishlist</a>
                                <a href="orders.php" class="dropdown-item">📦 My Orders</a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="dropdown-item logout-item">🚪 Logout</a>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Not Logged In: Show Login Icon -->
                        <a href="signin.php" class="action-icon auth-icon" title="Sign In">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
    </header>

    <script>
        function performSearch() {
            const searchQuery = document.getElementById('searchInput').value.trim();
            if (searchQuery) {
                window.location.href = 'search-results.php?q=' + encodeURIComponent(searchQuery);
            }
        }

        function handleSearchKeypress(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        }

        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            if (userMenu && !userMenu.contains(event.target)) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            }
        });
    </script>
