<?php

session_start();

// Capturer la sortie de check_session.php
ob_start();
require_once '../includes/check_session.php';
$sessionOutput = ob_get_clean();

// Décoder la sortie de la session
$sessionData = json_decode($sessionOutput, true);

// Vérifier si l'utilisateur est connecté et est admin
if (isset($sessionData['error']) || $sessionData['droit'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

require_once '../config/database.php';

// Récupérer les données envoyées en POST
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que l'ID est présent
if (!isset($data['id'])) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "ID manquant"]);
    exit;
}

// Validation de l'ID
if (!is_numeric($data['id']) || $data['id'] <= 0) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "ID invalide"]);
    exit;
}

// Supprimer la FAQ
$query = "DELETE FROM faq WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $data['id']);
    if (mysqli_stmt_execute($stmt)) {
        // Vérifier le nombre de lignes affectées
        if (mysqli_stmt_affected_rows($stmt) === 0) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "FAQ non trouvée"]);
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            "success" => true,
            "message" => "FAQ supprimée avec succès"
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["error" => "Erreur lors de la suppression : " . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Erreur de préparation de la requête : " . mysqli_error($conn)]);
}
exit;
