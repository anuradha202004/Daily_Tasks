<?php

namespace Models;

/**
 * User Model
 * Handles user authentication and management
 */
class User extends Model {
    protected $table = 'users';
    
    /**
     * Find user by email
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Create a new user
     * @param array $data
     * @return int User ID
     */
    public function createUser($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table} (email, password, name, created_at) 
            VALUES (:email, :password, :name, NOW()) 
            RETURNING id
        ");
        
        $stmt->execute([
            ':email' => $data['email'],
            ':password' => $data['password'], // Should already be hashed
            ':name' => $data['name']
        ]);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Verify user credentials
     * @param string $email
     * @param string $password
     * @return array|false User data or false
     */
    public function verifyCredentials($email, $password) {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Verify password using password_verify()
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Update user password
     * @param int $userId
     * @param string $newPassword (plain text, will be hashed)
     * @return bool
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table} 
            SET password = :password, updated_at = NOW() 
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }
    
    /**
     * Get user by ID
     * @param int $id
     * @return array|null
     */
    public function getUserById($id) {
        return $this->find($id);
    }
}
