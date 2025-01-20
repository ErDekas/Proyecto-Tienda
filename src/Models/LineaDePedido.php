<?php

namespace Models;

use DateTime;
use Lib\BaseDatos;
use Lib\Validar;

/**
 * Clase para crear los objetos OrderLine
 */
class LineaDePedido
{
    private BaseDatos $conexion;
    private mixed $stmt;

    /**
     * Constructor que inicializa los campos del objeto OrderLine
     */
    public function __construct(
        private ?int $id = null,
        private int $pedido_id = 0,
        private int $producto_id = 0,
        private int $unidades = 0
    ) {
        $this->conexion = new BaseDatos();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedidoID(): int
    {
        return $this->pedido_id;
    }

    public function getProductoID(): string
    {
        return $this->producto_id;
    }

    public function getUnidades(): string
    {
        return $this->unidades;
    }



    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setPedidoID(int $pedido_id): void
    {
        $this->pedido_id = $pedido_id;
    }

    public function setProductoID(string $producto_id): void
    {
        $this->producto_id = $producto_id;
    }

    public function setUnidades(string $unidades): void
    {
        $this->unidades = $unidades;
    }
}
