<?php

class Core_Controllers_Front{
    protected $__request;

    // public function __construct(){
        
    // }

    public function run(){
        
        // $request = new Core_Model_Request();
        $request = Sdp :: getModel("core/request");

        $module = ucfirst($request->getModuleName());
        $controller = implode("_", array_map("ucfirst", explode("_", $request->getControllerName())));
        
        $className = sprintf("%s_Controllers_%s", $module, $controller);

        $action = $request->getActionName()."Action";
        $classObj = new $className();
        $classObj->$action();
    }

}
?>