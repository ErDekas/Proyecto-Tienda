<?php

namespace Controllers;

use Lib\Pages;
use Models\Usuario;
use Services\UsuarioServicio;
use Lib\Utils;

class UsuarioController
{

    private Pages $pages;
    private Usuario $user;
    private UsuarioServicio $userService;
    private Utils $utils;

    public function __construct()
    {
        $this->pages = new Pages();
        $this->user = new Usuario();
        $this->userService = new UsuarioServicio();
        $this->utils = new Utils();
    }

    // Método para registrarse
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            unset($_SESSION['registrado']);
            $this->pages->render('usuarios/registrar');
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['data']) {
                $data = $_POST['data'];
                $usuario = $this->user = Usuario::fromArray($data);

                $usuario->sanitizarDatos();
                $errores = $usuario->validarDatosRegistro();

                if ($data['password'] !== $data['confirmar_password']) {
                    $errores['confirmar_password'] = "Las contraseñas no son iguales";
                }

                if ($this->userService->comprobarCorreo($data['email'])) {
                    $errores['email'] = "El correo ya existe";
                }

                if (empty($errores)) {
                    $password_segura = password_hash($usuario->getPassword(), PASSWORD_BCRYPT, ['cost' => 10]);
                    $usuario->setPassword($password_segura);

                    $userData = [
                        'nombre' => $usuario->getNombre(),
                        'apellidos' => $usuario->getApellidos(),
                        'correo' => $usuario->getCorreo(),
                        'password' => $password_segura,
                        'rol' => $usuario->getRol()
                    ];

                    $resultado = $this->userService->insertarUsuarios($userData);

                    if ($resultado === true) {
                        $_SESSION['mensaje'] = "Usuario registrado. Por favor, revisa tu correo para confirmar la cuenta.";
                        $this->pages->render('usuarios/registrar');
                        exit;
                    } else {
                        $errores['db'] = "Error al registrar al usuario: " . $resultado;
                        $this->pages->render('usuarios/registrar', [
                            "errores" => $errores,
                            "user" => $this->user
                        ]);
                    }
                }

                $this->pages->render('usuarios/registrar', [
                    "errores" => $errores,
                    "user" => $this->user
                ]);
            } else {
                $_SESSION['falloDatos'] = 'fallo';
            }
        }
    }

    


    public function confirmarCuenta() {
        $token = $_GET['token'] ?? null;
        
        if (!$token) {
            $_SESSION['error'] = "Token no proporcionado";
            header("Location: " . BASE_URL);
            exit;
        }
    
        try {
            $resultado = $this->userService->confirmarCuenta($token);
            if ($resultado) {
                $_SESSION['mensaje'] = "Cuenta confirmada exitosamente. Ya puedes iniciar sesión.";
            } else {
                $_SESSION['error'] = "No se pudo confirmar la cuenta. El token puede haber expirado o ser inválido.";
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Error al confirmar la cuenta: " . $e->getMessage();
        }
        
        header("Location: " . BASE_URL);
        exit;
    }

    public function iniciarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if($this->utils->isSession()){
                header("Location: " . BASE_URL ."");
            }
            else{

                $this->pages->render('usuarios/iniciaSesion');
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            $errores = [];
        
           
                $correo = $_POST['correo'];
                $passwordInicioSesion = $_POST['password'];

                $datosUsuario = $this->userService->obtenerCorreo($correo);

                // Crear objeto Usuario con los datos para iniciar sesión
                $usuario = new Usuario( null, "", "", $correo, $passwordInicioSesion, "", $datosUsuario['confirmado'], "", "");

                // Sanitizar datos
                $usuario->sanitizarDatos();

                // Validar datos
                $errores = $usuario->validarDatosLogin();

                // Si no hay errores volver a inicio si hay mostrarlos en el formulario
                if (empty($errores)) {
                    $resultado = $this->userService->iniciarSesion($usuario->getCorreo(), $usuario->getPassword());
    
                    if ($resultado) {
                        $_SESSION['usuario'] = $resultado;

                        if (!isset($_SESSION['usuario'])) {
                            echo "Error: la sesión no se ha establecido";
                            exit;
                        }
                        header("Location: " . BASE_URL);
                        exit;
                    } 
                    else {
                        $errores['login'] = "Los datos introducidos son incorrectos";
                    }
                }

                // Redirigir la vista con los errores 
                $this->pages->render('usuarios/iniciaSesion', ["errores" => $errores]);
            
        
        }
    }


    // Método para cerrar sesión
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL);
        exit;
    }

    // Método para ver los datos del usuario que esta logueado
    public function verTusDatos()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Pasar datos guardados en la sesión
        $usuActual = $_SESSION['usuario'];

        $this->pages->render("usuarios/datosUsuario", ["usuario" => $usuActual]);
    }
}
