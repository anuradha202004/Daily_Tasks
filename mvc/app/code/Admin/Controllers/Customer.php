<?php

class Admin_Controllers_Customer
{
    public function deleteAction()
    {
        $root = Sdp::getBlock("page/root");
        $delete = Sdp::getBlock("admin/customer_delete");
        $root->getChild('content')->addChild("delete", $delete);
        $root->toHtml();
    }
    public function editAction()
    {
        $root = Sdp::getBlock("page/root");
        $edit = Sdp::getBlock("admin/customer_edit");
        $root->getChild('content')->addChild("edit", $edit);
        $root->toHtml();
    }

    public function listAction()
    {
        $root = Sdp::getBlock("page/root");
        $list = Sdp::getBlock("admin/customer_list");
        $root->getChild('content')->addChild("list", $list);
        $root->toHtml();
    }

    public function newAction()
    {
        $root = Sdp::getBlock("page/root");
        $new = Sdp::getBlock("admin/customer_new");
        $root->getChild('content')->addChild("new", $new);
        $root->toHtml();
    }





}
?>