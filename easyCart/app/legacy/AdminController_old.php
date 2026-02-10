<?php

class AdminController extends Controller {
    public function dashboard() {
        // Ensure user is admin
        if (!isAdmin()) {
            $this->redirect('');
        }
        
        $data = [
            'title' => 'Admin Dashboard',
            'user' => getCurrentUser()
        ];
        
        $this->view('admin/dashboard', $data);
    }

    public function importExport() {
        // Ensure user is admin
        if (!isAdmin()) {
            $this->redirect('');
        }

        $message = '';
        $error = '';

        /**
         * Handle Export
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_csv'])) {
            try {
                global $pdo;
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_csv']) && isset($_FILES['csv_file'])) {
            if ($_FILES['csv_file']['error'] == 0) {
                $file = $_FILES['csv_file']['tmp_name'];
                $handle = fopen($file, "r");
                
                if ($handle) {
                    global $pdo;
                    
                    try {
                        $pdo->beginTransaction();
                        
                        // Read headers
                        $headers = fgetcsv($handle);
                        
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
                                         $insImg->execute([$image, $existingId]);
                                     }
                                }
                                $updatedCount++;
                            } else {
                                // INSERT new product
                                $insEntity = $pdo->prepare("INSERT INTO catalog_product_entity (sku, name, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)");
                                $insEntity->execute([$sku, $name]);
                                
                                $newId = $pdo->lastInsertId('catalog_product_entity_entity_id_seq'); 
                                
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
                                } else {
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

        $data = [
            'title' => 'Import / Export Products',
            'message' => $message,
            'error' => $error
        ];

        $this->view('admin/import_export', $data);
    }
}
