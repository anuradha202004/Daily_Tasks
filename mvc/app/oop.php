<?php
// echo "<pre>";
// class A
// {
//     protected $n = 10;
//     public function i($n)
//     {
//         $this->n = $n;

//     }

//     public function g()
//     {
//         return $this->n;
//     }
// }
// class B
// {
//     public $a = null;
//     public function a()
//     {
//         if ($this->a == null) {
//             $this->a = new A;
//         }
//         print_r($this->a);
//         return $this->a;

//     }

// }

// $b = new B;
// $b->a()->i(20);
// echo $b->a()->g();

// echo "<pre>";

// // --- SIMPLE OOP EXAMPLES ---

// /**
//  * EXAMPLE 1: OBJECT CHAINING
//  * This follows your A and B style. 
//  * Class 'Mobile' has an 'Owner'.
//  */
// class Owner {
//     public $name = "Guest";

//     public function setName($n) {
//         $this->name = $n;
//         return $this; // Returning $this allows "chaining" ->
//     }

//     public function getName() {
//         return $this->name;
//     }
// }

// class Mobile {
//     public $ownerObj = null;

//     // This function ensures the 'Owner' object exists
//     public function owner() {
//         if ($this->ownerObj == null) {
//             $this->ownerObj = new Owner();
//         }
//         return $this->ownerObj;
//     }
// }

// // How to use it:
// $myMobile = new Mobile();
// // We chain the methods: owner() returns the object, then we call setName()
// $myMobile->owner()->setName("John"); 

// echo "Mobile Owner: " . $myMobile->owner()->getName(); 
// echo "\n------------------------\n";


// /**
//  * EXAMPLE 2: INHERITANCE (Simple)
//  * Using 'extends' to share code.
//  */
// class Animal {
//     public function eat() {
//         echo "Eating food...\n";
//     }
// }

// // Dog 'extends' Animal (gets the 'eat' function for free)
// class Dog extends Animal {
//     public function bark() {
//         echo "Woof! Woof!\n";
//     }
// }

// $myDog = new Dog();
// $myDog->eat();   // From Animal
// $myDog->bark();  // From Dog
// echo "\n------------------------\n";


// /**
//  * EXAMPLE 3: CONSTRUCTOR
//  * A function that runs automatically.
//  */
// class Laptop {
//     public $brand;

//     // __construct runs the moment you say 'new Laptop'
//     public function __construct($b) {
//         $this->brand = $b;
//         echo "Laptop Object Created for: " . $this->brand . "\n";
//     }
// }

// $workLaptop = new Laptop("Dell");
// $homeLaptop = new Laptop("HP");


class Link
{
    public string $name;
    public string $type;

    public function Create($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function Show()
    {
        echo "name: $this->name ,type: $this->type";
    }
}

class shoLink extends Link
{

}
class User
{
    public Link $link;

    public function __construct()
    {
        $this->link = new Link();
    }
}
$user = new User();
$user->link->Create("Anu", "Short");
$user->link->Show();

?>