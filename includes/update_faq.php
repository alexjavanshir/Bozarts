<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Vérifier que c'est bien l'admin
$query = "SELECT email, droit FROM utilisateurs WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user || $user['droit'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Récupérer les données POST
$data = json_decode(file_get_contents('php://input'), true);
$content = $data['content'] ?? null;

if (!$content) {
    http_response_code(400);
    echo json_encode(['error' => 'Contenu manquant']);
    exit;
}

try {
    // Mettre à jour le fichier FAQ
    $file_path = '../pages/faq.html';
    if (file_put_contents($file_path, $content)) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Erreur lors de l'écriture du fichier");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour de la FAQ']);
} 