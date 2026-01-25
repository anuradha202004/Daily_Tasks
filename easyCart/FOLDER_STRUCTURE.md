# EasyCart - Folder Structure

```
easyCart/
│
├── 📄 Main Pages (Root Level)
│   ├── index.php                    # Home page
│   ├── products.php                 # Products listing page
│   ├── product-detail.php           # Product details page
│   ├── search-results.php           # Search results page
│   ├── cart.php                     # Shopping cart page
│   ├── checkout.php                 # Checkout page
│   ├── order-confirmation.php       # Order confirmation page
│   ├── orders.php                   # My Orders page
│   ├── track-order.php              # Track order page
│   ├── wishlist.php                 # Wishlist page
│   ├── profile.php                  # User profile page
│   ├── signin.php                   # Sign in page
│   ├── signup.php                   # Sign up page
│   ├── logout.php                   # Logout handler
│   └── index.html                   # Static index (if needed)
│
├── 📁 includes/ (Core Components & Functions)
│   ├── header.php                   # Header component (included in all pages)
│   ├── footer.php                   # Footer component (included in all pages)
│   ├── auth.php                     # Authentication & user management functions
│   └── data.php                     # Products data & helper functions
│
├── 📁 css/ (Stylesheets)
│   └── style.css                    # Main stylesheet (all page styles)
│
├── 📁 js/ (JavaScript)
│   ├── cart.js                      # Cart interactions (when needed)
│   ├── validation.js                # Form validations (when needed)
│   ├── wishlist.js                  # Wishlist interactions (when needed)
│   └── common.js                    # Common functions (when needed)
│
├── 📁 data/ (JSON Data Storage)
│   ├── users.json                   # Registered user accounts
│   ├── cart_[email_hash].json       # Individual user cart data
│   ├── wishlist_[email_hash].json   # Individual user wishlist data
│   └── 📁 orders/
│       └── order_[id].json          # Order records
│
├── 📁 documentation/ (Project Documentation)
│   ├── README.md                    # Project overview
│   ├── AUTH_DOCUMENTATION.md        # Authentication system docs
│   ├── AUTH_QUICKSTART.md           # Quick start guide
│   ├── CART_UPDATE.md               # Cart feature documentation
│   ├── IMPLEMENTATION_COMPLETE.md   # Implementation status
│   ├── QUICK_REFERENCE.md           # Quick reference guide
│   └── SETUP_TESTING.md             # Setup & testing guide
│
├── 📁 assets/ (Static Assets)
│   └── images/                      # Product images (optional)
│
├── 📁 (Original Files - For Reference)
│   ├── auth.php (old)
│   ├── data.php (old)
│   ├── header.php (old)
│   ├── footer.php (old)
│   └── style.css (old)
│
└── 📄 Configuration Files
    ├── FOLDER_STRUCTURE.md          # This file
    └── .gitignore                   # Git ignore (optional)
```

## File Organization Summary

### Root Level
- **Main pages**: All user-facing PHP pages that handle routing and display
- **Configuration**: Settings and structure documentation

### includes/
- **header.php**: Navigation bar, logo, search, cart/wishlist icons
- **footer.php**: Footer content, links, and helper scripts
- **auth.php**: Login, signup, session management, user validation
- **data.php**: Product catalog, categories, brands, helper functions

### css/
- **style.css**: All styling for header, pages, forms, modals, responsive design

### js/
- **Reserved for future**: JavaScript files for enhanced interactivity
- Currently using inline scripts in PHP files
- Can be extracted and organized here as project grows

### data/
- **users.json**: User account database
- **cart_*.json**: Individual user shopping carts (email hash as filename)
- **wishlist_*.json**: Individual user wishlists (email hash as filename)
- **orders/**: Order records organized by order ID

### documentation/
- **All project documentation** consolidated in one place
- Easy to find guides, implementation status, and references

### assets/
- **Reserved for media files**
- Currently empty; can be used for product images

## Include Path Examples

### From Root Level Pages
```php
require_once 'includes/auth.php';
require_once 'includes/data.php';
include 'includes/header.php';
include 'includes/footer.php';
```

### From includes/header.php
```php
require_once __DIR__ . '/auth.php';
<link rel="stylesheet" href="css/style.css">
```

### From includes/auth.php
```php
$usersDataFile = __DIR__ . '/../data/users.json';
require_once __DIR__ . '/data.php';
```

## Migration Status ✅

- ✅ Style.css → css/style.css
- ✅ header.php → includes/header.php
- ✅ footer.php → includes/footer.php
- ✅ auth.php → includes/auth.php
- ✅ data.php → includes/data.php
- ✅ All markdown files → documentation/
- ✅ All require/include paths updated in root pages
- ✅ Directory structure created (js/, assets/, data/orders/)
- ✅ data/ folder already has user, cart, wishlist JSON files

## Next Steps

1. **Optional**: Extract inline JavaScript from PHP files into js/ folder
2. **Optional**: Add product images to assets/images/
3. **Optional**: Create .gitignore to exclude data/ folder from version control
4. **Optional**: Remove old files from root level (auth.php, data.php, etc.)

This clean structure makes the project:
- **Maintainable**: Each file type in its own folder
- **Scalable**: Easy to add new pages and components
- **Professional**: Industry-standard organization
- **Secure**: Data files separated from public pages
