<?php
// Header Component - Used in all pages
// require_once __DIR__ . '/auth.php'; // Handled by bootstrap.php
$isUserLoggedIn = isLoggedIn();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - EasyCart' : 'EasyCart'; ?></title>
    <link rel="stylesheet" href="<?php echo baseUrl('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/home.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/auth.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/cart.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/product.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/wishlist.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/order.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/checkout.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/user-menu.css'); ?>">
    <script>const BASE_URL = "<?php echo baseUrl(''); ?>";</script>
    <script src="<?php echo baseUrl('js/common.js'); ?>"></script>
    <script src="<?php echo baseUrl('js/header.js'); ?>"></script>

</head>
<body class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin') !== false ? 'admin-body' : ''; ?>">
    <!-- Header -->
    <header class="main-header">
        <div class="header-wrapper">
            <!-- Logo (Left) -->
            <div class="header-logo">
                <a href="/" class="logo">🛒 EasyCart</a>
            </div>

            <!-- Navigation (Center-Left) -->
            <nav class="header-nav">
                <a href="<?php echo url(''); ?>" class="nav-link">Home</a>
                <a href="<?php echo url('products'); ?>" class="nav-link">Products</a>
                <a href="<?php echo url('about'); ?>" class="nav-link">About</a>
                <a href="<?php echo url('contact'); ?>" class="nav-link">Contact</a>
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
                    <a href="<?php echo url('cart'); ?>" class="action-icon cart-icon" title="Shopping Cart">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <?php
                            $cartModel = new Model_Cart();
                            $items = $cartModel->load();
                            $count = 0;
                            foreach ($items as $item) {
                                $count += $item['quantity'];
                            }
                        ?>
                        <span class="badge cart-badge <?php echo $count == 0 ? 'd-none' : 'd-flex'; ?>" id="cart-badge"><?php echo $count; ?></span>
                    </a>

                    <?php if ($isUserLoggedIn): ?>
                        <!-- Logged In: Show Wishlist, Orders, User Menu -->
                        <a href="/wishlist" class="action-icon wishlist-icon" title="My Wishlist">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="badge wishlist-badge <?php echo empty($_SESSION['wishlist']) ? 'd-none' : 'd-flex'; ?>" id="wishlist-badge"><?php echo !empty($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : '0'; ?></span>
                        </a>

                        <a href="/orders" class="action-icon orders-icon" title="My Orders">
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
                                <a href="<?php echo url('profile'); ?>" class="dropdown-item">👤 My Profile</a>
                                <a href="<?php echo url('wishlist'); ?>" class="dropdown-item">❤️ My Wishlist</a>
                                <a href="<?php echo url('orders'); ?>" class="dropdown-item">📦 My Orders</a>
                                <?php if (isset($currentUser['role']) && $currentUser['role'] === 'admin'): ?>
                                    <a href="<?php echo url('admin/dashboard'); ?>" class="dropdown-item" style="color: #dc2626; font-weight: 500;">⚙️ Admin Dashboard</a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="<?php echo url('logout'); ?>" class="dropdown-item logout-item" onclick="return confirmLogout()">🚪 Logout</a>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Not Logged In: Show Login Icon -->
                        <a href="/signin" class="action-icon auth-icon" title="Sign In">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
    </header>
