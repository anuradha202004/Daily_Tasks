# Project Structure & Organization Update

**Date**: February 4, 2026
**Status**: ✅ **RESTRUCTURED & CLEANED**

The project has been reorganized into a professional MVC-like structure for better maintainability and scalability.

## 📂 New Directory Structure

### 1. **`app/`** (Backend Logic)
Contains all the core PHP logic and configuration.
- **`app/bootstrap.php`**: The main entry point loaded by every page. Defines path constants (`TEMPLATES_PATH`, `PUBLIC_PATH`, etc.).
- **`app/Core/`**:
  - `db.php`: Database connection.
  - `auth.php`: User authentication system.
  - `data.php`: Data management (Products, Orders, Cart).
- **`app/Config/`**: Database configuration.

### 2. **`public/`** (Frontend Assets)
All static files are now here.
- **`public/css/`**: Stylesheets (e.g., `style.css`).
- **`public/js/`**: JavaScript files (e.g., `cart.js`, `validation.js`).
- **`public/img/`**: Images and assets.

### 3. **`resources/`** (Views & Templates)
- **`resources/templates/`**: Shared layout files (`header.php`, `footer.php`).

### 4. **`storage/`**
- **`storage/data/`**: JSON files for cart/wishlist storage (managed automatically).

### 5. **`_archive/`**
- Contains all old files, backups, and unused scripts.
- **Removed from Root**: `docs`, `documentation`, `pages`, `routes`, `scripts`, `includes`, `router.php`, `seed_products.php` (All moved here).

---

## 🚀 How it Works Now
All active root pages (`index.php`, `products.php`, etc.) now do the following:
1. Load **`app/bootstrap.php`**.
2. Use constants like **`TEMPLATES_PATH`** to include headers/footers.
3. Link assets using **`public/css/...`** and **`public/js/...`**.

## ✅ Action Items Completed
- [x] Moved Logic to `app/Core`
- [x] Moved Assets to `public/`
- [x] Moved Templates to `resources/templates/`
- [x] Updated all Root PHP files to use new structure
- [x] **Fully Cleaned Root Directory** (Moved 50+ unused files to `_archive`)
