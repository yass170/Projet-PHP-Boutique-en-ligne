<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

function envoyerMail(string $destinataire, string $sujet, string $corps): bool {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USER, SMTP_APP_NAME);
        $mail->addAddress($destinataire);

        $mail->isHTML(false);
        $mail->Subject = $sujet;
        $mail->Body    = $corps;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Erreur PHPMailer : " . $mail->ErrorInfo;
        return false;
    }
}
