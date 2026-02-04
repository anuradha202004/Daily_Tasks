# EasyCart MVC - Quick Reference Card

## 📁 Project Structure

```
app/
├── Models/              → Database access
├── Services/            → Business logic
├── bootstrap.php        → App initialization
├── helpers.php          → Utility functions
└── legacy_bridge.php    → Compatibility layer

config/database/         → Database config
storage/                 → Files & logs
legacy_old/             → Original files backup
```

## 🚀 Quick Start

### Using Existing Code (No Changes Needed)
```php
<?php
require_once 'includes/auth.php';
require_once 'includes/data.php';

// Everything works as before!
```

### Using New MVC Structure
```php
<?php
require_once 'app/bootstrap.php';

use Services\ProductService;

$productService = new ProductService();
$products = $productService->getAllProducts();
```

## 📚 Available Services

### AuthService
```php
$authService = new AuthService();
$authService->login($email, $password);
$authService->register($email, $password, $name, $confirmPassword);
$authService->logout();
```

### ProductService
```php
$productService = new ProductService();
$productService->getAllProducts();
$productService->getProduct($id);
$productService->getProductsByCategory($categoryId);
$productService->searchProducts($query);
$productService->getAllCategories();
$productService->getAllBrands();
```

### CartService
```php
$cartService = new CartService();
$cartService->syncCartToDb($sessionId, $userId, $cartItems);
$cartService->loadCartFromDb($sessionId, $userId);
$cartService->calculateCartSummary($cart);
```

### OrderService
```php
$orderService = new OrderService();
$orderService->createOrder($userId, $orderData, $items);
$orderService->getUserOrders($userId);
```

### WishlistService
```php
$wishlistService = new WishlistService();
$wishlistService->loadUserWishlist($userId);
$wishlistService->saveUserWishlist($userId, $wishlist);
```

## 🔧 Helper Functions

```php
formatPrice($price)           // Format as currency
redirect($url)                // Redirect to URL
baseUrl($path)                // Get base URL
view($view, $data)            // Load view file
e($string)                    // Escape HTML
isLoggedIn()                  // Check auth status
getCurrentUser()              // Get current user
requireLogin()                // Require authentication
getDBConnection()             // Get PDO instance
```

## 📊 Models Available

- `Product` - Product operations
- `User` - User management
- `Category` - Category operations
- `Cart` - Cart persistence
- `Order` - Order management

### Base Model Methods
```php
$model->find($id)             // Find by ID
$model->all()                 // Get all records
$model->where($conditions)    // Find by conditions
$model->create($data)         // Insert record
$model->update($id, $data)    // Update record
$model->delete($id)           // Delete record
$model->query($sql, $params)  // Raw query
```

## 🎯 Common Patterns

### Get Product
```php
// Old way (still works)
$product = getProductById($id);

// New way
$productService = new ProductService();
$product = $productService->getProduct($id);
```

### User Login
```php
// Old way (still works)
$result = loginUser($email, $password);

// New way
$authService = new AuthService();
$result = $authService->login($email, $password);
```

### Sync Cart
```php
// Old way (still works)
syncCartToDb(session_id(), $userId, $cart);

// New way
$cartService = new CartService();
$cartService->syncCartToDb(session_id(), $userId, $cart);
```

## 🔐 Configuration

### Database (`config/database/config.php`)
```php
return [
    'driver' => 'pgsql',
    'host' => 'localhost',
    'port' => '5432',
    'database' => 'easycart',
    'username' => 'postgres',
    'password' => '1234',
];
```

## 📝 File Locations

| Type | Location | Purpose |
|------|----------|---------|
| Models | `app/Models/` | Database access |
| Services | `app/Services/` | Business logic |
| Config | `config/` | Configuration |
| Views | `app/Views/` | Templates (future) |
| Storage | `storage/` | Files & logs |
| Legacy | `legacy_old/` | Original files |

## ⚡ Tips

1. **No changes required** - existing code works as-is
2. **Use Services** for new features
3. **Check legacy_bridge.php** for function mappings
4. **Read MVC_ARCHITECTURE.md** for details
5. **See MIGRATION_GUIDE.md** for examples

## 🐛 Debugging

### Enable Error Display
Already enabled in `bootstrap.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check Logs
```php
error_log("Debug message");
// Logs to PHP error log
```

### Database Connection
```php
$pdo = getDBConnection();
// Returns PDO instance or dies with error
```

## 📖 Documentation Files

- `MVC_ARCHITECTURE.md` - Complete architecture overview
- `MIGRATION_GUIDE.md` - Step-by-step migration guide
- `QUICK_REFERENCE.md` - This file

---

**Remember**: Your existing code works without changes. Use this reference when adding new features or when you're ready to use the MVC structure directly!
