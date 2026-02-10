<?php

class Router {
    protected $routes = [];

    // Register a route
    public function add($route, $controller, $action) {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
    }

    // Dispatch the request
    public function dispatch($url) {
        // Remove query string
        $url = strtok($url, '?');
        // Remove leading/trailing slashes
        $url = trim($url, '/');

        // Check for defined routes first
        if (array_key_exists($url, $this->routes)) {
            $controllerName = $this->routes[$url]['controller'];
            $actionName = $this->routes[$url]['action'];
        } else {
            // Fallback to convention: /controller/method
            $parts = explode('/', $url);
            
            // Default controller
            if (empty($parts[0])) {
                $controllerName = 'HomeController';
                $actionName = 'index';
            } else {
                // Capitalize first letter and append 'Controller'
                $controllerName = ucfirst($parts[0]) . 'Controller';
                // Use second part as method, default to 'index'
                $actionName = isset($parts[1]) ? $parts[1] : 'index';
            }
        }

        // Try to load the controller class via Autoloader
        if (class_exists($controllerName)) {
            $controller = new $controllerName;
            
            if (method_exists($controller, $actionName)) {
                call_user_func_array([$controller, $actionName], []);
            } else {
                // Try appending 'Action' convention if simple method not found
                $actionMethod = $actionName . 'Action';
                if (method_exists($controller, $actionMethod)) {
                    call_user_func_array([$controller, $actionMethod], []);
                } else {
                    die("Action $actionName not found in controller $controllerName");
                }
            }
        } 
        // Fallback for legacy controllers (if not autoloaded by namespace map)
        elseif (file_exists(__DIR__ . '/../Controllers/' . $controllerName . '.php')) {
            require_once __DIR__ . '/../Controllers/' . $controllerName . '.php';
            $controller = new $controllerName;

            if (method_exists($controller, $actionName)) {
                call_user_func_array([$controller, $actionName], []);
            } else {
                die("Method $actionName not found in controller $controllerName");
            }
        } else {
            // Handle 404
            die("Controller $controllerName not found");
        }
    }
}
