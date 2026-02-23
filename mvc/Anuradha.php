<?php

class Owner
{
    public $name = "Guest";

    public function setName($n)
    {
        $this->name = $n;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}

class Mobile
{
    public $ownerObj = null;

    public function owner()
    {
        if ($this->ownerObj == null) {
            $this->ownerObj = new Owner();
        }
        return $this->ownerObj;
    }
}

$myMobile = new Mobile();
// We chain the methods: owner() returns the object, then we call setName()
$myMobile->owner()->setName("Anuradha");

echo "Mobile Owner: " . $myMobile->owner()->getName();
echo "\n------------------------\n";


/*
 * Inheritance-Using 'extends'
 */
class Animal
{
    public function eat()
    {
        echo "Eating food...\n";
    }
}

class Dog extends Animal
{
    public function bark()
    {
        echo "Woof! Woof!\n";
    }
}

$myDog = new Dog();
$myDog->eat();
$myDog->bark();
echo "\n------------------------\n";


/**
 * Constructor-runs automatically.
 */
class Laptop
{
    public $brand;
    public function __construct($b)
    {
        $this->brand = $b;
        echo "Laptop Object Created for: " . $this->brand . "\n";
    }
}

$workLaptop = new Laptop("Dell");
$homeLaptop = new Laptop("HP");


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


class examples
{
    private $name;

    // public function __get($pro)
    // {
    //     return $this->$pro;
    // }

    public function __set($pro, $value){
        $this->$pro=$value;
    }

}
$obj = new examples();
$obj->name="XYZ";

?>