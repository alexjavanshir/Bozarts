<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../connexion.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION["id"];
    $artisan_id = trim($_POST["artisan_id"]);
    $note = trim($_POST["note"]);
    $commentaire = trim($_POST["commentaire"]);

    // Validation de la note
    if ($note < 1 || $note > 5) {
        $error = "La note doit être comprise entre 1 et 5.";
    } else {
        $sql = "INSERT INTO avis (client_id, artisan_id, note, commentaire) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiis", $client_id, $artisan_id, $note, $commentaire);
            if (mysqli_stmt_execute($stmt)) {
                header("location: ../profil-artisan.php?id=" . $artisan_id);
                exit();
            } else {
                $error = "Une erreur est survenue lors de l'ajout de l'avis.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>