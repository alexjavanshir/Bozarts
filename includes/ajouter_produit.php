<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../connexion.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $artisan_id = $_SESSION["id"];
    $nom = trim($_POST["nom"]);
    $description = trim($_POST["description"]);
    $prix = trim($_POST["prix"]);
    $materiau = trim($_POST["materiau"]);
    $dimensions = trim($_POST["dimensions"]);
    $delai_fabrication = trim($_POST["delai_fabrication"]);

    // Gestion de l'upload d'image
    $target_dir = "../uploads/products/";
    $image_url = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Vérification du type de fichier
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $error = "Désolé, seuls les fichiers JPG, JPEG et PNG sont autorisés.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = "uploads/products/" . basename($_FILES["image"]["name"]);
            } else {
                $error = "Une erreur est survenue lors de l'upload de l'image.";
            }
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO produits (artisan_id, nom, description, prix, materiau, dimensions, delai_fabrication, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "issdssis", $artisan_id, $nom, $description, $prix, $materiau, $dimensions, $delai_fabrication, $image_url);
            if (mysqli_stmt_execute($stmt)) {
                header("location: ../mes-annonces.html");
                exit();
            } else {
                $error = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>