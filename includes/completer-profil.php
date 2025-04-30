<?php
session_start();
require_once "../config/database.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/inscription.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
    $prenom = filter_var($_POST['prenom'], FILTER_SANITIZE_STRING);
    $type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
    $adresse = filter_var($_POST['adresse'], FILTER_SANITIZE_STRING);
    $telephone = filter_var($_POST['telephone'], FILTER_SANITIZE_STRING);

    // Mettre à jour le profil utilisateur
    $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, type = ?, adresse = ?, telephone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nom, $prenom, $type, $adresse, $telephone, $user_id);

    if ($stmt->execute()) {
        // Rediriger vers la page de profil
        header("Location: ../pages/profil.html?id=" . $user_id);
        exit();
    } else {
        die("Erreur lors de la mise à jour du profil : " . $stmt->error);
    }
}

// Si ce n'est pas une requête POST, rediriger vers le formulaire HTML
header("Location: ../pages/completer-profil.html");
exit();
?>