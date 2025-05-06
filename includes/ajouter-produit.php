<?php
// 1. Connexion à la base de données
$host = 'localhost';
$dbname = 'nom_de_ta_base';  // remplace par le vrai nom
$username = 'root';          // ou autre identifiant
$password = '';              // mot de passe si besoin

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// 2. Vérification que les champs sont bien envoyés
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $categorie = $_POST['categorie'] ?? '';

    // Gestion de l'image (upload)
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Création du dossier s’il n’existe pas
        }

        $fileTmpPath = $_FILES['image_url']['tmp_name'];
        $fileName = basename($_FILES['image_url']['name']);
        $targetFilePath = $uploadDir . uniqid() . '-' . $fileName;

        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            // 3. Requête d'insertion
            $sql = "INSERT INTO produits (nom, description, prix, image_url, categorie)
                    VALUES (:nom, :description, :prix, :image_url, :categorie)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':prix' => $prix,
                ':image_url' => $targetFilePath,
                ':categorie' => $categorie
            ]);

            echo "Produit ajouté avec succès !";
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