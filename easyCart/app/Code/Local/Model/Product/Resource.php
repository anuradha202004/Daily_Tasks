<?php

class Model_Product_Resource extends Core_Model {
    protected $tableName = 'catalog_product_entity';
    protected $primaryKey = 'entity_id';

    public function getProductById($id) {
        $sql = "
        SELECT 
            e.entity_id as id, e.sku, e.name, e.created_at,
            a.price, a.description, a.color, a.size, a.brand, a.stock, a.emoji,
            4.5 as rating, 10 as reviews,
            i.image_path as image,
            COALESCE(c.category_id, 1) as category_id
        FROM catalog_product_entity e
        LEFT JOIN catalog_product_attribute a ON e.entity_id = a.product_id
        LEFT JOIN catalog_product_image i ON e.entity_id = i.product_id AND i.is_primary = TRUE
        LEFT JOIN (
            SELECT product_id, category_id 
            FROM catalog_category_products 
            LIMIT 1 -- Simplified join for category
        ) c ON e.entity_id = c.product_id
        WHERE e.entity_id = :id
        ";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
}
