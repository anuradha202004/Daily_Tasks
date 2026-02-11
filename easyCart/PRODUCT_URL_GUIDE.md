# 🔗 Product URL System - Complete Guide

## 📋 Overview

Your easyCart application uses **SEO-friendly URLs** that convert product names into clean, readable URLs (called "slugs").

**Example:**
- Product Name: `"Wireless Headphones"`
- URL Slug: `wireless-headphones`
- Full URL: `http://localhost/easyCart/public/product/wireless-headphones`

---

## 🔄 How It Works - Complete Flow

### **Step 1: Product Name → URL Slug (In Views)**

When displaying products on pages, the product name is converted to a URL slug using PHP:

**Location:** `app/Views/home/index.php` (lines 97-98, 199-200)

```php
<?php 
    // Convert product name to URL-friendly slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));
    $productUrl = baseUrl('product/' . $slug);
?>
```

**What this does:**
1. `preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])` - Replaces all non-alphanumeric characters with hyphens
2. `strtolower()` - Converts to lowercase
3. `trim()` - Removes extra whitespace
4. `baseUrl('product/' . $slug)` - Creates full URL

**Examples:**
```php
"Wireless Headphones"     → "wireless-headphones"
"Apple iPhone 15 Pro"     → "apple-iphone-15-pro"
"Men's Running Shoes"     → "men-s-running-shoes"
"4K Smart TV (55 inch)"   → "4k-smart-tv-55-inch"
```

---

### **Step 2: User Clicks Product Link**

The generated URL is used in the product card:

```php
<div class="product-card" onclick="window.location.href='<?php echo $productUrl; ?>'">
    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
</div>
```

**User clicks → Browser navigates to:**
```
http://localhost/easyCart/public/product/wireless-headphones
```

---

### **Step 3: Router Matches URL Pattern**

**Location:** `public/index.php` (line 46)

```php
$router->add('product/{slug}', 'Controller_Product', 'detail');
```

**What happens:**
1. Router receives URL: `product/wireless-headphones`
2. Matches pattern: `product/{slug}`
3. Extracts slug: `wireless-headphones`
4. Calls: `Controller_Product::detail('wireless-headphones')`

---

### **Step 4: Controller Receives Slug**

**Location:** `app/Code/Local/Controller/Product.php` (lines 68-86)

```php
public function detail($slug = null) {
    $product = null;
    $model = new Model_Product();
    
    if ($slug) {
        // Use Model to load by slug
        $product = $model->loadBySlug($slug);
    } else {
        // Fallback to legacy id parameter
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($productId) {
            $product = $model->load($productId);
        }
    }

    if (!$product) {
        $this->redirect('products');
        return;
    }
    
    // ... render product detail page
}
```

**What it does:**
- Receives slug parameter: `"wireless-headphones"`
- Calls `Model_Product::loadBySlug($slug)`
- If product not found, redirects to products page

---

### **Step 5: Model Converts Slug Back to Product Name**

**Location:** `app/Code/Local/Model/Product.php` (lines 36-47)

```php
public function loadBySlug($slug) {
    $productId = $this->resource->getProductIdBySlug($slug);
    if ($productId) {
        return $this->load($productId);
    }
    return null;
}
```

---

### **Step 6: Database Query Matches Slug to Product**

**Location:** `app/Code/Local/Model/Product/Resource.php` (lines 34-44)

```php
public function getProductIdBySlug($slug) {
    // Normalize slug: replace hyphens with spaces for matching
    $searchName = str_replace('-', ' ', $slug);
    
    $sql = "SELECT entity_id FROM {$this->tableName} 
            WHERE REPLACE(LOWER(name), '-', ' ') = :name 
            LIMIT 1";
    
    $row = $this->db->fetchOne($sql, ['name' => strtolower($searchName)]);
    return $row ? $row['entity_id'] : null;
}
```

**How it works:**
1. Converts slug back: `"wireless-headphones"` → `"wireless headphones"`
2. Queries database: Finds product where `LOWER(name)` matches `"wireless headphones"`
3. Returns product ID
4. Product is loaded by ID and displayed

---

## 📍 Where Product URLs Are Generated

### **1. Homepage** (`app/Views/home/index.php`)

**Featured Products Section (lines 93-177):**
```php
<?php foreach ($featuredProducts as $product): ?>
    <?php 
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));
        $productUrl = baseUrl('product/' . $slug);
    ?>
    <div class="product-card" onclick="window.location.href='<?php echo $productUrl; ?>'">
        <!-- Product details -->
    </div>
<?php endforeach; ?>
```

**All Products Section (lines 195-281):**
```php
<?php foreach (array_slice($products, 0, 8, true) as $product): ?>
    <?php 
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));
        $productUrl = baseUrl('product/' . $slug);
    ?>
    <!-- Product card with URL -->
<?php endforeach; ?>
```

---

### **2. Products Page** (`app/Views/product/index.php`)

**Line 99:**
```php
$productUrl = baseUrl('product/' . $slug);
```

---

### **3. Product Detail Page** (`app/Views/product/detail.php`)

**Related Products Section (line 136):**
```php
$relUrl = baseUrl('product/' . $relSlug);
```

---

### **4. Cart Page** (`app/Views/cart/index.php`)

**Line 65:**
```php
$productUrl = baseUrl('product/' . $slug);
```

---

## 🛠️ How to Fetch Product Name from URL

### **Method 1: Using the Router (Recommended)**

This is already implemented in your application:

```php
// In Controller_Product::detail($slug)
public function detail($slug = null) {
    $model = new Model_Product();
    $product = $model->loadBySlug($slug);
    
    // Now you have the product name
    echo $product['name']; // "Wireless Headphones"
}
```

---

### **Method 2: Direct Database Query**

If you need to fetch product name from a slug manually:

```php
// Convert slug to search name
$slug = "wireless-headphones";
$searchName = str_replace('-', ' ', $slug); // "wireless headphones"

// Query database
$db = Core_Database::getInstance();
$sql = "SELECT name FROM catalog_product_entity 
        WHERE REPLACE(LOWER(name), '-', ' ') = :name 
        LIMIT 1";
$product = $db->fetchOne($sql, ['name' => strtolower($searchName)]);

echo $product['name']; // "Wireless Headphones"
```

---

### **Method 3: Using Model (Best Practice)**

```php
$model = new Model_Product();
$product = $model->loadBySlug('wireless-headphones');

if ($product) {
    echo $product['name'];      // "Wireless Headphones"
    echo $product['price'];     // 299.99
    echo $product['description']; // Product description
}
```

---

## 🔍 Reverse Process: Name → Slug

If you have a product name and need to generate the slug:

```php
function generateSlug($productName) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productName)));
}

// Examples:
echo generateSlug("Wireless Headphones");     // wireless-headphones
echo generateSlug("Apple iPhone 15 Pro");     // apple-iphone-15-pro
echo generateSlug("Men's Running Shoes");     // men-s-running-shoes
```

---

## 📊 Complete Example Flow

Let's trace a complete request:

### **1. Database has product:**
```sql
name: "Wireless Headphones"
entity_id: 42
price: 299.99
```

### **2. Homepage generates URL:**
```php
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', "Wireless Headphones")));
// Result: "wireless-headphones"

$productUrl = baseUrl('product/' . $slug);
// Result: "http://localhost/easyCart/public/product/wireless-headphones"
```

### **3. User clicks link:**
```
Browser navigates to: http://localhost/easyCart/public/product/wireless-headphones
```

### **4. Router processes:**
```php
// Matches pattern: product/{slug}
// Extracts: $slug = "wireless-headphones"
// Calls: Controller_Product::detail("wireless-headphones")
```

### **5. Controller loads product:**
```php
$model = new Model_Product();
$product = $model->loadBySlug("wireless-headphones");
```

### **6. Model queries database:**
```php
// Converts: "wireless-headphones" → "wireless headphones"
// Queries: WHERE LOWER(name) = "wireless headphones"
// Finds: entity_id = 42
// Loads full product data
```

### **7. Product displayed:**
```php
<h1><?php echo $product['name']; ?></h1>
<!-- Output: Wireless Headphones -->
```

---

## 🎯 Key Files Reference

| File | Purpose | Line Numbers |
|------|---------|--------------|
| `public/index.php` | Route definition | 46 |
| `app/Code/Local/Controller/Product.php` | Receives slug, loads product | 68-86 |
| `app/Code/Local/Model/Product.php` | Calls resource to get product | 36-47 |
| `app/Code/Local/Model/Product/Resource.php` | Database query to match slug | 34-44 |
| `app/Views/home/index.php` | Generates slugs for display | 97-98, 199-200 |
| `app/Views/product/index.php` | Generates slugs on product page | 99 |
| `app/Views/product/detail.php` | Generates slugs for related products | 136 |
| `app/Views/cart/index.php` | Generates slugs in cart | 65 |

---

## 🚀 Quick Reference

### **Generate Slug from Product Name:**
```php
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));
```

### **Generate Full URL:**
```php
$productUrl = baseUrl('product/' . $slug);
```

### **Fetch Product by Slug:**
```php
$model = new Model_Product();
$product = $model->loadBySlug($slug);
```

### **Get Product Name from Slug:**
```php
$product = $model->loadBySlug('wireless-headphones');
echo $product['name']; // "Wireless Headphones"
```

---

## 💡 Important Notes

1. **Slug Generation is Consistent:** The same product name always generates the same slug
2. **Case Insensitive:** Database matching is case-insensitive
3. **Special Characters:** Removed and replaced with hyphens
4. **Spaces:** Converted to hyphens
5. **SEO Friendly:** Clean URLs are better for search engines
6. **No Database Column:** Slugs are generated on-the-fly, not stored in database

---

## 🔧 Troubleshooting

### **Problem: Product not found by slug**

**Solution:** Check if the slug generation matches the database query:

```php
// In view (generating slug):
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));

// In resource (querying):
$searchName = str_replace('-', ' ', $slug);
// Both must normalize the same way!
```

### **Problem: Special characters in product name**

**Example:** Product name: `"Men's T-Shirt (Blue)"`

```php
// Generated slug:
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', "Men's T-Shirt (Blue)")));
// Result: "men-s-t-shirt-blue"

// Database query converts back:
$searchName = str_replace('-', ' ', "men-s-t-shirt-blue");
// Result: "men s t shirt blue"

// Database matching:
WHERE REPLACE(LOWER(name), '-', ' ') = 'men s t shirt blue'
// Matches: "Men's T-Shirt (Blue)" → "men s t shirt blue" ✅
```

---

**Created:** February 11, 2026  
**Last Updated:** February 11, 2026  
**Version:** 1.0
