<?php

namespace Services;

use Models\Pedido;
use Repositories\PedidoRepository;

class PedidoServicio
{
    private PedidoRepository $pedidoRepository;

    public function __construct()
    {
        $this->pedidoRepository = new PedidoRepository();
    }

    public function guardarPedido(array $datosPedido): bool|string
    {
        try {
            $pedido = new Pedido(
                null,
                $datosPedido['usuario_id'],
                $datosPedido['provincia'],
                $datosPedido['localidad'],
                $datosPedido['direccion'],
                $datosPedido['coste'],
                $datosPedido['estado'],
                $datosPedido['fecha'],
                $datosPedido['hora']
            );
            return $this->pedidoRepository->guardarPedido($pedido);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function elegirPedido(): array
    {
        return $this->pedidoRepository->elegirPedido();
    }

    public function verPedidos(): array
    {
        return $this->pedidoRepository->verPedidos();
    }

    public function verTodosLosPedidos(): array
    {
        return $this->pedidoRepository->verTodosLosPedidos();
    }
    public function actualizarPedido(array $userData, int $id): bool|string {
        try {
            $order = new Pedido(
                null,                        
                0,                            
                '',          
                '',          
                '',          
                0.0,                          
                $userData['estado'],                           
                '',                           
                ''                           
            );

            return $this->pedidoRepository->actualizarPedido($order, $id);
        } 
        catch (\Exception $e) {
            error_log("Error al actualizar la categoria: " . $e->getMessage());
            return false;
        }
    }
}
