<?php

namespace Models;

use Lib\BaseDatos;
use Lib\Validar;

class Usuario
{
    private BaseDatos $conexion;
    private mixed $stmt;

    public function __construct(
        private ?int $id = null,
        private string $nombre = "",
        private string $apellidos = "",
        private string $correo = "",
        private string $password = "",
        private string $rol = ""
    ) {
        $this->conexion = new BaseDatos();
    }

    // Metodos Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    // Metodos Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setApellidos(string $apellidos): void
    {
        $this->apellidos = $apellidos;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRol(string $rol): void
    {
        $this->rol = $rol;
    }

    // Métodos de validación
    public function validarDatosRegistro(): array
    {
        $errores = [];

        // Validar campos requeridos
        if (empty($this->nombre) || empty($this->password) || empty($this->rol)) {
            $errores[] = "Los campos 'Nombre', 'Contraseña' y 'Rol' son obligatorios";
        }

        // Validar nombre
        if (!Validar::validarNombre($this->nombre)) {
            $errores['nombre'] = "El nombre no puede contener caracteres especiales";
        }

        // Validar apellidos
        if (!Validar::validarApellidos($this->apellidos)) {
            $errores['apellidos'] = "Los apellidos no pueden contener caracteres especiales";
        }

        // Validar email
        if (!Validar::validarEmail($this->correo)) {
            $errores['email'] = "El correo electrónico no es válido";
        }

        // Validar contraseña
        if (!Validar::validarPassword($this->password)) {
            $errores['password'] = "La contraseña debe de ser de al menos 8 caracteres y debe tener una letra mayúscula, minúscula, un número y un símbolo especial";
        }

        // Validar rol
        if (!in_array($this->rol, ["admin", "user"])) {
            $errores['rol'] = "El rol tiene que ser 'admin' o el rol 'user'";
        }

        return $errores;
    }

    public function sanitizarDatos(): void
    {
        $this->id = Validar::sanitizarInt($this->id);
        $this->nombre = Validar::sanitizarString($this->nombre);
        $this->apellidos = Validar::sanitizarString($this->apellidos);
        $this->correo = Validar::sanitizarEmail($this->correo);
        $this->password = Validar::sanitizarString($this->password);
        $this->rol = Validar::sanitizarString($this->rol);
    }


    public function validarDatosLogin(): array
    {
        $errores = [];

        if (empty($this->correo)) {
            $errores['correo'] = "El campo correo es obligatorio.";
        }

        if (empty($this->password)) {
            $errores['password'] = "El campo contraseña es obligatorio.";
        }

        return $errores;
    }

    public static function fromArray(array $data): Usuario
    {
        return new Usuario(
            $data['id'] ?? null,
            $data['nombre'] ?? "",
            $data['apellidos'] ?? "",
            $data['email'] ?? "",
            $data['password'] ?? "",
            $data['rol'] ?? 'user' ?? 'admin'
        );
    }
}
