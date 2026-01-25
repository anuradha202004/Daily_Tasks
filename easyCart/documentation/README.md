# EasyCart - Phase 2: Server-Side Rendering with PHP

## Project Overview
EasyCart Phase 2 is a complete e-commerce platform built with PHP using server-side rendering. It features dynamic product listings, session-based shopping cart, and comprehensive order management.

## Technology Stack
- **Backend**: PHP 7.0+
- **Frontend**: HTML5, CSS3
- **Session Management**: PHP Sessions
- **Data Storage**: Static PHP Arrays (data.php)

## Project Structure

```
easyCart/
├── index.php                 # Home page with featured products
├── products.php              # Product listing page with category filtering
├── product-detail.php        # Individual product detail page
├── cart.php                  # Shopping cart with session management
├── checkout.php              # Checkout form with order summary
├── order-confirmation.php    # Order confirmation page
├── orders.php                # My Orders page with order history
├── data.php                  # Static data (products, categories, brands, orders)
├── header.php                # Shared header component
├── footer.php                # Shared footer component
├── style.css                 # Stylesheet
└── README.md                 # This file
```

## Key Features

### 1. Dynamic Product Listing
- **File**: `products.php`
- **Features**:
  - Display all products dynamically from PHP data
  - Category filtering with active state indication
  - Product count display for each category
  - Responsive grid layout

### 2. Product Detail Page
- **File**: `product-detail.php`
- **Features**:
  - Load product data using product ID from URL parameter
  - Display complete product information (name, price, rating, stock, description)
  - Add to cart with quantity selection
  - Related products from same category
  - Stock availability checks
  - Product-specific emoji icon

### 3. Shopping Cart (Session-Based)
- **File**: `cart.php`
- **Features**:
  - Server-side session storage for cart items
  - Add products with quantity
  - Update product quantities
  - Remove individual items
  - Clear entire cart
  - Real-time subtotal calculation
  - Tax calculation (10%)
  - Shipping cost determination (Free over $50, $9.99 otherwise)
  - Display total price

### 4. Checkout Process
- **File**: `checkout.php`
- **Features**:
  - Personal information form (First name, Last name, Email, Phone)
  - Shipping address form (Address, City, State, Zip)
  - Payment information form (Card number, Expiry, CVV)
  - Terms & conditions acceptance
  - Real-time order summary with all items
  - Order subtotal, tax, and total calculation
  - Order validation before submission

### 5. Order Confirmation
- **File**: `order-confirmation.php`
- **Features**:
  - Display complete order details
  - Show shipping address
  - List all ordered items with prices
  - Display order total and tracking information
  - Customer support information
  - Links to continue shopping or view orders

### 6. My Orders Page
- **File**: `orders.php`
- **Features**:
  - Display static order history from PHP data
  - Show order status (Delivered, Processing)
  - Order summary with items, quantities, and prices
  - Status badges with color coding
  - Tracking information for delivered orders
  - Order date and total display
  - View details and download invoice options

### 7. Static Data Management
- **File**: `data.php`
- **Data Includes**:
  - **12 Products** with full details (name, price, category, brand, stock, rating, emoji)
  - **5 Categories** (Electronics, Fashion, Home & Living, Sports & Outdoors, Books & Media)
  - **5 Brands** (TechPro, StyleMax, HomeComfort, SportZone, MediaHub)
  - **3 Sample Orders** with order history
- **Helper Functions**:
  - `getProductById()` - Get product by ID
  - `getCategoryById()` - Get category details
  - `getBrandById()` - Get brand details
  - `getProductsByCategory()` - Filter products by category
  - `formatPrice()` - Format prices with $ symbol
  - `renderStars()` - Display rating stars

### 8. Shared Components
- **header.php**: Sticky header with navigation, logo, cart link, and orders link
- **footer.php**: Footer with links, social media, and JavaScript utilities
- **Cart counter**: Dynamic display of items in cart

## Data Flow

### Session-Based Cart Flow
```
User Action → POST Request
    ↓
cart.php handles action (add/remove/update/clear)
    ↓
Store/Update $_SESSION['cart'] array
    ↓
Display cart from session data with products from data.php
    ↓
Calculate totals (subtotal, tax, shipping)
    ↓
Render HTML output
```

### Order Processing Flow
```
User at Checkout → Fill form → Submit
    ↓
Validate all required fields
    ↓
Store order in $_SESSION['last_order']
    ↓
Clear cart ($_SESSION['cart'] = [])
    ↓
Redirect to order-confirmation.php
    ↓
Display order confirmation from session data
```

### Product Display Flow
```
User requests products.php
    ↓
Optional category filter from URL parameter
    ↓
Filter products from data.php using getProductsByCategory()
    ↓
Loop through filtered products
    ↓
Generate HTML for each product
    ↓
Display with category info and count
```

## Usage Guide

### Starting the Application
1. Ensure PHP is installed on your system
2. Place all files in a web-accessible directory
3. Start a PHP server:
   ```bash
   php -S localhost:8000
   ```
4. Access at `http://localhost:8000/index.php`

### Adding Products to Cart
1. Browse products on products.php or product-detail.php
2. Select quantity
3. Click "Add to Cart" button
4. Product is stored in PHP session
5. Cart count updates in header

### Viewing Cart
1. Click "Cart" button in header
2. View all cart items with quantities and prices
3. Update quantities or remove items
4. See real-time total calculations

### Checking Out
1. Click "Proceed to Checkout" from cart
2. Fill in personal and shipping information
3. Enter payment details
4. Review order summary
5. Click "Complete Order"
6. View order confirmation

### Viewing Orders
1. Click "Orders" in header
2. View all orders with status and details
3. See tracking information for delivered orders
4. Access order history from static data

## Key PHP Concepts Used

### 1. Sessions
```php
session_start();                    // Initialize session
$_SESSION['cart'][$id] = $item;    // Store in session
unset($_SESSION['cart'][$id]);     // Remove from session
```

### 2. Data Retrieval
```php
$product = getProductById($id);          // Get single product
$products = getProductsByCategory($cat); // Get filtered products
```

### 3. Calculations
```php
$subtotal = $product['price'] * $quantity;
$tax = $subtotal * 0.10;
$total = $subtotal + $tax + $shipping;
```

### 4. Form Handling
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data
    // Validate inputs
    // Update session or redirect
}
```

### 5. URL Parameters
```php
$productId = isset($_GET['id']) ? intval($_GET['id']) : null;
$categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;
```

## Security Features

1. **Input Validation**
   - Checking if POST parameters exist
   - Type casting to integers for IDs
   - Required field validation in checkout

2. **HTML Escaping**
   - Using `htmlspecialchars()` for output
   - Prevents XSS attacks

3. **Session-Based Data**
   - Cart data stored server-side in sessions
   - Not exposed through URLs or cookies

4. **Redirects**
   - Redirect to appropriate pages if data missing
   - Prevent direct access to checkout with empty cart

## File Descriptions

| File | Purpose | Key Functions |
|------|---------|---|
| `index.php` | Home page | Display featured products, features section |
| `products.php` | Product listing | Filter by category, display all products |
| `product-detail.php` | Product detail | Show full product info, handle add to cart |
| `cart.php` | Shopping cart | Display cart items, calculate totals, manage session |
| `checkout.php` | Checkout form | Collect shipping/payment info, create order |
| `order-confirmation.php` | Confirmation | Display order details from session |
| `orders.php` | Order history | Display static order history |
| `data.php` | Data source | Define all static data and helper functions |
| `header.php` | Header component | Navigation, logo, cart/orders links |
| `footer.php` | Footer component | Footer links, JavaScript utilities |
| `style.css` | Stylesheet | All styling for the application |

## Example Product Data Structure
```php
$products[1] = [
    'id' => 1,
    'name' => 'Wireless Headphones',
    'description' => 'Premium wireless headphones with noise cancellation',
    'price' => 89.99,
    'category_id' => 1,
    'brand_id' => 1,
    'stock' => 45,
    'rating' => 4.5,
    'reviews' => 234,
    'emoji' => '🎧'
];
```

## Example Session Cart Structure
```php
$_SESSION['cart'][1] = [
    'product_id' => 1,
    'quantity' => 2
];
```

## Responsive Design
- Mobile-friendly grid layouts
- Flexible navigation
- Responsive product cards
- Mobile-optimized forms

## Future Enhancements (Phase 3+)
1. Database integration (MySQL/PostgreSQL)
2. User authentication system
3. Payment gateway integration (Stripe, PayPal)
4. Email notifications
5. Admin panel for product management
6. Advanced search and filtering
7. Wishlist functionality
8. Product reviews and ratings
9. Inventory management
10. Shipping provider integration

## Browser Support
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Considerations
- Static data loaded at request time
- Session data stored server-side
- Minimal database calls (N/A for Phase 2)
- Optimized CSS with minimal reflows

## Testing Checklist
- [x] Add products to cart
- [x] Update cart quantities
- [x] Remove items from cart
- [x] Calculate correct totals
- [x] Proceed to checkout
- [x] View order confirmation
- [x] Browse product categories
- [x] View product details
- [x] View order history
- [x] Cart persists with sessions
- [x] All forms validate correctly

## Version History
- **Phase 2.0** (Current): Server-side rendering with PHP, session-based cart, static data

## License
This project is created for educational purposes as part of the EasyCart internship project.

## Contact & Support
For questions or support:
- Email: support@easycart.com
- Phone: +1 (555) 123-4567
- Hours: Monday - Friday, 9:00 AM - 6:00 PM
