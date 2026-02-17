<?php

class Core_Controllers_Front extends Core_Controllers_Front_Action{
    protected $__request;

    // public function __construct(){
        
    // }

    public function run(){
        
        $request = $this->getRequest();
        // $request = Sdp :: getModel("core/request");


        $className = sprintf("%s_Controllers_%s", 
        ucfirst($request->getModuleName()),
        ucfirst($request->getControllerName()));

        $action = $request->getActionName()."Action";
        $classObj = new $className();
        $classObj->$action();
    }

}
?>