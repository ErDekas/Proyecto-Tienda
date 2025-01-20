<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\LineaDePedido;
use PDO;
use PDOException;

class LineaDePedidoRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    public function guardarLineasPedido(int $idPedido): bool|string
    {
        try {
            $this->conexion->empezarTransaccion();

            $stmt = $this->conexion->prepare(
                "INSERT INTO lineas_pedidos (pedido_id, producto_id, unidades)
                 VALUES (:pedido_id, :producto_id, :unidades)"
            );

            foreach ($_SESSION['carrito'] as $producto) {
                $stmt->bindValue(':pedido_id', $idPedido, PDO::PARAM_INT);
                $stmt->bindValue(':producto_id', $producto['id'], PDO::PARAM_INT);
                $stmt->bindValue(':unidades', $producto['cantidad'], PDO::PARAM_INT);


                $stmt->execute();
            }

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->deshacer();
            return $e->getMessage();
        }
    }

    public function verLineasDePedido(int $pedido_id): array
    {
        try {
            $stmt = $this->conexion->prepare("
            SELECT 
                lineas_pedidos.*, 
                productos.nombre as producto_nombre,
                productos.precio as producto_precio,
                productos.descripcion as producto_descripcion
            FROM lineas_pedidos 
            LEFT JOIN productos ON lineas_pedidos.producto_id = productos.id 
            WHERE pedido_id = :pedido_id
        ");
            $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
