<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../connexion.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION["id"];
    $produit_id = trim($_POST["produit_id"]);
    $quantite = trim($_POST["quantite"]);

    // Récupération des informations du produit
    $sql = "SELECT artisan_id, prix FROM produits WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $produit_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $artisan_id, $prix);
            if (mysqli_stmt_fetch($stmt)) {
                // Insertion de la commande
                $sql = "INSERT INTO commandes (client_id, produit_id, artisan_id, quantite) VALUES (?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "iiii", $client_id, $produit_id, $artisan_id, $quantite);
                    if (mysqli_stmt_execute($stmt)) {
                        header("location: ../mes-transactions.html");
                        exit();
                    } else {
                        $error = "Une erreur est survenue lors de la commande.";
                    }
                }
            } else {
                $error = "Produit non trouvé.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>