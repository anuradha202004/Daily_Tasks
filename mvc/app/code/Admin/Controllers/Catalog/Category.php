<?php

class Admin_Controllers_Catalog_Category
{
    public function deleteAction()
    {
        $root = Sdp::getBlock("page/root");
        $delete = Sdp::getBlock("admin/catalog_category_delete");
        $root->getChild('content')->addChild("delete", $delete);
        $root->toHtml();
    }
    public function editAction()
    {
        $root = Sdp::getBlock("page/root");
        $edit = Sdp::getBlock("admin/catalog_category_edit");
        $root->getChild('content')->addChild("edit", $edit);
        $root->toHtml();
    }

    public function listAction()
    {
        $root = Sdp::getBlock("page/root");
        $list = Sdp::getBlock("admin/catalog_category_list");
        $root->getChild('content')->addChild("list", $list);
        $root->toHtml();
    }

    public function newAction()
    {
        $root = Sdp::getBlock("page/root");
        $new = Sdp::getBlock("admin/catalog_category_new");
        $root->getChild('content')->addChild("new", $new);
        $root->toHtml();
    }





}
?>