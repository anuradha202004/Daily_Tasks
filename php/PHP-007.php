<?php
// ===============================
// Task: PHP-007
// Topic: Class & Constructor
// ===============================

class Employee {
    public $name;
    private $salary;

    // Constructor
    public function __construct($name, $salary) {
        $this->name = $name;
        $this->salary = $salary;
    }

    // Extra practical method
    public function displayInfo() {
        return "Employee Name: {$this->name}";
    }
}

// Object creation
$emp1 = new Employee("Anuradha", 30000);

// Output
echo $emp1->displayInfo();
?>
