<?php

class Model_Category extends Core_Model {

    public function getCategories() {
        // Fetch categories from database
        $sql = "
            SELECT 
                category_id as id, 
                name, 
                slug, 
                description 
            FROM catalog_category_attribute
            ORDER BY id
        ";
        // Check availability of table? Using fetchAll safely
        try {
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return []; // Return empty if table doesn't exist
        }
    }
}
