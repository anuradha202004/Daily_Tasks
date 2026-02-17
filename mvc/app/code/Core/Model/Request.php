<?php

class Core_Model_Request{

        protected $_module = "page";
        protected $_controllers = "index";
        protected $_action ="index";
 
        public function __construct(){
            $uri = $this->getRequestUrl();
            $base = $this->getBaseUrl();
            $uri = str_replace($base, "", $uri);
            // Handle index.php if present in URL
            $uri = str_replace("index.php/", "", $uri);
            $uri = str_replace("index.php", "", $uri);
            
            $uri = array_values(array_filter(explode("/",$uri)));
            $this->_module      = isset($uri[0]) ? $uri[0]:"page";
            $this->_controllers = isset($uri[1]) ? $uri[1]:"index";
            $this->_action      = isset($uri[2]) ? $uri[2]:"index";
        }
        
        public function getRequestUrl(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $fullUrl  = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];    
        return $fullUrl;
   
        }
 
        public function getParams(){
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
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $scriptName = $_SERVER['SCRIPT_NAME'];
            $baseDir = dirname($scriptName);
            // Ensure trailing slash
            $baseDir = rtrim($baseDir, '/\\') . '/';
            // If the baseDir is just /, we might want to keep it or join it
            return $protocol . "://" . $host . $baseDir;
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


