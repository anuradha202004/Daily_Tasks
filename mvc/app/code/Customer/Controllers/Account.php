<?php

class Customer_Controllers_Account{
  // public function indexAction(){
  //   echo"customer Account";
  // }

  public function addressAction(){
        $root = Sdp::getBlock("page/root");
        $address = Sdp::getBlock("customer/account_address");
        $root->getChild('content')->addChild("address", $address);
        $root->toHtml();
  }

  public function editAction(){
        $root = Sdp::getBlock("page/root");
        $edit = Sdp::getBlock("customer/account_edit");
        $root->getChild('content')->addChild("edit", $edit);
        $root->toHtml();
  }

  public function saveAction(){
        $root = Sdp::getBlock("page/root");
        $save = Sdp::getBlock("customer/account_save");
        $root->getChild('content')->addChild("save", $save);
        $root->toHtml();
  }



}
?>