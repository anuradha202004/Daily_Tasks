<?php

namespace Models;

use PDO;

/**
 * Base Model Class
 * Provides common database operations for all models
 */
abstract class Model {
    protected $pdo;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Find a record by ID
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get all records
     * @return array
     */
    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }
    
    /**
     * Find records by condition
     * @param array $conditions
     * @return array
     */
    public function where($conditions) {
        $where = [];
        $values = [];
        
        foreach ($conditions as $key => $value) {
            $where[] = "$key = ?";
            $values[] = $value;
        }
        
        $whereClause = implode(' AND ', $where);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE $whereClause");
        $stmt->execute($values);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Insert a new record
     * @param array $data
     * @return int|bool
     */
    public function create($data) {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Update a record
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $set = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $set[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        $setClause = implode(', ', $set);
        
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = ?");
        return $stmt->execute($values);
    }
    
    /**
     * Delete a record
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Execute a raw query
     * @param string $query
     * @param array $params
     * @return array
     */
    public function query($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        $this->pdo->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        $this->pdo->rollBack();
    }
}
