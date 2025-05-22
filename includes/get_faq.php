<?php

session_start();
require_once '../config/database.php';

$query = "SELECT id, question, titre_reponse, reponse FROM faq ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$faqs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $faqs[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($faqs);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des FAQ: ' . mysqli_error($conn)]);
}
?>