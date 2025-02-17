<?php

namespace Lib;

use Exception;
use TCPDF;

class PDF
{
    private $tcpdf;

    public function __construct()
    {
        $this->tcpdf = new TCPDF();
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->SetMargins(15, 15, 15);
        $this->tcpdf->SetAutoPageBreak(true, 20);
    }

    public function generarPedidoPDF(array $order): string
    {

        // Validar que las variables de sesión necesarias existen
        if (!isset($_SESSION['usuario']['nombre'], $_SESSION['carrito'], $_SESSION['totalCost'])) {
            throw new Exception("Faltan datos en la sesión para generar el pedido.");
        }

        $usuarioNombre = $_SESSION['usuario']['nombre'];
        $carrito = $_SESSION['carrito'];
        $costeTotal = $_SESSION['totalCost'];

        $this->tcpdf->AddPage();

        // Título principal
        $contenido = "<h1>Pedido realizado a nombre de $usuarioNombre</h1>";
        $contenido .= "<h1>Datos de su pedido con el número {$order[0]['id']}:</h1>";

        // Tabla de productos
        $contenido .= "<table border='1' cellpadding='5'>";
        $contenido .= "<tr><th>Producto</th><th>Cantidad</th><th>Precio</th></tr>";

        foreach ($carrito as $product) {
            $contenido .= "<tr>
                            <td>{$product['nombre']}</td>
                            <td>{$product['cantidad']}</td>
                            <td>{$product['precio']}</td>
                          </tr>";
        }

        $contenido .= "</table>";

        // Estado y total
        $contenido .= "<p><strong>Estado: {$order[0]['estado']}</strong></p>";
        $contenido .= "<p><strong>Total: {$costeTotal}</strong></p>";

        // Escribir contenido en PDF
        $this->tcpdf->writeHTML($contenido, true, false, true, false, '');

        // Ruta para guardar el PDF
        $filePath = __DIR__ . "/pedido_{$order[0]['id']}.pdf";

        // Guardar el PDF en el servidor
        $this->tcpdf->Output($filePath, 'F');

        return $filePath;
    }
}