<?php

namespace Services;

use Models\LineaDePedido;
use Repositories\LineaDePedidoRepository;

class LineaDePedidoServicio
{
    private LineaDePedidoRepository $lineaDePedidoRepository;

    public function __construct()
    {
        $this->lineaDePedidoRepository = new LineaDePedidoRepository();
    }

    public function verLineasDePedido(int $id): array
    {
        $lineas = $this->lineaDePedidoRepository->verLineasDePedido($id);
        if (empty($lineas)) {
            error_log("No se encontraron líneas de pedido para el ID: $id");
        }
        return $lineas;
    }
}
