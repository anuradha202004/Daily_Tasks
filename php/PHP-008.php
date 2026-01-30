<?php
// ===============================
// Task: PHP-008
// Topic: Getters & Setters
// ===============================

class Employee {
    public $name;
    private $salary;

    public function __construct($name, $salary) {
        $this->name = $name;
        $this->salary = $salary;
    }

    // Getter
    public function getSalary() {
        return $this->salary;
    }

    // Setter with validation
    public function setSalary($amount) {
        if ($amount > 0) {
            $this->salary = $amount;
        } else {
            echo "Invalid salary amount<br>";
        }
    }

    // Extra practical
    public function yearlySalary() {
        return $this->salary * 12;
    }
}

// Usage
$emp = new Employee("Rahul", 25000);
echo "Monthly Salary: " . $emp->getSalary() . "<br>";

$emp->setSalary(30000);
echo "Updated Salary: " . $emp->getSalary() . "<br>";

echo "Yearly Salary: " . $emp->yearlySalary();
?>
