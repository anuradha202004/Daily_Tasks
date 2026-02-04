# EasyCart Phase 2.1 - Authentication System Documentation

## Overview

Phase 2.1 adds a complete authentication system to EasyCart that allows:
- ✅ Free browsing of products without login
- ✅ Sign-up and Sign-in functionality
- ✅ Login-required shopping features (Add to Cart, Checkout, Orders)
- ✅ User profile management
- ✅ Session-based user management

## New Files Created

### 1. `auth.php` - Authentication Core
Handles all authentication logic including:
- User registration (sign-up)
- User login verification
- Session management
- Login status checking
- User data retrieval

**Key Functions:**
```php
isLoggedIn()              // Check if user is logged in
getCurrentUser()          // Get current user info
registerUser()            // Register new user
loginUser()               // Login user
logoutUser()              // Logout user
requireLogin()            // Require login middleware
```

### 2. `signin.php` - Sign In Page
User login interface with:
- Email and password fields
- "Remember Me" option
- Demo account information
- Link to sign-up page
- Error handling and validation

### 3. `signup.php` - Sign Up Page
User registration interface with:
- Full name, email, password fields
- Password confirmation
- Terms & Conditions checkbox
- Email validation
- Benefits section

### 4. `logout.php` - Logout Handler
Simple logout handler that:
- Clears all session data
- Destroys user session
- Redirects to home page

### 5. `profile.php` - User Profile Page
User dashboard displaying:
- Account information
- Shopping statistics
- Recent orders preview
- Quick action links
- Help section

## Updated Files

### `header.php` - Enhanced Header
Now includes:
- Login/Signup buttons for guest users
- User menu dropdown for logged-in users
- Dynamic cart and orders links (only for logged-in users)
- Login-required modal notification
- Responsive user menu

### `product-detail.php` - Product Page
Updated with:
- Check for login before adding to cart
- Show sign-in/sign-up prompts for non-logged-in users
- Disabled add-to-cart button with explanation
- Redirect to signin page if trying to add without login

### `cart.php` - Cart Page
Updated with:
- Login requirement via `requireLogin()`
- Redirects to signin page if accessing without login

### `checkout.php` - Checkout Page
Updated with:
- Login requirement via `requireLogin()`
- Prevents order placement without authentication

### `orders.php` - Orders Page
Updated with:
- Login requirement via `requireLogin()`
- Shows only logged-in user's orders

## Static User Data

**Demo Account** (for testing):
```
Email: demo@example.com
Password: password123
```

## User Registration Data Structure

```php
$registeredUsers = [
    'email@example.com' => [
        'email' => 'email@example.com',
        'password' => 'hashed_password',  // Plain text in Phase 2, will be hashed in Phase 3
        'name' => 'User Full Name',
        'created' => '2026-01-22 10:30:00'
    ]
];
```

## Session Variables

### User Session Structure
```php
$_SESSION['user_id']       // MD5 hash of email
$_SESSION['user_email']    // User email address
$_SESSION['user_name']     // User full name
$_SESSION['login_time']    // Login timestamp

// Example:
[
    'user_id' => 'abc123def456',
    'user_email' => 'demo@example.com',
    'user_name' => 'Demo User',
    'login_time' => '2026-01-22 10:30:00'
]
```

### Post-Login Redirect
```php
$_SESSION['redirect_after_login']  // URL to redirect to after login
```

## Authentication Flow

### Sign-Up Flow
```
User visits signup.php
    ↓
Fill sign-up form (Name, Email, Password, Confirm Password)
    ↓
Submit form (POST to signup.php)
    ↓
validate input (email format, password strength, matching passwords, agreement)
    ↓
Check if email already registered
    ↓
If valid: Create session and store user data
         Set $_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name']
    ↓
Redirect to products.php
    ↓
User is now logged in
```

### Sign-In Flow
```
User visits signin.php
    ↓
Enter email and password
    ↓
Submit form (POST to signin.php)
    ↓
Verify email exists
    ↓
Verify password matches
    ↓
If valid: Create session with user data
    ↓
Check for redirect URL in session
    ↓
If exists: Redirect to that URL
If not: Redirect to products.php
    ↓
User is now logged in
```

### Add to Cart (Logged-Out User) Flow
```
Logged-out user views product
    ↓
See "Sign in to add items" message
    ↓
Click "Add to Cart" button redirects to signin.php
    ↓
Form action: POST to product-detail.php?id=1
    ↓
Redirect to signin.php?login_required=1
    ↓
Show login-required modal notification
    ↓
User signs in or creates account
    ↓
Redirected back to product-detail.php
    ↓
Can now add to cart
```

### Checkout (Must Be Logged In)
```
User accesses checkout.php
    ↓
Check if logged in via requireLogin()
    ↓
If not logged in: Redirect to signin.php
    ↓
If logged in: Show checkout form
    ↓
Complete order
    ↓
Clear cart and show confirmation
```

### View Orders (Must Be Logged In)
```
User accesses orders.php
    ↓
Check if logged in via requireLogin()
    ↓
If not logged in: Redirect to signin.php
    ↓
If logged in: Show user's order history
```

## Page Access Rules

| Page | Guest Access | Requires Login |
|------|------|---|
| index.php | ✅ Yes | ❌ No |
| products.php | ✅ Yes | ❌ No |
| product-detail.php | ✅ View Only | ⚠️ Add to cart needs login |
| cart.php | ❌ No | ✅ Yes |
| checkout.php | ❌ No | ✅ Yes |
| order-confirmation.php | ❌ No | ✅ Yes |
| orders.php | ❌ No | ✅ Yes |
| profile.php | ❌ No | ✅ Yes |
| signin.php | ✅ Yes | ❌ Redirects if already logged in |
| signup.php | ✅ Yes | ❌ Redirects if already logged in |
| logout.php | ✅ Yes | ❌ Works for anyone |

## User Interface Changes

### Header Changes
**For Guest Users:**
- Shows "Sign In" button
- Shows "Sign Up" button
- No Cart link
- No Orders link

**For Logged-In Users:**
- Shows user name with dropdown menu
- Shows Cart link with count
- Shows Orders link
- Dropdown menu: Profile, My Orders, Logout

### Product Detail Page Changes
**For Guest Users:**
- See full product information
- Cannot add to cart
- See message: "Sign in to add items to your cart"
- Two buttons: "Sign In" and "Create Account"
- Disabled "Add to Cart" button

**For Logged-In Users:**
- See full product information
- Can add to cart
- Active "Add to Cart" button
- Success message when added

## Validation Rules

### Sign-Up Validation
```php
✅ Email is required and must be valid email format
✅ Password must be at least 6 characters
✅ Password and Confirm Password must match
✅ Full Name is required
✅ Email must not already be registered
✅ Must agree to Terms & Conditions
```

### Sign-In Validation
```php
✅ Email is required
✅ Password is required
✅ Email must exist in system
✅ Password must match stored password
```

### Add to Cart Validation
```php
✅ User must be logged in
✅ Quantity must be > 0
✅ Quantity must not exceed stock
```

## Security Features (Phase 2)

1. **Session-Based Authentication**
   - User data stored server-side in sessions
   - Not exposed through URLs

2. **Password Verification**
   - Password checked against stored value
   - Passwords not displayed in logs

3. **Input Validation**
   - All inputs validated and sanitized
   - HTML output escaped with htmlspecialchars()

4. **Login Protection**
   - Sensitive pages (cart, checkout, orders) require login
   - requireLogin() redirects unauthorized access

5. **Session Management**
   - Sessions created on successful login
   - Sessions destroyed on logout
   - Redirect URLs cleared after use

## Testing Guide

### Test 1: Browse as Guest
1. Start fresh (clear cookies/session)
2. Access `http://localhost:8000`
3. View home page ✅
4. Browse products ✅
5. Click "View Details" on a product ✅
6. See "Sign in to add items" message ✅

### Test 2: Sign Up
1. Click "Sign Up" button
2. Fill form with:
   - Name: Test User
   - Email: test@example.com
   - Password: password123
   - Confirm: password123
3. Check Terms checkbox
4. Click "Create Account"
5. **Expected**: Redirected to products page, logged in

### Test 3: Add to Cart (Logged In)
1. From products, click "View Details"
2. Enter quantity (e.g., 2)
3. Click "Add to Cart"
4. **Expected**: Success message, cart count updates in header

### Test 4: Demo Account Login
1. Go to Sign In page
2. Email: demo@example.com
3. Password: password123
4. Click "Sign In"
5. **Expected**: Logged in, redirected to products page

### Test 5: Invalid Login
1. Go to Sign In page
2. Enter wrong email or password
3. Click "Sign In"
4. **Expected**: Error message appears

### Test 6: View Profile
1. Log in with any account
2. Click on username in header
3. Click "👤 My Profile"
4. **Expected**: Profile page shows user info and stats

### Test 7: Logout
1. Log in first
2. Click on username in header
3. Click "🚪 Logout"
4. **Expected**: Logged out, redirected to home, Sign In button shows

### Test 8: Protected Page Access
1. Logout completely
2. Try to access `http://localhost:8000/cart.php`
3. **Expected**: Redirected to signin.php

### Test 9: Redirect After Login
1. Logout completely
2. Try to add product to cart
3. Redirected to signin
4. Sign in with demo account
5. **Expected**: Redirected back to product page (not products list)

### Test 10: Session Persistence
1. Log in
2. Browse different pages
3. Go back to home
4. Still logged in ✅
5. Logout
6. Refresh page
7. **Expected**: Logged out

## Demo Account Credentials

```
Email:    demo@example.com
Password: password123
Name:     Demo User
```

Use this account for testing all features.

## Error Messages

### Sign-Up Errors
- "Email is required"
- "Invalid email format"
- "Password is required"
- "Password must be at least 6 characters"
- "Passwords do not match"
- "Full name is required"
- "Email already registered"

### Sign-In Errors
- "Email is required"
- "Password is required"
- "Email not found"
- "Invalid password"

## Key Implementation Details

### Auth Check on Every Page
```php
// In header.php
require_once 'auth.php';
$isUserLoggedIn = isLoggedIn();
$currentUser = getCurrentUser();
```

### Protect Sensitive Pages
```php
// In cart.php, checkout.php, orders.php, profile.php
requireLogin();
```

### Check Before Cart Operations
```php
// In product-detail.php
if (!isLoggedIn()) {
    header('Location: signin.php?login_required=1');
    exit;
}
```

### Show Different UI Based on Login
```php
// In header.php
<?php if ($isUserLoggedIn): ?>
    // Show cart, orders, user menu
<?php else: ?>
    // Show sign in, sign up buttons
<?php endif; ?>
```

## Future Enhancements (Phase 3)

1. **Password Security**
   - Use `password_hash()` and `password_verify()`
   - Implement password reset functionality

2. **Database Integration**
   - Move user data to MySQL/PostgreSQL
   - Store passwords securely

3. **Email Verification**
   - Verify email on signup
   - Send confirmation emails

4. **Forgot Password**
   - Email-based password reset
   - Reset token validation

5. **Social Login**
   - Google OAuth integration
   - Facebook login

6. **User Roles**
   - Admin accounts
   - Customer vs Admin pages

7. **Session Security**
   - Implement CSRF tokens
   - Add session timeout

8. **Two-Factor Authentication**
   - SMS verification
   - Authenticator app support

## File Statistics

- **New Files**: 5 (auth.php, signin.php, signup.php, logout.php, profile.php)
- **Updated Files**: 6 (header.php, product-detail.php, cart.php, checkout.php, orders.php, style.css)
- **Total Lines of Code**: ~2,000+ (auth system)
- **Authentication Functions**: 6
- **Session Variables**: 4+
- **User Data Fields**: 5

## Browser Compatibility

✅ All modern browsers
✅ Mobile browsers
✅ Sessions enabled
✅ Cookies enabled

## Known Limitations (Phase 2)

1. Passwords stored in plain text (Phase 3: Use hashing)
2. User data in memory (Phase 3: Use database)
3. Single user per session (Phase 3: Multi-device support)
4. No email verification (Phase 3: Email confirmation)
5. No password reset (Phase 3: Forgot password)

## Troubleshooting

**Issue**: Login page keeps showing after sign-up
- **Solution**: Clear browser cookies, try new email

**Issue**: User menu not appearing
- **Solution**: Ensure JavaScript is enabled, check browser console

**Issue**: Can't access cart after login
- **Solution**: Check session_start() is called in cart.php

**Issue**: Login redirects but user still not recognized
- **Solution**: Clear session and login again

## Summary

The authentication system successfully implements:
- ✅ Guest browsing (no login needed)
- ✅ User registration (sign-up)
- ✅ User authentication (sign-in)
- ✅ Protected pages (cart, checkout, orders)
- ✅ User profile
- ✅ Session management
- ✅ Responsive UI

**Ready for Phase 3 database integration!**
