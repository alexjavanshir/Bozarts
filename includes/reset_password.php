<?php
session_start();
require_once 'config.php';
require_once 'mail_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/connexion.html');
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    $_SESSION['error'] = "Email invalide";
    header('Location: ../pages/reset-password.html');
    exit;
}

try {
    // Vérifier si l'email existe
    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['success'] = "Si votre email est enregistré, vous recevrez un lien de réinitialisation";
        header('Location: ../pages/connexion.html');
        exit;
    }

    // Préparer l'email avec un design simple mais efficace
    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/Bozarts/pages/new-password.html?email=" . urlencode($email);
    $subject = "Réinitialisation de votre mot de passe - Bozarts";
    
    $body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #f4f4f4; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background-color: white; padding: 30px; border: 1px solid #ddd; }
            .button { 
                display: inline-block; 
                padding: 15px 30px; 
                background-color: #007bff; 
                color: white !important; 
                text-decoration: none; 
                border-radius: 5px; 
                font-weight: bold;
                margin: 20px 0;
            }
            .footer { background-color: #f4f4f4; padding: 15px; text-align: center; border-radius: 0 0 10px 10px; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0; color: #007bff;">Bozarts</h1>
            </div>
            <div class="content">
                <h2>Réinitialisation de votre mot de passe</h2>
                <p>Bonjour,</p>
                <p>Vous avez demandé la réinitialisation de votre mot de passe sur Bozarts.</p>
                <p>Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe :</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $resetLink . '" class="button">Réinitialiser mon mot de passe</a>
                </div>
                
                <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
                <p style="background-color: #f8f9fa; padding: 10px; border-radius: 3px; word-break: break-all;">
                    ' . $resetLink . '
                </p>
                
                <p>Si vous n\'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.</p>
            </div>
            <div class="footer">
                <p>Cordialement,<br><strong>L\'équipe Bozarts</strong></p>
            </div>
        </div>
    </body>
    </html>';

    // Tentative d'envoi de l'email
    error_log("Tentative d'envoi d'email à: " . $email);
    
    if (sendMail($email, $subject, $body)) {
        error_log("Email envoyé avec succès à: " . $email);
        $_SESSION['success'] = "Un email de réinitialisation a été envoyé à votre adresse";
    } else {
        error_log("Échec d'envoi d'email à: " . $email);
        $_SESSION['error'] = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
    }

    header('Location: ../pages/connexion.html');
    exit;

} catch (Exception $e) {
    error_log("Erreur dans reset_password.php: " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue";
    header('Location: ../pages/reset-password.html');
    exit;
}