<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.html');
    exit;
}

try {
    // Utiliser les constantes définies dans database.php
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérification que les champs sont bien envoyés
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Afficher l'ID de l'artisan
    error_log("Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'non défini'));
    
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $categorie = $_POST['categorie'] ?? '';

    // Debug: Afficher les données reçues
    error_log("Données reçues - Nom: $nom, Description: $description, Prix: $prix, Catégorie: $categorie");

    // Gestion de l'image (upload)
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/articles/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Création du dossier s'il n'existe pas
        }

            $fileTmpPath = $_FILES['image_url']['tmp_name'];
            $fileName = basename($_FILES['image_url']['name']);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            
            // Vérifier que c'est bien une image
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                echo "Le fichier doit être une image (jpg, jpeg, png ou gif).";
                exit;
            }
            
            // Générer un nom de fichier unique
            $newFileName = uniqid() . '-' . $fileName;
            $targetFilePath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            // Sauvegarder le chemin relatif complet dans la base de données
            $imageDbPath = '../assets/articles/' . $newFileName;
            $sql = "INSERT INTO produits (nom, description, prix, image_url, categorie, artisan_id)
                    VALUES (:nom, :description, :prix, :image_url, :categorie, :artisan_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':prix' => $prix,
                ':image_url' => $imageDbPath,
                ':categorie' => $categorie,
                ':artisan_id' => $_SESSION['user_id']
            ]);

            // Debug: Vérifier si l'insertion a réussi
            error_log("Produit ajouté avec succès. ID: " . $pdo->lastInsertId());

            // Rediriger vers la page mes-annonces après l'ajout réussi
            header('Location: ../pages/mes-annonces.html');
            exit;
        } else {
            echo "Erreur lors du téléchargement de l'image.";
        }
    } else {
        echo "Image manquante ou incorrecte.";
    }
} else {
    echo "Méthode non autorisée.";
}
?>