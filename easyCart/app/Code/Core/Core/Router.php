<?php

class Core_Router {
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

        $controllerName = null;
        $actionName = null;
        $params = [];

        // First, check for defined routes (both exact and parameterized)
        foreach ($this->routes as $route => $routeParams) {
            // Convert route to regex if it contains parameters (e.g., {})
            if (strpos($route, '{') !== false) {
                 // Escape standard regex characters but NOT curly braces yet (we handle them)
                 // Actually, simpler approach: replace {param} with regex group first
                 // Then escape the rest? No.
                 // Manual construction:
                 $pattern = $route;
                 // Escape delimiters if any (none in our simple routes usually, but safer)
                 // We use # as delimiter
                 
                 // Replace {param} with named placeholder or just group
                 // We use ([^/]+) for segment
                 $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $pattern);
                 
                 // Now wrap in delimiters and anchor
                 $pattern = '#^' . $pattern . '$#';
                 
                 if (preg_match($pattern, $url, $matches)) {
                     array_shift($matches); // Remove full match
                     
                     $controllerName = $routeParams['controller'];
                     $actionName = $routeParams['action'];
                     $params = $matches;
                     break; // Found a match, stop iterating
                 }
            } elseif ($url == $route) {
                // Exact match
                $controllerName = $routeParams['controller'];
                $actionName = $routeParams['action'];
                $params = [];
                break; // Found a match, stop iterating
            }
        }

        // If no explicit route matched, fall back to convention: /controller/method
        if ($controllerName === null) {
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
                // Remaining parts are parameters
                $params = array_slice($parts, 2);
            }
        }

        // Try to load the controller class via Autoloader
        if (class_exists($controllerName)) {
            $controller = new $controllerName;
            
            if (method_exists($controller, $actionName)) {
                call_user_func_array([$controller, $actionName], $params);
            } else {
                // Try appending 'Action' convention if simple method not found
                $actionMethod = $actionName . 'Action';
                if (method_exists($controller, $actionMethod)) {
                    call_user_func_array([$controller, $actionMethod], $params);
                } else {
                    die("Action $actionName not found in controller $controllerName");
                }
            }
        } 
        // Fallback for legacy controllers (if not autoloaded by namespace map)
        elseif (file_exists(__DIR__ . '/../../../Controllers/' . $controllerName . '.php')) {
            require_once __DIR__ . '/../../../Controllers/' . $controllerName . '.php';
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
