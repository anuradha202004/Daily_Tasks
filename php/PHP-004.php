<?php
/*
--------------------------------------------
Task ID: PHP-004
Objective: Iteration using for, while, and foreach.
Practice Work: 
Use for loop to print a multiplication table for 5.
Use foreach to iterate through the $student array from Practice 1 and print 'Key: Value'.
--------------------------------------------
*/

//Multiplication Table of 5 (Using for loop)
 for($i=1; $i<=10; $i++){
    echo "5 *  $i = ". (5 * $i) ."<br>";
 }

 $student = [
    "Name" => "Anuradha",
    "Age" => 21,
    "Course" => "Computer Engineering",
    "College" => "GEC, Dahod"
 ];

 //Iterate Student Array Using foreach
 foreach($student as $key => $value){
    echo "$key : $value";
 }
?>