# EasyCart MVC Migration Guide

## ✅ What Has Been Done

### 1. New MVC Structure Created
- **Models**: Database interaction layer (`app/Models/`)
  - `Model.php` - Base model with CRUD operations
  - `Product.php` - Product data access
  - `User.php` - User authentication
  - `Category.php` - Category management
  - `Cart.php` - Cart persistence
  - `Order.php` - Order management

- **Services**: Business logic layer (`app/Services/`)
  - `AuthService.php` - Login, register, logout
  - `CartService.php` - Cart operations
  - `OrderService.php` - Order processing
  - `ProductService.php` - Product operations
  - `WishlistService.php` - Wishlist management

- **Configuration**: Centralized config (`config/database/`)
  - `config.php` - Database settings

- **Core Files**:
  - `app/bootstrap.php` - Application initialization
  - `app/helpers.php` - Global utility functions
  - `app/legacy_bridge.php` - **Compatibility layer**

### 2. Backward Compatibility Maintained
All existing pages work **without any modification**!

The `legacy_bridge.php` file maps old function calls to the new MVC structure:
- Old: `require_once 'includes/auth.php';`
- New: Automatically uses `AuthService` through the bridge

### 3. Files Backed Up
- Original `includes/` files copied to `legacy_old/`
- New `includes/` files now point to MVC structure

## 🎯 Current Status

### ✅ Working
- All existing pages (index.php, products.php, cart.php, checkout.php, etc.)
- User authentication (login, register, logout)
- Product display and search
- Shopping cart (add, update, remove)
- Order creation
- Wishlist management

### 📝 No Changes Required
Your existing code continues to work as-is. The MVC structure is transparent to the current implementation.

## 🚀 How to Add New Features

### Option 1: Use Legacy Functions (Quick)
Continue using the existing function calls:

```php
<?php
require_once 'includes/auth.php';
require_once 'includes/data.php';

// Works exactly as before
$product = getProductById(1);
$result = loginUser($email, $password);
```

### Option 2: Use MVC Directly (Recommended for New Code)
Use the new structure for better organization:

```php
<?php
require_once 'app/bootstrap.php';

use Services\ProductService;
use Services\AuthService;

$productService = new ProductService();
$product = $productService->getProduct(1);

$authService = new AuthService();
$result = $authService->login($email, $password);
```

## 📚 Common Tasks

### Adding a New Product Feature

**Old Way** (still works):
```php
// In includes/data.php
function getProductsOnSale() {
    global $products;
    return array_filter($products, function($p) {
        return $p['on_sale'] ?? false;
    });
}
```

**New Way** (recommended):
```php
// In app/Services/ProductService.php
public function getProductsOnSale() {
    return $this->productModel->where(['on_sale' => true]);
}
```

### Adding a New Page

**Current Approach**:
```php
<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/data.php';

// Your page logic
$products = getAllProducts(); // Uses MVC automatically
?>
```

**Future Approach** (when fully migrated):
```php
<?php
require_once 'app/bootstrap.php';

use Controllers\ProductController;

$controller = new ProductController();
$controller->index();
```

## 🔧 Troubleshooting

### Issue: "Function not found"
**Solution**: Make sure you're including the right file:
```php
require_once 'includes/auth.php';  // For auth functions
require_once 'includes/data.php';  // For data functions
```

### Issue: "Class not found"
**Solution**: Include bootstrap first:
```php
require_once 'app/bootstrap.php';
use Services\ProductService;
```

### Issue: Database connection error
**Solution**: Check `config/database/config.php` settings

## 📖 Function Mapping Reference

### Authentication
| Old Function | New Service Method |
|-------------|-------------------|
| `isLoggedIn()` | `AuthService->isLoggedIn()` |
| `loginUser()` | `AuthService->login()` |
| `registerUser()` | `AuthService->register()` |
| `logoutUser()` | `AuthService->logout()` |

### Products
| Old Function | New Service Method |
|-------------|-------------------|
| `getProductById()` | `ProductService->getProduct()` |
| `getAllProducts()` | `ProductService->getAllProducts()` |
| `searchProducts()` | `ProductService->searchProducts()` |

### Cart
| Old Function | New Service Method |
|-------------|-------------------|
| `syncCartToDb()` | `CartService->syncCartToDb()` |
| `loadCartFromDb()` | `CartService->loadCartFromDb()` |

### Orders
| Old Function | New Service Method |
|-------------|-------------------|
| `createOrder()` | `OrderService->createOrder()` |

## 🎓 Learning the New Structure

### 1. Start with Services
Services contain the business logic. Look at `app/Services/` to understand what operations are available.

### 2. Understand Models
Models handle database access. Check `app/Models/` to see how data is retrieved and stored.

### 3. Use the Bridge
The `app/legacy_bridge.php` file shows how old functions map to new services. It's a great reference!

## 🔄 Migration Phases

### Phase 1: ✅ COMPLETE
- MVC structure created
- Backward compatibility maintained
- All existing code works

### Phase 2: OPTIONAL (When You're Ready)
- Migrate individual pages to use Services directly
- Create Controllers for each page
- Move views to `app/Views/`

### Phase 3: FUTURE
- Implement routing
- Add middleware
- Create API endpoints

## 💡 Best Practices

1. **For Quick Fixes**: Use existing functions (they work through the bridge)
2. **For New Features**: Use Services directly
3. **For Complex Logic**: Add methods to Services
4. **For Database Queries**: Add methods to Models

## 📞 Need Help?

1. Check `MVC_ARCHITECTURE.md` for structure overview
2. Look at `app/legacy_bridge.php` for function mappings
3. Review Service files for available methods
4. Check Model files for database operations

## 🎉 Benefits You're Getting

1. **No Breaking Changes**: Everything still works
2. **Better Organization**: Code is now separated by concern
3. **Easier Testing**: Each layer can be tested independently
4. **Scalability**: Easy to add new features
5. **Maintainability**: Clear structure makes debugging easier
6. **Future-Proof**: Ready for modern PHP practices

---

**Remember**: You don't need to change anything right now. The new structure is working behind the scenes, making your code more maintainable while keeping everything functional!
