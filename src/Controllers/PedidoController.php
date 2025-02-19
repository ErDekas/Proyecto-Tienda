<?php

namespace Controllers;

use Lib\Pages;
use Lib\Utils;
use Lib\Mail;
use Models\Pedido;
use Models\LineaDePedido;
use Services\ProductosServicio;
use Services\PedidoServicio;
use Services\PaypalServicio;
use Controllers\CartController;
use Services\LineaDePedidoServicio;

class PedidoController
{
    private Pages $pages;
    private Utils $utils;
    private Mail $mail;
    private PedidoServicio $pedidoServicio;
    private LineaDePedidoServicio $lineaDePedidoServicio;
    private PaypalServicio $paypalService;
    private ProductosServicio $productosServicio;
    private CartController $cartController;

    public function __construct()
    {
        $this->pages = new Pages();
        $this->utils = new Utils();
        $this->mail = new Mail();
        $this->pedidoServicio = new PedidoServicio();
        $this->paypalService = new PaypalServicio();
        $this->lineaDePedidoServicio = new LineaDePedidoServicio();
        $this->productosServicio = new ProductosServicio();
        $this->cartController = new CartController();
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
                    'coste' => $_SESSION['totalCost'],
                    'estado' => 'confirmado',
                    'fecha' => (new \DateTime())->format('Y-m-d'),
                    'hora' => (new \DateTime())->format('H:i:s'),
                ];
                $totalAmount = $_SESSION['totalCost'];
                $currency = 'EUR';
                $description = 'Pedido en Fake Web Storage';
                $returnUrl = BASE_URL;
                $cancelUrl = BASE_URL . 'pedido/pagoCancelado';
                $paypalUrl = $this->paypalService->createPayment($totalAmount, $currency, $description, $returnUrl, $cancelUrl);
                if ($paypalUrl) {
                    header("Location: " . $paypalUrl);
                } else {
                    $errores['payment'] = 'No se pudo procesar el pago. Intenta nuevamente.';
                    $this->pages->render('pedido/formularioDePedido', ["errores" => $errores]);
                    return;
                }
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
        $admin = false;
        if ($this->utils->isSession() && $this->utils->isAdmin()) {
            $admin = true;
        }

        $pedidos = $this->pedidoServicio->verPedidos();
        $lineaDePedidos = [];
        $productos = [];

        foreach ($pedidos as $pedido) {
            $lineaDePedido = $this->lineaDePedidoServicio->verLineasDePedido($pedido['id']);

            foreach ($lineaDePedido as $linea) {
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
            'admin' => $admin  // Añadiendo la variable admin
        ]);
    }

    public function verTodosLosPedidos()
    {
        // Debug de sesión
        error_log('SESSION: ' . print_r($_SESSION, true));

        

        // Verifica si el usuario es administrador
        if (!isset($_SESSION['usuario']['rol']) || $_SESSION['usuario']['rol'] !== "admin") {
            error_log('Acceso denegado: el usuario no es administrador.');
            header("Location: " . BASE_URL);
            exit();
        }

        error_log('Acceso concedido: el usuario es administrador.');

        // Obtener todos los pedidos
        $pedidos = $this->pedidoServicio->verTodosLosPedidos();
        $lineaDePedidos = [];
        $productos = [];

        foreach ($pedidos as $pedido) {
            // Debug de cada pedido
            error_log('Procesando pedido ID: ' . $pedido['id']);

            $lineasPedido = $this->lineaDePedidoServicio->verLineasDePedido($pedido['id']);

            // Si no hay líneas de pedido, se asigna un array vacío
            $lineaDePedidos[$pedido['id']] = !empty($lineasPedido) ? $lineasPedido : [];

            // Debug de líneas de pedido
            error_log('Líneas encontradas para pedido ' . $pedido['id'] . ': ' . print_r($lineaDePedidos[$pedido['id']], true));

            // Guardar productos si hay líneas de pedido
            if (!empty($lineasPedido)) {
                foreach ($lineasPedido as $linea) {
                    if (!empty($linea['producto_nombre'])) {
                        $productos[$linea['producto_id']] = $linea['producto_nombre'];
                    }
                }
            }
        }

        $_SESSION['productosPedidos'] = $productos;

        // Debug final
        error_log('Productos en sesión: ' . print_r($productos, true));
        error_log('Líneas de pedidos: ' . print_r($lineaDePedidos, true));

        // Renderizar la vista con los pedidos
        $this->pages->render('pedido/pedidos', [
            'pedidos' => $pedidos,
            'lineaDePedidos' => $lineaDePedidos,
            'admin' => true
        ]);
    }

    public function actualizarEstadoPedidos(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $this->pages->render('pedido/formularioDeActualizacionDePedido', ["idPedido" => $id]);
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $order = new Pedido(
                null,
                0,
                '',
                '',
                '',
                0.0,
                $_POST['orderState'],
                '',
                ''
            );

            // Sanitizar datos
            $order->sanitizarDatosActualizado();

            // Validar datos
            $errores = $order->validarDatosActualizado();

            if (empty($errores)) {

                $orderData = [
                    'estado' => $order->getEstado(),
                ];
                //die("No hay errores");

                $resultado = $this->pedidoServicio->actualizarPedido($orderData, $id);


                if ($resultado === true) {
                    $_SESSION['actualizado'] = true;
                    $this->pages->render('pedido/formularioDeActualizacionDePedido');
                    exit;
                } else {
                    $errores['db'] = "Error al actualizar el pedido: " . $resultado;
                    $this->pages->render('pedido/formularioDeActualizacionDePedido', ["errores" => $errores]);
                }
            } else {
                $this->pages->render('pedido/formularioDeActualizacionDePedido', ["errores" => $errores]);
            }
        }
    }
    /**
     * Metodo que redirije si se cancela el pago
     * @return void
     */
    public function paymentCancel()
    {
        $this->pages->render('Order/paymentCancel');
    }

    /**
     * Metodo que redirije si el pago ha sido exitoso
     * @return void
     */
    public function paymentSuccess()
    {
        $paymentId = $_GET['paymentId'];
        $payerId = $_GET['PayerID'];

        // Ejecuta el pago
        $paymentExecuted = $this->paypalService->executePayment($paymentId, $payerId);

        if ($paymentExecuted) {
            return true;
        } else {
            // Si el pago no fue exitoso
            $errores['payment'] = 'El pago no se completó correctamente.';
            $this->pages->render('pedido/formularioDePedido', ["errores" => $errores]);
        }
    }
}
