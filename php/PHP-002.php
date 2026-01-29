<?php
/*
--------------------------------------------
Task ID: PHP-002
Objective: Understanding Arrays and Debugging
Topics Covered:
1. Associative Array
2. Nested Arrays
3. print_r() for structure
4. var_dump() for data types & length
--------------------------------------------
*/

// 1️⃣ Create an associative array describing a person
$student = [
    "name" => "Rahul",
    "age" => 23,
    "skills" => ["PHP", "HTML", "CSS"]
];

// 2️⃣ Display array using print_r()
echo "<h2>Using print_r()</h2>";
echo "<pre>"; // Makes output readable
print_r($student);
echo "</pre>";

// 3️⃣ Display array using var_dump()
echo "<h2>Using var_dump()</h2>";
echo "<pre>";
var_dump($student);
echo "</pre>";

// 4️⃣ Access individual elements
echo "<h2>Accessing Individual Elements</h2>";
echo "Name: " . $student["name"] . "<br>";
echo "Age: " . $student["age"] . "<br>";
echo "First Skill: " . $student["skills"][0] . "<br>";

// 5️⃣ Loop through skills array
echo "<h2>Looping Through Skills</h2>";
echo "<ul>";
foreach ($student["skills"] as $skill) {
    echo "<li>$skill</li>";
}
echo "</ul>";

// 6️⃣ Debug a specific value
echo "<h2>Debugging a Single Value</h2>";
echo "<pre>";
var_dump($student["skills"]);
echo "</pre>";

// 7️⃣ Check array type
echo "<h2>Array Type Check</h2>";
echo is_array($student) ? "Student is an array" : "Not an array";

?>
