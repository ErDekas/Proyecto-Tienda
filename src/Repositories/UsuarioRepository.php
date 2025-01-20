<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Usuario;
use PDO;
use PDOException;


class UsuarioRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    // Método para guardar un nuevo usuario en la base de datos
    public function insertarUsuarios(Usuario $usuario): bool|string
    {
        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO usuarios (nombre, apellidos, email, password, rol)
                 VALUES (:nombre, :apellidos, :correo, :password, :rol)"
            );

            $stmt->bindValue(':nombre', $usuario->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':apellidos', $usuario->getApellidos(), PDO::PARAM_STR);
            $stmt->bindValue(':correo', $usuario->getCorreo(), PDO::PARAM_STR);
            $stmt->bindValue(':password', $usuario->getPassword(), PDO::PARAM_STR);
            $stmt->bindValue(':rol', $usuario->getRol(), PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
        }
    }

    // Método para obtener el nombre de usuario y poder verificar el logueo
    public function obtenerCorreo(string $correoUsuario): ?array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->bindValue(':email', $correoUsuario, PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        } catch (PDOException $e) {
            error_log("Error al obtener el usuario: " . $e->getMessage());
            return null;
        }
    }

    // Método para obtener el correo
    public function comprobarCorreo(string $correoUsuario): bool
    {
        try {
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmt->bindValue(':email', $correoUsuario, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchColumn();
            return $result > 0;
        } catch (PDOException $e) {
            error_log("Error al comprobar el correo: " . $e->getMessage());
            return null;
        }
    }
}
