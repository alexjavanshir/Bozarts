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
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $categorie = $_POST['categorie'] ?? '';

    // Validation des données
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom du produit est requis";
    if (empty($description)) $errors[] = "La description est requise";
    if (empty($prix) || !is_numeric($prix)) $errors[] = "Le prix doit être un nombre valide";
    if (empty($categorie)) $errors[] = "La catégorie est requise";

    if (empty($errors)) {
        // Gestion de l'image (upload) - Temporairement désactivé
        /*
        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/images/';

            $fileTmpPath = $_FILES['image_url']['tmp_name'];
            $fileName = basename($_FILES['image_url']['name']);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            
            // Vérifier que c'est bien une image
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                echo "Le fichier doit être une image (jpg, jpeg, png ou gif).";
                exit;
            }
            
            $targetFilePath = $uploadDir . uniqid() . '-' . $fileName;

            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                // Chemin relatif pour la base de données
                $dbFilePath = str_replace('../', '', $targetFilePath);
        */
                
                // Requête d'insertion
                $sql = "INSERT INTO produits (artisan_id, nom, description, prix, categorie)
                        VALUES (:artisan_id, :nom, :description, :prix, :categorie)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':artisan_id' => $_SESSION['user_id'],
                    ':nom' => $nom,
                    ':description' => $description,
                    ':prix' => $prix,
                    ':categorie' => $categorie
                ]);

                // Redirection vers la page des annonces
                header('Location: ../pages/mes-annonces.html?success=1');
                exit;
        /*
            } else {
                echo "Erreur lors du téléchargement de l'image.";
            }
        } else {
            echo "Image manquante ou incorrecte.";
        }
        */
    } else {
        // Afficher les erreurs
        echo "<div class='error-message'>";
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        echo "</div>";
        echo "<a href='../pages/ajouter-produit.html'>Retour au formulaire</a>";
    }
} else {
    // Redirection vers le formulaire si accès direct
    header('Location: ../pages/ajouter-produit.html');
    exit;
}
?>