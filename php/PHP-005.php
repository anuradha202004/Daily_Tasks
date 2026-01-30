<?php
/*
--------------------------------------------
Task ID: PHP-005
Objective: Custom functions with strict types & scope.
Practice Work: 
Enable strict types: declare(strict_types=1);
Create a function calculateTotal(float $price, int $qty): float.
Demonstrate 'Global' vs 'Local' variable scope inside a function.
--------------------------------------------
*/
// Enable strict type checking
declare(strict_types=1);

/*
--------------------------------------------------
GLOBAL VARIABLE
--------------------------------------------------
*/
$taxRate = 18;   // Global scope variable

/*
--------------------------------------------------
FUNCTION 1: calculateTotal()
- Uses strict types
- Uses typed parameters
- Uses return type
- Uses local variable
--------------------------------------------------
*/
function calculateTotal(float $price, int $qty): float
{
    // Local variable (scope limited to this function)
    $total = $price * $qty;

    return $total;
}

/*
--------------------------------------------------
FUNCTION 2: calculateTax()
- Demonstrates GLOBAL variable access
--------------------------------------------------
*/
function calculateTax(float $amount): float
{
    global $taxRate;   // Access global variable

    $tax = ($amount * $taxRate) / 100;
    return $tax;
}

/*
--------------------------------------------------
FUNCTION 3: finalAmount()
- Best practice: passing values as parameters
--------------------------------------------------
*/
function finalAmount(float $total, float $tax): float
{
    return $total + $tax;
}

/*
--------------------------------------------------
FUNCTION CALLING (Execution Area)
--------------------------------------------------
*/
$price = 250.50;
$quantity = 2;

// Call calculateTotal()
$totalAmount = calculateTotal($price, $quantity);

// Call calculateTax()
$taxAmount = calculateTax($totalAmount);

// Call finalAmount()
$grandTotal = finalAmount($totalAmount, $taxAmount);

/*
--------------------------------------------------
OUTPUT
--------------------------------------------------
*/
echo "Price: Rs. $price <br>";
echo "Quantity: $quantity <br>";
echo "Total Amount: Rs. $totalAmount <br>";
echo "Tax ($taxRate%): Rs. $taxAmount <br>";
echo "<strong>Grand Total: Rs. $grandTotal</strong>";

?>
