<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Vérifier que l'utilisateur existe toujours en base de données
    $query = "SELECT id FROM utilisateurs WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['id' => $userId]);
    } else {
        // L'utilisateur n'existe plus en base de données
        // Détruire la session
        session_destroy();
        echo json_encode(['error' => 'Session invalide']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Non connecté']);
}
?> 