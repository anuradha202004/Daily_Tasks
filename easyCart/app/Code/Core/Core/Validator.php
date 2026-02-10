<?php

/**
 * Core Validator
 * Centralized validation system with reusable rules
 * Follows MVC requirement for common validation class
 */
class Core_Validator {
    protected $rules = [];
    protected $errors = [];
    protected $data = [];
    
    /**
     * Add validation rule for a field
     * @param string $field Field name
     * @param string $rules Pipe-separated rules (e.g., 'required|email|min:8')
     * @param string $label Optional custom label for error messages
     * @return $this
     */
    public function addRule($field, $rules, $label = null) {
        $this->rules[$field] = [
            'rules' => explode('|', $rules),
            'label' => $label ?: ucfirst(str_replace('_', ' ', $field))
        ];
        return $this;
    }
    
    /**
     * Validate data against rules
     * @param array $data
     * @return bool
     */
    public function validate($data) {
        $this->data = $data;
        $this->errors = [];
        
        foreach ($this->rules as $field => $config) {
            $value = $data[$field] ?? null;
            $label = $config['label'];
            
            foreach ($config['rules'] as $rule) {
                if (!$this->applyRule($field, $value, $rule, $label)) {
                    // Stop validating this field on first error
                    break;
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Apply a single validation rule
     * @param string $field
     * @param mixed $value
     * @param string $rule
     * @param string $label
     * @return bool
     */
    protected function applyRule($field, $value, $rule, $label) {
        // Parse rule and parameter (e.g., "min:8" => rule="min", param="8")
        $parts = explode(':', $rule, 2);
        $ruleName = $parts[0];
        $ruleParam = $parts[1] ?? null;
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field][] = "$label is required.";
                    return false;
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "$label must be a valid email address.";
                    return false;
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $ruleParam) {
                    $this->errors[$field][] = "$label must be at least $ruleParam characters.";
                    return false;
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $ruleParam) {
                    $this->errors[$field][] = "$label must not exceed $ruleParam characters.";
                    return false;
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "$label must be a number.";
                    return false;
                }
                break;
                
            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errors[$field][] = "$label must be an integer.";
                    return false;
                }
                break;
                
            case 'match':
                // Compare with another field
                if (!empty($value) && $value !== ($this->data[$ruleParam] ?? null)) {
                    $this->errors[$field][] = "$label must match " . ucfirst($ruleParam) . ".";
                    return false;
                }
                break;
                
            case 'in':
                // Check if value is in allowed list
                $allowed = explode(',', $ruleParam);
                if (!empty($value) && !in_array($value, $allowed)) {
                    $this->errors[$field][] = "$label must be one of: " . implode(', ', $allowed) . ".";
                    return false;
                }
                break;
                
            case 'unique':
                // Check database uniqueness (requires table:column format)
                list($table, $column) = explode(',', $ruleParam);
                if (!empty($value) && $this->isNotUnique($table, $column, $value)) {
                    $this->errors[$field][] = "$label already exists.";
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    /**
     * Check if value exists in database
     * @param string $table
     * @param string $column
     * @param mixed $value
     * @return bool
     */
    protected function isNotUnique($table, $column, $value) {
        $db = Core_Database::getInstance();
        $sql = "SELECT 1 FROM $table WHERE $column = :value LIMIT 1";
        $result = $db->fetchOne($sql, ['value' => $value]);
        return !empty($result);
    }
    
    /**
     * Get all validation errors
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get errors for a specific field
     * @param string $field
     * @return array
     */
    public function getFieldErrors($field) {
        return $this->errors[$field] ?? [];
    }
    
    /**
     * Get first error message for a field
     * @param string $field
     * @return string|null
     */
    public function getFirstError($field) {
        $errors = $this->getFieldErrors($field);
        return !empty($errors) ? $errors[0] : null;
    }
    
    /**
     * Check if validation has errors
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Helper: Validate email and check existence
     * Used for add-to-cart flow
     * @param string $email
     * @return array ['exists' => bool, 'user_id' => int|null, 'errors' => array]
     */
    public static function validateEmailForCart($email) {
        $validator = new self();
        $validator->addRule('email', 'required|email');
        
        if (!$validator->validate(['email' => $email])) {
            return [
                'exists' => false,
                'user_id' => null,
                'errors' => $validator->getErrors()
            ];
        }
        
        // Check if email exists in database (using 'users' table)
        $db = Core_Database::getInstance();
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $result = $db->fetchOne($sql, ['email' => $email]);
        
        return [
            'exists' => !empty($result),
            'user_id' => $result ? $result['id'] : null,
            'errors' => []
        ];
    }
}
