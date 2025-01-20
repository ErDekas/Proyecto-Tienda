<?php

namespace Controllers;

use Lib\Pages;
use Models\Usuario;
use Services\UsuarioService;
use Services\UsuarioServicio;

class UsuarioController
{

    private Pages $pages;
    private Usuario $user;
    private UsuarioServicio $userService;


    public function __construct()
    {
        $this->pages = new Pages();
        $this->user = new Usuario();
        $this->userService = new usuarioServicio();
    }

    // Método para registrarse
    public function registrar()
    {
        //Obtener datos formularios, sanetizarlos y validarlos


        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            unset($_SESSION['registrado']);
            $this->pages->render('usuarios/registrar');
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Crear instancia de Usuario con los datos del POST

            if ($_POST['data']) {


                $data = $_POST['data'];
                $usuario = $this->user = Usuario::fromArray($data);

                /*die(var_dump($data));*/

                // Sanitizar datos
                $usuario->sanitizarDatos();

                // Validar datos
                $errores = $usuario->validarDatosRegistro();

                // Validar que las contraseñas coincidan
                if ($data['password'] !== $data['confirmar_password']) {
                    $errores['confirmar_password'] = "Las contraseñas no son iguales";
                }

                if ($this->userService->comprobarCorreo($data['email'])) {
                    $errores['email'] = "El correo ya existe";
                }

                if (empty($errores)) {
                    // Cifrar la contraseña
                    $password_segura = password_hash($usuario->getpassword(), PASSWORD_BCRYPT, ['cost' => 10]);
                    $usuario->setpassword($password_segura);

                    $userData = [
                        'nombre' => $usuario->getNombre(),
                        'apellidos' => $usuario->getApellidos(),
                        'correo' => $usuario->getCorreo(),
                        'password' => $password_segura,
                        'rol' => $usuario->getRol()
                    ];

                    $resultado = $this->userService->insertarUsuarios($userData);

                    if ($resultado === true) {
                        $_SESSION['registrado'] = true;
                        $this->pages->render('usuarios/registrar');
                        exit;
                    } else {
                        $errores['db'] = "Error al registrar al usuario: " . $resultado;
                        $this->pages->render('usuarios/registrar', [
                            "errores" => $errores,
                            "user" => $this->user
                        ]);
                    }
                } else {
                    $this->pages->render('usuarios/registrar', [
                        "errores" => $errores,
                        "user" => $this->user
                    ]);
                }
            } else {
                $_SESSION['falloDatos'] = 'fallo';
            }
        }
    }



    // Método para iniciar sesión
    public function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->pages->render('usuarios/iniciaSesion');
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $errores = [];


            $correo = $_POST['correo'];
            $passwordInicioSesion = $_POST['password'];

            // Crear objeto Usuario con los datos para iniciar sesión
            $usuario = new Usuario(null, "", "", $correo, $passwordInicioSesion, "");

            // Sanitizar datos
            $usuario->sanitizarDatos();

            // Validar datos
            $errores = $usuario->validarDatosLogin();

            // Si no hay errores volver a inicio si hay mostrarlos en el formulario
            if (empty($errores)) {
                $resultado = $this->userService->iniciarSesion($usuario->getCorreo(), $usuario->getpassword());

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
}
