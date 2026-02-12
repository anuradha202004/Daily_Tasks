<?php

class Core_Model_Request{

    protected $_module = "page";
    protected $_controllers = "index";
    protected $_action = "index";

    public function __construct(){
        $uri = $this -> getRequestUri();
        $base = $this->getBaseUrl();
        if (strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        $uri = trim($uri, "/");
        $uri = explode("/", $uri);

        $this-> _module = isset($uri[0]) && $uri[0] != "" ? $uri[0] : "core";
        $this-> _controllers = isset($uri[1]) && $uri[1] != "" ? $uri[1] : "front";
        $this-> _action = isset($uri[2]) && $uri[2] != "" ? $uri[2] : "index";
    }
    public function getRequestUri(){
        return $_SERVER['REQUEST_URI'];
    }
     public function getParam(){
        return $_REQUEST;
    }

     public function isPost(){
        return (isset($_POST)) ? true : false;
    }

     public function getQuery(){
        return $_GET;
    }

     public function getPost(){
        return $_POST;
    }

     public function getBaseUrl(){
        return str_replace("index.php", "", $_SERVER['SCRIPT_NAME']);
    }

     public function getControllerName(){
        return $this->_controllers;
    }

    public function getModuleName(){
        return $this->_module;
    }

    public function getActionName(){
        return $this->_action;
    }
}
?>


