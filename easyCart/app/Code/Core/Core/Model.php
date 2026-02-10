<?php
// app/Code/Core/Core/Model.php

class Core_Model {
    protected $db;
    protected $tableName;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Core_Database::getInstance();
    }

    public function load($id) {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    public function save($data) {
        // Placeholder for save logic (INSERT/UPDATE)
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql, ['id' => $id]);
    }
}
