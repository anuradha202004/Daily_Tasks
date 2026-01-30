<?php
/*
--------------------------------------------------
PHP-006 : BUILT-IN FUNCTIONS
Covers:
- String functions
- Array functions
--------------------------------------------------
*/

/*
==============================
STRING FUNCTIONS
==============================
*/

// Original string
$str = "  Hello World  ";

// Step 1: trim spaces
$trimmedStr = trim($str);

// Step 2: convert to lowercase
$lowerStr = strtolower($trimmedStr);

// Step 3: replace 'World' with 'PHP'
$finalStr = str_replace("world", "php", $lowerStr);

echo "<h3>String Functions Output</h3>";
echo "Original String: '$str' <br>";
echo "After trim(): '$trimmedStr' <br>";
echo "After strtolower(): '$lowerStr' <br>";
echo "After str_replace(): '$finalStr' <br>";

/*
==============================
ARRAY FUNCTIONS
==============================
*/

// Create an array of numbers
$numbers = [1, 2, 3, 4];

echo "<h3>Array Functions Output</h3>";
echo "Original Array: ";
print_r($numbers);
echo "<br><br>";

// Check if 5 exists in array
if (in_array(5, $numbers)) {
    echo "5 exists in the array <br>";
} else {
    echo "5 does NOT exist in the array <br>";
}

// Add a number using array_push()
array_push($numbers, 5);

// Create another array
$moreNumbers = [6, 7];

// Merge arrays
$mergedArray = array_merge($numbers, $moreNumbers);

echo "<br>Array after array_push(): ";
print_r($numbers);

echo "<br><br>Merged Array: ";
print_r($mergedArray);

/*
==============================
EXPLODE FUNCTION
==============================
*/

// File name example
$fileName = "image.jpg";

// Split string into array
$fileParts = explode(".", $fileName);

echo "<h3>explode() Function Output</h3>";
echo "File Name: $fileName <br>";
echo "File Name Part: " . $fileParts[0] . "<br>";
echo "File Extension: " . $fileParts[1] . "<br>";
?>
