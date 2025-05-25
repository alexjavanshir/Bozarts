<?php

session_start();
require_once '../config/database.php';

// Récupérer toutes les FAQ
$query = "SELECT id, question, titre_reponse, reponse FROM faq ORDER BY id";
$result = mysqli_query($conn, $query);

if ($result) {
    $faqs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $faqs[] = [
            'id' => $row['id'],
            'question' => $row['question'],
                'titre_reponse' => $row['titre_reponse'],
            'reponse' => $row['reponse']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($faqs);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur lors de la récupération des FAQ']);
}
exit;
?>