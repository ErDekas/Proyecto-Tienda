<?php

namespace Models;

use Lib\BaseDatos;
use Lib\Validar;
use \InvalidArgumentException;

class Productos
{
    private BaseDatos $conexion;

    public function __construct(
        private ?int $id = null,
        private int $categoria_id = 0,
        private string $nombre = "",
        private string $descripcion = "",
        private float $precio = 0.0,
        private int $stock = 0,
        private string $oferta = "",
        private string $fecha = "",
        private string $imagen = ""
    ) {
        if ($precio < 0) {
            throw new InvalidArgumentException('Precio no puede ser negativo.');
        }
        if ($stock < 0) {
            throw new InvalidArgumentException('Stock no puede ser negativo.');
        }
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
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }
    public function getPrecio(): float
    {
        return $this->precio;
    }
    public function getCategoriaId(): int
    {
        return $this->categoria_id;
    }
    public function getStock(): int
    {
        return $this->stock;
    }
    public function getOferta(): string
    {
        return $this->oferta;
    }
    public function getFecha(): string
    {
        return $this->fecha;
    }
    public function getImagen(): string
    {
        return $this->imagen;
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
    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }
    public function setPrecio(float $precio): void
    {
        $this->precio = $precio;
    }
    public function setCategoriaId(int $categoria_id): void
    {
        $this->categoria_id = $categoria_id;
    }
    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }
    public function setOferta(string $oferta): void
    {
        $this->oferta = $oferta;
    }
    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }
    public function setImagen(string $imagen): void
    {
        $this->imagen = $imagen;
    }


    // Validaciones
    public function validarDatosProductos(): array
    {
        $errores = [];

        if (empty($this->nombre)) {
            $errores["nombre"] = "El campo 'nombre' es obligatorio";
        }

        if (empty($this->precio)) {
            $errores["precio"] = "El campo 'precio' es obligatorio";
        }

        if (empty($this->stock)) {
            $errores["stock"] = "El campo 'stock' es obligatorio";
        }

        if (empty($this->fecha)) {
            $errores["fecha"] = "El campo 'fecha' es obligatorio";
        }

        // Validar nombre
        if (!empty($this->nombre) && !Validar::validarString($this->nombre)) {
            $errores['nombre'] = "El nombre no puede contener caracteres especiales";
        }

        // Validar descripcion
        if (!empty($this->descripcion) && strlen($this->descripcion) > 65535) {
            $errores['descripcion'] = "La longitud de la descripción supera la longitud máxima";
        }

        // Validar precio
        if (!empty($this->precio) && !Validar::validarDouble($this->precio)) {
            $errores['precio'] = "La precio debe ser un número decimal";
        }

        // Validar stock
        if (!empty($this->stock) && !Validar::validarInt($this->stock)) {
            $errores['stock'] = "El stock debe ser un número entero";
        }

        // Validar oferta
        if (!empty($this->oferta) && !Validar::validarString($this->oferta) && strlen($this->oferta) > 2) {
            $errores['oferta'] = "La oferta no puede contener caracteres especiales y no puede ser más largo de 2 caracteres";
        }

        // Validar fecha
        if (!empty($this->fecha) && !Validar::validarDate($this->fecha)) {
            $errores['fecha'] = "La fecha no es válida";
        }

        // Validar imagen
        if (!empty($this->imagen) && !Validar::validarString($this->imagen)) {
            $errores['imagen'] = "El nombre de la imagen no puede contener caracteres especiales";
        }

        return $errores;
    }

    public function validarUpdate(): array
    {
        $errores = [];

        if (empty($this->nombre)) {
            $errores["nombre"] = "El campo 'nombre' es obligatorio";
        }

        if (empty($this->precio)) {
            $errores["precio"] = "El campo 'precio' es obligatorio";
        }

        if (empty($this->stock)) {
            $errores["stock"] = "El campo 'stock' es obligatorio";
        }

        // Validar nombre
        if (!empty($this->nombre) && !Validar::validarString($this->nombre)) {
            $errores['nombre'] = "El nombre no puede contener caracteres especiales";
        }

        // Validar descripcion
        if (!empty($this->descripcion) && strlen($this->descripcion) > 65535) {
            $errores['descripcion'] = "La longitud de la descripción supera la longitud máxima";
        }

        // Validar precio
        if (!empty($this->precio) && !Validar::validarDouble($this->precio)) {
            $errores['precio'] = "La precio debe ser un número decimal";
        }

        // Validar stock
        if (!empty($this->stock) && !Validar::validarInt($this->stock)) {
            $errores['stock'] = "El stock debe ser un número entero";
        }

        // Validar oferta
        if (!empty($this->oferta) && !Validar::validarString($this->oferta) && strlen($this->oferta) > 2) {
            $errores['oferta'] = "La oferta no puede contener caracteres especiales y no puede ser más largo de 2 caracteres";
        }

        // Validar imagen
        if (!empty($this->imagen) && !Validar::validarString($this->imagen)) {
            $errores['imagen'] = "El nombre de la imagen no puede contener caracteres especiales";
        }

        return $errores;
    }

    // Sanitizaciones
    public function sanitizarDatos(): void
    {
        $this->id = Validar::sanitizarInt($this->id);
        $this->categoria_id = Validar::sanitizarInt($this->categoria_id);
        $this->nombre = Validar::sanitizarString($this->nombre);
        $this->descripcion = Validar::sanitizarString($this->descripcion);
        $this->precio = Validar::sanitizarDouble($this->precio);
        $this->stock = Validar::sanitizarInt($this->stock);
        $this->oferta = Validar::sanitizarString($this->oferta);
        $this->fecha = Validar::sanitizarString($this->fecha);
        $this->imagen = Validar::sanitizarString($this->imagen);
    }
}
