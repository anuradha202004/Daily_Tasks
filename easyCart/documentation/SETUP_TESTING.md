# EasyCart Phase 2 - Setup & Testing Guide

## Quick Start

### Prerequisites
- PHP 7.0 or higher installed
- Command line access
- Web browser

### Installation Steps

1. **Navigate to project directory**
   ```bash
   cd c:\Users\Anuradha\OneDrive\Desktop\Internship\easyCart
   ```

2. **Start PHP Built-in Server**
   ```bash
   php -S localhost:8000
   ```

3. **Access the application**
   - Open browser and go to: `http://localhost:8000`
   - Or directly to: `http://localhost:8000/index.php`

## File Structure Verification

All required files should be present:
- ✅ `index.php` - Home page
- ✅ `products.php` - Product listing with category filter
- ✅ `product-detail.php` - Individual product page
- ✅ `cart.php` - Shopping cart
- ✅ `checkout.php` - Checkout form
- ✅ `order-confirmation.php` - Order confirmation
- ✅ `orders.php` - Order history (My Orders)
- ✅ `data.php` - Static data and helper functions
- ✅ `header.php` - Shared header component
- ✅ `footer.php` - Shared footer component
- ✅ `style.css` - Stylesheet
- ✅ `README.md` - Documentation

## Testing Scenarios

### Test 1: Browse Products
1. Go to `http://localhost:8000/index.php`
2. Click "Products" link or "Shop Now" button
3. Verify 12 products are displayed
4. Click category filters (Electronics, Fashion, etc.)
5. Verify only products in that category show
6. **Expected**: Products filter correctly by category

### Test 2: Add Product to Cart
1. From products page, click "View Details" on any product
2. Verify product details page loads
3. Enter a quantity (1-5)
4. Click "Add to Cart" button
5. Verify cart count updates in header
6. **Expected**: Cart count increases by 1

### Test 3: Multiple Products in Cart
1. Go back to products page
2. Select 3 different products and add each to cart
3. Notice cart count shows 3
4. Click "Cart" link in header
5. **Expected**: All 3 products show in cart with correct quantities

### Test 4: Update Cart Quantities
1. From cart page, change quantity of a product to 5
2. Click "Update" button
3. Verify new quantity shows immediately
4. Verify subtotal recalculates
5. **Expected**: Quantity updates and totals recalculate correctly

### Test 5: Remove Items from Cart
1. From cart page, click "Remove" on any item
2. Verify item disappears from cart
3. Verify cart count in header updates
4. Verify totals recalculate
5. **Expected**: Item removed and totals update

### Test 6: Cart Totals Calculation
1. Add product costing $89.99 (Qty: 1) to cart
2. Add product costing $29.99 (Qty: 2) to cart
3. Go to cart page
4. **Expected**:
   - Subtotal: $149.97
   - Tax (10%): $14.997 (~$15.00)
   - Shipping: $9.99 (subtotal < $50)
   - Total: ~$179.96

### Test 7: Free Shipping Over $50
1. Add 5 items with total > $50 to cart
2. Go to cart page
3. Check shipping line
4. **Expected**: Shipping shows as "Free"

### Test 8: Proceed to Checkout
1. With items in cart, click "Proceed to Checkout"
2. Verify checkout form loads
3. Verify all form fields are empty and required
4. Verify order summary shows all items
5. **Expected**: Checkout page displays correctly

### Test 9: Checkout Form Validation
1. On checkout page, click "Complete Order" without filling form
2. **Expected**: Validation message appears

### Test 10: Complete Order
1. Fill in all checkout form fields:
   - First Name: John
   - Last Name: Doe
   - Email: john@example.com
   - Phone: 555-1234567
   - Address: 123 Main St
   - City: New York
   - State: NY
   - Zip: 10001
   - Card Number: 4111111111111111
   - Expiry: 12/25
   - CVV: 123
2. Check "I agree to Terms & Conditions"
3. Click "Complete Order"
4. **Expected**: Redirected to order confirmation page

### Test 11: Order Confirmation
1. After completing order, verify confirmation page shows:
   - Shipping address
   - All ordered items with quantities
   - Order total breakdown
   - Order confirmation message
2. Click "View My Orders"
3. **Expected**: Taken to orders page with order history

### Test 12: Category Filtering
1. Go to products page
2. Click on different categories:
   - Electronics (should show Headphones, Smart Watch, etc.)
   - Fashion (should show Running Shoes, Handbag, etc.)
   - Home & Living (should show Coffee Maker, Lamp, etc.)
3. **Expected**: Products filter correctly, count displays

### Test 13: Product Detail Page
1. From products page, click any product
2. Verify product details load correctly:
   - Product name
   - Price
   - Rating and reviews
   - Stock status
   - Category and brand
   - Full description
   - Related products section
3. **Expected**: All product info displays correctly

### Test 14: Product Stock Check
1. Find product with low stock (Desk Lamp has 75, Table Clock has 75)
2. Click View Details
3. Try to add quantity exceeding stock
4. **Expected**: Quantity input limited to stock amount

### Test 15: My Orders
1. Click "Orders" link in header
2. Verify order history displays
3. Check 3 sample orders show with:
   - Order ID (#ORD-001, etc.)
   - Date
   - Status (Delivered/Processing)
   - Items ordered
   - Total price
   - Tracking info (if delivered)
4. **Expected**: All order information displays correctly

### Test 16: Session Persistence
1. Add items to cart
2. Go to different pages (products, home, orders)
3. Come back to cart
4. **Expected**: Cart items still there

### Test 17: Clear Cart
1. From cart page, click "Clear Cart" button
2. **Expected**: Cart empties completely, shows empty state

### Test 18: Empty Cart State
1. Clear cart or start fresh
2. Click "Cart" link
3. **Expected**: Empty cart message displays with "Start Shopping" link

### Test 19: Navigation
1. Test all header links work:
   - Logo → Home
   - Home link → Home
   - Products link → Products
   - About link → About section on home
   - Contact link → Contact section on home
   - Cart link → Cart page
   - Orders link → Orders page
2. **Expected**: All navigation works correctly

### Test 20: Responsive Design
1. Open app on different screen sizes:
   - Desktop (1920x1080)
   - Tablet (768x1024)
   - Mobile (375x667)
2. Verify layout adapts
3. **Expected**: Layout responsive and readable

## Performance Checks

- Page load time for home page: < 500ms
- Product page load: < 300ms
- Add to cart response: Immediate
- Cart calculations: < 100ms

## Data Verification

### Products Count: 12
```
1. Wireless Headphones ($89.99) - Electronics
2. Smart Watch ($199.99) - Electronics
3. Running Shoes ($79.99) - Fashion
4. Designer Handbag ($149.99) - Fashion
5. Coffee Maker ($69.99) - Home & Living
6. Yoga Mat ($29.99) - Sports & Outdoors
7. Bluetooth Speaker ($59.99) - Electronics
8. Novel Collection ($24.99) - Books & Media
9. Desk Lamp ($39.99) - Home & Living
10. Backpack ($54.99) - Fashion
11. Fitness Tracker ($49.99) - Electronics
12. Table Clock ($19.99) - Home & Living
```

### Categories: 5
- Electronics
- Fashion
- Home & Living
- Sports & Outdoors
- Books & Media

### Brands: 5
- TechPro
- StyleMax
- HomeComfort
- SportZone
- MediaHub

### Orders: 3
- Order #ORD-001: $149.97 (Delivered)
- Order #ORD-002: $79.99 (Delivered)
- Order #ORD-003: $219.98 (Processing)

## Debugging

### Issue: Cart not updating
- Check browser console for JavaScript errors
- Verify sessions are enabled in PHP
- Ensure `session_start()` is called in each file

### Issue: Products not displaying
- Verify `data.php` is in same directory
- Check PHP error logs
- Ensure array structure matches

### Issue: Page not found
- Verify URL is correct (lowercase .php)
- Check file names match exactly
- Ensure all files uploaded to server

### Issue: Session not persisting
- Verify PHP session.save_path is writable
- Check PHP error logs for session warnings
- Browser may have cookies disabled

## FAQ

**Q: How do I add more products?**
A: Edit `data.php` and add entries to the `$products` array with ID, name, price, etc.

**Q: How do I change product prices?**
A: Edit the `price` field in `data.php` for the specific product.

**Q: Can I change the number of products displayed?**
A: Yes, edit the `array_slice()` calls in `index.php` and `products.php` to change how many products show.

**Q: How do I add new categories?**
A: Add entries to the `$categories` array in `data.php`.

**Q: Are orders saved to a database?**
A: No, Phase 2 uses static data. Phase 3 will include database integration.

## Deployment Notes

### For local testing:
```bash
php -S localhost:8000
```

### For online deployment:
1. Upload all files to web host
2. Ensure PHP 7.0+ is installed
3. Enable sessions in php.ini
4. Set proper file permissions (644 for files, 755 for directories)
5. Test all functionality on live server

## Troubleshooting Checklist

- [ ] PHP server is running
- [ ] All files uploaded/present
- [ ] File permissions are correct
- [ ] Sessions are enabled
- [ ] No PHP errors in logs
- [ ] Browser has cookies enabled
- [ ] JavaScript is enabled
- [ ] CSS loading properly
- [ ] All links working
- [ ] Forms submitting correctly

## Next Steps

After Phase 2 completion:
1. Database integration (MySQL)
2. User authentication
3. Payment gateway integration
4. Email notifications
5. Admin panel
6. Advanced features

---

**Last Updated**: January 22, 2026
**Phase**: 2.0 (Server-Side Rendering with PHP)
