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

// Vérifier que toutes les données requises sont présentes
if (!isset($data['id']) || !isset($data['question']) || !isset($data['titre_reponse']) || !isset($data['reponse'])) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Données manquantes"]);
    exit;
}

// Mettre à jour la FAQ
$query = "UPDATE faq SET question = ?, titre_reponse = ?, reponse = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sssi", $data['question'], $data['titre_reponse'], $data['reponse'], $data['id']);
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) === 0) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "FAQ non trouvée ou aucune modification apportée"]);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode([
            "success" => true,
            "message" => "FAQ mise à jour avec succès",
            "faq" => [
                "id" => $data['id'],
                "question" => $data['question'],
                "titre_reponse" => $data['titre_reponse'],
                "reponse" => $data['reponse']
            ]
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["error" => "Erreur lors de la mise à jour : " . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Erreur de préparation de la requête : " . mysqli_error($conn)]);
}
exit; 