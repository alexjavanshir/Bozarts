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
$makeAdmin = $data['makeAdmin'] ?? false;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'ID utilisateur manquant']);
    exit;
}

try {
    // Ne pas permettre la modification de l'admin principal
    $stmt = $conn->prepare("SELECT email FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $targetUser = $result->fetch_assoc();

    if ($targetUser && $targetUser['email'] === 'admin@bozarts.com') {
        http_response_code(403);
        echo json_encode(['error' => 'Impossible de modifier les droits du compte administrateur principal']);
        exit;
    }

    // Mettre à jour les droits de l'utilisateur
    $stmt = $conn->prepare("UPDATE utilisateurs SET droit = ? WHERE id = ?");
    $newDroit = $makeAdmin ? 'admin' : 'sans-droit';
    $stmt->bind_param("si", $newDroit, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Erreur lors de la mise à jour");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la modification des droits administrateur: ' . $e->getMessage()]);
} 