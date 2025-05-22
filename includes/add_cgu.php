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
$titre = $data['titre'] ?? null;
$contenu = $data['contenu'] ?? null;

if (!$titre || !$contenu) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

try {
    // Trouver le dernier ordre
    $query = "SELECT MAX(ordre) as max_ordre FROM cgu";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $ordre = ($row['max_ordre'] ?? 0) + 1;

    // Insérer la nouvelle section
    $query = "INSERT INTO cgu (titre, contenu, ordre) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $titre, $contenu, $ordre);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Section ajoutée avec succès']);
    } else {
        throw new Exception("Erreur lors de l'ajout de la section");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'ajout de la section']);
}
?> 