# EasyCart - MVC Architecture Documentation

## Project Structure

```
easyCart/
├── app/
│   ├── Controllers/          # Request handlers and business flow control
│   ├── Models/              # Database models and data access
│   ├── Services/            # Business logic layer
│   ├── Repositories/        # Data access abstraction (future use)
│   ├── Views/               # View templates (future migration)
│   ├── Middleware/          # Request/response filters (future use)
│   ├── bootstrap.php        # Application initialization
│   ├── helpers.php          # Global utility functions
│   └── legacy_bridge.php    # Compatibility layer for existing code
│
├── config/
│   └── database/
│       └── config.php       # Database configuration
│
├── public/                  # Public web root (future)
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
│
├── routes/                  # Route definitions (future use)
│
├── storage/                 # Application storage
│   ├── logs/               # Log files
│   ├── cache/              # Cache files
│   └── wishlist/           # Wishlist data files
│
├── legacy_old/             # Original files (backup)
│
├── scripts/                # Database setup and utility scripts
│
└── [root PHP files]        # Current page files (to be migrated)
```

## Architecture Layers

### 1. Models (`app/Models/`)
- **Purpose**: Database interaction and data structure
- **Files**:
  - `Model.php` - Base model with common CRUD operations
  - `Product.php` - Product data access
  - `User.php` - User authentication data
  - `Category.php` - Category data
  - `Cart.php` - Shopping cart persistence
  - `Order.php` - Order management

### 2. Services (`app/Services/`)
- **Purpose**: Business logic and complex operations
- **Files**:
  - `AuthService.php` - Authentication logic (login, register, logout)
  - `CartService.php` - Cart management (sync, load, calculate)
  - `OrderService.php` - Order processing
  - `ProductService.php` - Product operations
  - `WishlistService.php` - Wishlist management

### 3. Legacy Bridge (`app/legacy_bridge.php`)
- **Purpose**: Maintains backward compatibility
- **Function**: Maps old function calls to new MVC structure
- **Usage**: Include this file instead of old `includes/data.php` and `includes/auth.php`

## Migration Strategy

### Phase 1: ✅ COMPLETE
- Created MVC folder structure
- Implemented Models layer
- Implemented Services layer
- Created compatibility bridge
- **Result**: Existing code works without modification

### Phase 2: IN PROGRESS
- Migrate page files to use new structure directly
- Create Controllers for each page
- Move views to `app/Views/`

### Phase 3: FUTURE
- Implement routing system
- Add middleware for authentication
- Create API endpoints
- Implement caching layer

## How to Use

### For Existing Pages
No changes needed! The legacy bridge automatically maps old functions to new MVC structure.

```php
<?php
// Old way (still works)
require_once 'includes/auth.php';
require_once 'includes/data.php';

// Automatically uses new MVC structure through legacy_bridge.php
```

### For New Development
Use the MVC structure directly:

```php
<?php
require_once 'app/bootstrap.php';

use Services\ProductService;
use Services\CartService;

$productService = new ProductService();
$products = $productService->getAllProducts();

$cartService = new CartService();
$cartService->syncCartToDb(session_id(), $userId, $cart);
```

## Key Benefits

1. **Separation of Concerns**: Business logic separated from data access
2. **Reusability**: Services can be used across multiple pages
3. **Testability**: Each layer can be tested independently
4. **Maintainability**: Clear structure makes code easier to understand
5. **Scalability**: Easy to add new features without breaking existing code
6. **Backward Compatibility**: Existing code continues to work

## Database Configuration

Located in `config/database/config.php`:

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

## Common Operations

### Get All Products
```php
$productService = new ProductService();
$products = $productService->getAllProducts();
```

### User Login
```php
$authService = new AuthService();
$result = $authService->login($email, $password);
```

### Sync Cart
```php
$cartService = new CartService();
$cartService->syncCartToDb(session_id(), $userId, $cartItems);
```

### Create Order
```php
$orderService = new OrderService();
$orderService->createOrder($userId, $orderData, $items);
```

## Next Steps

1. **Migrate Views**: Move HTML/PHP templates to `app/Views/`
2. **Create Controllers**: Implement controller classes for each page
3. **Implement Routing**: Add URL routing system
4. **Add Middleware**: Authentication and authorization middleware
5. **API Development**: Create RESTful API endpoints

## Support

For questions or issues with the new structure, refer to:
- Model documentation in each model file
- Service documentation in each service file
- Legacy bridge for function mappings
