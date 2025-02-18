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
                lp.*, 
                p.nombre as producto_nombre,
                p.precio as producto_precio,
                p.descripcion as producto_descripcion
            FROM lineas_pedidos lp
            LEFT JOIN productos p ON lp.producto_id = p.id 
            WHERE lp.pedido_id = :pedido_id
        ");
        
        $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug
        error_log("SQL para pedido $pedido_id: " . print_r($result, true));
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error en verLineasDePedido: " . $e->getMessage());
        return [];
    }
}
}
