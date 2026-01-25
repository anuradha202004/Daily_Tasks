# EasyCart Authentication - Quick Start Guide

## ✅ What's New (Phase 2.1)

### Authentication System Added
- ✅ Sign-up page (`signup.php`)
- ✅ Sign-in page (`signin.php`)
- ✅ User profile page (`profile.php`)
- ✅ Authentication core (`auth.php`)
- ✅ Logout functionality (`logout.php`)

### Behavior Changes
- ✅ Users can browse products **WITHOUT login**
- ✅ Users **MUST login** to add items to cart
- ✅ Users **MUST login** to checkout
- ✅ Users **MUST login** to view orders
- ✅ Header shows different buttons for logged-in vs guest users

## 🚀 Getting Started

### 1. Start PHP Server
```bash
cd c:\Users\Anuradha\OneDrive\Desktop\Internship\easyCart
php -S localhost:8000
```

### 2. Visit Home Page
```
http://localhost:8000
```

## 👤 User Accounts

### Demo Account (Pre-made for testing)
```
Email:    demo@example.com
Password: password123
```

### Create New Account
1. Click "Sign Up" button in header
2. Fill in details
3. Create account
4. Automatically logged in

## 🎯 User Flows

### Flow 1: Browse as Guest
```
Home Page → Products → View Details → Read Info (✅ Can do)
Try Add to Cart → See "Sign in required" message (❌ Cannot do)
Click Sign In → Sign in or Create Account
Add to Cart → Success ✅
```

### Flow 2: Sign Up New User
```
Home → Sign Up Button → Fill Form → Create Account
✅ Automatically logged in
✅ Redirected to products page
✅ Cart and Orders buttons visible
✅ Can now add to cart and checkout
```

### Flow 3: Sign In Existing User
```
Home → Sign In Button → Enter Credentials → Sign In
✅ Logged in successfully
✅ Redirected to products page
✅ User name shows in header
✅ Can now shop
```

### Flow 4: Shop After Login
```
Logged In → Browse Products → View Details
Add to Cart → See Success Message ✅ → Cart Count Updates ✅
View Cart → Update Quantities → Proceed to Checkout
Fill Checkout Form → Place Order → See Confirmation
```

## 🔑 Key Features

### Header Changes (Guest Users)
```
[EasyCart] [Home] [Products] [About] [Contact] [Sign In] [Sign Up]
```

### Header Changes (Logged-In Users)
```
[EasyCart] [Home] [Products] [About] [Contact] [Cart] [Orders] [Demo User ▼]
```

### User Menu Dropdown
```
👤 My Profile
📦 My Orders
────────────
🚪 Logout
```

## 📝 Demo Walkthrough

### Step 1: Test Guest Browsing
1. Open browser, go to `http://localhost:8000`
2. Click "Products" - see all products ✅
3. Click on any product "View Details" - see product info ✅
4. See message: "Sign in to add items to your cart" ✅
5. Two buttons: "Sign In" and "Create Account" ✅

### Step 2: Test Sign Up
1. Click "Create Account" button
2. Fill form:
   - Name: John Doe
   - Email: john@example.com
   - Password: secure123
   - Confirm: secure123
3. Check "I agree to Terms"
4. Click "Create Account"
5. ✅ Logged in, see username in header
6. ✅ Can now add to cart

### Step 3: Test Add to Cart
1. From products page, view product detail
2. Enter quantity (e.g., 2)
3. Click "Add to Cart"
4. ✅ Success message appears
5. ✅ Cart count updates in header
6. Click "Cart" link to view cart

### Step 4: Test Checkout
1. From cart page, click "Proceed to Checkout"
2. ✅ Checkout form appears
3. Fill form and click "Complete Order"
4. ✅ Order confirmation page shows

### Step 5: Test My Orders
1. Click "Orders" in header
2. ✅ See order history
3. Click "My Profile" (from user menu)
4. ✅ See account stats and recent orders

### Step 6: Test Logout
1. Click on username (dropdown)
2. Click "Logout"
3. ✅ Logged out
4. ✅ See "Sign In" and "Sign Up" buttons
5. ✅ Cart and Orders links gone

## 🔐 Protected Pages

These pages REQUIRE login:
- `cart.php` - Will redirect to signin if not logged in
- `checkout.php` - Will redirect to signin if not logged in
- `orders.php` - Will redirect to signin if not logged in
- `profile.php` - Will redirect to signin if not logged in

## 📱 Session Variables

When user logs in, sessions are created:
```php
$_SESSION['user_id']       // User ID hash
$_SESSION['user_email']    // Email address
$_SESSION['user_name']     // Full name
$_SESSION['login_time']    // Login timestamp
```

## ✨ Quick Commands for Testing

### Test 1: Visit Public Pages (No Login)
```
✅ http://localhost:8000              (Home)
✅ http://localhost:8000/products.php (Products)
✅ http://localhost:8000/product-detail.php?id=1 (Product Detail)
```

### Test 2: Try Protected Pages (Will Redirect)
```
❌ http://localhost:8000/cart.php              → Redirects to signin
❌ http://localhost:8000/checkout.php          → Redirects to signin
❌ http://localhost:8000/orders.php            → Redirects to signin
❌ http://localhost:8000/profile.php           → Redirects to signin
```

### Test 3: Authentication Pages
```
✅ http://localhost:8000/signin.php   (Sign In)
✅ http://localhost:8000/signup.php   (Sign Up)
✅ http://localhost:8000/logout.php   (Logout - redirects home)
```

## 🎨 UI Updates

### Before (Phase 2)
```
Header: [Home] [Products] [About] [Contact] [Cart] [Orders]
```

### After (Phase 2.1)
```
For Guests:
Header: [Home] [Products] [About] [Contact] [Sign In] [Sign Up]

For Logged In:
Header: [Home] [Products] [About] [Contact] [Cart] [Orders] [Username ▼]
  └─ Username Menu:
     - 👤 My Profile
     - 📦 My Orders
     - 🚪 Logout
```

## 📊 Current Status

| Feature | Status |
|---------|--------|
| Guest Browsing | ✅ Complete |
| Sign Up | ✅ Complete |
| Sign In | ✅ Complete |
| User Profile | ✅ Complete |
| Session Management | ✅ Complete |
| Protected Pages | ✅ Complete |
| Login-Required Shopping | ✅ Complete |
| Header Updates | ✅ Complete |
| Product Detail Login Check | ✅ Complete |
| Logout | ✅ Complete |

## 🚨 Important Notes

1. **Demo Account**: Use `demo@example.com` / `password123` for testing
2. **New Accounts**: Created on signup page, stored in PHP memory (Phase 3: Database)
3. **Sessions**: Cleared when user logs out or browser closes
4. **Password Security**: Plain text in Phase 2 (Phase 3: Will be hashed)
5. **Email Verification**: Not implemented (Phase 3: Will be added)

## 🐛 Troubleshooting

### "Sign In button not appearing"
- **Solution**: Clear browser cache, refresh page

### "Can't add to cart after sign in"
- **Solution**: Make sure PHP session is enabled, try logging in again

### "Login page appears but won't redirect"
- **Solution**: Check browser cookies are enabled

### "Can't create account"
- **Solution**: Use a new email, try refreshing the page

## 📞 Help

For issues or questions:
1. Check browser console (F12) for JavaScript errors
2. Check PHP server logs
3. Try logging in with demo account
4. Clear all cookies and try again
5. Restart PHP server

## 📚 Documentation Files

- `README.md` - Main documentation
- `SETUP_TESTING.md` - Setup and testing guide
- `QUICK_REFERENCE.md` - Quick reference for developers
- `AUTH_DOCUMENTATION.md` - Detailed auth system documentation

## 🎯 Next Steps

### To Test Everything:
1. ✅ Browse as guest
2. ✅ Create new account
3. ✅ Add items to cart
4. ✅ Proceed to checkout
5. ✅ Place order
6. ✅ View order history
7. ✅ Logout and login again

### Phase 3 Roadmap:
- 🔲 Move user data to database
- 🔲 Hash passwords securely
- 🔲 Email verification
- 🔲 Forgot password
- 🔲 Admin panel
- 🔲 Payment gateway

---

**Version**: 2.1 (Authentication Added)
**Date**: January 22, 2026
**Status**: ✅ Ready for Testing
