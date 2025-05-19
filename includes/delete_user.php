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
$userId = $data['userId'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'ID utilisateur manquant']);
    exit;
}

try {
    // Ne pas permettre la suppression de l'admin principal
    $stmt = mysqli_prepare($conn, "SELECT email FROM utilisateurs WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $targetUser = mysqli_fetch_assoc($result);

    if ($targetUser && $targetUser['email'] === 'admin@bozarts.com') {
        http_response_code(403);
        echo json_encode(['error' => 'Impossible de supprimer le compte administrateur principal']);
        exit;
    }

    // Supprimer l'utilisateur
    $stmt = mysqli_prepare($conn, "DELETE FROM utilisateurs WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression de l\'utilisateur']);
} 