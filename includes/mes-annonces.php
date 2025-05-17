<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les produits de l'artisan
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE artisan_id = :artisan_id ORDER BY id DESC");
    $stmt->execute([':artisan_id' => $_SESSION['user_id']]);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Afficher l'ID de l'artisan et le nombre de produits trouvés
    error_log("Artisan ID: " . $_SESSION['user_id']);
    error_log("Nombre de produits trouvés: " . count($produits));

    // Si aucun produit
    if (empty($produits)) {
        echo '<p class="no-products">Vous n\'avez pas encore ajouté de produits.</p>';
        exit;
    }

    // Afficher chaque produit
    foreach ($produits as $produit) {
        echo '<div class="product-card">';
        if (!empty($produit['image_url'])) {
            echo '<img src="' . htmlspecialchars($produit['image_url']) . '" alt="' . htmlspecialchars($produit['nom']) . '">';
        }
        echo '<h3>' . htmlspecialchars($produit['nom']) . '</h3>';
        echo '<p class="description">' . htmlspecialchars($produit['description']) . '</p>';
        echo '<p class="price">' . number_format($produit['prix'], 2) . ' €</p>';
        echo '<p class="category">Catégorie: ' . htmlspecialchars($produit['categorie']) . '</p>';
        echo '<div class="product-actions">';
        echo '<button class="delete-button" data-id="' . $produit['id'] . '">Supprimer</button>';
        echo '</div>';
        echo '</div>';
    }
} catch (PDOException $e) {
    echo '<p class="error">Erreur lors du chargement des produits</p>';
}
?> 