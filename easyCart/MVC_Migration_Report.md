# MVC Migration Plan & Report

## 1. Project Structure
The application has been restructured to adhere to a strict MVC pattern with Autoloading.

- **App Code**: `app/Code/Local/` (Custom Code) & `app/Code/Core/` (Framework Base)
- **Routes**: Manually defined in `public/index.php` mapping URLs to specific Controllers.
- **Autoloading**: `app/Autoloader.php` handles class loading following the `Prefix_Name` -> `Prefix/Name.php` convention.

## 2. Component Migration Status

| Component | Status | Location | Notes |
| :--- | :--- | :--- | :--- |
| **Product** | ✅ Migrated | `app/Code/Local/Controller/Product.php` | Full MVC implementation (Model, Resource, Collection, View) |
| **Cart** | ✅ Migrated | `app/Code/Local/Controller/Cart.php` | Includes validation and stock checks. AJAX supported. |
| **Order** | ✅ Migrated | `app/Code/Local/Controller/Order.php` | Checkout enforces login. Transaction-based order creation. |
| **Auth** | ✅ Migrated | `app/Code/Local/Controller/Auth.php` | Handles Signin/Signup/Logout with `isActive` check. |
| **Admin** | ✅ Migrated | `app/Code/Local/Controller/Admin.php` | Dashboard & Import/Export functionality migrated. |
| **Profile** | ✅ Migrated | `app/Code/Local/Controller/Profile.php` | Displays user info and order history via `Model_Order`. |
| **Wishlist** | ✅ Migrated | `app/Code/Local/Controller/Wishlist.php` | Full CRUD operations via `Model_Wishlist`. |
| **Pages** | ✅ Migrated | `app/Code/Local/Controller/Page.php` | Handles static content (About, Contact). |
| **Router** | ✅ Updated | `app/Code/Core/Core/Router.php` | Supports Autoloading and Legacy Fallback. |
| **Database** | ✅ Centralized | `app/Code/Core/Core/Database.php` | Replaces scattered `global $pdo` usage. |

## 3. Validation & Rules Implementation
- **Add to Cart / Checkout**:
  - The "Check email first" requirement is enforced by requiring login at the Checkout step (`Controller_Order::checkout`).
  - Guests are redirected to signin/signup before proceeding.
- **Deactivated Users**:
  - `Model_Customer::authenticate` includes a check for account status (`isActive()`). Deactivated users cannot log in.
  - Admin access is strictly enforced in `Controller_Admin::__construct`.
- **Cart Management**:
  - Stock validation is performed in `Model_Cart`.
  - Sessions are managed via standard PHP sessions, compatible with legacy `auth.php`.

## 4. Next Steps / Pending Items
- **URL Keys**: The `catalog_product_entity` table requires a schema update to support unique `url_key` for SEO-friendly URLs (currently using `id`).
- **Cleanup**: Verify and delete files in `app/legacy/` once thoroughly tested in production.

## 5. File Movements
Legacy controllers have been moved to `app/legacy/`:
- `ProductController.php` -> `app/legacy/ProductController_old.php`
- `CartController.php` -> `app/legacy/CartController_old.php`
- `OrderController.php` -> `app/legacy/OrderController_old.php`
- `AuthController.php` -> `app/legacy/AuthController_old.php`
- `AdminController.php` -> `app/legacy/AdminController_old.php`
- `ProfileController.php` -> `app/legacy/ProfileController_old.php`
- `WishlistController.php` -> `app/legacy/WishlistController_old.php`
- `PageController.php` -> `app/legacy/PageController_old.php`

The application is fully functional with the new MVC architecture for all user flows.
