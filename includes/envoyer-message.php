<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

// Vérifier si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données du message
$destinataire_id = $_POST['destinataire_id'] ?? null;
$sujet = $_POST['sujet'] ?? '';
$contenu = $_POST['contenu'] ?? '';

// Vérifier que toutes les données nécessaires sont présentes
if (!$destinataire_id || !$sujet || !$contenu) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insérer le message
    $stmt = $pdo->prepare("
        INSERT INTO messages (expediteur_id, destinataire_id, sujet, contenu)
        VALUES (:expediteur_id, :destinataire_id, :sujet, :contenu)
    ");

    $stmt->execute([
        ':expediteur_id' => $_SESSION['user_id'],
        ':destinataire_id' => $destinataire_id,
        ':sujet' => $sujet,
        ':contenu' => $contenu
    ]);

    echo json_encode(['success' => true, 'message_id' => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur lors de l\'envoi du message']);
}
?> 