<?php

namespace Services;

use Models\Usuario;
use Repositories\UsuarioRepository;
use Lib\Security;
use Lib\MailConfirmacion;

class UsuarioServicio
{
    private UsuarioRepository $repository;
    private Security $security;
    private MailConfirmacion $mailConfirmacion;

    public function __construct()
    {
        $this->repository = new UsuarioRepository();
        $this->security = new Security();
        $this->mailConfirmacion = new MailConfirmacion();
    }

    // Método que llama al repository para guardar un usuario en la base de datos
    public function insertarUsuarios(array $userData): bool|string {
        try {
            // Generate email confirmation token
            $tokenData = $this->security->generateEmailToken();
            
            $usuario = new Usuario(
                null,
                $userData['nombre'],
                $userData['apellidos'],
                $userData['correo'],
                $userData['password'],
                $userData['rol'],
                false, // confirmado
                $tokenData['token'],
                $tokenData['expiration']
            );

            $result = $this->repository->guardarUsuarios($usuario);
            
            if ($result === true) {
                // Send confirmation email
                $this->mailConfirmacion->sendConfirmationEmail(
                    $usuario->getCorreo(),
                    $usuario->getNombre(),
                    $tokenData['token']
                );
                return true;
            }
            
            return $result;
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

    public function confirmarCuenta(string $token): bool {
        try {
            return $this->repository->confirmarCuenta($token);
        } catch (\Exception $e) {
            error_log("Error al confirmar la cuenta: " . $e->getMessage());
            return false;
        }
    }

    public function iniciarSesion(string $correo, string $contrasena): ?array {
        $usuario = $this->obtenerCorreo($correo);
        error_log("Tipo de contraseña: " . gettype($contrasena));
        error_log("Longitud de contraseña: " . strlen($contrasena));
        error_log("Contraseña en bytes: " . bin2hex($contrasena));
        if ($usuario && $this->security->validatePassw($contrasena, $usuario['password'])) {
            
        if ($usuario && password_verify($contrasena, $usuario['password'])) {
            if (!$usuario['confirmado']) {
                throw new \Exception('Cuenta no confirmada. Por favor, revisa tu correo.');
            }

            // Generate JWT token
            $token = $this->security->generateToken([
                'id' => $usuario['id'],
                'email' => $usuario['email'],
                'rol' => $usuario['rol']
            ]);

            $usuario['token'] = $token;
            return $usuario;
        }
    }

        return null;
    }
    
    public function updateUserPassword(array $userData, int $id): bool|string {
        try {
            $user = new Usuario(
                null,
                $userData['nombre'],
                $userData['apellidos'],
                $userData['correo'],
                $userData['password'],
                $userData['rol'],
                $userData['confirmado'],
                $userData['token'],
                $userData['token_exp']
            );

            return $this->repository->updateUserPassword($user, $id);
        } 
        catch (\Exception $e) {
            error_log("Error al actualizar el usuario: " . $e->getMessage());
            return false;
        }
    }
    public function changePassword(string $token, string $password){
        return $this->repository->cambiarContraseña($token, $password);
    }
}
