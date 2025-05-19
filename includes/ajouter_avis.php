<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Vous devez être connecté pour laisser un avis']);
    exit;
}

// Récupérer les données POST
$data = json_decode(file_get_contents('php://input'), true);
$produitId = $data['produitId'] ?? null;
$note = $data['note'] ?? null;
$commentaire = $data['commentaire'] ?? null;

if (!$produitId || !$note || !$commentaire) {
    http_response_code(400);
    echo json_encode(['error' => 'Tous les champs sont requis']);
    exit;
}

// Vérifier que la note est entre 1 et 5
if ($note < 1 || $note > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'La note doit être comprise entre 1 et 5']);
    exit;
}

try {
    // Récupérer l'ID de l'artisan pour ce produit
    $stmt = $conn->prepare("SELECT artisan_id FROM produits WHERE id = ?");
    $stmt->bind_param("i", $produitId);
    $stmt->execute();
    $result = $stmt->get_result();
    $produit = $result->fetch_assoc();

    if (!$produit) {
        http_response_code(404);
        echo json_encode(['error' => 'Produit non trouvé']);
        exit;
    }

    // Ajouter l'avis
    $stmt = $conn->prepare("INSERT INTO avis (client_id, artisan_id, produit_id, note, commentaire, date_creation) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiiss", $_SESSION['user_id'], $produit['artisan_id'], $produitId, $note, $commentaire);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Avis ajouté avec succès']);
    } else {
        throw new Exception("Erreur lors de l'ajout de l'avis");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'ajout de l\'avis: ' . $e->getMessage()]);
}