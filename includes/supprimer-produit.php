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
    
    // Vérifier que le produit appartient bien à l'artisan
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id AND artisan_id = :artisan_id");
    $stmt->execute([
        ':id' => $_POST['id'],
        ':artisan_id' => $_SESSION['user_id']
    ]);
    
    if ($stmt->rowCount() === 0) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Produit non trouvé ou non autorisé']);
        exit;
    }
    
    // Supprimer le produit
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