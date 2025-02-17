<?php
namespace Lib;
use Controllers\ErrorController;
use Lib\Security;

/**
 * Clase que redirige a las diferente rutas del proyecto 
 * correctamente pudiendole pasarle parametros por la url
 */
class Router {

    private static array $routes = [];
    private static array $protectedRoutes = [];
    private static Security $security;

    public static function init(): void 
    {
        self::$security = new Security();
    }

    /**
     * Metodo que asigna las rutas estando ya protegidas
     * @return void
     */
    public static function add(string $method, string $action, callable $controller, bool $protected = false): void{
        self::assignRoute($method, trim($action, '/'), $controller, $protected);
    }

    /**
     * Metodo para crear la rutas protegidas, pasandole el metodo
     * la accion, el controlador y si esta protegido o no
     * @return void
     */
    private static function assignRoute(string $method, string $action, callable $controller, bool $protected): void{
        if (!isset(self::$routes[$method])) {
            self::$routes[$method] = [];
        }

        self::$routes[$method][$action] = $controller;

        if ($protected) {
            if (!isset(self::$protectedRoutes[$method])) {
                self::$protectedRoutes[$method] = [];
            }

            self::$protectedRoutes[$method][$action] = true;
        }
    }   

    /**
     * Metodo para validar el token que recibes por url 
     * desde el enlace del correo
     * @return bool
     */
    private static function validateToken(): bool{
        $token = self::extractToken();

        if (!$token) {
            return false;
        }

        return self::$security->validaToken($token) !== null;
    }
    
    /**
     * Metodo que extrae el token para validarlo
     * @return string
     */
    private static function extractToken(): ?string{
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? $_SESSION['token'] ?? null;

        if ($token && strpos($token, 'Bearer ') === 0) {
            return substr($token, 7);
        }

        return $token;
    }
   
    /**
     * Metodo que redirige a las diferentes rutas del sitio web
     */
    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = preg_replace('/Proyecto-Tienda/', '', $_SERVER['REQUEST_URI']);
        $action = trim($action, '/');

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

        $fn = self::$routes[$method][$action] ?? null;

        if ($fn) {
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