<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// AJUSTA ESTAS RUTAS según dónde guardaste la carpeta de PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function enviarTicketPorCorreo($email_usuario, $nombre_usuario, $nombre_archivo) {
    $mail = new PHPMailer(true);
    $debugLog = '';
    
    try {
        // --- CONFIGURACIÓN DEL SERVIDOR ---
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = function($str, $level) use (&$debugLog) {
            $debugLog .= "[{$level}] {$str}\n";
        };
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'autohub2311@gmail.com'; 
        $mail->Password   = 'bixk pmxv ekpq kxll'; // La clave de 16 letras de Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8'; // Para que salgan bien los acentos

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // --- DESTINATARIOS ---
        $mail->setFrom('autohub2311@gmail.com', 'Sistemas II - AutoHub');
        $mail->addAddress($email_usuario, $nombre_usuario);

        // --- ADJUNTO ---
        if (file_exists($nombre_archivo)) {
            $mail->addAttachment($nombre_archivo); // Adjunta el .txt generado
        }

        // --- CONTENIDO ---
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de Compra - Ticket #' . date('dmY');
        
        $mail->Body = "
        <div style='font-family: Arial; border: 1px solid #ddd; padding: 20px;'>
            <h2 style='color: #2e7d32;'>¡Gracias por tu compra, $nombre_usuario!</h2>
            <p>Tu pedido ha sido procesado con éxito.</p>
            <p>Adjunto a este correo encontrarás tu <b>ticket de compra</b> con el detalle de tus productos.</p>
            <hr>
            <p style='font-size: 12px; color: #777;'>AutoHub - Proyecto Sistemas II</p>
        </div>";

        $mail->send();
        file_put_contents('/tmp/autohub_mail.log', date('c') . " OK to={$email_usuario} file={$nombre_archivo}\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        $msg = date('c') . " ERROR to={$email_usuario} err=" . $mail->ErrorInfo . " ex=" . $e->getMessage() . "\n" . $debugLog . "\n";
        file_put_contents('/tmp/autohub_mail.log', $msg, FILE_APPEND);
        return false;
    }
}