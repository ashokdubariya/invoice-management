<?php
/**
 * Simple Router Class
 */

class Router {
    private $routes = [];

    /**
     * Add a GET route
     */
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    /**
     * Add a POST route
     */
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    /**
     * Dispatch the request
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Get the base path from SCRIPT_NAME (e.g., /php-invoice/public)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        // Remove base path from request URI
        if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
            $path = substr($requestUri, strlen($scriptName));
        } else {
            $path = $requestUri;
        }
        
        // Ensure path starts with /
        if (empty($path) || $path[0] !== '/') {
            $path = '/' . $path;
        }
        
        // Remove trailing slash except for root
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        // Check if route exists
        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            
            if (is_callable($callback)) {
                call_user_func($callback);
            } elseif (is_array($callback)) {
                [$controller, $method] = $callback;
                $controllerInstance = new $controller();
                $controllerInstance->$method();
            }
        } else {
            // 404 Not Found
            http_response_code(404);
            echo "404 - Page Not Found";
        }
    }
}
