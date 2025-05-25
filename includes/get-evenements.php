<?php
require_once '../config/database.php';

try {
    // Récupérer les événements à venir
    $query = "SELECT e.*, u.nom as createur_nom, u.prenom as createur_prenom,
              (SELECT COUNT(*) FROM evenement_participants WHERE evenement_id = e.id) as nombre_participants
              FROM evenements e
              JOIN utilisateurs u ON e.createur_id = u.id
              WHERE e.date_fin >= NOW()
              ORDER BY e.date_debut ASC";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $evenements = array();
        while ($row = mysqli_fetch_assoc($result)) {
            // Formater les dates pour l'affichage
            $row['date_debut_formatted'] = date('d/m/Y H:i', strtotime($row['date_debut']));
            $row['date_fin_formatted'] = date('d/m/Y H:i', strtotime($row['date_fin']));
            $evenements[] = $row;
        }
        
        echo json_encode($evenements);
    } else {
        echo json_encode(['error' => 'Erreur lors de la récupération des événements']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
}
?> 