<?php

class Catalog_Controllers_Product{
  public function listAction(){
        $root = Sdp::getBlock("page/root");
        $list = Sdp::getBlock("catalog/product_list");
        $root->getChild('content')->addChild("list", $list);
        $root->toHtml();
  }
    public function viewAction(){
        $root = Sdp::getBlock("page/root");
        $view = Sdp::getBlock("catalog/product_view");
        $root->getChild('content')->addChild("view", $view);
        $root->toHtml();
        // $root->getChild('content')->appChild("home", $home);

  }


 


}
?>