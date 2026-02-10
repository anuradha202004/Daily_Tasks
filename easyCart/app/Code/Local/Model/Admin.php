<?php

class Model_Admin extends Core_Model {
    protected $resource;

    public function __construct() {
        parent::__construct();
        $this->resource = new Model_Admin_Resource();
    }

    public function getDashboardData() {
        $stats = $this->resource->getDashboardStats();
        $recentOrders = $this->resource->getRecentOrders();
        
        // Format Currency
        $stats['sales_formatted'] = '$' . number_format($stats['sales']['total'], 2); // Using $ as per default, helper usage better
        
        return [
            'stats' => $stats,
            'recent_orders' => $recentOrders
        ];
    }
    
    // Import/Export Logic
    public function importProducts($filePath) {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return ['success' => false, 'message' => "File not found or not readable."];
        }

        $handle = fopen($filePath, "r");
        $header = fgetcsv($handle);
        
        // Expected columns: sku,name,price,description,stock,color,size,brand,emoji,image
        $expectedColumns = ['sku', 'name', 'price', 'description', 'stock', 'color', 'size', 'brand', 'emoji', 'image'];
        
        // map headers to indices
        $colMap = array_flip($header);
        foreach ($expectedColumns as $col) {
            if (!isset($colMap[$col])) {
                fclose($handle);
                return ['success' => false, 'message' => "Missing required column: $col"];
            }
        }

        $successCount = 0;
        $updateCount = 0;
        $errorCount = 0;

        try {
            $this->db->beginTransaction();

            while (($row = fgetcsv($handle)) !== false) {
                $data = [];
                foreach ($expectedColumns as $col) {
                    $data[$col] = $row[$colMap[$col]] ?? null;
                }

                if (empty($data['sku'])) {
                    $errorCount++;
                    continue;
                }

                // Check if product exists
                $existing = $this->db->fetchOne("SELECT entity_id FROM catalog_product_entity WHERE sku = :sku", ['sku' => $data['sku']]);
                
                if ($existing) {
                    $productId = $existing['entity_id'];
                    // Update entity
                    $this->db->query("UPDATE catalog_product_entity SET name = :name WHERE entity_id = :id", [
                        'name' => $data['name'],
                        'id' => $productId
                    ]);
                    $updateCount++;
                } else {
                    // Insert entity
                    $this->db->query("INSERT INTO catalog_product_entity (sku, name, created_at) VALUES (:sku, :name, NOW())", [
                        'sku' => $data['sku'],
                        'name' => $data['name']
                    ]);
                    $productId = $this->db->lastInsertId('catalog_product_entity_entity_id_seq');
                    $successCount++;
                }

                // Upsert Attributes
                $attrCheck = $this->db->fetchOne("SELECT 1 FROM catalog_product_attribute WHERE product_id = :id", ['id' => $productId]);
                if ($attrCheck) {
                    $this->db->query("
                        UPDATE catalog_product_attribute 
                        SET price = :price, description = :desc, color = :color, size = :size, brand = :brand, stock = :stock, emoji = :emoji 
                        WHERE product_id = :id", [
                        'price' => (float)$data['price'],
                        'desc' => $data['description'],
                        'color' => $data['color'],
                        'size' => $data['size'],
                        'brand' => $data['brand'],
                        'stock' => (int)$data['stock'],
                        'emoji' => $data['emoji'],
                        'id' => $productId
                    ]);
                } else {
                    $this->db->query("
                        INSERT INTO catalog_product_attribute (product_id, price, description, color, size, brand, stock, emoji)
                        VALUES (:id, :price, :desc, :color, :size, :brand, :stock, :emoji)", [
                        'id' => $productId,
                        'price' => (float)$data['price'],
                        'desc' => $data['description'],
                        'color' => $data['color'],
                        'size' => $data['size'],
                        'brand' => $data['brand'],
                        'stock' => (int)$data['stock'],
                        'emoji' => $data['emoji']
                    ]);
                }

                // Image logic
                if (!empty($data['image'])) {
                    $imgCheck = $this->db->fetchOne("SELECT id FROM catalog_product_image WHERE product_id = :id AND is_primary = TRUE", ['id' => $productId]);
                    if ($imgCheck) {
                        $this->db->query("UPDATE catalog_product_image SET image_path = :path WHERE id = :id", [
                            'path' => $data['image'],
                            'id' => $imgCheck['id']
                        ]);
                    } else {
                        $this->db->query("INSERT INTO catalog_product_image (product_id, image_path, is_primary) VALUES (:id, :path, TRUE)", [
                            'id' => $productId,
                            'path' => $data['image']
                        ]);
                    }
                }
            }

            $this->db->commit();
            fclose($handle);
            
            $msg = "Import complete. $successCount added, $updateCount updated.";
            if ($errorCount > 0) $msg .= " $errorCount skipped due to missing SKU.";
            
            return ['success' => true, 'message' => $msg];

        } catch (Exception $e) {
            $this->db->rollBack();
            fclose($handle);
            return ['success' => false, 'message' => "Error during import: " . $e->getMessage()];
        }
    }

    public function exportProducts() {
        $sql = "
            SELECT 
                e.sku, e.name, 
                a.price, a.description, a.stock, a.color, a.size, a.brand, a.emoji,
                i.image_path as image
            FROM catalog_product_entity e
            LEFT JOIN catalog_product_attribute a ON e.entity_id = a.product_id
            LEFT JOIN catalog_product_image i ON e.entity_id = i.product_id AND i.is_primary = TRUE
            ORDER BY e.entity_id ASC
        ";
        return $this->db->fetchAll($sql);
    }
}
