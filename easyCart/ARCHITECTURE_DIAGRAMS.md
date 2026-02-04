# EasyCart MVC Architecture - Visual Guide

## 📊 Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                      USER REQUEST                            │
│                    (Browser/Client)                          │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                   EXISTING PAGES                             │
│  (index.php, products.php, cart.php, checkout.php, etc.)    │
│                                                              │
│  require_once 'includes/auth.php';                          │
│  require_once 'includes/data.php';                          │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                  INCLUDES LAYER                              │
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  auth.php    │  │  data.php    │  │   db.php     │     │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘     │
│         │                  │                  │              │
│         └──────────────────┴──────────────────┘              │
│                            │                                 │
└────────────────────────────┼─────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│              LEGACY BRIDGE (Compatibility Layer)             │
│                  app/legacy_bridge.php                       │
│                                                              │
│  Maps old functions to new MVC structure:                   │
│  • getProductById() → ProductService->getProduct()          │
│  • loginUser() → AuthService->login()                       │
│  • syncCartToDb() → CartService->syncCartToDb()            │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                   SERVICES LAYER                             │
│              (Business Logic - app/Services/)                │
│                                                              │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐       │
│  │ AuthService  │ │ CartService  │ │OrderService  │       │
│  │              │ │              │ │              │       │
│  │ • login()    │ │ • syncCart() │ │ • create()   │       │
│  │ • register() │ │ • loadCart() │ │ • getOrders()│       │
│  │ • logout()   │ │ • calculate()│ │              │       │
│  └──────┬───────┘ └──────┬───────┘ └──────┬───────┘       │
│         │                 │                 │                │
│  ┌──────────────┐ ┌──────────────┐                         │
│  │ProductService│ │WishlistSvc   │                         │
│  │              │ │              │                         │
│  │ • getAll()   │ │ • load()     │                         │
│  │ • search()   │ │ • save()     │                         │
│  └──────┬───────┘ └──────────────┘                         │
└─────────┼──────────────────────────────────────────────────┘
          │
          ▼
┌─────────────────────────────────────────────────────────────┐
│                    MODELS LAYER                              │
│              (Database Access - app/Models/)                 │
│                                                              │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐       │
│  │   Product    │ │     User     │ │   Category   │       │
│  │              │ │              │ │              │       │
│  │ • find()     │ │ • findBy     │ │ • getAll()   │       │
│  │ • getAll()   │ │   Email()    │ │ • find()     │       │
│  │ • search()   │ │ • create()   │ │              │       │
│  └──────┬───────┘ └──────┬───────┘ └──────┬───────┘       │
│         │                 │                 │                │
│  ┌──────────────┐ ┌──────────────┐                         │
│  │     Cart     │ │    Order     │                         │
│  │              │ │              │                         │
│  │ • getActive()│ │ • create()   │                         │
│  │ • addItem()  │ │ • addItem()  │                         │
│  └──────┬───────┘ └──────┬───────┘                         │
│         │                 │                                  │
│         └─────────────────┴──────────────────┐              │
└──────────────────────────────────────────────┼──────────────┘
                                               │
                                               ▼
┌─────────────────────────────────────────────────────────────┐
│                      DATABASE                                │
│                   PostgreSQL (easycart)                      │
│                                                              │
│  Tables:                                                     │
│  • catalog_product_entity, catalog_product_attribute        │
│  • catalog_category_entity, catalog_category_attribute      │
│  • sales_cart, sales_cart_products                          │
│  • sales_order, sales_order_products, sales_order_address   │
│  • users                                                     │
└─────────────────────────────────────────────────────────────┘
```

## 🔄 Request Flow Example

### Example: User Views Product Details

```
1. Browser Request
   GET /product-detail.php?id=5
   
2. Page File (product-detail.php)
   require_once 'includes/data.php';
   $product = getProductById(5);
   
3. Includes Layer (includes/data.php)
   require_once '../app/legacy_bridge.php';
   
4. Legacy Bridge (app/legacy_bridge.php)
   function getProductById($id) {
       global $products;
       return $products[$id] ?? null;
   }
   // $products loaded via ProductService
   
5. Service Layer (Services/ProductService.php)
   public function getProduct($id) {
       return $this->productModel->getProductWithAttributes($id);
   }
   
6. Model Layer (Models/Product.php)
   public function getProductWithAttributes($id) {
       $query = "SELECT ... FROM catalog_product_entity ...";
       return $this->query($query, [$id]);
   }
   
7. Database
   Execute SQL query
   Return product data
   
8. Response Flow (back up the chain)
   Database → Model → Service → Bridge → Page → Browser
```

## 📦 Component Interaction

```
┌─────────────────────────────────────────────────────────────┐
│                    COMPONENT LAYERS                          │
└─────────────────────────────────────────────────────────────┘

PRESENTATION LAYER (What user sees)
├── index.php
├── products.php
├── cart.php
├── checkout.php
└── ... (other pages)
    │
    ├── Uses: includes/auth.php, includes/data.php
    │
    └─────────────────────────────────────────────────────┐
                                                           │
COMPATIBILITY LAYER (Bridges old & new)                   │
├── includes/auth.php ──────────────────────┐             │
├── includes/data.php ──────────────────────┼─────────────┘
└── includes/db.php ────────────────────────┤
    │                                        │
    └──► app/legacy_bridge.php ◄────────────┘
            │
            ├── Initializes Services
            └── Maps old functions to new methods
                │
                └─────────────────────────────────────────┐
                                                          │
BUSINESS LOGIC LAYER (What happens)                      │
├── Services/AuthService.php ◄────────────────────────────┤
├── Services/CartService.php                              │
├── Services/OrderService.php                             │
├── Services/ProductService.php                           │
└── Services/WishlistService.php                          │
    │                                                      │
    ├── Validates data                                    │
    ├── Implements business rules                         │
    └── Calls Models for data                             │
        │                                                  │
        └─────────────────────────────────────────────────┘
                                                          │
DATA ACCESS LAYER (How data is stored)                   │
├── Models/Product.php ◄──────────────────────────────────┤
├── Models/User.php                                       │
├── Models/Category.php                                   │
├── Models/Cart.php                                       │
└── Models/Order.php                                      │
    │                                                      │
    ├── Executes SQL queries                              │
    ├── Returns data objects                              │
    └── Handles transactions                              │
        │                                                  │
        ▼                                                  │
    DATABASE (PostgreSQL)                                 │
```

## 🎯 Data Flow Patterns

### Pattern 1: Read Operation (Get Products)
```
Page → Include → Bridge → Service → Model → Database
                                              ↓
Page ← Include ← Bridge ← Service ← Model ← Data
```

### Pattern 2: Write Operation (Create Order)
```
Page → Include → Bridge → Service → Model → Database
  ↓                          ↓        ↓         ↓
Validate                  Business  Insert   Commit
  ↓                        Rules     Data    Transaction
Success ← ← ← ← ← ← ← ← ← ← ← ← ← ← ← ← ← ← ←
```

### Pattern 3: Authentication Flow
```
Login Page → AuthService → User Model → Database
    ↓            ↓            ↓            ↓
  Form       Validate     Check DB    Verify
    ↓            ↓            ↓            ↓
Session ← ← Success ← ← Found ← ← Match
```

## 🔐 Separation of Concerns

```
┌─────────────────────────────────────────────────────────┐
│ PRESENTATION (Pages)                                     │
│ • Display data                                          │
│ • Handle user input                                     │
│ • Render HTML                                           │
└─────────────────────────────────────────────────────────┘
                         ↕
┌─────────────────────────────────────────────────────────┐
│ BUSINESS LOGIC (Services)                               │
│ • Validate input                                        │
│ • Apply business rules                                  │
│ • Coordinate operations                                 │
└─────────────────────────────────────────────────────────┘
                         ↕
┌─────────────────────────────────────────────────────────┐
│ DATA ACCESS (Models)                                    │
│ • Execute queries                                       │
│ • Map to objects                                        │
│ • Handle transactions                                   │
└─────────────────────────────────────────────────────────┘
                         ↕
┌─────────────────────────────────────────────────────────┐
│ DATABASE (PostgreSQL)                                   │
│ • Store data                                            │
│ • Ensure integrity                                      │
│ • Handle concurrency                                    │
└─────────────────────────────────────────────────────────┘
```

## 📁 File Organization

```
easyCart/
│
├── 🎨 PRESENTATION
│   ├── index.php
│   ├── products.php
│   ├── cart.php
│   └── checkout.php
│
├── 🔧 APPLICATION CORE
│   └── app/
│       ├── bootstrap.php        (Initialization)
│       ├── helpers.php          (Utilities)
│       └── legacy_bridge.php    (Compatibility)
│
├── 🧩 BUSINESS LOGIC
│   └── app/Services/
│       ├── AuthService.php
│       ├── CartService.php
│       ├── OrderService.php
│       ├── ProductService.php
│       └── WishlistService.php
│
├── 💾 DATA ACCESS
│   └── app/Models/
│       ├── Model.php (Base)
│       ├── Product.php
│       ├── User.php
│       ├── Category.php
│       ├── Cart.php
│       └── Order.php
│
├── ⚙️ CONFIGURATION
│   └── config/
│       └── database/
│           └── config.php
│
├── 📦 STORAGE
│   └── storage/
│       ├── logs/
│       ├── cache/
│       └── wishlist/
│
└── 📚 DOCUMENTATION
    ├── MVC_ARCHITECTURE.md
    ├── MIGRATION_GUIDE.md
    ├── QUICK_REFERENCE.md
    ├── RESTRUCTURING_SUMMARY.md
    └── ARCHITECTURE_DIAGRAMS.md (this file)
```

## 🎓 Understanding the Flow

### Simple Example: Login

```
┌──────────────┐
│ User enters  │
│ credentials  │
└──────┬───────┘
       │
       ▼
┌──────────────────────────────┐
│ signin.php                   │
│ • Displays form              │
│ • Receives POST data         │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│ includes/auth.php            │
│ • require legacy_bridge.php  │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│ legacy_bridge.php            │
│ • loginUser($email, $pass)   │
│ • Calls AuthService          │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│ AuthService                  │
│ • Validates input            │
│ • Calls User model           │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│ User Model                   │
│ • Queries database           │
│ • Verifies credentials       │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│ Database                     │
│ • Returns user data          │
└──────┬───────────────────────┘
       │
       ▼ (Success)
┌──────────────────────────────┐
│ Set Session                  │
│ • $_SESSION['user_id']       │
│ • $_SESSION['user_email']    │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│ Redirect to products.php     │
└──────────────────────────────┘
```

---

This visual guide helps you understand how all the pieces fit together in the new MVC architecture!
