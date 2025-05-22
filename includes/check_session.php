<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactiver l'affichage des erreurs

session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Vérifier que l'utilisateur existe toujours en base de données et récupérer son type
    $query = "SELECT id, type, email, droit, nom, prenom FROM utilisateurs WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $response = [
            'id' => $userId,
            'type' => $user['type'],
            'email' => $user['email'],
            'droit' => $user['droit'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom']
        ];
        echo json_encode($response);
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