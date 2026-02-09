<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';

// Basic Admin Auth Check
// In a real app, check for admin role.
requireAdmin();
// Make sure to restrict this in production!

$pageTitle = 'Import / Export Products';
$message = '';
$error = '';

/**
 * Handle Export
 */
if (isset($_POST['export_csv'])) {
    try {
        $pdo = getDBConnection();
        // Fetch all products
        $sql = "
            SELECT 
                e.sku, 
                e.name, 
                a.price, 
                a.description, 
                a.stock, 
                a.color, 
                a.size, 
                a.brand, 
                a.emoji,
                i.image_path as image
            FROM catalog_product_entity e
            LEFT JOIN catalog_product_attribute a ON e.entity_id = a.product_id
            LEFT JOIN catalog_product_image i ON e.entity_id = i.product_id AND i.is_primary = TRUE
            ORDER BY e.entity_id ASC
        ";
        $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Header for CSV
        $filename = "products_export_" . date('Y-m-d_H-i-s') . ".csv";
        
        // Clean buffer
        if (ob_get_level()) ob_end_clean();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['sku', 'name', 'price', 'description', 'stock', 'color', 'size', 'brand', 'emoji', 'image']);

        foreach ($products as $product) {
            fputcsv($output, [
                $product['sku'],
                $product['name'],
                $product['price'],
                $product['description'],
                $product['stock'],
                $product['color'],
                $product['size'],
                $product['brand'],
                $product['emoji'],
                $product['image']
            ]);
        }
        
        fclose($output);
        exit;

    } catch (Exception $e) {
        $error = "Export failed: " . $e->getMessage();
    }
}

/**
 * Handle Import
 */
if (isset($_POST['import_csv']) && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        if ($handle) {
            $pdo = getDBConnection();
            $pdo->beginTransaction();
            
            try {
                // Get headers
                $headers = fgetcsv($handle);
                // Expected headers: sku, name, price, description, stock, color, size, brand, emoji, image
                
                $importedCount = 0;
                $updatedCount = 0;
                
                while (($data = fgetcsv($handle)) !== false) {
                    // Map data (assuming exact order or use keys if mapped)
                    // For simplicity, we assume strict column order matching export
                    if (count($data) < 10) continue; // Skip invalid rows

                    $sku = trim($data[0]);
                    $name = trim($data[1]);
                    $price = (float)$data[2];
                    $description = trim($data[3]);
                    $stock = (int)$data[4];
                    $color = trim($data[5]);
                    $size = trim($data[6]);
                    $brand = trim($data[7]);
                    $emoji = trim($data[8]);
                    $image = trim($data[9]);

                    if (empty($sku) || empty($name)) continue;

                    // Check if exists
                    $stmt = $pdo->prepare("SELECT entity_id FROM catalog_product_entity WHERE sku = ?");
                    $stmt->execute([$sku]);
                    $existingId = $stmt->fetchColumn();

                    if ($existingId) {
                        // Update
                        $updateEntity = $pdo->prepare("UPDATE catalog_product_entity SET name = ? WHERE entity_id = ?");
                        $updateEntity->execute([$name, $existingId]);
                        
                        $updateAttr = $pdo->prepare("
                            UPDATE catalog_product_attribute 
                            SET price = ?, description = ?, stock = ?, color = ?, size = ?, brand = ?, emoji = ?
                            WHERE product_id = ?
                        ");
                        $updateAttr->execute([$price, $description, $stock, $color, $size, $brand, $emoji, $existingId]);
                        
                        // Update Image (if provided)
                        if (!empty($image)) {
                             // Assuming primary image logic, handling simple overwrite for primary
                             $checkImg = $pdo->prepare("SELECT id FROM catalog_product_image WHERE product_id = ? AND is_primary = TRUE");
                             $checkImg->execute([$existingId]);
                             if ($checkImg->fetchColumn()) {
                                 $updImg = $pdo->prepare("UPDATE catalog_product_image SET image_path = ? WHERE product_id = ? AND is_primary = TRUE");
                                 $updImg->execute([$image, $existingId]);
                             } else {
                                 $insImg = $pdo->prepare("INSERT INTO catalog_product_image (product_id, image_path, is_primary) VALUES (?, ?, TRUE)");
                                 $insImg->execute([$existingId, $image]);
                             }
                        }
                        
                        $updatedCount++;
                    } else {
                        // Insert
                        $insEntity = $pdo->prepare("INSERT INTO catalog_product_entity (sku, name, created_at) VALUES (?, ?, NOW()) RETURNING entity_id");
                        // Note: RETURNING is Postgres specific, but 'lastInsertId' might not work with standard SQL unless configured. 
                        // If using MySQL, use lastInsertId. This app uses Postgres based on "PostgreSQL" comment in data.php
                        // Actually, data.php line 4 says "via PostgreSQL". We should use RETURNING entity_id or sequence.
                        // Let's assume standard PDO lastInsertId works if implied or configured, but Postgres usually needs 'RETURNING id'.
                        // Let's try standard execute and fetch for Postgres.
                        
                        // For broad compatibility or if uncertain, let's use the safer select or RETURNING.
                        // The user said "PostgreSQL" in data.php header.
                        
                        $insEntity = $pdo->prepare("INSERT INTO catalog_product_entity (sku, name, created_at) VALUES (?, ?, NOW())");
                        $insEntity->execute([$sku, $name]);
                        $newId = $pdo->lastInsertId(); // Should work in recent PDO drivers if ID is serial
                        
                        // If lastInsertId fails, we query back by SKU
                        if (!$newId) {
                             $stmt = $pdo->prepare("SELECT entity_id FROM catalog_product_entity WHERE sku = ?");
                             $stmt->execute([$sku]);
                             $newId = $stmt->fetchColumn();
                        }

                        $insAttr = $pdo->prepare("
                            INSERT INTO catalog_product_attribute (product_id, price, description, stock, color, size, brand, emoji)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        $insAttr->execute([$newId, $price, $description, $stock, $color, $size, $brand, $emoji]);
                        
                        if (!empty($image)) {
                            $insImg = $pdo->prepare("INSERT INTO catalog_product_image (product_id, image_path, is_primary) VALUES (?, ?, TRUE)");
                            $insImg->execute([$newId, $image]);
                        }
                        
                        $importedCount++;
                    }
                }
                
                $pdo->commit();
                $message = "Import Successful! Added: $importedCount, Updated: $updatedCount";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Import Failed: " . $e->getMessage();
            }
            
            fclose($handle);
        } else {
            $error = "Could not open file.";
        }
    } else {
        $error = "Upload error code: " . $_FILES['csv_file']['error'];
    }
}

// Include Header
include dirname(__DIR__) . '/resources/templates/header.php'; 
?>
<style>
    /* Page Specific Styles */
    body { background-color: #f3f4f6; }
    .admin-container { max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .btn-primary { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
    .btn-secondary { background: #4b5563; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; }
    .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
    .alert-success { background: #d1fae5; color: #065f46; }
    .alert-error { background: #fee2e2; color: #991b1b; }
    .section { margin-bottom: 40px; border-bottom: 1px solid #e5e7eb; padding-bottom: 30px; }
    .section:last-child { border-bottom: none; }
</style>

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="margin: 0;">📦 Product Import / Export</h1>
            <a href="dashboard" class="btn-secondary">← Back to Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Import Section -->
        <div class="section">
            <h2>📥 Import Products (CSV)</h2>
            <p style="color: #666; margin-bottom: 20px;">
                Upload a CSV file to update existing products or add new ones.<br>
                <small>Required Columns: sku, name, price, description, stock, color, size, brand, emoji, image</small>
            </p>
            
            <form method="POST" enctype="multipart/form-data">
                <div style="margin-bottom: 15px;">
                    <input type="file" name="csv_file" accept=".csv" required style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>
                <button type="submit" name="import_csv" class="btn-primary">Upload & Import</button>
            </form>
        </div>

        <!-- Export Section -->
        <div class="section">
            <h2>📤 Export Products</h2>
            <p style="color: #666; margin-bottom: 20px;">
                Download all product data in CSV format.
            </p>
            <form method="POST">
                <button type="submit" name="export_csv" class="btn-primary" style="background: #059669;">Download CSV</button>
            </form>
        </div>
        
    </div>

    <?php include dirname(__DIR__) . '/resources/templates/footer.php'; ?>
