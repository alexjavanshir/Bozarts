<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Vous devez être connecté pour supprimer un événement']);
    exit;
}

// Récupérer les données JSON (ID de l'événement)
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['event_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de l\'événement manquant']);
    exit;
}

$event_id = intval($data['event_id']);
$user_id = $_SESSION['user_id'];

try {
    // Vérifier si l'utilisateur connecté est bien le créateur de l'événement
    $check_query = "SELECT createur_id, image_url FROM evenements WHERE id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "i", $event_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $evenement = mysqli_fetch_assoc($result);

    if (!$evenement) {
        echo json_encode(['success' => false, 'error' => 'Événement non trouvé']);
        exit;
    }

    if ($evenement['createur_id'] != $user_id) {
        echo json_encode(['success' => false, 'error' => 'Vous n\'êtes pas autorisé à supprimer cet événement']);
        exit;
    }

    // Supprimer les participants associés à l'événement
    $delete_participants_query = "DELETE FROM evenement_participants WHERE evenement_id = ?";
    $delete_participants_stmt = mysqli_prepare($conn, $delete_participants_query);
    mysqli_stmt_bind_param($delete_participants_stmt, "i", $event_id);
    mysqli_stmt_execute($delete_participants_stmt);

    // Supprimer l'événement de la base de données
    $delete_event_query = "DELETE FROM evenements WHERE id = ?";
    $delete_event_stmt = mysqli_prepare($conn, $delete_event_query);
    mysqli_stmt_bind_param($delete_event_stmt, "i", $event_id);
    
    if (mysqli_stmt_execute($delete_event_stmt)) {
        // Supprimer l'image associée si elle existe
        if ($evenement['image_url'] && file_exists('../' . $evenement['image_url'])) {
            unlink('../' . $evenement['image_url']);
        }
        echo json_encode(['success' => true, 'message' => 'Événement supprimé avec succès']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression de l\'événement']);
    }

    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($delete_participants_stmt);
    mysqli_stmt_close($delete_event_stmt);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Une erreur est survenue : ' . $e->getMessage()]);
}
?> 