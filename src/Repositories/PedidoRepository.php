<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Pedido;
use PDO;
use PDOException;
use Repositories\LineaDePedidoRepository;

class PedidoRepository
{
    private BaseDatos $conexion;
    private LineaDePedidoRepository $lineaDePedidoRepository;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
        $this->lineaDePedidoRepository = new LineaDePedidoRepository();
    }

    // Método para guardar pedidos
    public function guardarPedido(Pedido $pedido): bool|string
    {
        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO pedidos (usuario_id, provincia, localidad, direccion, coste, estado, fecha, hora)
                 VALUES (:usuario_id, :provincia, :localidad, :direccion, :coste, :estado, :fecha, :hora)"
            );

            $stmt->bindValue(':usuario_id', $pedido->getUsuarioId(), PDO::PARAM_INT);
            $stmt->bindValue(':provincia', $pedido->getProvincia(), PDO::PARAM_STR);
            $stmt->bindValue(':localidad', $pedido->getLocalidad(), PDO::PARAM_STR);
            $stmt->bindValue(':direccion', $pedido->getDireccion(), PDO::PARAM_STR);
            $stmt->bindValue(':coste', number_format($pedido->getCoste(), 2, '.', ''), PDO::PARAM_STR);
            $stmt->bindValue(':estado', $pedido->getEstado(), PDO::PARAM_STR);
            $stmt->bindValue(':fecha', $pedido->getFecha(), PDO::PARAM_STR);
            $stmt->bindValue(':hora', $pedido->getHora(), PDO::PARAM_STR);

            $stmt->execute();

            $pedidoId = $this->conexion->ultimoIDInsertado();

            $_SESSION['pedidoID'] = $pedidoId;

            $this->lineaDePedidoRepository->guardarLineasPedido($pedidoId);
            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    // Método para elegir pedido
    public function elegirPedido(): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM pedidos WHERE id = :id");
            $stmt->bindValue(':id', $_SESSION['pedidoID'], PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para ver los pedidos de un usuario
    public function verPedidos(): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM pedidos WHERE usuario_id = :usuario_id");
            $stmt->bindValue(':usuario_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para ver todos los pedidos
    public function verTodosLosPedidos(): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM pedidos");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function actualizarPedido(Pedido $order, int $id): bool|string{
        try {
            $stmt = $this->conexion->prepare(
                "UPDATE pedidos SET estado = :estado WHERE id = :idPedido");

               
            $stmt->bindValue(':estado', $order->getEstado(), PDO::PARAM_STR);
            $stmt->bindValue(':idPedido', $id, PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } 
        catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
