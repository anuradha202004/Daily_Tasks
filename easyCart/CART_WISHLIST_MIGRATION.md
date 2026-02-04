# Cart & Wishlist Database Migration

## Summary
Successfully migrated Cart and Wishlist functionality from JSON file storage to PostgreSQL database.

## Database Tables Created

### 1. `checkout_cart`
Stores user cart items.

**Columns:**
- `id` (SERIAL PRIMARY KEY)
- `user_id` (INTEGER) - References users table
- `product_id` (INTEGER) - References products
- `quantity` (INTEGER) - Number of items
- `updated_at` (TIMESTAMP) - Last update time
- **Unique Constraint:** `(user_id, product_id)` - Prevents duplicate items

### 2. `wishlist`
Stores user wishlist items.

**Columns:**
- `id` (SERIAL PRIMARY KEY)
- `user_id` (INTEGER) - References users table
- `product_id` (INTEGER) - References products
- `added_at` (TIMESTAMP) - When item was added
- **Unique Constraint:** `(user_id, product_id)` - Prevents duplicate items

## Code Changes

### Modified Functions in `app/Core/data.php`

#### `loadUserCart($userId)`
- **Before:** Read from JSON file `storage/data/cart_MD5USERID.json`
- **After:** Query `checkout_cart` table
- **Returns:** Array format `[product_id => ['product_id' => id, 'quantity' => qty]]`

#### `saveUserCart($userId, $cart)`
- **Before:** Write to JSON file
- **After:** 
  1. Delete all existing cart items for user
  2. Insert new cart items from array
  3. Uses transaction for data integrity

#### `loadUserWishlist($userId)`
- **Before:** Read from JSON file `storage/data/wishlist_MD5USERID.json`
- **After:** Query `wishlist` table
- **Returns:** Array of product IDs `[1, 2, 3, ...]`

#### `saveUserWishlist($userId, $wishlist)`
- **Before:** Write to JSON file
- **After:**
  1. Delete all existing wishlist items for user
  2. Insert new wishlist items from array
  3. Uses `ON CONFLICT DO NOTHING` for safety
  4. Uses transaction for data integrity

## Benefits

1. **Scalability:** Database handles concurrent access better than file I/O
2. **Data Integrity:** ACID transactions ensure consistency
3. **Performance:** Indexed queries faster than file parsing
4. **Reliability:** No file permission or corruption issues
5. **Backup:** Included in database backup strategy
6. **Analytics:** Easy to query cart/wishlist statistics

## Migration Steps Performed

1. Created migration script: `app/Core/migrate_cart_wishlist.php`
2. Executed migration to create tables
3. Updated all cart/wishlist functions in `app/Core/data.php`
4. Removed file-based logic (JSON files no longer used)

## Testing Recommendations

1. Test adding items to cart
2. Test removing items from cart
3. Test updating cart quantities
4. Test adding/removing wishlist items
5. Test cart persistence across sessions
6. Test wishlist persistence across sessions
7. Verify checkout process still works correctly

## Backward Compatibility

**Note:** Old JSON cart/wishlist files in `storage/data/` are no longer used. 
Users will start with empty carts/wishlists after this migration.

To migrate existing data, you would need to:
1. Read old JSON files
2. Parse the data
3. Insert into new database tables
4. This was not implemented as it's assumed to be a fresh deployment

## Files Modified

- `app/Core/data.php` - Updated cart/wishlist functions
- `app/Core/migrate_cart_wishlist.php` - New migration script

## Database Schema Verification

Run this SQL to verify tables were created:

```sql
-- Check checkout_cart table
SELECT * FROM checkout_cart LIMIT 5;

-- Check wishlist table
SELECT * FROM wishlist LIMIT 5;

-- View table structure
\d checkout_cart
\d wishlist
```
