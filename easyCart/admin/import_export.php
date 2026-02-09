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
// Improved CSV Import Logic
if (isset($_POST['import_csv']) && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        if ($handle) {
            $pdo = getDBConnection();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ensure exceptions
            
            try {
                $pdo->beginTransaction();
                
                // Read headers
                $headers = fgetcsv($handle);
                // Columns: sku,name,price,description,stock,color,size,brand,emoji,image
                
                $importedCount = 0;
                $updatedCount = 0;
                $line = 1;

                while (($data = fgetcsv($handle)) !== false) {
                    $line++;
                    if (count($data) < 10) continue; 

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

                    // Check if product exists by SKU
                    $stmt = $pdo->prepare("SELECT entity_id FROM catalog_product_entity WHERE sku = ?");
                    $stmt->execute([$sku]);
                    $existingId = $stmt->fetchColumn();

                    if ($existingId) {
                        // UPDATE existing product
                        $updateEntity = $pdo->prepare("UPDATE catalog_product_entity SET name = ? WHERE entity_id = ?");
                        $updateEntity->execute([$name, $existingId]);
                        
                        // Check if attributes exist for this ID, otherwise insert
                        $checkAttr = $pdo->prepare("SELECT 1 FROM catalog_product_attribute WHERE product_id = ?");
                        $checkAttr->execute([$existingId]);
                        
                        if ($checkAttr->fetchColumn()) {
                            $updateAttr = $pdo->prepare("
                                UPDATE catalog_product_attribute 
                                SET price = ?, description = ?, stock = ?, color = ?, size = ?, brand = ?, emoji = ?
                                WHERE product_id = ?
                            ");
                            $updateAttr->execute([$price, $description, $stock, $color, $size, $brand, $emoji, $existingId]);
                        } else {
                            $insAttr = $pdo->prepare("
                                INSERT INTO catalog_product_attribute (product_id, price, description, stock, color, size, brand, emoji)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                            ");
                            $insAttr->execute([$existingId, $price, $description, $stock, $color, $size, $brand, $emoji]);
                        }
                        
                        // Update Image
                        if (!empty($image)) {
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
                        // INSERT new product
                        // 1. Insert Entity
                        $insEntity = $pdo->prepare("INSERT INTO catalog_product_entity (sku, name, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)");
                        $insEntity->execute([$sku, $name]);
                        
                        // Get the ID of the newly inserted product
                        $newId = $pdo->lastInsertId('catalog_product_entity_entity_id_seq'); 
                        
                        // If standard lastInsertId fails (which it might on some Postgres setups without sequence name), fallback to selecting by SKU
                        if (!$newId) {
                            $stmt = $pdo->prepare("SELECT entity_id FROM catalog_product_entity WHERE sku = ?");
                            $stmt->execute([$sku]);
                            $newId = $stmt->fetchColumn();
                        }
                        
                        if (!$newId) {
                            throw new Exception("Failed to retrieve ID for new product SKU: $sku");
                        }

                        // 2. Insert Attributes
                        $insAttr = $pdo->prepare("
                            INSERT INTO catalog_product_attribute (product_id, price, description, stock, color, size, brand, emoji)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        $insAttr->execute([$newId, $price, $description, $stock, $color, $size, $brand, $emoji]);
                        
                        // 3. Insert Image
                        if (!empty($image)) {
                            $insImg = $pdo->prepare("INSERT INTO catalog_product_image (product_id, image_path, is_primary) VALUES (?, ?, TRUE)");
                            $insImg->execute([$newId, $image]);
                        } else {
                            // Default placeholder if no image
                             $insImg = $pdo->prepare("INSERT INTO catalog_product_image (product_id, image_path, is_primary) VALUES (?, ?, TRUE)");
                             $insImg->execute([$newId, '/public/img/placeholder.png']);
                        }
                        
                        $importedCount++;
                    }
                }
                
                $pdo->commit();
                $message = "Import Successful! Added: $importedCount, Updated: $updatedCount";
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = "Import Failed at line $line: " . $e->getMessage();
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
    <div class="admin-panel">
        <div class="admin-page-header">
            <h1 class="admin-page-title">📦 Product Import / Export</h1>
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
            <h2 class="section-title">📥 Import Products (CSV)</h2>
            <p class="section-desc">
                Upload a CSV file to update existing products or add new ones.<br>
                <small>Required Columns: sku, name, price, description, stock, color, size, brand, emoji, image</small>
            </p>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="file" name="csv_file" accept=".csv" required class="file-input">
                </div>
                <button type="submit" name="import_csv" class="btn-primary">Upload & Import</button>
            </form>
        </div>

        <!-- Export Section -->
        <div class="section">
            <h2 class="section-title">📤 Export Products</h2>
            <p class="section-desc">
                Download all product data in CSV format.
            </p>
            <form method="POST">
                <button type="submit" name="export_csv" class="btn-primary btn-success">Download CSV</button>
            </form>
        </div>
        
    </div>

    <?php include dirname(__DIR__) . '/resources/templates/footer.php'; ?>
