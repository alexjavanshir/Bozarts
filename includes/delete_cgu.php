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
$id = $data['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

try {
    // Supprimer la section
    $query = "DELETE FROM cgu WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Réorganiser l'ordre des sections restantes
        $query = "SET @count = 0; UPDATE cgu SET ordre = @count:= @count + 1 ORDER BY ordre;";
        mysqli_multi_query($conn, $query);
        
        echo json_encode(['success' => true, 'message' => 'Section supprimée avec succès']);
    } else {
        throw new Exception("Erreur lors de la suppression de la section");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression de la section']);
}
?> 