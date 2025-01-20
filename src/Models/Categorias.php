<?php

namespace Models;

use Lib\BaseDatos;
use Lib\Validar;

class Categorias
{
    private BaseDatos $conexion;

    public function __construct(
        private ?int $id = null,
        private string $nombre = "",
    ) {
        $this->conexion = new BaseDatos();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNombre(): string
    {
        return $this->nombre;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    // Validaciones
    public function validarDatos(): array
    {
        $errores = [];

        // Validar campos
        if (empty($this->nombre)) {
            $errores['nombre'] = "El campo nombre es obligatorio";
        }

        // Validar nombre
        if (!Validar::validarNombre($this->nombre)) {
            $errores['nombre'] = "El campo nombre no puede contener caracteres especiales ni números";
        }

        return $errores;
    }

    public function validarBorrado(int $id): array
    {
        $errores = [];

        // Validar campos
        if (empty($id)) {
            $errores['id'] = "La categoria es obligatorio";
        }

        // Validar ID
        if (!Validar::validarInt($id)) {
            $errores['id'] = "La categoria seleccionada no es válida";
        }

        return $errores;
    }

    // Sanitizaciones
    public function sanitizarDatos(): void
    {
        $this->nombre = Validar::sanitizarString($this->nombre);
    }

    public function sanitizarBorrado(): void
    {
        $this->id = Validar::sanitizarInt($this->id);
    }

    public static function fromArray(array $data): Categorias
    {
        return new Categorias(
            $data['id'] ?? null,
            $data['nombre'] ?? ""
        );
    }
}
