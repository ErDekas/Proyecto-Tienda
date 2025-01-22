<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Usuario;
use PDO;
use PDOException;
use DateTime;


class UsuarioRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    // Método para guardar un nuevo usuario en la base de datos
    public function guardarUsuarios(Usuario $usuario): bool|string
    {
        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO usuarios (nombre, apellidos, email, password, rol, confirmado, token, token_exp)
                 VALUES (:nombre, :apellidos, :correo, :contrasena, :rol, :confirmado, :token, :token_exp)"
            );

            $stmt->bindValue(':nombre', $usuario->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':apellidos', $usuario->getApellidos(), PDO::PARAM_STR);
            $stmt->bindValue(':correo', $usuario->getCorreo(), PDO::PARAM_STR);
            $stmt->bindValue(':contrasena', $usuario->getPassword(), PDO::PARAM_STR);
            $stmt->bindValue(':rol', $usuario->getRol(), PDO::PARAM_STR);
            $stmt->bindValue(':confirmado', $usuario->getConfirmado(), PDO::PARAM_BOOL);
            $stmt->bindValue(':token', $usuario->getToken(), PDO::PARAM_STR);
            $stmt->bindValue(':token_exp', $usuario->getToken_Exp(), PDO::PARAM_STR);

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
            error_log("Resultado de obtenerCorreo: " . print_r($usuario, true));
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

    // Método para comprobar la confirmacion
    public function confirmarCuenta(string $token): bool
    {
        try {
            // Primero verificamos si el token existe y no ha expirado
            $stmt = $this->conexion->prepare(
                "SELECT id, confirmado, token_exp 
             FROM usuarios 
             WHERE token = :token"
            );
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return false; // Token no encontrado
            }

            if ($usuario['confirmado']) {
                return true; // La cuenta ya estaba confirmada
            }

            // Verificar si el token ha expirado
            $tokenExp = new DateTime($usuario['token_exp']);
            $ahora = new DateTime();

            if ($ahora > $tokenExp) {
                return false; // Token expirado
            }

            // Actualizar el usuario: confirmar cuenta y limpiar el token
            $stmt = $this->conexion->prepare(
                "UPDATE usuarios 
             SET confirmado = 1, 
                 token = NULL, 
                 token_exp = NULL 
             WHERE id = :id AND token = :token"
            );

            $stmt->bindValue(':id', $usuario['id'], PDO::PARAM_INT);
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al confirmar la cuenta: " . $e->getMessage());
            return false;
        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
        }
    }

    public function regenerarTokenConfirmacion(string $email): bool
{
    try {
        // Generar nuevo token y fecha de expiración
        $nuevoToken = bin2hex(random_bytes(32));
        $expiracion = (new DateTime())->modify('+24 hours')->format('Y-m-d H:i:s');
        
        $stmt = $this->conexion->prepare(
            "UPDATE usuarios 
             SET token = :token, 
                 token_exp = :expiracion 
             WHERE email = :email 
             AND confirmado = 0"
        );
        
        $stmt->bindValue(':token', $nuevoToken, PDO::PARAM_STR);
        $stmt->bindValue(':expiracion', $expiracion, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        error_log("Error al regenerar el token: " . $e->getMessage());
        return false;
    } finally {
        if(isset($stmt)) {
            $stmt->closeCursor();
        }
    }
}
}
