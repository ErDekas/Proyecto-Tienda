<?php

namespace Lib;

use Controllers\ErrorController;
use Lib\Security;

class Router
{
    private static array $routes = [];
    private static array $protectedRoutes = [];
    private static Security $security;

    public static function init(): void 
    {
        self::$security = new Security();
    }

    public static function add(string $method, string $action, callable $controller, bool $protected = false): void
    {
        $action = trim($action, '/');
        self::$routes[$method][$action] = $controller;
        
        if ($protected) {
            self::$protectedRoutes[$method][$action] = true;
        }
    }

    private static function validateToken(): bool
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? $_SESSION['token'] ?? null;

        if (!$token) {
            return false;
        }

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $decoded = self::$security->verifyToken($token);
        return $decoded !== null;
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = preg_replace('/Proyecto-Tienda/', '', $_SERVER['REQUEST_URI']);
        $action = trim($action, '/');

        // Extract query parameters
        $queryPosition = strpos($action, '?');
        if ($queryPosition !== false) {
            $action = substr($action, 0, $queryPosition);
        }

        $param = null;
        preg_match('/[0-9]+$/', $action, $match);

        if (!empty($match)) {
            $param = $match[0];
            $action = preg_replace('/' . $match[0] . '/', ':id', $action);
        }

        // Check if route exists
        $fn = self::$routes[$method][$action] ?? null;

        if ($fn) {
            // Check if route is protected
            if (isset(self::$protectedRoutes[$method][$action])) {
                if (!self::validateToken()) {
                    header('HTTP/1.0 401 Unauthorized');
                    echo json_encode(['error' => 'Unauthorized access']);
                    exit;
                }
            }

            $callback = self::$routes[$method][$action];
            echo call_user_func($callback, $param);
        } else {
            ErrorController::error404();
        }
    }
}