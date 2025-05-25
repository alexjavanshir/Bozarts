<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Vous devez être connecté pour participer à un événement']);
    exit;
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['event_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de l\'événement manquant']);
    exit;
}

$event_id = intval($data['event_id']);
$user_id = $_SESSION['user_id'];

try {
    // Vérifier si l'événement existe et n'est pas terminé
    $check_query = "SELECT id FROM evenements WHERE id = ? AND date_fin >= NOW()";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "i", $event_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'error' => 'Événement non trouvé ou terminé']);
        exit;
    }

    // Vérifier si l'utilisateur participe déjà
    $check_participation = "SELECT id FROM evenement_participants WHERE evenement_id = ? AND participant_id = ?";
    $check_participation_stmt = mysqli_prepare($conn, $check_participation);
    mysqli_stmt_bind_param($check_participation_stmt, "ii", $event_id, $user_id);
    mysqli_stmt_execute($check_participation_stmt);
    $participation_result = mysqli_stmt_get_result($check_participation_stmt);

    if (mysqli_num_rows($participation_result) > 0) {
        echo json_encode(['success' => false, 'error' => 'Vous participez déjà à cet événement']);
        exit;
    }

    // Ajouter la participation
    $insert_query = "INSERT INTO evenement_participants (evenement_id, participant_id) VALUES (?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "ii", $event_id, $user_id);

    if (mysqli_stmt_execute($insert_stmt)) {
        echo json_encode(['success' => true, 'message' => 'Inscription réussie à l\'événement']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'inscription à l\'événement']);
    }

    mysqli_stmt_close($insert_stmt);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Une erreur est survenue : ' . $e->getMessage()]);
}
?> 