<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/connexion.html');
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? ''; // Corrigé: le nom correspond au HTML

if (!$email || !$password || !$confirmPassword) {
    $_SESSION['error'] = "Tous les champs sont requis";
    header('Location: ../pages/new-password.html?email=' . urlencode($email));
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['error'] = "Les mots de passe ne correspondent pas";
    header('Location: ../pages/new-password.html?email=' . urlencode($email));
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères";
    header('Location: ../pages/new-password.html?email=' . urlencode($email));
    exit;
}

try {
    // Vérifier si l'email existe
    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error'] = "Utilisateur non trouvé";
        header('Location: ../pages/connexion.html');
        exit;
    }

    // Hasher le nouveau mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe
    $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?");
    $stmt->execute([$hashedPassword, $email]);

    $_SESSION['success'] = "Votre mot de passe a été réinitialisé avec succès";
    header('Location: ../pages/connexion.html');
    exit;

} catch (Exception $e) {
    error_log("Erreur dans update_password.php: " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue lors de la réinitialisation du mot de passe";
    header('Location: ../pages/new-password.html?email=' . urlencode($email));
    exit;
}