<?php

class Admin_Controllers_Catalog_Product extends Core_Controllers_Admin
{
    public function deleteAction()
    {
        $root = Sdp::getBlock("page/root");
        $delete = Sdp::getBlock("admin/catalog_product_delete");
        $root->getChild('content')->addChild("delete", $delete);
        $root->toHtml();
    }
    public function editAction()
    {
        $root = Sdp::getBlock("page/root");
        $edit = Sdp::getBlock("admin/catalog_product_edit");
        $root->getChild('content')->addChild("edit", $edit);
        $root->toHtml();
    }

    public function listAction()
    {
        $root = Sdp::getBlock("page/root");
        $list = Sdp::getBlock("admin/catalog_product_list");
        $root->getChild('content')->addChild("list", $list);
        $root->toHtml();
    }

    public function newAction()
    {
        $root = Sdp::getBlock("page/root");
        $new = Sdp::getBlock("admin/catalog_product_new");
        $root->getChild('content')->addChild("new", $new);
        $root->toHtml();
    }

 public function saveAction()
    {
        $product = Sdp::getModel('catalog/product');
        echo "<pre>";
        $product->load(1);
        print_r($product);
        print_r($product->getUrl());
    }

}
?>