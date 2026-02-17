<?php

class Page_Block_Menu extends Core_Block_Template{
    public function __construct(){
        $this->setTemplate("Page/View/menu.phtml");
    }

    public function getMenuArray(){

        return [
        "category1" => "category1",
        "category2" => "category2"
        ];
}
}

?>