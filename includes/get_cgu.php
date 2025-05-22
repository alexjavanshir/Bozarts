<?php
session_start();
require_once '../config/database.php';

// Récupérer toutes les CGU
$query = "SELECT id, titre, contenu FROM cgu ORDER BY ordre";
$result = mysqli_query($conn, $query);

if ($result) {
    $cgus = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cgus[] = [
            'id' => $row['id'],
            'titre' => $row['titre'],
            'contenu' => $row['contenu']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($cgus);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur lors de la récupération des CGU']);
}
exit;
?> 