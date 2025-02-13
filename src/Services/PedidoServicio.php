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
}
