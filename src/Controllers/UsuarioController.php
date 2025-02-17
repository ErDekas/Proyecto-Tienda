<?php

namespace Controllers;

use Lib\Pages;
use Models\Usuario;
use Services\UsuarioServicio;
use Lib\Utils;
use Lib\Security;
use Lib\MailRecuperacion;

class UsuarioController
{

    private Pages $pages;
    private Usuario $user;
    private UsuarioServicio $userService;
    private MailRecuperacion $mailer;
    private Utils $utils;
    private Security $security;

    public function __construct()
    {
        $this->pages = new Pages();
        $this->user = new Usuario();
        $this->userService = new UsuarioServicio();
        $this->mailer = new MailRecuperacion();
        $this->utils = new Utils();
        $this->security = new Security();

    }

    // Método para registrarse
    public function registrar()
    {
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




    public function confirmarCuenta()
    {
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

    public function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if ($this->utils->isSession()) {
                header("Location: " . BASE_URL . "");
            } else {

                $this->pages->render('usuarios/iniciaSesion');
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $errores = [];


            $correo = $_POST['correo'];
            $passwordInicioSesion = $_POST['password'];

            $datosUsuario = $this->userService->obtenerCorreo($correo);

            // Crear objeto Usuario con los datos para iniciar sesión
            $usuario = new Usuario(null, "", "", $correo, $passwordInicioSesion, "", $datosUsuario['confirmado'], "", "");

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
                } else {
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

    public function password(){

        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if($this->utils->isSession()){
                header("Location: " . BASE_URL ."");
            }
            else{

                $this->pages->render('usuarios/recuperar');
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

          if($_POST['correo']){

            $correo = $_POST['correo'] ?? '';
            $user = new Usuario(null, "", "", $correo, "", "", false, "", "");
            $user->sanitizarDatos();
        
            $errores = $user->validarDatosRecuperar();

            if (!empty($errores)) {
                $this->pages->render('usuarios/recuperar', ["errores" => $errores]);
                return;
            }

            $userToRecover = $this->userService->obtenerCorreo($correo);

            $data = [
                'id' => $userToRecover['id'],
                'correo' => $correo
            ];
    
            $token = $this->security->generateToken($data);
            $key = $this->security->secretKey();
            $token_exp = $this->security->generateEmailToken();

            $userData = [
                'nombre' => '',
                'apellidos' => '',
                'correo' => $correo,
                'contrasena' => '',
                'rol' => '',
                'confirmado' => '',
                'token' => $token,
                'token_exp' => $token_exp['expiration']
            ];


            $resultado = $this->userService->updateUserPassword($userData, $userToRecover['id']);

            if ($resultado === true) {
                $this->mailer->sendRecoveryEmail($userData['correo'], $userData['nombre'], $userData['token']);
                $_SESSION['cambio'] = true;
                $this->pages->render('usuarios/recuperar');
                exit;
            } 
            else {
                $errores['db'] = "Error al actualizar el token en el usuario: " . $resultado;
                $this->pages->render('usuarios/recuperar', [
                    "errores" => $errores,
                    "user" => $this->user
                ]);
            }

          }
          else{
            $_SESSION['falloDatos'] = 'fallo';
          }
        }

    }


    /**
     * Metodo que cambia la contraseña si el usuario lo ha olvidado
     * @var string con el token del usuario que va cambiar la contraseña
     * @return void
     */
    public function changePassword(string $token){

        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if($this->utils->isSession()){
                header("Location: " . BASE_URL ."");
            }
            else{

                if (!$token) {
                    $_SESSION['error'] = "Token no proporcionado";
                    header("Location: " . BASE_URL);
                    exit;
                }
                else{
                    $this->pages->render('usuarios/recuperarContra', ["token" => $token]);
                }
                
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

          if($_POST['data']){

                $data = $_POST['data'];
                $user = $this->user = Usuario::fromArray($data);
                
                // Sanitizar datos
                $user->sanitizarDatos();

                // Validar datos
                $errores = $user->validarDatosCambioContraseña();

                if($data['contrasena'] !== $data['confirmar_contrasena']){
                    $errores['confirmar_contrasena'] = "Las contraseñas no son iguales";
                }

                if (!empty($errores)) {
                    $this->pages->render('usuarios/recuperarContra', ["errores" => $errores]);
                    return;
                }

                $contraseñaCambiar = $this->security->encryptPassw($user->getPassword());

                //$token = $_GET['token'] ?? null;
                try {
                    $resultado = $this->userService->changePassword($token, $contraseñaCambiar);
                    if ($resultado) {
                        $_SESSION['mensaje'] = "Contraseña cambiada exitosamente. Ya puedes iniciar sesión.";
                    } else {
                        $_SESSION['error'] = "No se pudo cambiar la contraseña. El token puede haber expirado o ser inválido.";
                    }
                    header("Location: " . BASE_URL);
                } catch (\Exception $e) {
                    $_SESSION['error'] = "Error al cambiar la contraseña: " . $e->getMessage();
                }

          }
          else{
            $_SESSION['falloDatos'] = 'fallo';
          }
        }
    }
}
