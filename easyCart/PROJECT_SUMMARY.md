# 📦 EasyCart - Complete Project Summary

## 📁 Folder Structure

```
easyCart/
├── .gemini/                      # AI assistant configuration
├── .htaccess                     # Root URL rewriting (redirects to public/)
├── CLEANUP_COMPLETE.txt          # Cleanup documentation
├── simple_product.csv            # Sample product import file
│
├── admin/                        # Admin panel (legacy)
│   └── includes/                 # Admin includes
│
├── app/                          # Application Core (MVC Architecture)
│   ├── Autoloader.php            # Class autoloader (PSR-0 style)
│   ├── bootstrap.php             # Application initialization
│   ├── helpers.php               # Global helper functions
│   │
│   ├── Code/                     # Code pool (Magento-inspired)
│   │   ├── Core/                 # Framework core classes
│   │   │   └── Core/
│   │   │       ├── Controller.php    # Base controller
│   │   │       ├── Database.php      # Database singleton
│   │   │       ├── Model.php         # Base model
│   │   │       ├── Router.php        # URL routing engine
│   │   │       ├── auth.php          # Authentication logic
│   │   │       ├── data.php          # Data management
│   │   │       └── db.php            # Legacy database connection
│   │   │
│   │   └── Local/                # Custom application code
│   │       ├── Controller/       # Page controllers
│   │       │   ├── Admin.php         # Admin panel
│   │       │   ├── Auth.php          # Login/signup/logout
│   │       │   ├── Cart.php          # Shopping cart
│   │       │   ├── Home.php          # Homepage
│   │       │   ├── Order.php         # Order processing
│   │       │   ├── Page.php          # Static pages
│   │       │   ├── Product.php       # Product listing/detail
│   │       │   ├── Profile.php       # User profile
│   │       │   └── Wishlist.php      # Wishlist management
│   │       │
│   │       ├── Model/            # Business logic & data
│   │       │   ├── Admin.php         # Admin operations
│   │       │   ├── Cart.php          # Cart model
│   │       │   ├── Category.php      # Product categories
│   │       │   ├── Customer.php      # User management
│   │       │   ├── Order.php         # Order model
│   │       │   ├── Product.php       # Product model
│   │       │   ├── Wishlist.php      # Wishlist model
│   │       │   ├── Admin/
│   │       │   │   └── Importer.php  # CSV import
│   │       │   ├── Cart/
│   │       │   │   └── Item.php      # Cart item
│   │       │   └── Product/
│   │       │       ├── Collection.php    # Product collection
│   │       │       └── Resource.php      # Database operations
│   │       │
│   │       └── View/             # View layer
│   │           └── Product.php       # Product view renderer
│   │
│   ├── Controllers/              # (Empty - migrated to Code/Local)
│   ├── Core/                     # Legacy core files
│   ├── Models/                   # Legacy models
│   ├── Views/                    # View templates (PHP)
│   │   ├── admin/                # Admin templates
│   │   ├── auth/                 # Login/signup pages
│   │   ├── cart/                 # Cart page
│   │   ├── home/                 # Homepage
│   │   ├── order/                # Order pages
│   │   ├── page/                 # Static pages
│   │   ├── product/              # Product pages
│   │   ├── profile/              # Profile page
│   │   └── wishlist/             # Wishlist page
│   │
│   └── legacy/                   # Old controller files (backup)
│
├── config/                       # Configuration files
│   ├── database.php              # Database config (main)
│   └── database/
│       └── config.php            # Database credentials
│
├── public/                       # Public web root
│   ├── .htaccess                 # Clean URL routing
│   ├── index.php                 # Application entry point
│   │
│   ├── css/                      # Stylesheets
│   │   ├── style.css             # Main stylesheet (107KB)
│   │   ├── admin.css             # Admin panel styles
│   │   ├── auth.css              # Login/signup styles
│   │   ├── cart.css              # Cart page styles
│   │   ├── checkout.css          # Checkout styles
│   │   ├── home.css              # Homepage styles
│   │   ├── invoice.css           # Invoice styles
│   │   ├── order.css             # Order tracking styles
│   │   ├── product.css           # Product page styles
│   │   ├── user-menu.css         # User menu styles
│   │   └── wishlist.css          # Wishlist styles
│   │
│   ├── js/                       # JavaScript files
│   │   ├── carousel.js           # Image carousel
│   │   ├── cart.js               # Cart functionality
│   │   ├── checkout.js           # Checkout process
│   │   ├── common.js             # Common utilities
│   │   ├── contact.js            # Contact form
│   │   ├── footer.js             # Footer interactions
│   │   ├── header.js             # Header/navigation
│   │   ├── order-confirmation.js # Order confirmation
│   │   ├── product-detail.js     # Product detail page
│   │   ├── toast.js              # Toast notifications
│   │   ├── validation.js         # Form validation
│   │   └── wishlist.js           # Wishlist interactions
│   │
│   └── img/                      # Images and assets
│
├── resources/                    # Additional resources
│   └── templates/                # Email/print templates
│
└── storage/                      # Application data
    ├── cache/                    # Cache files
    ├── data/                     # JSON data storage
    │   ├── cart_*.json           # Guest cart files
    │   └── wishlist_*.json       # Guest wishlist files
    ├── logs/                     # Application logs
    └── wishlist/                 # Wishlist storage
```

---

## 🏗️ Architecture Overview

### **MVC Pattern (Model-View-Controller)**

Your application follows a **Magento-inspired MVC architecture** with a code pool system:

1. **Code/Core/** - Framework classes (reusable across projects)
2. **Code/Local/** - Your custom application code
3. **Autoloader** - Automatically loads classes using underscore naming (Class_Name_Model)

---

## 🔄 How the Application Works

### **1. Request Flow**

```
User Request (http://localhost/easyCart/public/products)
    ↓
.htaccess (root) → Redirects to public/
    ↓
public/.htaccess → Rewrites to index.php?url=products
    ↓
public/index.php → Entry point
    ↓
app/bootstrap.php → Initializes app (paths, autoloader, config)
    ↓
Core_Router → Matches URL to Controller/Action
    ↓
Controller_Product::index() → Executes business logic
    ↓
Model_Product → Fetches data from database
    ↓
View (app/Views/product/index.php) → Renders HTML
    ↓
Response sent to browser
```

---

### **2. Entry Point: `public/index.php`**

**Purpose:** Main application entry point

**What it does:**
- Starts PHP session
- Defines constants (APP_ROOT, URL_ROOT)
- Loads `bootstrap.php`
- Creates router instance
- Defines all application routes
- Dispatches request to appropriate controller

**Key Routes:**
```php
'' → Controller_Home::index()                    // Homepage
'products' → Controller_Product::index()         // Product listing
'product/{slug}' → Controller_Product::detail()  // Product detail
'cart' → Controller_Cart::index()                // Shopping cart
'checkout' → Controller_Order::checkout()        // Checkout page
'signin' → Controller_Auth::signin()             // Login
'profile' → Controller_Profile::index()          // User profile
'wishlist' → Controller_Wishlist::index()        // Wishlist
```

---

### **3. Bootstrap: `app/bootstrap.php`**

**Purpose:** Application initialization

**What it does:**
- Starts session (if not already started)
- Defines path constants (BASE_PATH, APP_PATH, CORE_PATH, etc.)
- Enables error reporting
- Registers autoloader
- Loads database configuration
- Loads legacy components (auth.php, data.php)
- Checks if logged-in user still exists in database
- Loads helper functions

---

### **4. Autoloader: `app/Autoloader.php`**

**Purpose:** Automatically loads classes without manual `require`

**How it works:**
- Converts class names to file paths using underscores
- Example: `Controller_Product` → `Controller/Product.php`
- Searches in order:
  1. `app/Code/Local/` (custom code)
  2. `app/Code/Core/` (framework code)

**Example:**
```php
$product = new Model_Product(); // Automatically loads app/Code/Local/Model/Product.php
```

---

### **5. Router: `app/Code/Core/Core/Router.php`**

**Purpose:** Maps URLs to controllers and actions

**Features:**
- **Exact matching:** `/cart` → `Controller_Cart::index()`
- **Parameterized routes:** `/product/{slug}` → `Controller_Product::detail($slug)`
- **Fallback routing:** `/controller/action` → `Controller::action()`

**How it works:**
1. Receives URL from `index.php`
2. Checks registered routes
3. Extracts parameters from URL
4. Instantiates controller
5. Calls action method with parameters

---

### **6. Controllers** (`app/Code/Local/Controller/`)

**Purpose:** Handle HTTP requests and coordinate between models and views

#### **Controller_Home.php**
- **Route:** `/` (homepage)
- **What it does:**
  - Fetches all products from database
  - Selects first 4 as "featured"
  - Fetches categories
  - Renders homepage view

#### **Controller_Product.php**
- **Routes:** `/products`, `/product/{slug}`, `/search-results`
- **What it does:**
  - `index()` - Lists all products with filters
  - `detail($slug)` - Shows single product by URL slug
  - `search()` - Searches products by keyword

#### **Controller_Cart.php**
- **Route:** `/cart`
- **What it does:**
  - Displays cart items
  - Handles add/remove/update cart actions
  - Supports both logged-in users and guests

#### **Controller_Order.php**
- **Routes:** `/checkout`, `/order-confirmation`, `/invoice`, `/orders`, `/track-order`
- **What it does:**
  - `checkout()` - Displays checkout form
  - `confirmation()` - Shows order confirmation
  - `invoice()` - Generates PDF invoice
  - `track()` - Order tracking page

#### **Controller_Auth.php**
- **Routes:** `/signin`, `/signup`, `/logout`
- **What it does:**
  - User authentication
  - Registration
  - Session management

#### **Controller_Profile.php**
- **Route:** `/profile`
- **What it does:**
  - Displays user profile
  - Shows order history

#### **Controller_Wishlist.php**
- **Routes:** `/wishlist`, `/wishlist/add`, `/wishlist/remove`
- **What it does:**
  - Manages user wishlist
  - Add/remove products

#### **Controller_Admin.php**
- **Routes:** `/admin/dashboard`, `/admin/import_export`
- **What it does:**
  - Admin dashboard
  - CSV product import/export

---

### **7. Models** (`app/Code/Local/Model/`)

**Purpose:** Business logic and database operations

#### **Model_Product.php**
- **Methods:**
  - `load($id)` - Load product by ID
  - `loadBySlug($slug)` - Load product by URL slug
  - `getCollection()` - Get product collection
- **What it does:**
  - Fetches product data from database
  - Applies business logic (price formatting, image fallbacks)
  - Returns normalized data

#### **Model_Product_Collection.php**
- **Methods:**
  - `getAll()` - Get all products
  - `addFilter($field, $value)` - Filter products
  - `search($keyword)` - Search products
  - `getData()` - Execute query and return results

#### **Model_Product_Resource.php**
- **Methods:**
  - `getProductById($id)` - Database query for single product
  - `getProductIdBySlug($slug)` - Get ID from slug
  - `getAllProducts()` - Get all products from DB

#### **Model_Cart.php**
- **Methods:**
  - `getItems()` - Get cart items
  - `addItem($productId, $quantity)` - Add to cart
  - `removeItem($productId)` - Remove from cart
  - `updateQuantity($productId, $quantity)` - Update quantity
  - `getTotal()` - Calculate cart total
- **Storage:**
  - Logged-in users: Database
  - Guests: JSON files in `storage/data/cart_*.json`

#### **Model_Order.php**
- **Methods:**
  - `create($orderData)` - Create new order
  - `getById($orderId)` - Get order details
  - `getByUserId($userId)` - Get user's orders
  - `updateStatus($orderId, $status)` - Update order status

#### **Model_Customer.php**
- **Methods:**
  - `authenticate($email, $password)` - Login
  - `register($userData)` - Create account
  - `getById($id)` - Get user data

#### **Model_Wishlist.php**
- **Methods:**
  - `getItems($userId)` - Get wishlist items
  - `addItem($userId, $productId)` - Add to wishlist
  - `removeItem($userId, $productId)` - Remove from wishlist

---

### **8. Database: `app/Code/Core/Core/Database.php`**

**Purpose:** Singleton database connection manager

**Configuration:** `config/database/config.php`
```php
'driver' => 'pgsql'           // PostgreSQL
'host' => 'localhost'
'port' => '5432'
'database' => 'easycart'
'username' => 'postgres'
'password' => '1234'
```

**Methods:**
- `getInstance()` - Get database instance (singleton)
- `query($sql, $params)` - Execute query
- `fetchAll($sql, $params)` - Fetch multiple rows
- `fetchOne($sql, $params)` - Fetch single row
- `beginTransaction()` - Start transaction
- `commit()` - Commit transaction
- `rollBack()` - Rollback transaction

**Usage:**
```php
$db = Core_Database::getInstance();
$products = $db->fetchAll("SELECT * FROM products WHERE category = ?", [$category]);
```

---

### **9. Views** (`app/Views/`)

**Purpose:** HTML templates for rendering pages

**Structure:**
- Each controller has a corresponding view folder
- Views use PHP for templating
- Data is passed from controllers as variables

**Example:** `app/Views/product/detail.php`
```php
<h1><?= e($product['name']) ?></h1>
<p><?= e($product['description']) ?></p>
<span>$<?= number_format($product['price'], 2) ?></span>
```

---

### **10. Helper Functions** (`app/helpers.php`)

**Purpose:** Global utility functions

**Functions:**
- `redirect($url)` - Redirect to URL
- `url($path)` - Generate application URL
- `baseUrl($path)` - Generate public asset URL
- `view($view, $data)` - Load view file
- `e($string)` - Escape HTML (security)
- `getGuestSessionId()` - Get/create guest session ID

**Usage:**
```php
redirect('cart');                    // Redirect to cart page
echo baseUrl('css/style.css');       // Output: http://localhost/easyCart/public/css/style.css
echo e($userInput);                  // Safely output user input
```

---

## 🎨 Frontend Assets

### **CSS Files** (`public/css/`)

- **style.css** (107KB) - Main stylesheet with all global styles
- **Page-specific CSS:**
  - `home.css` - Homepage hero section, featured products
  - `product.css` - Product listing and detail pages
  - `cart.css` - Shopping cart page
  - `checkout.css` - Checkout form and process
  - `order.css` - Order tracking and history
  - `auth.css` - Login/signup forms
  - `profile.css` - User profile page
  - `wishlist.css` - Wishlist page
  - `admin.css` - Admin panel

### **JavaScript Files** (`public/js/`)

- **cart.js** - Add to cart, update quantity, remove items
- **checkout.js** - Checkout form validation, payment processing
- **product-detail.js** - Image zoom, variant selection
- **wishlist.js** - Add/remove wishlist items
- **validation.js** - Form validation (email, phone, etc.)
- **toast.js** - Toast notification system
- **header.js** - Navigation, search, user menu
- **common.js** - Shared utilities

---

## 💾 Data Storage

### **Database (PostgreSQL)**

**Tables:**
- `products` - Product catalog
- `categories` - Product categories
- `users` / `customers` - User accounts
- `orders` - Order records
- `order_items` - Order line items
- `cart` - Shopping cart (logged-in users)
- `wishlist` - User wishlists

### **JSON Files** (`storage/data/`)

**For Guest Users:**
- `cart_[hash].json` - Guest shopping carts
- `wishlist_[hash].json` - Guest wishlists

**Hash Generation:**
- Uses `getGuestSessionId()` from helpers.php
- Creates unique ID using `bin2hex(random_bytes(16))`
- Stored in session and cookie

---

## 🔐 Authentication System

### **Files:**
- `app/Code/Core/Core/auth.php` - Authentication logic
- `app/Code/Local/Controller/Auth.php` - Login/signup controller

### **How it works:**
1. User submits login form
2. `Controller_Auth::signin()` validates credentials
3. Checks database for matching email/password
4. Creates session: `$_SESSION['user_id']`, `$_SESSION['user_email']`
5. Redirects to homepage

### **Session Management:**
- Session started in `bootstrap.php`
- User data stored in `$_SESSION`
- `checkUserExists()` verifies user still exists in DB

---

## 🛒 Shopping Cart System

### **For Logged-in Users:**
- Cart stored in database (`cart` table)
- Persists across sessions
- Linked to user ID

### **For Guest Users:**
- Cart stored in JSON file: `storage/data/cart_[guest_id].json`
- Guest ID stored in cookie (session cookie)
- Expires when browser closes

### **Cart Operations:**
1. **Add to Cart:**
   - AJAX request to `/cart` with product ID
   - `Model_Cart::addItem()` saves to DB or JSON
   - Returns JSON response
   - Toast notification shown

2. **Update Quantity:**
   - AJAX request with new quantity
   - `Model_Cart::updateQuantity()` updates storage
   - Cart total recalculated

3. **Remove Item:**
   - AJAX request with product ID
   - `Model_Cart::removeItem()` deletes item
   - Cart refreshed

---

## 📦 Order Processing Flow

1. **Cart Page** (`/cart`)
   - User reviews items
   - Clicks "Proceed to Checkout"

2. **Checkout Page** (`/checkout`)
   - User enters shipping/billing info
   - Selects payment method
   - Submits order

3. **Order Creation** (`Controller_Order::checkout()`)
   - Validates form data
   - Creates order in database
   - Generates order ID
   - Clears cart
   - Redirects to confirmation

4. **Order Confirmation** (`/order-confirmation`)
   - Displays order summary
   - Shows order number
   - Provides tracking link

5. **Order Tracking** (`/track-order`)
   - User enters order ID
   - Shows order status (Processing → Shipped → Delivered)

---

## 🔧 URL Routing System

### **Clean URLs (No .php extension)**

**Root `.htaccess`:**
```apache
# Redirects all requests to public/
RewriteRule ^$ public/ [L]
RewriteRule (.*) public/$1 [L]
```

**Public `.htaccess`:**
```apache
# Rewrites clean URLs to index.php
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
```

**Example:**
```
User visits: http://localhost/easyCart/public/product/wireless-headphones
    ↓
.htaccess rewrites to: index.php?url=product/wireless-headphones
    ↓
Router matches: product/{slug}
    ↓
Calls: Controller_Product::detail('wireless-headphones')
```

---

## 🎯 Key Features

### ✅ **Implemented Features:**
1. **User Authentication** - Login, signup, logout
2. **Product Catalog** - Browse, search, filter products
3. **Shopping Cart** - Add, remove, update items
4. **Wishlist** - Save favorite products
5. **Checkout** - Multi-step checkout process
6. **Order Management** - Order history, tracking
7. **Guest Shopping** - Cart/wishlist without login
8. **Admin Panel** - Product import/export, dashboard
9. **Clean URLs** - SEO-friendly URLs
10. **Responsive Design** - Mobile-friendly

### 🎨 **Design Features:**
- Modern, clean UI
- Toast notifications
- Image carousels
- Form validation
- Loading states
- Error handling

---

## 🚀 Deployment

### **Apache (XAMPP) Setup:**

**Access URLs:**
- Main: `http://localhost/easyCart/public/`
- IP: `http://192.168.1.103/easyCart/public/`

**Requirements:**
- Apache with mod_rewrite enabled
- PHP 7.4+
- PostgreSQL 12+

**Configuration:**
1. Copy project to `C:\xampp\htdocs\easyCart\`
2. Start Apache in XAMPP
3. Access via browser

---

## 📝 File Purposes Summary

### **Configuration Files:**
- `.htaccess` - URL rewriting rules
- `config/database/config.php` - Database credentials
- `app/bootstrap.php` - App initialization

### **Core Framework:**
- `app/Autoloader.php` - Class loading
- `app/Code/Core/Core/Router.php` - URL routing
- `app/Code/Core/Core/Database.php` - Database connection
- `app/Code/Core/Core/Controller.php` - Base controller
- `app/Code/Core/Core/Model.php` - Base model

### **Application Logic:**
- `app/Code/Local/Controller/` - Page controllers
- `app/Code/Local/Model/` - Business logic
- `app/Views/` - HTML templates

### **Frontend:**
- `public/css/` - Stylesheets
- `public/js/` - JavaScript
- `public/img/` - Images

### **Data Storage:**
- `storage/data/` - JSON files (guest carts/wishlists)
- Database - PostgreSQL (main data)

---

## 🔍 How to Find Things

**Need to modify homepage?**
- Controller: `app/Code/Local/Controller/Home.php`
- View: `app/Views/home/index.php`
- CSS: `public/css/home.css`

**Need to change product display?**
- Controller: `app/Code/Local/Controller/Product.php`
- Model: `app/Code/Local/Model/Product.php`
- View: `app/Views/product/detail.php`
- CSS: `public/css/product.css`
- JS: `public/js/product-detail.js`

**Need to modify cart?**
- Controller: `app/Code/Local/Controller/Cart.php`
- Model: `app/Code/Local/Model/Cart.php`
- View: `app/Views/cart/index.php`
- CSS: `public/css/cart.css`
- JS: `public/js/cart.js`

**Need to change checkout?**
- Controller: `app/Code/Local/Controller/Order.php`
- Model: `app/Code/Local/Model/Order.php`
- View: `app/Views/order/checkout.php`
- CSS: `public/css/checkout.css`
- JS: `public/js/checkout.js`

---

## 🎓 Summary

**EasyCart** is a full-featured e-commerce application built with:
- **Custom MVC framework** (Magento-inspired)
- **PostgreSQL database** for data persistence
- **Clean URL routing** for SEO
- **Guest shopping** support (JSON storage)
- **Modern responsive design**
- **Modular architecture** for easy maintenance

The application separates concerns into:
1. **Routing** - Maps URLs to controllers
2. **Controllers** - Handle requests
3. **Models** - Business logic and data
4. **Views** - HTML templates
5. **Helpers** - Utility functions

This architecture makes it easy to:
- Add new features
- Modify existing functionality
- Maintain code quality
- Scale the application

---

**Created:** February 11, 2026  
**Version:** 1.0  
**Author:** Anuradha
