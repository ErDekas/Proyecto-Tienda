<?php 

namespace Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mail {
    
    // Método para enviar correo del pedido
    public function enviarMail(array $pedido){

        $mail = new PHPMailer(true);

        try{
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST']; 
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME']; 
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTPSECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];




            $mail->setFrom($_ENV['SMTP_USERNAME'], "Deka's Shop");
            $mail->addAddress($_SESSION['usuario']['email'], $_SESSION['usuario']['nombre']); 

            $mail->Subject = 'Datos del pedido';
            
            $contenido = $this->generarMail($pedido);
            $mail->isHTML(true);
            $mail->Body = $contenido;

            // Enviar el correo
            $mail->send();
            return true;
        }
        catch(Exception $e){
            error_log("Error al enviar el correo: " . $e->getMessage());
            return false;
        }

    }

    // Método para generar el cuerpo del correo a enviar
    function generarMail(array $order){
        

        $contenido = "<h1>Pedido realizado a nombre de " . $_SESSION['usuario']['nombre'] .  "</h1>";
        $contenido .= "<h1>Datos de su pedido con el numero " .  $order[0]['id'] .":</h1>";
        $contenido .= "<table border='1'>";
        $contenido .= "<tr><th>Producto</th><th>Cantidad</th><th>Precio</th></tr>";

        // Recorremos el carrito y agregamos los productos al cuerpo del correo
        foreach ($_SESSION['carrito'] as $product) {
            $contenido .= "<tr>
                            <td>" . $product['nombre'] . "</td>
                            <td>" . $product['cantidad'] . "</td>
                            <td>" . $product['precio'] . "</td>
                        </tr>";
        }

        $total = $_SESSION['costeTotal'];
        $contenido .= "</table>";
        $contenido .= "<p><strong>Estado: " . $order[0]['estado'] ." </strong></p>";
        $contenido .= "<p><strong>Total: {$total}</strong></p>";

        return $contenido;

    }


}