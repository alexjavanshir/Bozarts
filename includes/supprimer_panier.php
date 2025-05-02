<?php
session_start();
require_once '../config/database.php';

// Pour le débogage
if (!file_exists("../logs")) {
    mkdir("../logs", 0755, true);
}
$log_file = fopen("../logs/panier_suppression.log", "a");
fwrite($log_file, "--------- " . date('Y-m-d H:i:s') . " ---------\n");
fwrite($log_file, "Session: " . print_r($_SESSION, true) . "\n");
fwrite($log_file, "POST: " . print_r($_POST, true) . "\n");

if (!isset($_SESSION["user_id"])) {
    fwrite($log_file, "Non connecté, redirection vers connexion.html\n");
    fclose($log_file);
    header("location: ../pages/connexion.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION["user_id"];
    $produit_id = intval(trim($_POST["produit_id"]));
    
    fwrite($log_file, "Suppression du produit $produit_id pour l'utilisateur $client_id\n");

    // Supprimer le produit du panier
    $sql = "DELETE FROM paniers WHERE client_id = ? AND produit_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $client_id, $produit_id);
        $result = mysqli_stmt_execute($stmt);
        fwrite($log_file, "Résultat de la suppression: " . ($result ? "Succès" : "Échec") . "\n");
        if (!$result) {
            fwrite($log_file, "Erreur lors de la suppression: " . mysqli_error($conn) . "\n");
        }
        mysqli_stmt_close($stmt);
    } else {
        fwrite($log_file, "Erreur préparation DELETE: " . mysqli_error($conn) . "\n");
    }
    mysqli_close($conn);
}

fwrite($log_file, "Fin du script de suppression\n");
fclose($log_file);
?> 