<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $config = require_once __DIR__ . '/../../config/database.php';

        try {
            $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
    
    // Helper query method
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
