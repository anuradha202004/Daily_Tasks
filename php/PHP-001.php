<?php
// ================================
// Practice 1: Basics & Variables
// Task ID: PHP-001
// ================================

// String variable
$greeting = "Hello";

// Integer variable
$year = 2024;

// Float variable
$price = 450.50;

// Boolean variable
$isActive = true;

// Array variable
$colors = array("Red", "Green", "Blue");

// Null variable
$result = null;

// -------------------------------
// Output using echo
// -------------------------------

// Concatenation example
echo $greeting . ", the year is " . $year;
echo "<br>";

// Printing float value
echo "Product price is: $" . $price;
echo "<br>";

// Printing boolean value
echo "Is Active: " . $isActive;
echo "<br>";

// Printing array using print_r
echo "Available Colors: ";
print_r($colors);
echo "<br>";

// Printing NULL value
echo "Result value: ";
var_dump($result);
?>
