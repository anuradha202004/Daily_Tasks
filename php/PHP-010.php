<?php
// ===============================
// Task: PHP-010
// Topic: Magic Methods
// ===============================

class User {
    public $name;
    public $email;

    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }

    // __toString magic method
    public function __toString() {
        return json_encode([
            "name" => $this->name,
            "email" => $this->email
        ]);
    }

    // __get magic method
    public function __get($property) {
        return "The property '{$property}' does not exist.<br>";
    }

    // Extra practical: __set
    public function __set($property, $value) {
        echo "Cannot set '{$property}' dynamically.<br>";
    }
}

// Usage
$user = new User("Anu", "anu@example.com");

// toString
echo $user . "<br>";

// undefined property
echo $user->address;

// try setting undefined property
$user->age = 21;
?>
