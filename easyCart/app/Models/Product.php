<?php

class Product extends Model {
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getFeatured() {
        // Example: Get first 4
        $stmt = $this->db->query("SELECT * FROM products LIMIT 4");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
