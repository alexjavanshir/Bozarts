<?php
// Configuration de l'email
define('SMTP_FROM_EMAIL', 'bozarts.site@gmail.com');
define('SMTP_FROM_NAME', 'Bozarts');

function sendMail($to, $subject, $body) {
    // Headers pour l'email HTML
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    // Encodage du sujet en UTF-8
    $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
    
    // Envoyer l'email
    $result = mail($to, $subject, $body, $headers);
    
    // Log pour debug
    if (!$result) {
        error_log("Échec d'envoi d'email à: $to");
    } else {
        error_log("Email envoyé avec succès à: $to");
    }
    
    return $result;
}