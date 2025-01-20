<?php

namespace Controllers;

use Lib\Pages;
use Lib\Utils;
use Lib\Mail;
use Models\Pedido;
use Models\LineaDePedido;
use Services\ProductosServicio;
use Services\PedidoServicio;
use Services\LineaDePedidoServicio;

class PedidoController
{
    private Pages $pages;
    private Utils $utils;
    private Mail $mail;
    private PedidoServicio $pedidoServicio;
    private LineaDePedidoServicio $lineaDePedidoServicio;
    private ProductosServicio $productosServicio;

    public function __construct()
    {
        $this->pages = new Pages();
        $this->utils = new Utils();
        $this->mail = new Mail();
        $this->pedidoServicio = new PedidoServicio();
        $this->lineaDePedidoServicio = new LineaDePedidoServicio();
        $this->productosServicio = new ProductosServicio();
    }

    // Método para comprobar login
    public function autenticarPedido()
    {
        if (isset($_SESSION['usuario'])) {
            $this->guardarPedido();
        } else {
            $this->pages->render('usuarios/iniciaSesion');
        }
    }

    // Método para guardar el pedido
    public function guardarPedido()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->pages->render('pedido/formularioDePedido');
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pedido = new Pedido(
                null,
                0,
                $_POST['provincia'],
                $_POST['localidad'],
                $_POST['direccion'],
                0.0,
                '',
                '',
                ''
            );

            $pedido->sanitizarDatos();
            $errores = $pedido->validarDatos();

            if (empty($errores)) {
                $datos_pedido = [
                    'usuario_id' => $_SESSION['usuario']['id'],
                    'provincia' => $pedido->getProvincia(),
                    'localidad' => $pedido->getLocalidad(),
                    'direccion' => $pedido->getDireccion(),
                    'coste' => $_SESSION['costeTotal'],
                    'estado' => 'confirmado',
                    'fecha' => (new \DateTime())->format('Y-m-d'),
                    'hora' => (new \DateTime())->format('H:i:s'),
                ];

                $resultado = $this->pedidoServicio->guardarPedido($datos_pedido);

                if ($resultado === true) {
                    $stock = $this->productosServicio->actualizarStockProducto();
                    if ($stock === true) {
                        $pedido = $this->pedidoServicio->elegirPedido();
                        $this->mail->enviarMail($pedido);
                        $_SESSION['pedido'] = true;
                        unset($_SESSION['carrito']);
                        unset($_SESSION['costeTotal']);
                        unset($_SESSION['pedidoId']);
                        $this->pages->render('pedido/formularioDePedido');
                        exit;
                    } else {
                        $errores['db'] = 'Error al actualizar el stock del producto' . $stock;
                        $this->pages->render('pedido/formularioDePedido', ['errores' => $errores]);
                    }
                } else {
                    $errores['db'] = 'Error al guardar el pedido' . $resultado;
                    $this->pages->render('pedido/formularioDePedido', ['errores' => $errores]);
                }
            } else {
                $this->pages->render('pedido/formularioDePedido', ['errores' => $errores]);
            }
        }
    }

    public function verPedidos()
    {
        $pedidos = $this->pedidoServicio->verPedidos();
        $lineaDePedidos = [];
        $productos = [];

        foreach ($pedidos as $pedido) {
            $lineaDePedido = $this->lineaDePedidoServicio->verLineasDePedido($pedido['id']);

            foreach ($lineaDePedido as $linea) {
                // Ahora podemos usar directamente el nombre del producto que viene del JOIN
                if (!empty($linea['producto_nombre'])) {
                    $productos[$linea['producto_id']] = $linea['producto_nombre'];
                }
            }

            $lineaDePedidos[] = $lineaDePedido;
        }

        $_SESSION['productosPedidos'] = $productos;

        $this->pages->render('pedido/pedidos', [
            'pedidos' => $pedidos,
            'lineaDePedidos' => $lineaDePedidos,
        ]);
    }
}
