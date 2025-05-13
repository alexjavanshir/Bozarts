<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.html');
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les produits de l'artisan
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE artisan_id = :artisan_id ORDER BY id DESC");
    $stmt->execute([':artisan_id' => $_SESSION['user_id']]);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?> 