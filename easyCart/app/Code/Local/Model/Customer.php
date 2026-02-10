<?php

class Model_Customer extends Core_Model {
    protected $resource;
    protected $data = [];

    public function __construct() {
        parent::__construct();
        // $this->resource = new Model_Customer_Resource(); // If we implement strict resource separation
        $this->tableName = 'users'; // Direct table access via Core_Model for now if resource not strictly required yet
    }

    public function load($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $this->data = $this->db->fetchOne($sql, ['id' => $id]);
        return $this->data;
    }

    public function loadByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $this->data = $this->db->fetchOne($sql, ['email' => $email]);
        return $this->data;
    }
    
    public function isActive() {
        if (empty($this->data)) return false;
        // Check 'is_active' column if it exists, or assume active if not defined yet
        // For now, assume active unless 'status' is 'banned' or similar
        return true; 
    }

    public function authenticate($email, $password) {
        $user = $this->loadByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            // Check active status here
            if (!$this->isActive()) {
                throw new Exception("Account is deactivated.");
            }
            return $user;
        }
        return false;
    }
}
