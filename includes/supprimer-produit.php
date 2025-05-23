<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

// Vérifier si l'ID du produit est fourni
if (!isset($_POST['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID du produit manquant']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier que le produit appartient bien à l'artisan et récupérer l'image_url
    $stmt = $pdo->prepare("SELECT image_url FROM produits WHERE id = :id AND artisan_id = :artisan_id");
    $stmt->execute([
        ':id' => $_POST['id'],
        ':artisan_id' => $_SESSION['user_id']
    ]);
    
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Produit non trouvé ou non autorisé']);
        exit;
    }

    // Supprimer le fichier image s'il existe
    if (!empty($produit['image_url'])) {
        $imagePath = '../assets/articles/' . basename($produit['image_url']);
        if (file_exists($imagePath)) {
            if (unlink($imagePath)) {
                error_log("Image supprimée avec succès : " . $imagePath);
            } else {
                error_log("Erreur lors de la suppression de l\'image : " . $imagePath);
                // Continuer la suppression dans la base de données même si l'image n'est pas supprimée
            }
        } else {
            error_log("Fichier image non trouvé, impossible de supprimer : " . $imagePath);
            // Continuer la suppression dans la base de données même si le fichier est introuvable
        }
    }
    
    // Supprimer le produit de la base de données
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = :id AND artisan_id = :artisan_id");
    $stmt->execute([
        ':id' => $_POST['id'],
        ':artisan_id' => $_SESSION['user_id']
    ]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur lors de la suppression du produit']);
}
?> 