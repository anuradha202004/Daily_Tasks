<?php
// ===============================
// Task: PHP-009
// Topic: Inheritance
// ===============================

class Employee {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function getDetails() {
        return "Employee Name: {$this->name}";
    }
}

// Child class
class Manager extends Employee {
    public $department;

    public function __construct($name, $department) {
        parent::__construct($name);
        $this->department = $department;
    }

    // Overridden method
    public function getDetails() {
        return "Manager Name: {$this->name}, Department: {$this->department}";
    }

    // Extra practical
    public function report() {
        return "{$this->name} manages the {$this->department} department.";
    }
}

// Usage
$mgr = new Manager("Sneha", "IT");

echo $mgr->getDetails() . "<br>";
echo $mgr->report();
?>
