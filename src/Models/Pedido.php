<?php

namespace Models;

use DateTime;
use Lib\BaseDatos;
use Lib\Validar;

class Pedido
{
    private BaseDatos $conexion;
    private mixed $stmt;

    public function __construct(
        private ?int $id = null,
        private int $usuario_id = 0,
        private string $provincia = "",
        private string $localidad = "",
        private string $direccion = "",
        private float $coste = 0.0,
        private string $estado = "",
        private string $fecha = "",
        private string $hora = ""
    ) {
        $this->conexion = new BaseDatos();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarioId(): int
    {
        return $this->usuario_id;
    }

    public function getProvincia(): string
    {
        return $this->provincia;
    }

    public function getLocalidad(): string
    {
        return $this->localidad;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function getCoste(): float
    {
        return $this->coste;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function getHora(): string
    {
        return $this->hora;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setUsuarioId(int $usuario_id): void
    {
        $this->usuario_id = $usuario_id;
    }

    public function setProvincia(string $provincia): void
    {
        $this->provincia = $provincia;
    }

    public function setLocalidad(string $localidad): void
    {
        $this->localidad = $localidad;
    }

    public function setDireccion(string $direccion): void
    {
        $this->direccion = $direccion;
    }

    public function setCoste(float $coste): void
    {
        $this->coste = $coste;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function setHora(string $hora): void
    {
        $this->hora = $hora;
    }

    /**
     * Metodo para validar los campos de los formularios
     * @return array array con los errores en caso de que haya
     */
    public function validarDatos(): array
    {
        $errores = [];

        // Validar dirección
        if (empty($this->direccion)) {
            $errores["direccion"] = "El campo 'dirección' es obligatorio";
        }

        if (empty($this->provincia)) {
            $errores["provincia"] = "El campo 'provincia' es obligatorio";
        }

        if (empty($this->localidad)) {
            $errores["localidad"] = "El campo 'localidad' es obligatorio";
        }



        if (!Validar::validarDireccion($this->direccion)) {
            $errores["direccion"] = "El formato de la direccion no es valido";
        }

        if (!Validar::validarCiudad($this->provincia)) {
            $errores["provincia"] = "El formato de la provincia no es valido";
        }

        if (!Validar::validarCiudad($this->localidad)) {
            $errores["localidad"] = "El formato de la localidad no es valido";
        }

        return $errores;
    }

    public function sanitizarDatos(): void
    {
        $this->direccion = Validar::sanitizarString($this->direccion);
        $this->provincia = Validar::sanitizarString($this->provincia);
        $this->localidad = Validar::sanitizarString($this->localidad);
    }
    
    public function sanitizarDatosActualizado(): void {
        $this->estado = Validar::sanitizarString($this->estado);
    }

    public function validarDatosActualizado(): array {
        $errores = [];
    
        // Validar dirección
        if (empty($this->estado)) {
            $errores["estado"] = "El campo 'estado' es obligatorio";
        } 

        if (!Validar::validarString($this->estado)) {
            $errores["estado"] = "El formato del estado no es valido";
        }
    
        return $errores;
    }
}
