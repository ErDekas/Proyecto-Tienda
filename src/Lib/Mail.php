<?php 

namespace Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Lib\PDF;

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

            // Generar contenido del correo con PDF
            $pdf = new PDF();
            $pdfPath = $pdf->generarPedidoPDF($pedido);

            $mail->isHTML(true);
            $mail->Body = "<p>Estimado {$_SESSION['usuario']['nombre']},</p><p>Adjuntamos el PDF con los detalles de su pedido.</p>";

            // Adjuntar el PDF generado
            $mail->addAttachment($pdfPath);

            // Enviar el correo
            $mail->send();
            return true;
        }
        catch(Exception $e){
            error_log("Error al enviar el correo: " . $e->getMessage());
            return false;
        }

    }
}
