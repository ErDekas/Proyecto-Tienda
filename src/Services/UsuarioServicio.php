<?php

namespace Services;

use Models\Usuario;
use Repositories\UsuarioRepository;

class UsuarioServicio
{
    private UsuarioRepository $repository;

    public function __construct()
    {
        $this->repository = new UsuarioRepository();
    }

    // Método que llama al repository para guardar un usuario en la base de datos
    public function insertarUsuarios(array $userData): bool|string
    {
        try {
            $usuario = new Usuario(
                null,
                $userData['nombre'],
                $userData['apellidos'],
                $userData['correo'],
                $userData['password'],
                $userData['rol']
            );

            return $this->repository->insertarUsuarios($usuario);
        } catch (\Exception $e) {
            error_log("Error al guardar el usuario: " . $e->getMessage());
            return false;
        }
    }

    // Método que llama al repository y obtiene el nombre de usuario
    public function obtenerCorreo(string $correo): ?array
    {
        return $this->repository->obtenerCorreo($correo);
    }

    // Método que llama al repository y comprueba el correo
    public function comprobarCorreo(string $correoUsuario): ?bool
    {
        return $this->repository->comprobarCorreo($correoUsuario);
    }

    // Método para comprobar el usuario que se esta introduciendo esta en la base de datos para poder loguearse
    public function iniciarSesion(string $correo, string $contrasena): ?array
    {
        $usuario = $this->obtenerCorreo($correo);

        if ($usuario && password_verify($contrasena, $usuario['password'])) {
            return $usuario;
        }

        return null;
    }
}
