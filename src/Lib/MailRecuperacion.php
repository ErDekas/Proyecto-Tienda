<?php

namespace Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailRecuperacion {
    private PHPMailer $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Configurar SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['SMTP_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['SMTP_USERNAME'];
        $this->mailer->Password = $_ENV['SMTP_PASSWORD'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $_ENV['SMTP_PORT'];
        
        $this->mailer->setFrom($_ENV['SMTP_USERNAME'], "Deka's Shop");
        $this->mailer->isHTML(true);
    }

    public function sendRecoveryEmail(string $email, string $nombre, string $token): bool {
        try {
            $resetUrl = BASE_URL . "usuarios/recuperarContra?token=" . $token;
            
            $this->mailer->addAddress($email, $nombre);
            $this->mailer->Subject = 'Recuperación de contraseña';
            
            $content = "
                <!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Recuperación de contraseña</title>
</head>
<body style='font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f9f9f9; padding: 20px;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); overflow: hidden;'>
                    <tr>
                        <td style='padding: 20px; text-align: center; background-color: #d9534f; color: #ffffff;'>
                            <h1 style='margin: 0; font-size: 24px;'>Recuperación de contraseña</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 20px; text-align: left; color: #333;'>
                            <p style='margin: 0 0 10px;'>Hola {$nombre},</p>
                            <p style='margin: 0 0 10px;'>Has solicitado restablecer tu contraseña. Para continuar, haz clic en el siguiente enlace:</p>
                            <p style='margin: 20px 0; text-align: center;'>
                                <a href='{$resetUrl}' style='background-color: #d9534f; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 16px;'>Restablecer contraseña</a>
                            </p>
                            <p style='margin: 0 0 10px;'>Si no has solicitado este cambio, puedes ignorar este mensaje.</p>
                            <p style='margin: 0 0 10px;'>El enlace expirará en 24 horas.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; text-align: center; background-color: #f1f1f1; font-size: 12px; color: #888;'>
                            <p style='margin: 0;'>© 2025 Tu Empresa. Todos los derechos reservados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
            ";
            
            $this->mailer->Body = $content;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return false;
        }
    }
}
