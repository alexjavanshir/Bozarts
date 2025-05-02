<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION["user_id"])) {
    header("location: ../pages/connexion.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION["user_id"];
    $produit_id = intval(trim($_POST["produit_id"]));

    // Supprimer le produit du panier
    $sql = "DELETE FROM paniers WHERE client_id = ? AND produit_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $client_id, $produit_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?> 