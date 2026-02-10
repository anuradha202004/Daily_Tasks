<?php
// app/Code/Core/Core/Database.php

class Core_Database {
    protected static $instance = null;
    protected $connection;

    private function __construct() {
        // Load config properly
        // For now, we reuse the existing db.php approach but wrapped in a class
        // In fully refactored system, config should be injected or loaded from Config class
        $config = require __DIR__ . '/../../../../config/database.php';
        
        try {
            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            $this->connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
}
