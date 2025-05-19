<?php
require_once '../config/database.php';

$produitId = $_GET['produitId'] ?? null;

if (!$produitId) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du produit manquant']);
    exit;
}

try {
    // Récupérer les avis avec les informations des clients
    $query = "SELECT a.*, u.nom, u.prenom 
              FROM avis a 
              JOIN utilisateurs u ON a.client_id = u.id 
              WHERE a.produit_id = ? 
              ORDER BY a.date_creation DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $produitId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $avis = [];
    while ($row = $result->fetch_assoc()) {
        // Masquer l'email pour la confidentialité
        unset($row['email']);
        $avis[] = $row;
    }
    
    echo json_encode(['success' => true, 'avis' => $avis]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des avis: ' . $e->getMessage()]);
} 