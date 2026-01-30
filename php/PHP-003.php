<?php
/*
--------------------------------------------
Task ID: PHP-003
Objective: Decision making with if, else, & switch.
Practice Work: 
Create a variable $marks. Write an if-elseif chain to assign a grade (A, B, C, Fail).
Create a variable $day. Use switch to print 'Weekend' for Sat/Sun and 'Weekday' for Mon-Fri.
--------------------------------------------
*/
//Assign Grade Using if-elseif-else
$marks = 78;

if($marks >= 80){
    echo "Grade: A";
}elseif($marks >= 60){
    echo "Grade: B";
}elseif($marks >= 40){
    echo "Grade: C";
}else{
    echo "Grade: Fail";
}

echo "<br>";
//Day Type Using switch
$day = "Saturday";

switch ($day){
    case "Saturday":
    case "Sunday":
        echo "Weekend";
        break;
    
    case "Monday":
    case "Tuesday":
    case "Wednesday":
    case "Thursday":
    case "Friday":
        echo "Weekday";
        break;
    
    default:
        echo "Invalid day";
}

 //Pattern Programs in PHP
 //1. 
 for($i=1; $i<=4; $i++){
    for($j=1; $j<=4; $j++){
        echo "* ";
    }
    echo "<br>";
 }
 
 //2.
 for($i=4; $i>=1; $i++){
    for($j=1; $j>=$i; $j++){
        echo "* ";
    }
    echo "<br>";
 }
 ?>